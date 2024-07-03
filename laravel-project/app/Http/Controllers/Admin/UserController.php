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

        if (!empty($name) && !empty($email)) {
            $users = User::where('is_admin', false)
                ->where('name', 'LIKE', "%{$name}%")
                ->where('email', 'LIKE', "%{$email}%")
                ->get();
        } elseif (!empty($name)) {
            $users = User::where('is_admin', false)
                ->where('name', 'LIKE', "%{$name}%")
                ->get();
        } elseif (!empty($email)) {
            $users = User::where('is_admin', false)
                ->where('email', 'LIKE', "%{$email}%")
                ->get();
        } else {
            $users = User::where('is_admin', false)->get();
        }

        return view('admin.index', compact(['users', 'name', 'email']));
    }
}
