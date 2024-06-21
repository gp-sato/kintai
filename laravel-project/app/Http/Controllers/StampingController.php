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
        if (Gate::denies('view.stamping')) {
            abort(403);
        }

        $user = Auth::user();

        $attendance = Attendance::where(['user_id' => $user->id, 'working_day' => today()])->first();

        return view('stamping', compact(['user', 'attendance']));
    }

    public function store(User $user)
    {
        if (is_null($user)) {
            abort(403);
        }

        if (Gate::denies('store.stamping', [$user->id])) {
            abort(403);
        }

        $attendance = Attendance::where(['user_id' => $user->id, 'working_day' => today()])->first();

        if (is_null($attendance)) {
            $attendance = new Attendance();
            $attendance->user_id = $user->id;
            $attendance->working_day = today();
        }

        if (is_null($attendance->start_time)) {
            $current_time = now();

            if ($current_time->minute >= 0 && $current_time->minute <= 29):
                $start_time = Carbon::createFromTime($current_time->hour, 30, 0);
            elseif ($current_time->minute >= 30 && $current_time->minute <= 59):
                $start_time = Carbon::createFromTime($current_time->hour + 1, 0, 0);
            endif;

            $attendance->start_time = $start_time;
            $attendance->finish_time = null;
            $attendance->save();

        } elseif (is_null($attendance->finish_time)) {
            $current_time = now();

            if ($current_time->minute >= 0 && $current_time->minute <= 29):
                $finish_time = Carbon::createFromTime($current_time->hour, 0, 0);
            elseif ($current_time->minute >= 30 && $current_time->minute <= 59):
                $finish_time = Carbon::createFromTime($current_time->hour, 30, 0);
            endif;

            if ($finish_time->lt($attendance->start_time)) {
                $finish_time = $attendance->start_time;
            }

            $attendance->finish_time = $finish_time;
            $attendance->save();
        }

        return redirect()->route('stamping.index')->with('message', '打刻しました。');
    }
}
