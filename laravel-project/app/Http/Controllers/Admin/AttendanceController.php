<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceCreateRequest;
use App\Http\Requests\AttendanceEditRequest;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request, User $user)
    {
        $selectYear = $request->query('year');
        $selectMonth = $request->query('month');

        if (empty($selectYear) || empty($selectMonth)) {
            $selectYear = Carbon::now()->year;
            $selectMonth = Carbon::now()->month;
        }

        $attendance = Attendance::where('user_id', $user->id)
            ->whereYear('working_day', $selectYear)
            ->whereMonth('working_day', $selectMonth)
            ->orderBy('working_day', 'ASC')
            ->get();

        $totalWorkingTime = $attendance->sum(function ($day) {
            return $day->working_time ?? 0;
        });

        return view('admin.attendance.index', compact([
            'user',
            'attendance',
            'totalWorkingTime',
            'selectYear',
            'selectMonth',
        ]));
    }

    public function create(User $user)
    {
        return view('admin.attendance.create', compact(['user']));
    }

    public function store(AttendanceCreateRequest $request, User $user)
    {
        $attendance = new Attendance();
        $attendance->user_id = $user->id;
        $attendance->working_day = $request['working_day'];
        $attendance->start_time = Carbon::create($request['labor_year'], $request['labor_month'], $request['labor_day'], $request['start_hour'], $request['start_minute']);
        $attendance->finish_time = ! is_null($request['finish_time']) ? Carbon::create($request['labor_year'], $request['labor_month'], $request['labor_day'], $request['finish_hour'], $request['finish_minute']) : null;
        $attendance->save();

        return redirect()->route('admin.attendance.index', ['user' => $user, 'year' => $request['labor_year'], 'month' => $request['labor_month']]);
    }

    public function edit(Attendance $attendance)
    {
        return view('admin.attendance.edit', compact(['attendance']));
    }

    public function update(AttendanceEditRequest $request, Attendance $attendance)
    {
        $cloned_attendance = clone $attendance;
        $attendance->start_time = $cloned_attendance->start_time->hour($request['start_hour'])->minute($request['start_minute']);
        $attendance->finish_time = ! is_null($request['finish_time']) ? $cloned_attendance->start_time->hour($request['finish_hour'])->minute($request['finish_minute']) : null;
        $attendance->save();

        return redirect()->route('admin.attendance.index', ['user' => $attendance->user, 'year' => $attendance->start_time->format('Y'), 'month' => $attendance->start_time->format('n')]);
    }

    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return redirect()->route('admin.attendance.index', ['user' => $attendance->user, 'year' => $attendance->start_time->format('Y'), 'month' => $attendance->start_time->format('n')]);
    }
}
