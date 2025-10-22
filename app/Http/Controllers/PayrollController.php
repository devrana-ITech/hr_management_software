<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeExpense;
use App\Models\EmployeeLoan;
use App\Models\LoanRepayment;
use App\Models\Payroll;
use App\Models\PayrollBenefit;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WorkingHour;
use App\Notifications\PayslipNotification;
use DataTables;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Validator;

class PayrollController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $assets = ['datatable'];
        return view('backend.admin.payroll.list', compact('assets'));
    }

    public function get_table_data() {
        $payrolls = Payroll::with('staff')->select('payslips.*');

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
                if ($payroll->status == 0) {
                    return '<div class="dropdown text-center">'
                    . '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                    . '</button>'
                    . '<div class="dropdown-menu">'
                    . '<a class="dropdown-item" href="' . route('payslips.edit', $payroll['id']) . '"><i class="fas fa-pencil-alt"></i> ' . _lang('Edit') . '</a>'
                    . '<a class="dropdown-item" href="' . route('payslips.show', $payroll['id']) . '"><i class="fas fa-eye"></i> ' . _lang('Details') . '</a>'
                    . '<form action="' . route('payslips.destroy', $payroll['id']) . '" method="post">'
                    . csrf_field()
                    . '<input name="_method" type="hidden" value="DELETE">'
                    . '<button class="dropdown-item btn-remove" type="submit"><i class="fas fa-trash-alt"></i> ' . _lang('Delete') . '</button>'
                        . '</form>'
                        . '</div>'
                        . '</div>';
                } else {
                    return '<div class="dropdown text-center">'
                    . '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                    . '</button>'
                    . '<div class="dropdown-menu">'
                    . '<a class="dropdown-item" href="' . route('payslips.show', $payroll['id']) . '"><i class="fas fa-eye"></i> ' . _lang('Details') . '</a>'
                        . '</div>'
                        . '</div>';
                }
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        $alert_col = 'col-lg-4 offset-lg-4';
        return view('backend.admin.payroll.create', compact('alert_col'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        @ini_set('max_execution_time', 0);
        @set_time_limit(0);

        $validator = Validator::make($request->all(), [
            'month' => 'required',
            'year'  => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('payslips.create')
                ->withErrors($validator)
                ->withInput();
        }

        $month = $request->month;
        $year  = $request->year;

        DB::beginTransaction();

        $employees = Employee::active()
            ->with('benefit_deductions')
            ->whereDoesntHave('payslips', function (Builder $query) use ($month, $year) {
                $query->whereRaw("payslips.month = $month AND payslips.year = $year");
            })
            ->get();

        if ($employees->count() == 0) {
            return back()->with('error', _lang('Payslip is already generated for the selected period !'));
        }

        foreach ($employees as $employee) {
            //It requires if user spend money from his own pocket
            $expense = EmployeeExpense::whereMonth("trans_date", $month)
                ->whereYear("trans_date", $year)
                ->where('employee_id', $employee->id)
                ->where('status', 1)
                ->sum('amount');

            //Process Loan Amount
            $loan_amount   = 0;
            $loan_interest = 0;
            $loans         = EmployeeLoan::where('employee_id', $employee->id)->where('status', 'approved')->get();

            foreach ($loans as $loan) {
                $loan_amount += $loan ? $loan->monthly_installment : 0;
                $loan_interest += $loan->interest_type == 'declining' ? (($loan->interest_rate / 100) * $loan->remaining_balance) / 12 : ($loan->interest_rate / 100) * $loan->loan_amount;
            }

            //Get Absence Fine
            $absence_fine = 0;
            $full_day     = $employee->full_day_absence_fine;
            $half_day     = $employee->half_day_absence_fine;

            $absence_fine = Attendance::select([
                DB::raw("IFNULL(SUM(CASE WHEN leave_duration = 'half_day' THEN $half_day ELSE $full_day END),0) AS absence_fine"),
            ])
                ->where('employee_id', $employee->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->where('attendance.status', 0)
                ->first()
                ->absence_fine;

            $total_benefits  = 0;
            $total_deduction = 0;
            $salary_type     = $employee->salary_type;
            $basic_salary    = $employee->basic_salary;

            if ($salary_type == 'hourly') {
                $workHours = WorkingHour::selectRaw("SUM(work_hour) as work_hour, SUM(hour_deduct) as hour_deduct")
                    ->where('employee_id', $employee->id)
                    ->whereYear("date", $year)
                    ->whereMonth("date", $month)
                    ->first();

                $basic_salary = $basic_salary * ($workHours->work_hour - $workHours->hour_deduct);
            }

            $benefits   = $employee->benefit_deductions()->where('type', 'add')->get();
            $deductions = $employee->benefit_deductions()->where('type', 'deduct')->get();

            foreach ($benefits as $benefit) {
                $total_benefits += $benefit->amount_type == 'percent' ? ($benefit->amount / 100) * $basic_salary : $benefit->amount;
            }

            foreach ($deductions as $deduction) {
                $total_deduction += $deduction->amount_type == 'percent' ? ($deduction->amount / 100) * $basic_salary : $deduction->amount;
            }

            $total_benefits += $basic_salary + $expense;
            $total_deduction += $absence_fine;
            $total_deduction += ($loan_amount + $loan_interest);

            $payroll                 = new Payroll();
            $payroll->employee_id    = $employee->id;
            $payroll->month          = $month;
            $payroll->year           = $year;
            $payroll->current_salary = $basic_salary;
            if ($employee->salary_type == 'hourly') {
                $payroll->working_hours = $workHours->work_hour;
                $payroll->hour_deduct   = $workHours->hour_deduct;
            }
            $payroll->expense       = $expense;
            $payroll->absence_fine  = $absence_fine;
            $payroll->loan          = $loan_amount;
            $payroll->loan_interest = $loan_interest;
            $payroll->net_salary    = ($total_benefits - $total_deduction);
            $payroll->status        = 0;

            $payroll->save();

            foreach ($benefits as $benefit) {
                $payroll->payroll_benefits()->save(new PayrollBenefit([
                    'payslip_id'  => $payroll->id,
                    'type'        => 'add',
                    'name'        => $benefit->name,
                    'amount'      => $benefit->amount,
                    'amount_type' => $benefit->amount_type,
                ]));
            }

            foreach ($deductions as $deduction) {
                $payroll->payroll_benefits()->save(new PayrollBenefit([
                    'payslip_id'  => $payroll->id,
                    'type'        => 'deduct',
                    'name'        => $deduction->name,
                    'amount'      => $deduction->amount,
                    'amount_type' => $deduction->amount_type,
                ]));
            }
        }

        DB::commit();

        if ($payroll->id > 0) {
            return redirect()->route('payslips.index')->with('success', _lang('Payslip Generated Successfully'));
        } else {
            return redirect()->route('payslips.index')->with('error', _lang('Error Occured, Please try again !'));
        }

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
        return view('backend.admin.payroll.view', compact('payroll', 'id', 'currency_symbol', 'working_days', 'absence'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $alert_col = 'col-lg-10 offset-lg-1';
        $payroll   = Payroll::with('staff', 'payroll_benefits')->find($id);
        if ($payroll->status != 0) {
            return back()->with('error', _lang('Sorry, Only unpaid payslip can be modify !'));
        }
        return view('backend.admin.payroll.edit', compact('payroll', 'id', 'alert_col'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        DB::beginTransaction();

        $payroll = Payroll::find($id);
        if ($payroll->status != 0) {
            return back()->with('error', _lang('Sorry, Only unpaid payslip can be modify !'));
        }

        $payroll->payroll_benefits()->whereNotIn('id', isset($request->allowances['payslip_id']) ? $request->allowances['payslip_id'] : [])->delete();
        $payroll->payroll_benefits()->whereNotIn('id', isset($request->deductions['payslip_id']) ? $request->deductions['payslip_id'] : [])->delete();

        $benefits = 0;
        if (isset($request->allowances)) {
            for ($i = 0; $i < count($request->allowances['name']); $i++) {
                $payroll->payroll_benefits()->save(PayrollBenefit::firstOrNew([
                    'id'         => isset($request->allowances['payslip_id'][$i]) ? $request->allowances['payslip_id'][$i] : null,
                    'payslip_id' => $payroll->id,
                    'type'       => 'add',
                ], [
                    'name'        => $request->allowances['name'][$i],
                    'amount'      => $request->allowances['amount'][$i],
                    'amount_type' => $request->allowances['amount_type'][$i],
                ]));
                $benefits += $request->allowances['amount_type'][$i] == 'percent' ? ($request->allowances['amount'][$i] / 100) * $payroll->current_salary : $request->allowances['amount'][$i];
            }
        }

        $deductions = 0;
        if (isset($request->deductions)) {
            for ($i = 0; $i < count($request->deductions['name']); $i++) {
                $payroll->payroll_benefits()->save(PayrollBenefit::firstOrNew([
                    'id'         => isset($request->deductions['payslip_id'][$i]) ? $request->deductions['payslip_id'][$i] : null,
                    'payslip_id' => $payroll->id,
                    'type'       => 'deduct',
                ], [
                    'name'        => $request->deductions['name'][$i],
                    'amount'      => $request->deductions['amount'][$i],
                    'amount_type' => $request->deductions['amount_type'][$i],
                ]));

                $deductions += $request->deductions['amount_type'][$i] == 'percent' ? ($request->deductions['amount'][$i] / 100) * $payroll->current_salary : $request->deductions['amount'][$i];
            }
        }

        $total_benefits  = $payroll->current_salary + $payroll->expense + $benefits;
        $total_deduction = $payroll->absence_fine + $payroll->loan + $payroll->loan_interest + $deductions;

        $payroll->net_salary = ($total_benefits - $total_deduction);
        $payroll->save();

        DB::commit();

        if (!$request->ajax()) {
            return redirect()->route('payslips.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $payroll, 'table' => '#payslips_table']);
        }

    }

    public function make_payment(Request $request) {
        if ($request->isMethod('get')) {
            $alert_col = 'col-lg-4 offset-lg-4';
            return view('backend.admin.payroll.make_payment', compact('alert_col'));
        } else {
            $validator = Validator::make($request->all(), [
                'month'      => 'required',
                'year'       => 'required',
                'account_id' => 'required',
            ], [
                'account_id.required' => _lang('You must select an account'),
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }

            $payslips = Payroll::with('staff')
                ->where('month', $request->month)
                ->where('year', $request->year)
                ->where('status', 0)
                ->get();
            $currency_symbol         = currency_symbol();
            $account_id              = $request->account_id;
            $transaction_category_id = $request->transaction_category_id;
            $alert_col               = 'col-lg-10 offset-lg-1';
            return view('backend.admin.payroll.make_payment', compact('payslips', 'currency_symbol', 'alert_col', 'account_id', 'transaction_category_id'));
        }
    }

    public function store_payment(Request $request) {
        if (empty($request->payslip_ids)) {
            return back()->with('error', _lang('You must select at least one employee'))->withInput();
        }

        $validator = Validator::make($request->all(), [
            'account_id' => 'required',
        ], [
            'account_id.required' => _lang('You must select an account'),
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $account = Account::where('id', $request->account_id)
            ->where('type', 'asset')
            ->where('is_bank', 1)
            ->first();

        if (!$account) {
            return back()->with('error', _lang('Sorry, No account found'))->withInput();
        }

        DB::beginTransaction();

        $payslips = Payroll::whereIn('id', $request->payslip_ids)
            ->where('status', 0)
            ->get();

        $transaction = Transaction::create([
            'transaction_date' => now(),
            'description'      => _lang('Employee Salary'),
            'created_user_id'  => auth()->id(),
        ]);

        //Debit Salary Account
        $transaction->entries()->create([
            'account_id' => Account::where('slug', 'Salaries_and_Wages_Expense')->first()->id,
            'amount'     => $payslips->sum('net_salary'),
            'type'       => 'debit',
        ]);

        //Credit Asset Account
        $transaction->entries()->create([
            'account_id' => $request->account_id,
            'amount'     => $payslips->sum('net_salary'),
            'type'       => 'credit',
        ]);

        $this->loanRepaymentPosting($payslips, $request->account_id, $transaction->id);

        foreach ($payslips as $payslip) {
            $payslip->status         = 1;
            $payslip->transaction_id = $transaction->id;
            $payslip->save();
        }

        DB::commit();

        //Send Notification
        $users = User::whereHas('employee', function ($query) use ($payslips) {
            return $query->whereIn("id", $payslips->pluck('employee_id'));
        })->get();

        try {
            $monthYear = date('F', mktime(0, 0, 0, $payslip->month, 10)) . ',' . $payslip->year;
            Notification::send($users, new PayslipNotification($monthYear));
        } catch (Exception $e) {}

        return redirect()->route('payslips.index')->with('success', _lang('Payment made successfully'));
    }

    private function loanRepaymentPosting($payslips, $account_id, $parent_id) {
        if ($payslips->sum('loan') > 0) {
            $transaction = Transaction::create([
                'transaction_date' => now(),
                'description'      => _lang('Loan Repayment'),
                'parent_id'        => $parent_id,
                'created_user_id'  => auth()->id(),
            ]);

            //Debit Asset Account
            $transaction->entries()->create([
                'account_id' => $account_id,
                'amount'     => $payslips->sum('loan') + $payslips->sum('loan_interest'),
                'type'       => 'debit',
            ]);

            //Credit Loans Receivable Account
            $transaction->entries()->create([
                'account_id' => Account::where('slug', 'Employee_Loans_Receivable')->first()->id,
                'amount'     => $payslips->sum('loan'),
                'type'       => 'credit',
            ]);

            if ($payslips->sum('loan_interest') > 0) {
                //Credit Revenue Account for Loan Interest
                $transaction->entries()->create([
                    'account_id' => Account::where('slug', 'Interest_Income')->first()->id,
                    'amount'     => $payslips->sum('loan_interest'),
                    'type'       => 'credit',
                ]);
            }
        }

        foreach ($payslips as $payslip) {
            if ($payslip->loan > 0) {
                $loans = EmployeeLoan::where('employee_id', $payslip->employee_id)->where('status', 'approved')->get();
                foreach ($loans as $loan) {
                    //Create Repayment History
                    $repayment                   = new LoanRepayment();
                    $repayment->loan_id          = $loan->id;
                    $repayment->employee_id      = $payslip->employee_id;
                    $repayment->principle_amount = $loan->monthly_installment;
                    $repayment->interest         = $loan->interest_type == 'declining' ? (($loan->interest_rate / 100) * $loan->remaining_balance) / 12 : ($loan->interest_rate / 100) * $loan->loan_amount;
                    $repayment->save();

                    $loan->remaining_balance -= $loan->monthly_installment;
                    if ($loan->remaining_balance <= 0) {
                        $loan->status = 'repaid';
                    }
                    $loan->save();
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $payroll = Payroll::where('id', $id)->where('status', 0)->first();
        $payroll->delete();
        return redirect()->route('payslips.index')->with('success', _lang('Deleted Successfully'));
    }
}