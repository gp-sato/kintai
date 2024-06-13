<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StampingController extends Controller
{
    public function index()
    {
        if (Gate::denies('view.stamping')) {
            abort(403);
        }

        return view('stamping');
    }
}
