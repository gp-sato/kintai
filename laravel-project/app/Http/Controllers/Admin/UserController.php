<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

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

    public function editAdmin()
    {
        if (Gate::denies('admin.authority')) {
            abort(403);
        }

        $admin = Auth::user();

        return view('admin.edit', compact(['admin']));
    }

    public function confirmAdmin(Request $request)
    {
        if (Gate::denies('admin.authority')) {
            abort(403);
        }

        $adminId = Auth::id();

        $formData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($adminId), 'max:255'],
            'password' => 'nullable|min:8|confirmed',
        ]);

        return view('admin.confirm', compact(['formData']));
    }

    public function updateAdmin(Request $request)
    {
        if (Gate::denies('admin.authority')) {
            abort(403);
        }

        $adminId = Auth::id();

        $input = $request->only(['name', 'email', 'password']);

        $validator = Validator::make($input, [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($adminId), 'max:255'],
            'password' => 'nullable|min:8',
        ]);
        if ($validator->fails()) {
            return redirect()->route('admin.edit')
                ->withInput()
                ->withErrors($validator);
        }

        if ($request->input('back') == 'back') {
            return redirect()->route('admin.edit')
                ->withInput();
        }

        $formData = $validator->validated();

        if (is_null($formData['password'])) {
            unset($formData['password']);
        }

        $user = User::find($adminId);
        $user->fill($formData);
        $user->save();

        return redirect()->route('admin.index');
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

        $formData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|min:8|confirmed',
        ]);

        return view('admin.user.confirmCreate', compact(['formData']));
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
            'password' => 'required|min:8',
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

        $formData = $validator->validated();

        $user = new User();
        $user->fill($formData);
        $user->email_verified_at = now();
        $user->save();

        return redirect()->route('admin.index');
    }

    public function edit(User $user)
    {
        if (Gate::denies('admin.authority')) {
            abort(403);
        }

        if (is_null($user)) {
            abort(404);
        }

        if (session()->exists('user_id')) {
            session()->forget('user_id');
        }

        session()->put('user_id', $user->id);

        return view('admin.user.edit', compact(['user']));
    }

    public function confirmEdit(Request $request, User $user)
    {
        if (Gate::denies('admin.authority')) {
            abort(403);
        }

        if (is_null($user)) {
            abort(404);
        }

        if (!session()->exists('user_id')) {
            abort(404);
        }

        if (!session()->has('user_id')) {
            abort(404);
        }

        if ($user->id !== session()->get('user_id')) {
            abort(403);
        }

        $formData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id), 'max:255'],
            'password' => 'nullable|min:8|confirmed',
        ]);

        return view('admin.user.confirmEdit', compact(['user', 'formData']));
    }

    public function update(Request $request, User $user)
    {
        if (Gate::denies('admin.authority')) {
            abort(403);
        }

        if (is_null($user)) {
            abort(404);
        }

        if (!session()->exists('user_id')) {
            abort(404);
        }

        if (!session()->has('user_id')) {
            abort(404);
        }

        if ($user->id !== session()->get('user_id')) {
            abort(403);
        }

        $input = $request->only(['name', 'email', 'password']);

        $validator = Validator::make($input, [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id), 'max:255'],
            'password' => 'nullable|min:8',
        ]);
        if ($validator->fails()) {
            return redirect()->route('admin.user.edit', $user)
                ->withInput()
                ->withErrors($validator);
        }

        if ($request->input('back') == 'back') {
            return redirect()->route('admin.user.edit', $user)
                ->withInput();
        }

        $formData = $validator->validated();

        if (is_null($formData['password'])) {
            unset($formData['password']);
        }

        $user->fill($formData);
        $user->save();

        session()->forget('user_id');

        return redirect()->route('admin.index');
    }
}
