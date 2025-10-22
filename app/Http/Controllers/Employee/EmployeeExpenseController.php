<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\EmployeeExpense;
use DataTables;
use Illuminate\Http\Request;
use Validator;

class EmployeeExpenseController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $assets = ['datatable'];
        return view('backend.employee.employee_expense.list', compact('assets'));
    }

    public function get_table_data() {
        $employeeexpenses = EmployeeExpense::select('employee_expenses.*')
            ->with('employee', 'category')
            ->where('employee_expenses.employee_id', auth()->user()->employee->id)
            ->orderBy("employee_expenses.id", "desc");

        return Datatables::eloquent($employeeexpenses)
            ->editColumn('employee.first_name', function ($employeeexpense) {
                return $employeeexpense->employee->name;
            })
            ->editColumn('amount', function ($employeeexpense) {
                return decimalPlace($employeeexpense->amount, currency_symbol());
            })
            ->editColumn('status', function ($employeeexpense) {
                if ($employeeexpense->status == 0) {
                    return show_status(_lang('Pending'), 'warning');
                }
                return show_status(_lang('Completed'), 'success');
            })
            ->addColumn('action', function ($employeeexpense) {
                return '<div class="text-center">'
                . '<a class="btn btn-outline-primary btn-xs ajax-modal" href="' . route('my_expenses.show', $employeeexpense['id']) . '" data-title="' . _lang('Expense Details') . '"><i class="ti-eye"></i> ' . _lang('Details') . '</a>'
                    . '</div>';
            })
            ->filterColumn('employee.first_name', function ($query, $keyword) {
                $query->whereHas('employee', function ($query) use ($keyword) {
                    return $query->where("first_name", "like", "{$keyword}%")
                        ->orWhere("last_name", "like", "{$keyword}%");
                });
            }, true)
            ->setRowId(function ($employeeexpense) {
                return "row_" . $employeeexpense->id;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.employee.employee_expense.modal.create');
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'trans_date'          => 'required',
            'expense_category_id' => 'required',
            'amount'              => 'required|numeric',
            'attachment'          => 'nullable|file|mimes:jpeg,JPEG,png,PNG,jpg,doc,pdf,docx,zip|max:4096',
            'description'         => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('my_expenses.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $attachment = '';
        if ($request->hasfile('attachment')) {
            $file       = $request->file('attachment');
            $attachment = time() . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/media/", $attachment);
        }

        $employeeexpense                      = new EmployeeExpense();
        $employeeexpense->trans_date          = $request->input('trans_date');
        $employeeexpense->employee_id         = auth()->user()->employee->id;
        $employeeexpense->bill_no             = $request->input('bill_no');
        $employeeexpense->expense_category_id = $request->input('expense_category_id');
        $employeeexpense->amount              = $request->input('amount');
        $employeeexpense->description         = $request->input('description');
        $employeeexpense->attachment          = $attachment;

        $employeeexpense->save();

        if (!$request->ajax()) {
            return redirect()->route('my_expenses.create')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $employeeexpense, 'table' => '#employee_expenses_table']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $employeeexpense = EmployeeExpense::where('id', $id)
            ->where('employee_expenses.employee_id', auth()->user()->employee->id)
            ->first();
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.employee.employee_expense.modal.view', compact('employeeexpense', 'id'));
        }
    }

}
