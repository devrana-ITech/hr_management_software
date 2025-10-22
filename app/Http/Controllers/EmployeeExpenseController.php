<?php

namespace App\Http\Controllers;

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
        return view('backend.admin.employee_expense.list', compact('assets'));
    }

    public function get_table_data() {
        $employeeexpenses = EmployeeExpense::select('employee_expenses.*')
            ->with('employee', 'category')
            ->orderBy("employee_expenses.id", "desc");

        return Datatables::eloquent($employeeexpenses)
            ->editColumn('employee.first_name', function ($employeeexpense) {
                return $employeeexpense->employee->name;
            })
            ->editColumn('amount', function ($employeeexpense) {
                return decimalPlace($employeeexpense->amount, currency_symbol(currency()));
            })
            ->editColumn('status', function ($employeeexpense) {
                if ($employeeexpense->status == 0) {
                    return show_status(_lang('Pending'), 'warning');
                }
                return show_status(_lang('Completed'), 'success');
            })
            ->addColumn('action', function ($employeeexpense) {
                return '<div class="dropdown text-center">'
                . '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                . '</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item ajax-modal" href="' . route('employee_expenses.edit', $employeeexpense['id']) . '" data-title="' . _lang('Update Expense') . '"><i class="ti-pencil-alt"></i> ' . _lang('Edit') . '</a>'
                . '<a class="dropdown-item ajax-modal" href="' . route('employee_expenses.show', $employeeexpense['id']) . '" data-title="' . _lang('Expense Details') . '"><i class="ti-eye"></i> ' . _lang('Details') . '</a>'
                . '<form action="' . route('employee_expenses.destroy', $employeeexpense['id']) . '" method="post">'
                . csrf_field()
                . '<input name="_method" type="hidden" value="DELETE">'
                . '<button class="dropdown-item btn-remove" type="submit"><i class="ti-trash"></i> ' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
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
            return view('backend.admin.employee_expense.modal.create');
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
            'employee_id'         => 'required',
            'expense_category_id' => 'required',
            'amount'              => 'required|numeric',
            'attachment'          => 'nullable|file|mimes:jpeg,JPEG,png,PNG,jpg,doc,pdf,docx,zip|max:4096',
            'status'              => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('employee_expenses.create')
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
        $employeeexpense->employee_id         = $request->input('employee_id');
        $employeeexpense->bill_no             = $request->input('bill_no');
        $employeeexpense->expense_category_id = $request->input('expense_category_id');
        $employeeexpense->amount              = $request->input('amount');
        $employeeexpense->description         = $request->input('description');
        $employeeexpense->attachment          = $attachment;
        $employeeexpense->status              = $request->input('status');

        $employeeexpense->save();

        if (!$request->ajax()) {
            return redirect()->route('employee_expenses.create')->with('success', _lang('Saved Successfully'));
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
        $employeeexpense = EmployeeExpense::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.employee_expense.modal.view', compact('employeeexpense', 'id'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $employeeexpense = EmployeeExpense::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.employee_expense.modal.edit', compact('employeeexpense', 'id'));
        }
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
            'trans_date'          => 'required',
            'employee_id'         => 'required',
            'expense_category_id' => 'required',
            'amount'              => 'required|numeric',
            'attachment'          => 'nullable|file|mimes:jpeg,JPEG,png,PNG,jpg,doc,pdf,docx,zip|max:4096',
            'status'              => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('employee_expenses.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        if ($request->hasfile('attachment')) {
            $file       = $request->file('attachment');
            $attachment = time() . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/media/", $attachment);
        }

        $employeeexpense                      = EmployeeExpense::find($id);
        $employeeexpense->trans_date          = $request->input('trans_date');
        $employeeexpense->employee_id         = $request->input('employee_id');
        $employeeexpense->bill_no             = $request->input('bill_no');
        $employeeexpense->expense_category_id = $request->input('expense_category_id');
        $employeeexpense->amount              = $request->input('amount');
        $employeeexpense->description         = $request->input('description');
        if ($request->hasfile('attachment')) {
            $employeeexpense->attachment = $attachment;
        }
        $employeeexpense->status = $request->input('status');

        $employeeexpense->save();

        if (!$request->ajax()) {
            return redirect()->route('employee_expenses.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $employeeexpense, 'table' => '#employee_expenses_table']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $employeeexpense = EmployeeExpense::find($id);
        $employeeexpense->delete();
        return redirect()->route('employee_expenses.index')->with('success', _lang('Deleted Successfully'));
    }
}
