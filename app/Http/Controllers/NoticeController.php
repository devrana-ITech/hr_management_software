<?php

namespace App\Http\Controllers;

use App\Models\Notice;
use DataTables;
use Illuminate\Http\Request;
use Validator;

class NoticeController extends Controller {

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
        return view('backend.admin.notice.list', compact('assets'));
    }

    public function get_table_data() {
        $notices = Notice::select('notices.*')
            ->with('created_by')
            ->orderBy("notices.id", "desc");

        return Datatables::eloquent($notices)
            ->addColumn('status', function ($notice) {
                if ($notice->status == 0) {
                    return show_status(_lang('Draft'), 'warning');
                }
                return show_status(_lang('Published'), 'success');
            })
            ->addColumn('action', function ($notice) {
                return '<div class="dropdown text-center">'
                . '<button class="btn btn-outline-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                . '</button>'
                . '<div class="dropdown-menu">'
                . '<a class="dropdown-item" href="' . route('notices.edit', $notice['id']) . '"><i class="fas fa-pencil-alt"></i> ' . _lang('Edit') . '</a>'
                . '<a class="dropdown-item" href="' . route('notices.show', $notice['id']) . '"><i class="fas fa-eye"></i> ' . _lang('Details') . '</a>'
                . '<form action="' . route('notices.destroy', $notice['id']) . '" method="post">'
                . csrf_field()
                . '<input name="_method" type="hidden" value="DELETE">'
                . '<button class="dropdown-item btn-remove" type="submit"><i class="fas fa-trash-alt"></i> ' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
            })
            ->setRowId(function ($notice) {
                return "row_" . $notice->id;
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
        $assets    = ['tinymce'];
        $alert_col = 'col-lg-10 offset-lg-1';
        return view('backend.admin.notice.create', compact('assets', 'alert_col'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'title'      => 'required|max:191',
            'details'    => 'required',
            'attachment' => 'nullable|mimes:png,jpg,jpeg,pdf,doc,docx,xlsx,csv|max:4096',
            'status'     => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('notices.create')
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

        $notice                  = new Notice();
        $notice->title           = $request->input('title');
        $notice->details         = $request->input('details');
        $notice->attachment      = $attachment;
        $notice->status          = $request->input('status');
        $notice->created_user_id = auth()->id();

        $notice->save();

        if ($notice->status == 1) {
            return redirect()->route('notices.create')->with('success', _lang('Notice Published'));
        } else {
            return redirect()->route('notices.create')->with('success', _lang('Notice saved as draft'));
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $alert_col = 'col-lg-8 offset-lg-2';
        $notice    = Notice::find($id);
        return view('backend.admin.notice.view', compact('notice', 'id', 'alert_col'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $assets    = ['tinymce'];
        $alert_col = 'col-lg-10 offset-lg-1';
        $notice    = Notice::find($id);
        return view('backend.admin.notice.edit', compact('notice', 'id', 'assets', 'alert_col'));
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
            'title'      => 'required|max:191',
            'details'    => 'required',
            'attachment' => 'nullable|mimes:png,jpg,jpeg,pdf,doc,docx,xlsx,csv|max:4096',
            'status'     => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('notices.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        if ($request->hasfile('attachment')) {
            $file       = $request->file('attachment');
            $attachment = time() . $file->getClientOriginalName();
            $file->move(public_path() . "/uploads/media/", $attachment);
        }

        $notice          = Notice::find($id);
        $notice->title   = $request->input('title');
        $notice->details = $request->input('details');
        if ($request->hasfile('attachment')) {
            $notice->attachment = $attachment;
        }
        $notice->status = $request->input('status');

        $notice->save();

        if (!$request->ajax()) {
            return redirect()->route('notices.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $notice, 'table' => '#notices_table']);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        $notice = Notice::find($id);
        $notice->delete();
        return redirect()->route('notices.index')->with('success', _lang('Deleted Successfully'));
    }
}