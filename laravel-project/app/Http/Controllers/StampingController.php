<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StampingController extends Controller
{
    public function index()
    {
        return view('stamping');
    }
}
