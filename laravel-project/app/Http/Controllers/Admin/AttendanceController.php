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

    public function edit(User $user, $date)
    {
        if (Gate::denies('admin.authority')) {
            abort(403);
        }

        if (is_null($user)) {
            abort(404);
        }

        $attendance = Attendance::where('user_id', $user->id)
            ->where('working_day', $date)
            ->first();

        if (is_null($attendance)) {
            abort(404);
        }

        return view('admin.attendance.edit', compact(['user', 'date', 'attendance']));
    }

    public function update(AttendanceRequest $request, User $user, $date)
    {
        if (Gate::denies('admin.authority')) {
            abort(403);
        }

        if (is_null($user)) {
            abort(404);
        }

        $attendance = Attendance::where('user_id', $user->id)
            ->where('working_day', $date)
            ->first();

        if (is_null($attendance)) {
            abort(404);
        }

        $dt = new Carbon($date);

        $startTime = Carbon::create($dt->format('Y'), $dt->format('m'), $dt->format('d'), $request['start_hour'], $request['start_minute']);
        $finishTime = Carbon::create($dt->format('Y'), $dt->format('m'), $dt->format('d'), $request['finish_hour'], $request['finish_minute']);

        $attendance->start_time = $startTime;
        $attendance->finish_time = $finishTime;
        $attendance->save();

        return redirect()->route('admin.attendance.index', ['user' => $user, 'year' => $dt->format('Y'), 'month' => $dt->format('n')]);
    }
}
