<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class StampingController extends Controller
{
    public function index()
    {
        if (Gate::denies('allowed.stamping')) {
            abort(403);
        }

        $user = Auth::user();

        $attendance = Attendance::where(['user_id' => $user->id, 'working_day' => today()])->first();

        if (!is_null($attendance)) {
            $attendance->start_time = Attendance::stampRounding($attendance->start_time);
            $attendance->finish_time = Attendance::stampRounding($attendance->finish_time);
        }

        return view('stamping', compact(['user', 'attendance']));
    }

    public function store()
    {
        if (Gate::denies('allowed.stamping')) {
            abort(403);
        }

        $user = Auth::user();

        $attendance = Attendance::where(['user_id' => $user->id, 'working_day' => today()])->first();

        if (is_null($attendance)) {
            $attendance = new Attendance();
            $attendance->user_id = $user->id;
            $attendance->working_day = today();
        }

        if (is_null($attendance->start_time)) {
            $attendance->start_time = now();
            $attendance->finish_time = null;
            $attendance->save();
        } elseif (is_null($attendance->finish_time)) {
            $attendance->finish_time = now();
            $attendance->save();
        }

        return redirect()->route('stamping.index')->with('message', '打刻しました。');
    }
}
