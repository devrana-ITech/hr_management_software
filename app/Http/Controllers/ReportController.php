<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Entry;
use App\Models\Holiday;
use App\Models\Payroll;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ReportController extends Controller {

    public function attendance_report(Request $request) {
        if ($request->isMethod('get')) {
            $page_title = _lang('Attendance Report');
            return view('backend.admin.reports.attendance_report', compact('page_title'));
        } else if ($request->isMethod('post')) {
            @ini_set('max_execution_time', 0);
            @set_time_limit(0);
            $data  = array();
            $month = $request->month;
            $year  = $request->year;

            $data['calendar'] = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $attendance_list  = Attendance::select('attendance.*')
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->orderBy('date', 'asc')
                ->orderBy('employee_id', 'asc')
                ->get();

            $holidays = Holiday::whereMonth('date', $month)
                ->whereYear('date', $year)
                ->orderBy('date', 'ASC')
                ->pluck('date')
                ->toArray();

            $data['employees'] = Employee::active()
                ->orderBy('employees.id', 'asc')
                ->get();

            $weekends    = json_decode(get_option('weekends', '[]'));
            $report_data = [];

            for ($day = 1; $day <= $data['calendar']; $day++) {
                $date   = date('Y-m-d', strtotime("$year-$month-$day"));
                $status = ['A', 'P', 'L', 'W', 'H'];

                foreach ($attendance_list as $attendance) {
                    if (in_array($date, $holidays)) {
                        $report_data[$attendance->employee_id][$day] = $status[4]; // Holiday
                    } else {
                        if ($date == $attendance->getRawOriginal('date')) {
                            $report_data[$attendance->employee_id][$day] = $status[$attendance->status];
                        } else {
                            if (in_array(date('l', strtotime($date)), $weekends)) {
                                $report_data[$attendance->employee_id][$day] = $status[3];
                            }
                        }
                    }
                }

            }

            $data['month']           = $request->month;
            $data['year']            = $request->year;
            $data['page_title']      = _lang('Attendance Report');
            $data['report_data']     = $report_data;
            $data['attendance_list'] = $attendance_list;
            return view('backend.admin.reports.attendance_report', $data);
        }
    }

    public function payroll_report(Request $request) {
        if ($request->isMethod('get')) {
            $page_title = _lang('Payroll Report');
            return view('backend.admin.reports.payroll_report', compact('page_title'));
        } else if ($request->isMethod('post')) {
            @ini_set('max_execution_time', 0);
            @set_time_limit(0);

            $data  = array();
            $month = $request->month;
            $year  = $request->year;

            $data['report_data'] = Payroll::with('staff')
                ->select('payslips.*')
                ->where('month', $month)
                ->where('year', $year)
                ->get();

            $data['month']      = $request->month;
            $data['year']       = $request->year;
            $data['currency']   = currency();
            $data['page_title'] = _lang('Payroll Report');
            return view('backend.admin.reports.payroll_report', $data);
        }
    }

    public function trialBalance(Request $request) {
        $fromDate = $request->input('from_date', now()->startOfYear()->toDateString());
        $toDate   = $request->input('to_date', now()->endOfYear()->toDateString());

        $accounts     = Account::all()->sortBy('account_id');
        $trialBalance = [];

        foreach ($accounts as $account) {
            $debits = Entry::where('account_id', $account->id)
                ->whereHas('transaction', function (Builder $query) use ($fromDate, $toDate) {
                    $query->whereBetween('transaction_date', [$fromDate, $toDate]);
                })
                ->where('type', 'debit')
                ->sum('amount');

            $credits = Entry::where('account_id', $account->id)
                ->whereHas('transaction', function (Builder $query) use ($fromDate, $toDate) {
                    $query->whereBetween('transaction_date', [$fromDate, $toDate]);
                })
                ->where('type', 'credit')
                ->sum('amount');

            if ($debits > 0 || $credits > 0) {
                $trialBalance[] = [
                    'account_name' => $account->name,
                    'debit'        => $debits,
                    'credit'       => $credits,
                ];
            }
        }

        $totalDebits     = array_sum(array_column($trialBalance, 'debit'));
        $totalCredits    = array_sum(array_column($trialBalance, 'credit'));
        $currency_symbol = currency_symbol();

        return view('backend.admin.reports.trial_balance', compact('fromDate', 'toDate', 'trialBalance', 'totalDebits', 'totalCredits', 'currency_symbol'));
    }

    public function generalLedger(Request $request) {
        $fromDate = $request->input('from_date', now()->startOfYear()->toDateString());
        $toDate   = $request->input('to_date', now()->endOfYear()->toDateString());

        $accountId = $request->input('account_id', null);

        $allAccounts = Account::all()->sortBy('account_id');

        if ($accountId) {
            $accounts = Account::where('id', $accountId)->get(); // Specific account
        } else {
            $accounts = Account::all()->sortBy('account_id'); // All accounts
        }

        $generalLedger = [];
        foreach ($accounts as $account) {
            // Get the opening balance (sum of all transactions before fromDate)
            $openingBalance = $this->getOpeningBalance($account, $fromDate);

            $transactions = Entry::with('transaction')
                ->where('account_id', $account->id)
                ->whereHas('transaction', function (Builder $query) use ($fromDate, $toDate) {
                    $query->whereBetween('transaction_date', [$fromDate, $toDate]);
                })
                ->orderBy('created_at', 'asc')
                ->get();

            // Determine the nature of the account (debit or credit)
            $isDebitAccount = in_array($account->type, ['asset', 'expense']);

            // Calculate current balance after each transaction
            $currentBalance = $openingBalance;

            // Collect transaction data for the ledger
            $ledgerData = [];
            foreach ($transactions as $transaction) {
                // Adjust balance based on account type
                if ($transaction->type == 'debit') {
                    $currentBalance += $isDebitAccount ? $transaction->amount : -$transaction->amount;
                } else {
                    $currentBalance -= $isDebitAccount ? $transaction->amount : -$transaction->amount;
                }

                // Collect transaction details
                $ledgerData[] = [
                    'date'        => $transaction->transaction->transaction_date,
                    'description' => $transaction->description,
                    'debit'       => $transaction->type == 'debit' ? $transaction->amount : 0,
                    'credit'      => $transaction->type == 'credit' ? $transaction->amount : 0,
                    'balance'     => $currentBalance,
                ];
            }

            if ($openingBalance != 0 || $transactions->count() > 0) {
                // Add account data and transactions to the general ledger array
                $generalLedger[] = [
                    'account_name'    => $account->name,
                    'opening_balance' => $openingBalance,
                    'transactions'    => $ledgerData,
                    'closing_balance' => $currentBalance,
                ];
            }

        }

        $currency_symbol = currency_symbol();

        // Pass the general ledger data and account list to the view
        return view('backend.admin.reports.general_ledger', compact('fromDate', 'toDate', 'accountId', 'generalLedger', 'allAccounts', 'currency_symbol'));
    }

    public function profitAndLoss(Request $request) {
        $fromDate = $request->input('from_date', now()->startOfYear()->toDateString());
        $toDate   = $request->input('to_date', now()->endOfYear()->toDateString());

        $revenueAccounts = Account::where('type', 'revenue')->orderBy('account_id', 'asc')->get();
        $expenseAccounts = Account::where('type', 'expense')->orderBy('account_id', 'asc')->get();

        // Fetch transactions for the date range and organize by account
        $revenueData = $this->getAccountTransactions($revenueAccounts, $fromDate, $toDate, 'revenue');
        $expenseData = $this->getAccountTransactions($expenseAccounts, $fromDate, $toDate, 'expense');

        // Calculate total revenue and total expenses
        $totalRevenue  = $this->calculateTotal($revenueData);
        $totalExpenses = $this->calculateTotal($expenseData);

        // Calculate Net Profit or Loss
        $netProfitOrLoss = $totalRevenue - $totalExpenses;
        $currency_symbol = currency_symbol();

        return view('backend.admin.reports.profit_and_loss', compact('fromDate', 'toDate', 'revenueData', 'expenseData', 'totalRevenue', 'totalExpenses', 'netProfitOrLoss', 'currency_symbol'));
    }

    public function balanceSheet(Request $request) {
        $toDate = $request->input('to_date', now()->endOfYear()->toDateString());

        // Calculate total revenue (for net profit/loss)
        $revenueAccounts = Account::where('type', 'revenue')->get();
        $revenueEntries  = Entry::whereIn('account_id', $revenueAccounts->pluck('id'))
            ->whereHas('transaction', function (Builder $query) use ($toDate) {
                $query->where('transaction_date', '<=', $toDate);
            })
            ->get();

        $debit        = $revenueEntries->where('type', 'debit')->sum('amount');
        $credit       = $revenueEntries->where('type', 'credit')->sum('amount');
        $totalRevenue = $credit - $debit;

        // Calculate total expenses (for net profit/loss)
        $expenseAccounts = Account::where('type', 'expense')->get();
        $expenseEntries  = Entry::whereIn('account_id', $expenseAccounts->pluck('id'))
            ->whereHas('transaction', function (Builder $query) use ($toDate) {
                $query->where('transaction_date', '<=', $toDate);
            })
            ->get();

        $debit         = $expenseEntries->where('type', 'debit')->sum('amount');
        $credit        = $expenseEntries->where('type', 'credit')->sum('amount');
        $totalExpenses = $debit - $credit;

        // Calculate net profit or loss
        $netProfitOrLoss = $totalRevenue - $totalExpenses;

        // Fetch assets, liabilities, and equity accounts
        $assets      = Account::where('type', 'asset')->orderBy('account_id', 'asc')->get();
        $liabilities = Account::where('type', 'liability')->orderBy('account_id', 'asc')->get();
        $equity      = Account::where('type', 'equity')->orderBy('account_id', 'asc')->get();

        $assetsData      = [];
        $liabilitiesData = [];
        $equityData      = [];

        foreach ($assets as $asset) {
            $balance = $asset->balance($toDate);
            if ($balance == 0) {
                continue;
            }
            $assetsData[] = [
                'account_name' => $asset->name,
                'balance'      => $balance,
            ];
        }

        foreach ($liabilities as $liability) {
            $balance = $liability->balance($toDate);
            if ($balance == 0) {
                continue;
            }
            $liabilitiesData[] = [
                'account_name' => $liability->name,
                'balance'      => $balance,
            ];
        }

        foreach ($equity as $eq) {
            $balance = $eq->balance($toDate);
            if ($balance == 0) {
                continue;
            }
            $equityData[] = [
                'account_name' => $eq->name,
                'balance'      => $balance,
            ];
        }

        $currency_symbol = currency_symbol();

        return view('backend.admin.reports.balance_sheet', compact('toDate', 'assetsData', 'liabilitiesData', 'equityData', 'netProfitOrLoss', 'currency_symbol'));
    }

    //***** Private Functions *****//
    private function getAccountTransactions($accounts, $fromDate, $toDate, $type) {
        $data = [];
        foreach ($accounts as $account) {
            $transactions = Entry::where('account_id', $account->id)
                ->whereHas('transaction', function (Builder $query) use ($fromDate, $toDate) {
                    $query->whereBetween('transaction_date', [$fromDate, $toDate]);
                })
                ->get();

            $debit  = $transactions->where('type', 'debit')->sum('amount');
            $credit = $transactions->where('type', 'credit')->sum('amount');
            $total  = $type == 'revenue' ? $credit - $debit : $debit - $credit;

            if ($total != 0) {
                $data[] = [
                    'account_name' => $account->name,
                    'transactions' => $transactions,
                    'debit'        => $debit,
                    'credit'       => $credit,
                    'total'        => $total,
                ];
            }
        }
        return $data;
    }

    private function calculateTotal($accountData) {
        return collect($accountData)->sum('total');
    }

    // Helper function to get the opening balance for an account
    private function getOpeningBalance($account, $fromDate) {
        $debits = Entry::where('account_id', $account->id)
            ->where('type', 'debit')
            ->whereHas('transaction', function (Builder $query) use ($fromDate) {
                $query->where('transaction_date', '<', $fromDate);
            })
            ->sum('amount');

        $credits = Entry::where('account_id', $account->id)
            ->where('type', 'credit')
            ->whereHas('transaction', function (Builder $query) use ($fromDate) {
                $query->where('transaction_date', '<', $fromDate);
            })
            ->sum('amount');

        // Calculate the opening balance
        if ($account->type == 'asset' || $account->type == 'expense') {
            return $debits - $credits;
        } else {
            return $credits - $debits;
        }
    }

}
