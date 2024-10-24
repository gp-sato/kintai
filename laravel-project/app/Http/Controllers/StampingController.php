<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class StampingController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $attendance = Attendance::where(['user_id' => $user->id, 'working_day' => today()])->first();

        $userAll = User::where('is_admin', false)->get();
        $today = today()->format('Y-m-d');
        $userAll->each(function ($general_user) use ($today) {
            $attendee = Attendance::where('user_id', $general_user->id)
                ->where('working_day', $today)
                ->first();
            $general_user->string_round_start_time = $attendee?->round_start_time?->format('H:i');
            $general_user->string_round_finish_time = $attendee?->round_finish_time?->format('H:i');
        });

        return view('stamping', compact(['user', 'attendance', 'userAll']));
    }

    public function store()
    {
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
