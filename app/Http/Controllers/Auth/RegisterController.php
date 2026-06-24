<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    public function showRegister(Request $request)
    {
        $referralCode = $request->query('ref');
        return view('auth.register', compact('referralCode'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|unique:users,email',
            'user_email'    => 'required|email|max:255',
            'password'      => 'required|string|min:6|confirmed',
            'referral_code' => 'nullable|string|exists:users,referral_code',
        ], [
            'email.unique' => 'This phone number is already registered. Please login instead.',
        ]);

        do {
            $newReferralCode = strtoupper(Str::random(8));
        } while (User::where('referral_code', $newReferralCode)->exists());

        $referrer = null;
        if ($request->filled('referral_code')) {
            $referrer = User::where('referral_code', $request->referral_code)->first();
        }

        DB::beginTransaction();
        try {
            $user = User::create([
                'name'          => $request->name,
                'email'         => $request->email,
                'phone'         => $request->user_email,
                'password'      => Hash::make($request->password),
                'role'          => 'user',
                'referral_code' => $newReferralCode,
                'referred_by'   => $referrer?->id,
            ]);

            if ($referrer) {
                $bonus = (float) Setting::get('referral_bonus', 100);
                $referrer->creditWallet(
                    $bonus,
                    'referral_bonus',
                    "Referral bonus — {$user->name} joined using your link"
                );
                $referrer->increment('referral_bonus_balance', $bonus);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration failed: ' . $e->getMessage());
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['error' => 'Registration failed. Please try again.']);
        }

        Auth::login($user);
        return redirect()->route('user.dashboard')->with('success', 'Welcome! Account created successfully.');
    }
}