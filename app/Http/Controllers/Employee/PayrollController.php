<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Payroll;
use DataTables;
use Illuminate\Http\Request;

class PayrollController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $assets = ['datatable'];
        return view('backend.employee.payroll.list', compact('assets'));
    }

    public function get_table_data() {
        $payrolls = Payroll::with('staff')->select('payslips.*')
            ->where('payslips.employee_id', auth()->user()->employee->id);

        return Datatables::eloquent($payrolls)
            ->editColumn('staff.first_name', function ($payroll) {
                return $payroll->staff->name;
            })
            ->editColumn('month', function ($payroll) {
                return date('F', mktime(0, 0, 0, $payroll->month, 10));
            })
            ->editColumn('net_salary', function ($payroll) {
                return decimalPlace($payroll->net_salary, currency_symbol());
            })
            ->editColumn('status', function ($payroll) {
                return payroll_status($payroll->status);
            })
            ->addColumn('action', function ($payroll) {
                return '<div class="text-center">'
                . '<a class="btn btn-outline-primary btn-xs" href="' . route('my_payslips.show', $payroll['id']) . '"><i class="fas fa-eye"></i> ' . _lang('Details') . '</a>'
                    . '</div>';

            })
            ->filterColumn('staff.first_name', function ($query, $keyword) {
                $query->whereHas('staff', function ($query) use ($keyword) {
                    return $query->where("first_name", "like", "{$keyword}%")
                        ->orWhere("last_name", "like", "{$keyword}%");
                });
            }, true)
            ->setRowId(function ($payroll) {
                return "row_" . $payroll->id;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $alert_col    = 'col-lg-8 offset-lg-2';
        $payroll      = Payroll::with('staff', 'payroll_benefits')->find($id);
        $working_days = Attendance::whereMonth('date', $payroll->month)
            ->whereYear('date', $payroll->year)
            ->groupBy('date')->get()->count();
        $absence = Attendance::where('employee_id', $payroll->employee_id)
            ->selectRaw("SUM(CASE WHEN attendance.leave_duration = 'half_day' THEN 0.5 ELSE 1 END) as absence")
            ->whereMonth('date', $payroll->month)
            ->whereYear('date', $payroll->year)
            ->where('attendance.status', 0)
            ->first()
            ->absence;
        $currency_symbol = currency_symbol();
        return view('backend.employee.payroll.view', compact('payroll', 'id', 'currency_symbol', 'working_days', 'absence'));
    }

}