<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
       $request->validate([
    'email'    => 'required|string',
    'password' => 'required|string|min:6',
]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            if (auth()->user()->isAdmin()) {
                return redirect()->route('admin.dashboard');
            }

            if (!auth()->user()->is_active) {
                Auth::logout();
                return back()->with('error', 'Your account has been deactivated.');
            }

            return redirect()->route('user.dashboard');
        }

        return back()->with('error', 'Invalid email or password.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}