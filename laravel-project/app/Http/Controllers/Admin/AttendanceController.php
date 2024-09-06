<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AttendanceRequest;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class AttendanceController extends Controller
{
    public function index(Request $request, User $user)
    {
        if (Gate::denies('admin.authority')) {
            abort(403);
        }

        $selectYear = $request->query('year');
        $selectMonth = $request->query('month');

        if (empty($selectYear) || empty($selectMonth)) {
            $selectYear = Carbon::now()->year;
            $selectMonth = Carbon::now()->month;
        }

        $firstday = Carbon::createFromDate($selectYear, $selectMonth, 1)->startOfMonth()->toDateString();
        $lastday = Carbon::createFromDate($selectYear, $selectMonth, 1)->endOfMonth()->toDateString();

        $attendance = Attendance::where('user_id', $user->id)
            ->where('working_day', '>=', $firstday)
            ->where('working_day', '<=', $lastday)
            ->get();

        return view('admin.attendance.index', compact([
            'user',
            'attendance',
            'selectYear',
            'selectMonth',
        ]));
    }

    public function edit(Attendance $attendance)
    {
        if (Gate::denies('admin.authority')) {
            abort(403);
        }

        return view('admin.attendance.edit', compact(['attendance']));
    }

    public function update(AttendanceRequest $request, Attendance $attendance)
    {
        if (Gate::denies('admin.authority')) {
            abort(403);
        }

        $attendance2 = clone $attendance;
        $attendance->start_time = $attendance2->start_time->hour($request['start_hour'])->minute($request['start_minute']);
        $attendance->finish_time = $attendance2->start_time->hour($request['finish_hour'])->minute($request['finish_minute']);
        $attendance->save();

        return redirect()->route('admin.attendance.index', ['user' => $attendance->user, 'year' => $attendance->start_time->format('Y'), 'month' => $attendance->start_time->format('n')]);
    }
}
