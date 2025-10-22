<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransactionController extends Controller {
    public function index() {
        $assets       = ['datatable'];
        $transactions = Transaction::with('entries')->get();
        return view('backend.admin.transactions.list', compact('transactions', 'assets'));
    }

    public function get_table_data() {
        $transactions = Transaction::select('transactions.*')
            ->with('entries')
            ->orderBy("transactions.id", "desc");
        $currency_symbol = currency_symbol();

        return Datatables::eloquent($transactions)
            ->addColumn('amount', function ($transaction) use ($currency_symbol) {
                return decimalPlace($transaction->entries()->where('type', 'debit')->sum('amount'), $currency_symbol);
            })
            ->addColumn('action', function ($transaction) {
                if ($transaction->system_generated == 0) {
                    return '<div class="dropdown text-center">'
                    . '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                    . '</button>'
                    . '<div class="dropdown-menu">'
                    . '<a class="dropdown-item" href="' . route('transactions.edit', $transaction['id']) . '"><i class="fas fa-pencil-alt"></i> ' . _lang('Edit') . '</a>'
                    . '<a class="dropdown-item" href="' . route('transactions.show', $transaction['id']) . '"><i class="fas fa-eye"></i> ' . _lang('Details') . '</a>'
                    . '<form action="' . route('transactions.destroy', $transaction['id']) . '" method="post">'
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
                    . '<a class="dropdown-item disabled" href="#"><i class="fas fa-pencil-alt"></i> ' . _lang('Edit') . '</a>'
                    . '<a class="dropdown-item" href="' . route('transactions.show', $transaction['id']) . '"><i class="fas fa-eye"></i> ' . _lang('Details') . '</a>'
                    . '<form action="#" method="post">'
                    . '<input name="_method" type="hidden" value="DELETE">'
                    . '<button class="dropdown-item" type="submit" disabled><i class="fas fa-trash-alt"></i> ' . _lang('Delete') . '</button>'
                        . '</form>'
                        . '</div>'
                        . '</div>';
                }
            })
            ->setRowId(function ($transaction) {
                return "row_" . $transaction->id;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function create() {
        $alert_col = 'col-lg-10 offset-lg-1';
        $accounts  = Account::orderBy('account_id')->get();
        return view('backend.admin.transactions.create', compact('accounts', 'alert_col'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'transaction_date'     => 'required|date',
            'description'          => 'required|string|max:191',
            'entries'              => 'required|array',
            'entries.*.account_id' => 'required|exists:accounts,id',
            'entries.*.amount'     => 'required|numeric',
            'entries.*.type'       => 'required|in:debit,credit',
            'attachment'           => 'nullable|mimes:png,jpg,jpeg,pdf,doc,docx,xlsx,csv|max:4096',
        ]);

        $totalDebit  = 0;
        $totalCredit = 0;

        foreach ($validated['entries'] as $entry) {
            if ($entry['type'] === 'debit') {
                $totalDebit += $entry['amount'];
            } else {
                $totalCredit += $entry['amount'];
            }
        }

        if ($totalDebit !== $totalCredit) {
            return back()->withErrors(_lang('Debits and credits must be equal'))->withInput();
        }

        DB::beginTransaction();

        // Handle file attachment
        $attachment = '';
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('transactions', 'public');
        }

        $transaction = Transaction::create([
            'transaction_date' => $validated['transaction_date'],
            'description'      => $validated['description'],
            'created_user_id'  => auth()->id(),
            'attachment'       => $attachment,
        ]);

        foreach ($validated['entries'] as $entryData) {
            $transaction->entries()->create([
                'account_id' => $entryData['account_id'],
                'amount'     => $entryData['amount'],
                'type'       => $entryData['type'],
            ]);
        }
        DB::commit();

        return redirect()->route('transactions.index')->with('success', 'Transaction created successfully!');
    }

    public function add_income(Request $request) {
        if ($request->isMethod('get')) {
            $alert_col  = 'col-lg-8 offset-lg-2';
            $accounts   = Account::where('type', 'asset')->where('is_bank', 1)->orderBy('account_id')->get();
            $categories = Account::where('type', 'revenue')->orderBy('account_id')->get();
            return view('backend.admin.transactions.add_income', compact('accounts', 'categories', 'alert_col'));
        } else {
            $validated = $request->validate([
                'transaction_date' => 'required|date',
                'description'      => 'required|string|max:191',
                'account_id'       => 'required|exists:accounts,id',
                'category_id'      => 'required|exists:accounts,id',
                'amount'           => 'required|numeric',
                'attachment'       => 'nullable|mimes:png,jpg,jpeg,pdf,doc,docx,xlsx,csv|max:4096', // Validate files (optional)
            ]);

            DB::beginTransaction();

            // Handle file attachment
            $attachment = '';
            if ($request->hasFile('attachment')) {
                $attachment = $request->file('attachment')->store('transactions', 'public');
            }

            $transaction = Transaction::create([
                'transaction_date' => $validated['transaction_date'],
                'description'      => $validated['description'],
                'created_user_id'  => auth()->id(),
                'attachment'       => $attachment,
            ]);

            $transaction->entries()->create([
                'account_id' => $validated['account_id'],
                'amount'     => $validated['amount'],
                'type'       => 'debit',
            ]);

            $transaction->entries()->create([
                'account_id' => $validated['category_id'],
                'amount'     => $validated['amount'],
                'type'       => 'credit',
            ]);

            DB::commit();

            return redirect()->route('transactions.index')->with('success', 'Transaction created successfully!');
        }
    }

    public function add_expense(Request $request) {
        if ($request->isMethod('get')) {
            $alert_col  = 'col-lg-8 offset-lg-2';
            $accounts   = Account::where('type', 'asset')->where('is_bank', 1)->orderBy('account_id')->get();
            $categories = Account::where('type', 'expense')->orderBy('account_id')->get();
            return view('backend.admin.transactions.add_expense', compact('accounts', 'categories', 'alert_col'));
        } else {
            $validated = $request->validate([
                'transaction_date' => 'required|date',
                'description'      => 'required|string|max:191',
                'account_id'       => 'required|exists:accounts,id',
                'category_id'      => 'required|exists:accounts,id',
                'amount'           => 'required|numeric',
                'attachment'       => 'nullable|mimes:png,jpg,jpeg,pdf,doc,docx,xlsx,csv|max:4096', // Validate files (optional)
            ]);

            DB::beginTransaction();

            // Handle file attachment
            $attachment = '';
            if ($request->hasFile('attachment')) {
                $attachment = $request->file('attachment')->store('transactions', 'public');
            }

            $transaction = Transaction::create([
                'transaction_date' => $validated['transaction_date'],
                'description'      => $validated['description'],
                'created_user_id'  => auth()->id(),
                'attachment'       => $attachment,
            ]);

            $transaction->entries()->create([
                'account_id' => $validated['category_id'],
                'amount'     => $validated['amount'],
                'type'       => 'debit',
            ]);

            $transaction->entries()->create([
                'account_id' => $validated['account_id'],
                'amount'     => $validated['amount'],
                'type'       => 'credit',
            ]);

            DB::commit();

            return redirect()->route('transactions.index')->with('success', 'Transaction created successfully!');
        }
    }

    public function show($id) {
        $alert_col   = 'col-lg-8 offset-lg-2';
        $transaction = Transaction::findOrFail($id);
        return view('backend.admin.transactions.view', compact('transaction', 'alert_col'));
    }

    public function edit($id) {
        $alert_col   = 'col-lg-10 offset-lg-1';
        $transaction = Transaction::where('id', $id)->where('system_generated', 0)->first();
        $accounts    = Account::orderBy('account_id')->get();
        return view('backend.admin.transactions.edit', compact('transaction', 'accounts', 'alert_col'));
    }

    public function update(Request $request, $id) {
        $validated = $request->validate([
            'transaction_date'     => 'required|date',
            'description'          => 'required|string|max:191',
            'entries'              => 'required|array',
            'entries.*.account_id' => 'required|exists:accounts,id',
            'entries.*.amount'     => 'required|numeric',
            'entries.*.type'       => 'required|in:debit,credit',
            'attachment'           => 'nullable|mimes:png,jpg,jpeg,pdf,doc,docx,xlsx,csv|max:4096',
        ]);

        $debits  = array_column($validated['entries'], 'amount', 'type')['debit'] ?? 0;
        $credits = array_column($validated['entries'], 'amount', 'type')['credit'] ?? 0;

        if ($debits !== $credits) {
            return back()->withErrors(_lang('Debits and credits must be equal'))->withInput();
        }

        // Handle file attachment
        if ($request->hasFile('attachment')) {
            $attachment = $request->file('attachment')->store('transactions', 'public');
        }

        DB::beginTransaction();

        $transaction                   = Transaction::findOrFail($id);
        $transaction->transaction_date = $request->transaction_date;
        $transaction->description      = $request->description;
        $transaction->updated_user_id  = auth()->id();
        if ($request->hasFile('attachment')) {
            $transaction->attachment = $attachment;
        }
        $transaction->save();

        $transaction->entries()->delete();

        foreach ($validated['entries'] as $entryData) {
            $transaction->entries()->create([
                'account_id' => $entryData['account_id'],
                'amount'     => $entryData['amount'],
                'type'       => $entryData['type'],
            ]);
        }

        DB::commit();

        return redirect()->route('transactions.index')->with('success', 'Transaction updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $transaction = Transaction::where('id', $id)->where('system_generated', 0)->first();
        if ($transaction->attachment != null) {
            Storage::disk('public')->delete($transaction->attachment);
        }
        $transaction->delete();
        return redirect()->route('transactions.index')->with('success', _lang('Deleted Successfully'));
    }
}
