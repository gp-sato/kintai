<?php

namespace App\Http\Controllers;

use App\Models\Attendance;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today()->format('Y-m-d');
        $attendance = Attendance::where('working_day', $today)->get();

        return view('dashboard', compact(['attendance']));
    }
}
