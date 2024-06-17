<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
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
        if (is_Null($user)) {
            abort(403);
        }

        if (Gate::denies('store.stamping', [$user->id])) {
            abort(403);
        }

        return redirect()->route('stamping.index')->with('message', '打刻しました。');
    }
}
