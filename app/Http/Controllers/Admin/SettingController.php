<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\PasswordResetRequest;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'referral_bonus'         => Setting::get('referral_bonus', 100),
            'min_withdrawal'         => Setting::get('min_withdrawal', 500),
            'max_withdrawal'         => Setting::get('max_withdrawal', 50000),
            'min_deposit'            => Setting::get('min_deposit', 100),
            'site_name'              => Setting::get('site_name', 'Lottery App'),
            'maintenance_mode'       => Setting::get('maintenance_mode', 0),
            'payment_account_title'  => Setting::get('payment_account_title', ''),
            'payment_account_number' => Setting::get('payment_account_number', ''),
            'payment_account_type'   => Setting::get('payment_account_type', 'JazzCash'),
            'payment_instructions'   => Setting::get('payment_instructions', ''),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'referral_bonus'         => 'required|numeric|min:0',
            'min_withdrawal'         => 'required|numeric|min:0',
            'max_withdrawal'         => 'required|numeric|min:0',
            'min_deposit'            => 'required|numeric|min:0',
            'site_name'              => 'required|string|max:100',
            'maintenance_mode'       => 'nullable|boolean',
            'payment_account_title'  => 'nullable|string|max:100',
            'payment_account_number' => 'nullable|string|max:50',
            'payment_account_type'   => 'nullable|string|max:50',
            'payment_instructions'   => 'nullable|string|max:500',
        ]);

        Setting::set('referral_bonus',         $request->referral_bonus);
        Setting::set('min_withdrawal',         $request->min_withdrawal);
        Setting::set('max_withdrawal',         $request->max_withdrawal);
        Setting::set('min_deposit',            $request->min_deposit);
        Setting::set('site_name',              $request->site_name);
        Setting::set('maintenance_mode',       $request->has('maintenance_mode') ? 1 : 0);
        Setting::set('payment_account_title',  $request->payment_account_title);
        Setting::set('payment_account_number', $request->payment_account_number);
        Setting::set('payment_account_type',   $request->payment_account_type);
        Setting::set('payment_instructions',   $request->payment_instructions);

        return back()->with('success', '✅ Settings saved successfully!');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|string|min:6|confirmed',
        ]);

        $admin = auth()->user();

        if (!\Hash::check($request->current_password, $admin->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        $admin->update(['password' => \Hash::make($request->new_password)]);

        return back()->with('success', '✅ Password changed successfully!');
    }

    public function passwordResets()
    {
        $resets       = PasswordResetRequest::with('user')->latest()->paginate(20);
        $pendingCount = PasswordResetRequest::where('status', 'pending')->count();

        return view('admin.password-resets.index', compact('resets', 'pendingCount'));
    }

    public function resolvePasswordReset(PasswordResetRequest $reset)
    {
        $reset->update(['status' => 'resolved']);
        return back()->with('success', '✅ Marked as resolved!');
    }
}