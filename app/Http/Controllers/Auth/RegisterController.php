<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

class RegisterController extends Controller
{
    public function show()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $roles = Role::orderBy('name')->pluck('name');

        if ($roles->isEmpty()) {
            $roles = collect(['agency_owner', 'client']);
        }

        return view('auth.register', [
            'roles' => $roles,
        ]);
    }

    public function register(Request $request)
    {
        $roles = Role::pluck('name');

        if ($roles->isEmpty()) {
            $roles = collect(['agency_owner', 'client']);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role' => ['required', 'string', 'in:' . implode(',', $roles)],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        $user->assignRole($validated['role']);

        Auth::login($user, $request->boolean('remember'));

        return redirect()->route('dashboard');
    }
}
