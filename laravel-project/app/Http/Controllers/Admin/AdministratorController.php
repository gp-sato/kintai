<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AdministratorController extends Controller
{
    public function editAdmin()
    {
        if (Gate::denies('admin.authority')) {
            abort(403);
        }

        $admin = Auth::user();

        return view('admin.administrator.edit', compact(['admin']));
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

        return view('admin.administrator.confirm', compact(['formData']));
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
            return redirect()->route('admin.administrator.edit')
                ->withInput()
                ->withErrors($validator);
        }

        if ($request->input('back') == 'back') {
            return redirect()->route('admin.administrator.edit')
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
}
