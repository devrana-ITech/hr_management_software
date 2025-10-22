<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Holiday;
use App\Models\WorkingHour;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class WorkingHourController extends Controller {

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
        return view('backend.admin.working_hour.list', compact('assets'));
    }

    public function get_table_data() {
        $workinghours = WorkingHour::select('working_hours.*')
            ->with('staff')
            ->orderBy("working_hours.id", "desc");

        return Datatables::eloquent($workinghours)
            ->editColumn('staff.first_name', function ($workinghour) {
                return $workinghour->staff->name;
            })
            ->addColumn('action', function ($workinghour) {
                return '<div class="text-center">'
                . '<a class="btn btn-primary btn-xs ajax-modal" data-title="' . _lang('Update Working Hour') . '" href="' . route('working_hours.edit', $workinghour->id) . '"><i class="fas fa-pencil-alt"></i> ' . _lang('Edit') . '</a>'
                    . '</div>';
            })
            ->filterColumn('staff.first_name', function ($query, $keyword) {
                $query->whereHas('staff', function ($query) use ($keyword) {
                    return $query->where("first_name", "like", "{$keyword}%")
                        ->orWhere("last_name", "like", "{$keyword}%");
                });
            }, true)
            ->setRowId(function ($workinghour) {
                return "row_" . $workinghour->id;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        if (!isset($request->date)) {
            $alert_col = 'col-lg-4 offset-lg-4';
            return view('backend.admin.working_hour.create', compact('alert_col'));
        } else {
            $alert_col = 'col-lg-12';
            $validator = Validator::make($request->all(), [
                'date' => 'required|before:tomorrow',
            ]);

            if ($validator->fails()) {
                return redirect()->route('working_hours.create')->withErrors($validator)->withInput();
            }

            $date     = $request->date;
            $weekends = json_decode(get_option('weekends', '[]'));
            $message  = null;
            if (in_array(date('l', strtotime($date)), $weekends)) {
                $message = _lang('The date you selected which is a weekend !');
            }

            $holiday = Holiday::where('date', $date)->first();
            if ($holiday) {
                $message = _lang('The date you selected which is a holiday !');
            }

            $employees = Employee::active()->select('employees.*', 'working_hours.clock_in', 'working_hours.clock_out', 'working_hours.hour_deduct', 'working_hours.remarks', 'leaves.leave_type', 'leaves.leave_duration')
                ->leftJoin('working_hours', function ($join) use ($date) {
                    $join->on('working_hours.employee_id', 'employees.id')
                        ->where('working_hours.date', $date);
                })
                ->leftJoin('leaves', function ($join) use ($date) {
                    $join->on('leaves.employee_id', 'employees.id')
                        ->where('leaves.status', 1)
                        ->whereRaw("date(leaves.start_date) <= '$date' AND date(leaves.end_date) >= '$date'");
                })
                ->where('employees.salary_type', 'hourly')
                ->orderBy('employees.id', 'ASC')
                ->get();

            return view('backend.admin.working_hour.create', compact('employees', 'message', 'alert_col', 'date'));
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        foreach ($request->employee_id as $key => $employee_id) {
            $validator = Validator::make($request->all(), [
                "employee_id.$key" => 'required',
                'date'             => 'required|before:tomorrow',
                "clock_in.$key"    => 'required',
                "clock_out.$key"   => "required|after:clock_in.$key",
                "hour_deduct.$key" => 'required|numeric',
            ], [
                "clock_out.$key.after" => _lang('Clock Out must greater than Clock In'),
            ]);

            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput();
            }
        }

        if (empty($request->employee_id)) {
            return back()->with('error', _lang('No employee found'))->withInput();
        }

        DB::beginTransaction();

        $data = [];
        foreach ($request->employee_id as $key => $employee_id) {
            $workinghour = WorkingHour::firstOrNew([
                'employee_id' => $employee_id,
                'date'        => date('Y-m-d', strtotime($request->date)),
            ]);

            $workinghour->clock_in    = $request->clock_in[$key];
            $workinghour->clock_out   = $request->clock_out[$key];
            $workinghour->work_hour   = round(abs(strtotime($request->clock_out[$key]) - strtotime($request->clock_in[$key])) / 3600, 2);
            $workinghour->hour_deduct = $request->hour_deduct[$key];
            $workinghour->remarks     = $request->remarks[$key];
            $workinghour->save();
        }

        DB::commit();

        return redirect()->route('working_hours.index')->with('success', _lang('Saved Successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id) {
        $workinghour = WorkingHour::find($id);
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.admin.working_hour.modal.edit', compact('workinghour', 'id'));
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
            'date'        => 'required|before:tomorrow',
            "clock_in"    => 'required',
            "clock_out"   => "required|after:clock_in",
            "hour_deduct" => 'required|numeric',
        ], [
            "clock_out.after" => _lang('Clock Out must greater than Clock In'),
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
            } else {
                return redirect()->route('working_hours.edit', $id)
                    ->withErrors($validator)
                    ->withInput();
            }
        }

        $workinghour              = WorkingHour::find($id);
        $workinghour->date        = $request->input('date');
        $workinghour->clock_in    = $request->input('clock_in');
        $workinghour->clock_out   = $request->input('clock_out');
        $workinghour->work_hour   = round(abs(strtotime($request->clock_out) - strtotime($request->clock_in)) / 3600, 2);
        $workinghour->hour_deduct = $request->input('hour_deduct');
        $workinghour->remarks     = $request->input('remarks');

        $workinghour->save();

        if (!$request->ajax()) {
            return redirect()->route('working_hours.index')->with('success', _lang('Updated Successfully'));
        } else {
            return response()->json(['result' => 'success', 'action' => 'update', 'message' => _lang('Updated Successfully'), 'data' => $workinghour, 'table' => '#working_hours_table']);
        }

    }

}
