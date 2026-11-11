<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserIdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class RegistrationController extends Controller
{
    public function show() { return view('auth.register'); }

    public function store(Request $request)
    {
        $data = $request->validate([
            'role' => ['required', Rule::in(['student', 'teacher'])],
            'name' => ['required', 'string', 'max:100'],
            'lastname' => ['required', 'string', 'max:100'],
            'age' => ['required', 'integer', 'min:1', 'max:120'],
            'course' => ['nullable', 'required_if:role,student', 'string', 'max:120'],
            'gender' => ['nullable', 'required_if:role,student', Rule::in(['Male', 'Female', 'Other', 'Prefer not to say'])],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'username' => ['required', 'alpha_dash', 'min:3', 'max:50', 'unique:users,username'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = DB::transaction(function () use ($data) {
            $data['school_id'] = UserIdGenerator::next($data['role']);
            $data['is_active'] = true;

            return User::create($data);
        });

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($user->role . '.dashboard')->with('success', 'Registration successful.');
    }
}
