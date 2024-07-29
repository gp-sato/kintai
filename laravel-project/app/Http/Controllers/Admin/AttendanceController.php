<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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
}
