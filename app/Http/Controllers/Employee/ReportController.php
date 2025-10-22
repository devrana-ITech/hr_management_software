<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Holiday;
use App\Models\WorkingHour;
use Illuminate\Http\Request;

class ReportController extends Controller {

    public function attendance_report(Request $request) {
        @ini_set('max_execution_time', 0);
        @set_time_limit(0);

        $data  = array();
        $month = $request->input('month', date('m'));
        $year  = $request->input('year', date('Y'));

        $data['calendar'] = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $attendance_list  = Attendance::select('attendance.*')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('attendance.employee_id', auth()->user()->employee->id)
            ->orderBy('date', 'asc')
            ->get();

        $holidays = Holiday::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date', 'ASC')
            ->pluck('date')
            ->toArray();

        $data['employee'] = Employee::find(auth()->user()->employee->id);

        $weekends    = json_decode(get_option('weekends', '[]'));
        $report_data = [];

        for ($day = 1; $day <= $data['calendar']; $day++) {
            $date   = date('Y-m-d', strtotime("$year-$month-$day"));
            $status = ['A', 'P', 'L', 'W', 'H'];

            foreach ($attendance_list as $attendance) {
                if (in_array($date, $holidays)) {
                    $report_data[$day] = $status[4]; // Holiday
                } else {
                    if ($date == $attendance->getRawOriginal('date')) {
                        $report_data[$day] = $status[$attendance->status];
                    } else {
                        if (in_array(date('l', strtotime($date)), $weekends)) {
                            $report_data[$day] = $status[3];
                        }
                    }
                }
            }
        }

        $data['month']           = $month;
        $data['year']            = $year;
        $data['page_title']      = _lang('Attendance Report');
        $data['report_data']     = $report_data;
        $data['attendance_list'] = $attendance_list;
        return view('backend.employee.reports.attendance_report', $data);
    }

    public function work_hour_report(Request $request) {
        @ini_set('max_execution_time', 0);
        @set_time_limit(0);

        if (auth()->user()->employee->salary_type != 'hourly') {
            return back();
        }

        $data  = array();
        $month = $request->input('month', date('m'));
        $year  = $request->input('year', date('Y'));

        $data['calendar'] = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $workingHours     = WorkingHour::select('working_hours.*')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('working_hours.employee_id', auth()->user()->employee->id)
            ->orderBy('date', 'asc')
            ->get();

        $data['employee'] = Employee::find(auth()->user()->employee->id);
        $report_data      = [];

        for ($day = 1; $day <= $data['calendar']; $day++) {
            $date   = date('Y-m-d', strtotime("$year-$month-$day"));

            foreach ($workingHours as $workingHour) {
                if ($date == $workingHour->getRawOriginal('date')) {
                    $report_data[$day] = $workingHour->work_hour - $workingHour->hour_deduct;
                } else {
                    $report_data[$day] = 0;
                }
            }
        }

        $data['month']        = $month;
        $data['year']         = $year;
        $data['page_title']   = _lang('Work Hour Report');
        $data['report_data']  = $report_data;
        $data['workingHours'] = $workingHours;
        return view('backend.employee.reports.work_hour_report', $data);
    }

}
