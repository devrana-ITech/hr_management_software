<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assets = ['datatables'];
        $units = Unit::all()->sortByDesc('id');
        return view('backend.admin.unit.list', compact('assets', 'units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if(!$request->ajax()){
            return back();
        }else{
            return view('backend.admin.unit.create',);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if($validator->fails()){
            if($request->ajax()){
                return response()->json(['result'=> 'error', 'message' => $validator->errors()->all()]);
            }else{
                return redirect()->route('unit.create')
                ->withErrors($validator)
                ->whitInput();
            }
        }

        $units                  = new Unit();
        $units->name            = $request->input('name');
        $units->descriptions    = $request->input('descriptions');

        $units->save();

        if(!$request->ajax()){
            return redirect()->route('unit.create')->with('succuse', _lang('Save Successfully'));
        }else{
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Saved Successfully'), 'data' => $units, 'table' => '#units_tables']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Unit $unit, Request $request, $id)
    {
        $units = Unit::find($id);
        if(!$request->ajax()){
            return back();
        }else{
            return view('backend.admin.unit.view`');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Unit $unit, Request $request, $id)
    {
        $units = Unit::find($id);
        if(!$request->ajax()){
            return back();
        }else{
            return view('backend.admin.unit.edit', compact('units, id'));
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Unit $unit, $id)
    {
         $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('units.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $unit               = Unit::find($id);
        $unit->name         = $request->input('name');
        $unit->descriptions = $request->input('descriptions');
        $unit->save();

        if (!$request->ajax()) {
            return redirect()->route('units.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $unit, 'table' => '#departments_table']);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Unit $unit, $id)
    {
         $units = Unit::find($id);
        try {
            $units->delete();
            return redirect()->route('units.index')->with('success', _lang('Deleted Successfully'));
        } catch (\Exception $e) {
            return redirect()->route('units.index')->with('error', _lang('This items is already exists in other entity'));
        }
    }
}
