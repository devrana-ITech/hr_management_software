<?php

namespace App\Http\Controllers;

use App\Models\LoanType;
use Illuminate\Http\Request;
use Validator;

class LoanTypeController extends Controller {

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
        $assets = ['datatable'];
        $loantypes = LoanType::all()->sortByDesc("id");
        return view('backend.admin.loan_types.list', compact('loantypes', 'assets'));
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
            return view('backend.admin.loan_types.modal.create');
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
            'name'           => 'required',
            'minimum_amount' => 'required|numeric',
            'maximum_amount' => 'required|numeric',
            'interest_rate'  => 'required|numeric',
            'interest_type'  => 'required|in:fixed,declining',
            'term'           => 'required|integer',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('employee_loan_types.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $loantype                 = new LoanType();
        $loantype->name           = $request->input('name');
        $loantype->minimum_amount = $request->input('minimum_amount');
        $loantype->maximum_amount = $request->input('maximum_amount');
        $loantype->interest_rate  = $request->input('interest_rate');
        $loantype->interest_type  = $request->input('interest_type');
        $loantype->term           = $request->input('term');

        $loantype->save();

        $loantype->interest_type = ucwords($loantype->interest_type);

        if (!$request->ajax()) {
            return redirect()->route('employee_loan_types.create')->with('success', _lang('Saved Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $loantype, 'table' => '#employee_loan_types_table']);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $loantype = LoanType::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.loan_types.modal.view', compact('loantype', 'id'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $loantype = LoanType::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.loan_types.modal.edit', compact('loantype', 'id'));
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
            'name'           => 'required',
            'minimum_amount' => 'required|numeric',
            'maximum_amount' => 'required|numeric',
            'interest_rate'  => 'required|numeric',
            'interest_type'  => 'required|in:fixed,declining',
            'term'           => 'required|integer',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('employee_loan_types.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $loantype                 = LoanType::find($id);
        $loantype->name           = $request->input('name');
        $loantype->minimum_amount = $request->input('minimum_amount');
        $loantype->maximum_amount = $request->input('maximum_amount');
        $loantype->interest_rate  = $request->input('interest_rate');
        $loantype->interest_type  = $request->input('interest_type');
        $loantype->term           = $request->input('term');

        $loantype->save();

        $loantype->interest_type = ucwords($loantype->interest_type);

        if (!$request->ajax()) {
            return redirect()->route('employee_loan_types.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $loantype, 'table' => '#employee_loan_types_table']);
        }

    }

    public function get_loan_type($id) {
        $loantype = LoanType::find($id);
        return response()->json($loantype);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $loantype = LoanType::find($id);
        $loantype->delete();
        return redirect()->route('employee_loan_types.index')->with('success', _lang('Deleted Successfully'));
    }
}