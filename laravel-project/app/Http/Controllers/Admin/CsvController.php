<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CsvUploadRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CsvController extends Controller
{
    public function index()
    {
        if (Gate::denies('admin.authority')) {
            abort(403);
        }

        $users = User::where('is_admin', 0)->get();

        return view('admin.csv.index', compact(['users']));
    }

    public function upload(CsvUploadRequest $request)
    {
        if (Gate::denies('admin.authority')) {
            abort(403);
        }

        return redirect()->route('admin.csv.index');
    }
}
