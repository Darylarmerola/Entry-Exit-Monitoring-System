<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }

    public function login(Request $request)
    {
        $credentials = $request->validate(['username'=>'required|string','password'=>'required|string']);
        if (!Auth::attempt($credentials + ['is_active'=>true], $request->boolean('remember'))) {
            throw ValidationException::withMessages(['username'=>'The username or password is incorrect, or the account is inactive.']);
        }
        $request->session()->regenerate();
        return redirect()->intended(route(Auth::user()->role.'.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'You have been logged out.');
    }
}
