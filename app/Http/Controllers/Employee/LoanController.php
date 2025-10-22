<?php

namespace App\Http\Controllers\Employee;

use Exception;
use Validator;
use DataTables;
use App\Models\User;
use App\Models\LoanType;
use App\Models\EmployeeLoan;
use Illuminate\Http\Request;
use App\Models\LoanRepayment;
use App\Http\Controllers\Controller;
use App\Notifications\NewLoanApplication;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Notification;

class LoanController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        date_default_timezone_set(get_option('timezone', 'Asia/Dhaka'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $assets = ['datatable'];
        $status = $request->input('status', null);
        return view('backend.employee.employee_loans.list', compact('status', 'assets'));
    }

    public function get_table_data($status = '') {
        $employeeloans = EmployeeLoan::select('employee_loans.*')
            ->with('employee')
            ->when($status, function (Builder $query, $status) {
                $query->where('status', $status);
            })
            ->where('employee_loans.employee_id', auth()->user()->employee->id)
            ->orderBy("employee_loans.id", "desc");

        $currency_symbol = currency_symbol();

        return Datatables::eloquent($employeeloans)
            ->editColumn('employee.first_name', function ($employeeloan) {
                return $employeeloan->employee->name;
            })
            ->editColumn('interest_rate', function ($employeeloan) {
                return $employeeloan->interest_rate . '%';
            })
            ->editColumn('status', function ($employeeloan) {
                if ($employeeloan->status == 'pending') {
                    return '<span class="badge badge-warning">' . ucwords($employeeloan->status) . '<span>';
                } else if ($employeeloan->status == 'approved') {
                    return '<span class="badge badge-success">' . ucwords($employeeloan->status) . '<span>';
                } else if ($employeeloan->status == 'rejected') {
                    return '<span class="badge badge-danger">' . ucwords($employeeloan->status) . '<span>';
                } else if ($employeeloan->status == 'repaid') {
                    return '<span class="badge badge-primary">' . ucwords($employeeloan->status) . '<span>';
                }
            })
            ->editColumn('loan_amount', function ($employeeloan) use ($currency_symbol) {
                return decimalPlace($employeeloan->loan_amount, $currency_symbol);
            })
            ->editColumn('remaining_balance', function ($employeeloan) use ($currency_symbol) {
                return decimalPlace($employeeloan->remaining_balance, $currency_symbol);
            })
            ->editColumn('monthly_installment', function ($employeeloan) use ($currency_symbol) {
                return decimalPlace($employeeloan->monthly_installment, $currency_symbol);
            })
            ->addColumn('action', function ($employeeloan) {
                return '<div class="text-center">'
                . '<a class="btn btn-outline-primary btn-xs" href="' . route('my_loans.show', $employeeloan['id']) . '"><i class="fas fa-eye"></i> ' . _lang('Details') . '</a>'
                    . '</div>';
            })
            ->filterColumn('employee.first_name', function ($query, $keyword) {
                $query->whereHas('staff', function ($query) use ($keyword) {
                    return $query->where("first_name", "like", "{$keyword}%")
                        ->orWhere("last_name", "like", "{$keyword}%");
                });
            }, true)
            ->setRowId(function ($employeeloan) {
                return "row_" . $employeeloan->id;
            })
            ->rawColumns(['loan_amount', 'remaining_balance', 'monthly_installment', 'action', 'status'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $alert_col = 'col-lg-8 offset-lg-2';
        return view('backend.employee.employee_loans.create', compact('alert_col'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'loan_type_id' => 'required|exists:employee_loan_types,id',
            'loan_amount'  => 'required|numeric',
            'loan_purpose' => 'required',
            'attachment'   => 'nullable|mimes:png,jpg,jpeg,pdf,doc,docx,xlsx,csv|max:4096',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('my_loans.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $loanType = LoanType::find($request->loan_type_id);

        if ($request->loan_amount < $loanType->minimum_amount || $request->loan_amount > $loanType->maximum_amount) {
            return back()->with('error', _lang('Loan amount must be') . ' ' . $loanType->minimum_amount . ' ' . _lang('to') . ' ' . $loanType->maximum_amount)->withInput();
        }

        // Handle file attachment
        $attachment = '';
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('loans', 'public');
        }

        $employeeloan                      = new EmployeeLoan();
        $employeeloan->loan_type_id        = $request->loan_type_id;
        $employeeloan->application_date    = now();
        $employeeloan->employee_id         = auth()->user()->employee->id;
        $employeeloan->loan_amount         = $request->input('loan_amount');
        $employeeloan->remaining_balance   = $request->input('loan_amount');
        $employeeloan->interest_rate       = $loanType->interest_rate;
        $employeeloan->interest_type       = $loanType->interest_type;
        $employeeloan->monthly_installment = $employeeloan->loan_amount / $loanType->term;
        $employeeloan->loan_purpose        = $request->input('loan_purpose');
        $employeeloan->attachment          = $attachment;
        $employeeloan->description         = $request->input('description');
        $employeeloan->remarks             = $request->input('remarks');
        $employeeloan->created_user_id     = auth()->id();

        $employeeloan->save();

        try {
            $users = User::where('user_type', 'admin')->get();
            Notification::send($users, new NewLoanApplication($employeeloan));
        } catch (Exception $e) {}

        return redirect()->route('my_loans.index')->with('success', _lang('Loan application submitted'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $alert_col    = 'col-lg-10 offset-lg-1';
        $employeeloan = EmployeeLoan::where('id', $id)
            ->where('employee_loans.employee_id', auth()->user()->employee->id)
            ->first();
        $interest = $employeeloan->interest_type == 'declining' ? (($employeeloan->interest_rate / 100) * $employeeloan->remaining_balance) / 12 : ($employeeloan->interest_rate / 100) * $employeeloan->loan_amount;

        return view('backend.employee.employee_loans.view', compact('employeeloan', 'id', 'alert_col', 'interest'));
    }

    public function repayments() {
        $assets = ['datatable'];
        return view('backend.employee.employee_loans.loan_repayments', compact('assets'));
    }

    public function get_repayment_table_data() {
        $loanrepayments = LoanRepayment::select('loan_repayments.*')
            ->with('loan', 'employee')
            ->where('loan_repayments.employee_id', auth()->user()->employee->id)
            ->orderBy("loan_repayments.id", "desc");

        $currency = currency_symbol();

        return Datatables::eloquent($loanrepayments)
            ->editColumn('principle_amount', function ($employeeloan) use ($currency) {
                return decimalPlace($employeeloan->principle_amount, $currency);
            })
            ->editColumn('interest', function ($employeeloan) use ($currency) {
                return decimalPlace($employeeloan->interest, $currency);
            })
            ->editColumn('employee.first_name', function ($employeeloan) {
                return $employeeloan->employee->name;
            })
            ->filterColumn('employee.first_name', function ($query, $keyword) {
                $query->whereHas('employee', function ($query) use ($keyword) {
                    return $query->where("first_name", "like", "{$keyword}%")
                        ->orWhere("last_name", "like", "{$keyword}%");
                });
            }, true)
            ->setRowId(function ($loanrepayment) {
                return "row_" . $loanrepayment->id;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

}