<?php

namespace App\Http\Controllers;

use Exception;
use Validator;
use DataTables;
use App\Models\Account;
use App\Models\LoanType;
use App\Models\Transaction;
use App\Models\EmployeeLoan;
use Illuminate\Http\Request;
use App\Models\LoanRepayment;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\LoanApplicationApproved;
use App\Notifications\LoanApplicationRejected;

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
        return view('backend.admin.employee_loans.list', compact('status', 'assets'));
    }

    public function get_table_data($status = '') {
        $employeeloans = EmployeeLoan::select('employee_loans.*')
            ->with('employee')
            ->when($status, function (Builder $query, $status) {
                $query->where('status', $status);
            })
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
                $editButton           = '';
                $approveRejectButtons = '';
                if ($employeeloan->status == 'pending') {
                    $editButton = '<a class="dropdown-item" href="' . route('employee_loans.edit', $employeeloan['id']) . '"><i class="fas fa-pencil-alt"></i> ' . _lang('Edit') . '</a>';
                    $approveRejectButtons .= '<a class="dropdown-item ajax-modal-2 text-success" data-title="' . _lang('Loan Approve & Disbursement') . '" href="' . route('employee_loans.approve', $employeeloan['id']) . '"><i class="fas fa-check-circle"></i> ' . _lang('Approve') . '</a>';
                    $approveRejectButtons .= '<a class="dropdown-item text-danger" href="' . route('employee_loans.reject', $employeeloan['id']) . '"><i class="fas fa-times-circle"></i> ' . _lang('Reject') . '</a>';
                }

                return '<div class="dropdown text-center">'
                . '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                . '</button>'
                . '<div class="dropdown-menu">'
                . $editButton
                . '<a class="dropdown-item" href="' . route('employee_loans.show', $employeeloan['id']) . '"><i class="fas fa-eye"></i> ' . _lang('Details') . '</a>'
                . $approveRejectButtons
                . '<form action="' . route('employee_loans.destroy', $employeeloan['id']) . '" method="post">'
                . csrf_field()
                . '<input name="_method" type="hidden" value="DELETE">'
                . '<button class="dropdown-item btn-remove" type="submit"><i class="fas fa-trash-alt"></i> ' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
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
        return view('backend.admin.employee_loans.create', compact('alert_col'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'loan_id'          => 'required|unique:employee_loans',
            'loan_type_id'     => 'required|exists:employee_loan_types,id',
            'application_date' => 'required',
            'employee_id'      => 'required|exists:employees,id',
            'loan_amount'      => 'required|numeric',
            'loan_purpose'     => 'required',
            'attachment'       => 'nullable|mimes:png,jpg,jpeg,pdf,doc,docx,xlsx,csv|max:4096',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('employee_loans.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $loanType = LoanType::find($request->loan_type_id);

        if($request->loan_amount < $loanType->minimum_amount || $request->loan_amount > $loanType->maximum_amount){
            return back()->with('error',_lang('Loan amount must be').' '.$loanType->minimum_amount.' '._lang('to').' '.$loanType->maximum_amount)->withInput();
        }

        // Handle file attachment
        $attachment = '';
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('loans', 'public');
        }

        $employeeloan                    = new EmployeeLoan();
        $employeeloan->loan_id           = $request->input('loan_id');
        $employeeloan->loan_type_id      = $request->loan_type_id;
        $employeeloan->application_date  = $request->input('application_date');
        $employeeloan->employee_id       = $request->input('employee_id');
        $employeeloan->loan_amount       = $request->input('loan_amount');
        $employeeloan->remaining_balance = $request->input('loan_amount');

        $employeeloan->interest_rate       = $loanType->interest_rate;
        $employeeloan->interest_type       = $loanType->interest_type;
        $employeeloan->monthly_installment = $employeeloan->loan_amount / $loanType->term;

        $employeeloan->loan_purpose    = $request->input('loan_purpose');
        $employeeloan->attachment      = $attachment;
        $employeeloan->description     = $request->input('description');
        $employeeloan->remarks         = $request->input('remarks');
        $employeeloan->created_user_id = auth()->id();

        $employeeloan->save();

        return redirect()->route('employee_loans.index')->with('success', _lang('Loan application submitted'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $alert_col    = 'col-lg-10 offset-lg-1';
        $employeeloan = EmployeeLoan::find($id);
        $interest = $employeeloan->interest_type == 'declining' ? (($employeeloan->interest_rate / 100) * $employeeloan->remaining_balance) / 12 : ($employeeloan->interest_rate / 100) * $employeeloan->loan_amount;
        return view('backend.admin.employee_loans.view', compact('employeeloan', 'id', 'alert_col', 'interest'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $alert_col    = 'col-lg-8 offset-lg-2';
        $employeeloan = EmployeeLoan::where('id', $id)->where('status', 'pending')->first();
        return view('backend.admin.employee_loans.edit', compact('employeeloan', 'id', 'alert_col'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        $validator = Validator::make($request->all(), [
            'loan_id'          => [
                'required',
                Rule::unique('employee_loans')->ignore($id),
            ],
            'loan_type_id'     => 'required|exists:employee_loan_types,id',
            'application_date' => 'required',
            'employee_id'      => 'required|exists:employees,id',
            'loan_amount'      => 'required|numeric',
            'loan_purpose'     => 'required',
            'attachment'       => 'nullable|mimes:png,jpg,jpeg,pdf,doc,docx,xlsx,csv|max:4096',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('employee_loans.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $loanType = LoanType::find($request->loan_type_id);

        // Handle file attachment
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('loans', 'public');
        }

        $employeeloan                   = EmployeeLoan::where('id', $id)->where('status', 'pending')->first();
        $employeeloan->loan_id          = $request->input('loan_id');
        $employeeloan->loan_type_id     = $request->loan_type_id;
        $employeeloan->application_date = $request->input('application_date');
        $employeeloan->employee_id      = $request->input('employee_id');
        $employeeloan->loan_amount      = $request->input('loan_amount');

        $employeeloan->interest_rate       = $loanType->interest_rate;
        $employeeloan->interest_type       = $loanType->interest_type;
        $employeeloan->monthly_installment = $employeeloan->loan_amount / $loanType->term;

        $employeeloan->loan_due_at  = $request->input('loan_due_at');
        $employeeloan->loan_purpose = $request->input('loan_purpose');
        if ($request->hasfile('attachment')) {
            $employeeloan->attachment = $attachment;
        }
        $employeeloan->description = $request->input('description');
        $employeeloan->remarks     = $request->input('remarks');

        $employeeloan->save();

        return redirect()->route('employee_loans.index')->with('success', _lang('Updated Successfully'));

    }

    public function approve(Request $request, $loanId) {
        if ($request->isMethod('get')) {
            if (!$request->ajax()) {
                return back();
            }
            $loan     = EmployeeLoan::where('id', $loanId)->where('status', 'pending')->first();
            $accounts = Account::where('type', 'asset')->where('is_bank', 1)->orderBy('account_id')->get();

            $term    = $loan->loan_amount / $loan->monthly_installment;
            $dueDate = date('Y-m-d', strtotime("+$term month", strtotime(date('Y-m-d'))));

            return view('backend.admin.employee_loans.modal.approve', compact('loan', 'accounts', 'dueDate'));
        } else {
            $validated = $request->validate([
                'loan_id'     => [
                    'required',
                    Rule::unique('employee_loans')->ignore($loanId),
                ],
                'loan_due_at' => 'required',
                'account_id'  => 'required|exists:accounts,id',
            ]);

            DB::beginTransaction();

            $loan                 = EmployeeLoan::find($loanId);
            $loan->status         = 'approved';
            $loan->loan_issued_at = now();
            $loan->loan_due_at    = $request->loan_due_at;
            $loan->decision_date  = now();
            $loan->action_user_id = auth()->id();
            $loan->save();

            $transaction = Transaction::create([
                'transaction_date' => now(),
                'description'      => _lang('Loan Disbursement'),
                'created_user_id'  => auth()->id(),
            ]);

            // Debit - Loan Receivable (Asset)
            $transaction->entries()->create([
                'account_id' => Account::where('slug', 'Employee_Loans_Receivable')->first()->id,
                'amount'     => $loan->loan_amount,
                'type'       => 'debit',
            ]);

            // Credit - Cash or Bank (decreasing the asset)
            $transaction->entries()->create([
                'account_id' => $request->account_id,
                'amount'     => $loan->loan_amount,
                'type'       => 'credit',
            ]);

            DB::commit();

            try {
                $loan->employee->user->notify(new LoanApplicationApproved($loan));
            } catch (Exception $e) {}

            return back()->with('success', _lang('Loan approved and disbursed'));
        }
    }

    public function reject($loanId) {
        $loan                 = EmployeeLoan::where('id', $loanId)->where('status', 'pending')->first();
        $loan->status         = 'rejected';
        $loan->decision_date  = now();
        $loan->action_user_id = auth()->id();
        $loan->save();

        try {
            $loan->employee->user->notify(new LoanApplicationRejected($loan));
        } catch (Exception $e) {}

        return back()->with('success', _lang('Loan rejected'));
    }

    public function repayments() {
        $assets = ['datatable'];
        return view('backend.admin.employee_loans.loan_repayments', compact('assets'));
    }

    public function get_repayment_table_data() {
        $loanrepayments = LoanRepayment::select('loan_repayments.*')
            ->with('loan', 'employee')
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $employeeloan = EmployeeLoan::find($id);
        if ($employeeloan->attachment != null) {
            Storage::disk('public')->delete($employeeloan->attachment);
        }
        $employeeloan->delete();
        return redirect()->route('employee_loans.index')->with('success', _lang('Deleted Successfully'));
    }
}