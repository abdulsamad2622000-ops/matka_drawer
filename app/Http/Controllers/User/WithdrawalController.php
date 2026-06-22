<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WithdrawalController extends Controller
{
    public function index()
    {
        $user        = Auth::user();
        $withdrawals = $user->withdrawalRequests()->latest()->take(10)->get();

        return view('user.withdrawal.index', compact('withdrawals'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'amount'         => 'required|numeric|min:1',
            'method'         => 'required|in:bank,jazzcash,easypaisa',
            'bank_name'      => 'required_if:method,bank|nullable|string|max:100',
            'account_title'  => 'required_if:method,bank|nullable|string|max:100',
            'account_number' => 'required_if:method,bank|nullable|string|max:50',
            'mobile_number'  => 'required_if:method,jazzcash,easypaisa|nullable|string|max:20',
            'account_holder' => 'required_if:method,jazzcash,easypaisa|nullable|string|max:100',
        ]);

        $withdrawableBalance = $user->wallet_balance - $user->referral_bonus_balance;

        if ($withdrawableBalance < $request->amount) {
            $withdrawable = number_format($withdrawableBalance, 0);
            return back()->withErrors([
                'amount' => "Insufficient withdrawable balance. You can withdraw Rs. {$withdrawable} (referral bonus is not withdrawable)."
            ]);
        }

        // 2% fee calculate karo
        $fee            = round($request->amount * 0.02, 2);
        $amountAfterFee = round($request->amount - $fee, 2);

        // Wallet se pura amount deduct karo
        $user->debitWallet(
            $request->amount,
            'withdrawal',
            'Withdrawal — ' . strtoupper($request->method) . ' | 2% fee: Rs. ' . number_format($fee, 0)
        );

        WithdrawalRequest::create([
            'user_id'        => $user->id,
            'amount'         => $amountAfterFee,
            'method'         => $request->method,
            'status'         => 'pending',
            'bank_name'      => $request->bank_name,
            'account_title'  => $request->account_title,
            'account_number' => $request->account_number,
            'mobile_number'  => $request->mobile_number,
            'account_holder' => $request->account_holder,
        ]);

        return redirect()->route('user.withdrawal.index')
            ->with('success', '✅ Request submitted! Rs. ' . number_format($fee, 0) . ' fee deducted. You will receive Rs. ' . number_format($amountAfterFee, 0) . '.');
    }
}