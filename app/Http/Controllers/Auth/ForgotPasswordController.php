<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function show()
    {
        return view('auth.forgot-password');
    }

    public function store(Request $request)
    {
        $request->validate([
            'phone'  => 'required|string|max:20',
            'reason' => 'nullable|string|max:500',
        ]);

        // Phone se user dhundo
        $user = User::where('email', $request->phone)->first();

        if (!$user) {
            return back()->withErrors(['phone' => 'No account found with this phone number.']);
        }

        // Auto generate random password
        $generatedPassword = 'Matka@' . strtoupper(Str::random(6));

        PasswordResetRequest::create([
            'user_id'            => $user->id,
            'email'              => $user->phone,   // actual email (signup wala)
            'user_name'          => $user->name,    // full name (signup wala)
            'reason'             => $request->reason,
            'status'             => 'pending',
            'generated_password' => $generatedPassword,
        ]);

        return back()->with('success', '✅ Request submitted! Admin will send your new password soon.');
    }
}