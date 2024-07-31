<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (Gate::denies('admin.authority')) {
            abort(403);
        }

        $name = $request->query('name');
        $email = $request->query('email');

        $builder = User::where('is_admin', false);
        if (!empty($name)) {
            $builder->where('name', 'LIKE', "%{$name}%");
        }
        if (!empty($email)) {
            $builder->where('email', 'LIKE', "%{$email}%");
        }
        $users = $builder->get();

        return view('admin.index', compact(['users', 'name', 'email']));
    }

    public function create()
    {
        if (Gate::denies('admin.authority')) {
            abort(403);
        }

        return view('admin.user.create');
    }

    public function confirmCreate(Request $request)
    {
        if (Gate::denies('admin.authority')) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|max:255|min:8|confirmed',
        ]);

        $name = $request->input('name');
        $email = $request->input('email');
        $password = $request->input('password');

        return view('admin.user.confirmCreate', compact(['name', 'email', 'password']));
    }

    public function store(Request $request)
    {
        if (Gate::denies('admin.authority')) {
            abort(403);
        }

        $input = $request->only(['name', 'email', 'password']);

        $validator = Validator::make($input, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|max:255|min:8',
        ]);
        if ($validator->fails()) {
            return redirect()->route('admin.user.create')
                ->withInput()
                ->withErrors($validator);
        }

        if ($request->input('back') == 'back') {
            return redirect()->route('admin.user.create')
                ->withInput();
        }

        return redirect()->route('admin.index');
    }
}
