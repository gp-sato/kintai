<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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

        if (session()->exists('user_id')) {
            session()->forget('user_id');
        }
        if (session()->exists('working_day')) {
            session()->forget('working_day');
        }

        session()->put('user_id', $user->id);
        session()->put('working_day', $date);

        return view('admin.attendance.edit', compact(['user', 'date', 'attendance']));
    }

    public function update(Request $request, User $user, $date)
    {
        if (Gate::denies('admin.authority')) {
            abort(403);
        }

        if (is_null($user)) {
            abort(404);
        }

        if (!session()->exists('user_id')) {
            abort(404);
        }
        if (!session()->exists('working_day')) {
            abort(404);
        }

        if (!session()->has('user_id')) {
            abort(404);
        }
        if (!session()->has('working_day')) {
            abort(404);
        }

        if ($user->id !== session()->get('user_id')) {
            abort(403);
        }
        if ($date !== session()->get('working_day')) {
            abort(403);
        }

        $attendance = Attendance::where('user_id', $user->id)
            ->where('working_day', $date)
            ->first();

        if (is_null($attendance)) {
            abort(404);
        }

        $hours = '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23';
        $minutes = '0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59';

        $validator = Validator::make($request->all(), [
            'start_hour' => "required|integer|in:{$hours}",
            'start_minute' => "required|integer|in:{$minutes}",
            'finish_hour' => "required|integer|in:{$hours}|gte:start_hour",
            'finish_minute' => "required|integer|in:{$minutes}",
        ]);
        $validator->sometimes('finish_minute', "gte:start_minute", function ($request) {
            return $request->start_hour == $request->finish_hour;
        });
        if ($validator->fails()) {
            return redirect()->route('admin.attendance.edit', ['user' => $user, 'date' => $date]);
        }

        $formData = $validator->validated();

        session()->forget('user_id');
        session()->forget('working_day');

        $dt = new Carbon($date);

        return redirect()->route('admin.attendance.index', ['user' => $user, 'year' => $dt->format('Y'), 'month' => $dt->format('n')]);
    }
}
