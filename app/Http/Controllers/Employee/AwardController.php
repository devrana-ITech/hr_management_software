<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Award;
use DataTables;
use Illuminate\Http\Request;

class AwardController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        $assets = ['datatable'];
        return view('backend.employee.award.list', compact('assets'));
    }

    public function get_table_data() {
        $awards = Award::select('awards.*')
            ->with('employee')
            ->where('awards.employee_id', auth()->user()->employee->id)
            ->orderBy('id', 'desc');

        return Datatables::eloquent($awards)
            ->addColumn('action', function ($award) {
                return '<div class="text-center">'
                . '<a class="btn btn-outline-primary btn-xs ajax-modal" href="' . route('awards.show', $award['id']) . '" data-title="' . _lang('Award Details') . '"><i class="fas fa-eye"></i> ' . _lang('Details') . '</a>'
                    . '</div>';
            })
            ->setRowId(function ($award) {
                return "row_" . $award->id;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $award = Award::where('id', $id)->where('employee_id', auth()->user()->employee->id)->first();
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.employee.award.modal.view', compact('award', 'id'));
        }
    }
}