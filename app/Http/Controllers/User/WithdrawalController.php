<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{
    public function store(Request $request)
    {
        $user        = Auth::user();
        $minWithdraw = (float) Setting::get('min_withdrawal', 500);
        $maxWithdraw = (float) Setting::get('max_withdrawal', 50000);

        $request->validate([
            'amount'         => "required|numeric|min:{$minWithdraw}|max:{$maxWithdraw}",
            'method'         => 'required|in:bank,jazzcash,easypaisa',
            'bank_name'      => 'required_if:method,bank|nullable|string|max:100',
            'account_title'  => 'required_if:method,bank|nullable|string|max:100',
            'account_number' => 'required_if:method,bank|nullable|string|max:50',
            'mobile_number'  => 'required_if:method,jazzcash,easypaisa|nullable|string|max:20',
            'account_holder' => 'required_if:method,jazzcash,easypaisa|nullable|string|max:100',
        ]);

        // Withdrawable balance = total - referral bonus
        $withdrawableBalance = $user->wallet_balance - $user->referral_bonus_balance;

        if ($withdrawableBalance < $request->amount) {
            $withdrawable = number_format($withdrawableBalance, 0);
            return back()->withErrors([
                'amount' => "Insufficient withdrawable balance. You can withdraw Rs. {$withdrawable} (referral bonus is not withdrawable)."
            ]);
        }

        // Pending request check
        $hasPending = WithdrawalRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPending) {
            return back()->withErrors(['amount' => 'You already have a pending withdrawal request.']);
        }

        // Deduct from wallet
        $user->debitWallet(
            $request->amount,
            'withdrawal',
            'Withdrawal request — ' . strtoupper($request->method)
        );

        // Create request
        WithdrawalRequest::create([
            'user_id'        => $user->id,
            'amount'         => $request->amount,
            'method'         => $request->method,
            'status'         => 'pending',
            'bank_name'      => $request->bank_name,
            'account_title'  => $request->account_title,
            'account_number' => $request->account_number,
            'mobile_number'  => $request->mobile_number,
            'account_holder' => $request->account_holder,
        ]);

        return back()->with('success', '✅ Withdrawal request submitted! Admin will process it soon.');
    }
}