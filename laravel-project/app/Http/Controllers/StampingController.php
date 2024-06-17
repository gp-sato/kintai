<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
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
}
