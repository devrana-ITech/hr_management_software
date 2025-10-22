<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Models\Entry;
use App\Models\Holiday;
use App\Models\Notice;
use App\Models\Transaction;
use Carbon\Carbon;

class DashboardController extends Controller {
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        date_default_timezone_set(get_option('timezone', 'Asia/Dhaka'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index() {
        $user           = auth()->user();
        $user_type      = $user->user_type;
        $data           = array();
        $data['assets'] = ['datatable'];

        if ($user_type == 'admin') {
            $data['active_employees'] = Employee::active()->count();
            $data['transactions']     = Transaction::limit(10)->orderBy('id', 'desc')->get();
            return view("backend.admin.dashboard-admin", $data);
        } else if ($user_type == 'user') {
            $data['active_employees'] = Employee::active()->count();
            $data['transactions']     = Transaction::limit(10)->orderBy('id', 'desc')->get();
            return view("backend.admin.dashboard-user", $data);
        } else if ($user_type == 'employee') {
            $data['month'] = Carbon::now()->month;
            $data['year']  = Carbon::now()->year;

            $data['employee']    = auth()->user()->employee;
            $data['notices']     = Notice::where('status', 1)->orderBy('id', 'desc')->paginate(10);
            $data['holidays']    = Holiday::where('date', '>', Carbon::now())->orderBy('date', 'ASC')->paginate(10);
            $data['leave_taken'] = Attendance::where('employee_id', auth()->user()->employee->id)
                ->whereYear('date', $data['year'])
                ->where('status', 2)
                ->count();
            $data['available_leave'] = $data['employee']->yearly_leave_limit - $data['leave_taken'];
            $data['absent']          = Attendance::where('employee_id', auth()->user()->employee->id)->where('status', 3)->count();
            $data['loan_balance']    = EmployeeLoan::where('employee_id', auth()->user()->employee->id)
                ->where('status', 'approved')
                ->sum('remaining_balance');

            return view("backend.employee.dashboard-employee", $data);
        }
    }

    public function notice_details($id) {
        $notice = Notice::where('id', $id)->where('status', 1)->first();
        return view('backend.employee.notice-details', compact('notice'));
    }

    public function dashboard_widget() {
        return redirect()->route('dashboard.index');
    }

    public function json_profit_and_loss() {
        $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"];

        $income_entries = Entry::selectRaw('MONTH(transactions.transaction_date) as td, entries.type, ROUND(IFNULL(SUM(amount),0),2) as amount')
            ->join('transactions', 'transactions.id', 'entries.transaction_id')
            ->join('accounts', 'accounts.id', 'entries.account_id')
            ->whereYear('transactions.transaction_date', date('Y'))
            ->where('accounts.type', 'revenue')
            ->groupBy('td', 'type')
            ->get();

        $expense_entries = Entry::selectRaw('MONTH(transactions.transaction_date) as td, entries.type, ROUND(IFNULL(SUM(amount),0),2) as amount')
            ->join('transactions', 'transactions.id', 'entries.transaction_id')
            ->join('accounts', 'accounts.id', 'entries.account_id')
            ->whereYear('transactions.transaction_date', date('Y'))
            ->where('accounts.type', 'expense')
            ->groupBy('td', 'type')
            ->get();

        $income  = array();
        $expense = array();

        foreach ($income_entries as $income_entry) {
            if (!isset($income[$income_entry->td])) {
                $income[$income_entry->td] = 0;
            }

            if ($income_entry->type == 'credit') {
                $income[$income_entry->td] += $income_entry->amount;
            } else {
                $income[$income_entry->td] -= $income_entry->amount;
            }

        }

        foreach ($expense_entries as $expense_entry) {
            if (!isset($expense[$expense_entry->td])) {
                $expense[$expense_entry->td] = 0;
            }

            if ($expense_entry->type == 'credit') {
                $expense[$expense_entry->td] -= $expense_entry->amount;
            } else {
                $expense[$expense_entry->td] += $expense_entry->amount;
            }
        }

        $decimal_place = get_option('decimal_places', 2);

        echo json_encode(array('month' => $months, 'income' => $income, 'expense' => $expense, 'decimal_place' => $decimal_place));
    }

}
