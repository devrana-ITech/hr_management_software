<?php
namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Validator;

class AccountController extends Controller {

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
    public function index() {
        $assets      = ['datatable'];
        $allAccounts = Account::all()->sortBy('account_id');
        $accounts    = $allAccounts->sortBy(function ($account) {
            switch ($account->type) {
            case 'asset':
                return 1;
            case 'liability':
                return 2;
            case 'equity':
                return 3;
            case 'revenue':
                return 4;
            case 'expense':
                return 5;
            default:
                return 6;
            }
        });
        return view('backend.admin.accounts.list', compact('accounts', 'assets'));
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
            return view('backend.admin.accounts.modal.create');
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
            'account_id' => 'required|unique:accounts|integer',
            'name'       => 'required|string|max:255',
            'type'       => 'required|in:asset,liability,equity,revenue,expense',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('accounts.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $account             = new Account();
        $account->account_id = $request->input('account_id');
        $account->name       = $request->input('name');
        $account->type       = $request->input('type');
        $account->is_bank    = $account->type == 'asset' ? $request->is_bank : 0;
        $account->slug       = $account->is_bank ? 'Cash_&_Bank_Account' : '';

        $account->save();

        if (!$request->ajax()) {
            return redirect()->route('accounts.create')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $account, 'table' => '#accounts_table']);
        }

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $account = Account::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.accounts.modal.edit', compact('account', 'id'));
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
            'account_id' => [
                'required',
                Rule::unique('accounts')->ignore($id),
                'integer',
            ],
            'name'       => 'required|string|max:255',
            'type'       => 'required|in:asset,liability,equity,revenue,expense',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('accounts.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $account             = Account::find($id);
        $account->account_id = $request->input('account_id');
        $account->name       = $request->input('name');
        if ($account->is_default == 0) {
            $account->type    = $request->input('type');
            $account->is_bank = $account->type == 'asset' ? $request->is_bank : 0;
        }
        $account->slug       = $account->is_bank ? 'Cash_&_Bank_Account' : '';

        $account->save();

        if (!$request->ajax()) {
            return redirect()->route('accounts.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $account, 'table' => '#accounts_table']);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $account = Account::where('id', $id)->where('is_default', 0)->first();
        $account->delete();
        return redirect()->route('accounts.index')->with('success', _lang('Deleted Successfully'));
    }
}
