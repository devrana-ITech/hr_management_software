<?php

namespace App\Http\Controllers\Employee;

use Exception;
use Validator;
use DataTables;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Leave;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\NewLeaveApplication;
use Illuminate\Support\Facades\Notification;

class LeaveController extends Controller {

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
        return view('backend.employee.leave.list', compact('assets'));
    }

    public function get_table_data() {
        $leaves = Leave::select('leaves.*')
            ->with('staff')
            ->where('leaves.employee_id', auth()->user()->employee->id)
            ->orderBy('leaves.id', 'desc');

        return Datatables::eloquent($leaves)
            ->editColumn('leave_duration', function ($leave) {
                return $leave->leave_duration == 'full_day' ? _lang('Full Day') : _lang('Half Day');
            })
            ->editColumn('total_days', function ($leave) {
                return $leave->total_days . ' ' . _lang('days');
            })
            ->editColumn('status', function ($leave) {
                return '<div class="text-center">' . leave_status($leave->status) . '</div>';
            })
            ->addColumn('action', function ($leave) {
                return '<div class="text-center">'
                . '<a class="btn btn-outline-primary btn-xs ajax-modal" href="' . route('my_leaves.show', $leave['id']) . '" data-title="' . _lang('Leave Details') . '"><i class="fas fa-eye"></i> ' . _lang('Details') . '</a>'
                    . '</div>';
            })
            ->setRowId(function ($leave) {
                return "row_" . $leave->id;
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
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.employee.leave.modal.create');
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
            'leave_type'     => 'required',
            'leave_duration' => 'required',
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after_or_equal:start_date',
            'description'    => 'required',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('my_leaves.create')
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $yearly_leave = auth()->user()->employee->yearly_leave_limit;
        $leave_taken  = Attendance::where('employee_id', auth()->user()->employee->id)
            ->whereYear('date', Carbon::now()->year)
            ->where('status', 2)
            ->count();

        if ($yearly_leave - $leave_taken <= 0) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => _lang("Sorry, You don't have any remaining leaves")]);
            } else {
                return back()->with('error', _lang("Sorry, You don't have any remaining leaves"))->withInput();
            }
        }

        $leave                 = new Leave();
        $leave->employee_id    = auth()->user()->employee->id;
        $leave->leave_type     = $request->input('leave_type');
        $leave->leave_duration = $request->input('leave_duration');
        $leave->start_date     = $request->input('start_date');
        $leave->end_date       = $request->input('end_date');
        $leave->total_days     = $request->input('total_days');
        $leave->description    = $request->input('description');

        $leave->save();

        try {
            $users = User::where('user_type', 'admin')->get();
            Notification::send($users, new NewLeaveApplication($leave));
        } catch (Exception $e) {}

        if (!$request->ajax()) {
            return redirect()->route('my_leaves.create')->with('success', _lang('Leave application submitted'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'store', 'message' => _lang('Leave application submitted'), 'data' => $leave, 'table' => '#leaves_table']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id) {
        $leave = Leave::where('id', $id)->where('employee_id', auth()->user()->employee->id)->first();
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.employee.leave.modal.view', compact('leave', 'id'));
        }
    }

}