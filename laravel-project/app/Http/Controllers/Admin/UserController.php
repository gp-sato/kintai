<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function index()
    {
        if (Gate::denies('admin.authority')) {
            abort(403);
        }

        $users = User::where('is_admin', false)->get();

        return view('admin.index', compact('users'));
    }

    public function search(Request $request)
    {
        if (Gate::denies('admin.authority')) {
            abort(403);
        }

        $name = $request->input('name');
        $email = $request->input('email');

        $users = User::where('is_admin', false)->get();

        // if (!empty($name)) {
        //     $users->where('name', 'LIKE', "%{$name}%")->get();
        // }
        // if (!empty($email)) {
        //     $users->where('email', 'LIKE', "%{$email}%")->get();
        // }

        return view('admin.index', compact(['users', 'name', 'email']));
    }
}
