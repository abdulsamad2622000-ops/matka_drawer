<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\PaymentRequest;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index()
    {
        $user         = auth()->user();
        $transactions = $user->walletTransactions()->latest()->paginate(20);
        $pendingReqs  = $user->paymentRequests()->where('status', 'pending')->count();
        return view('user.wallet.index', compact('user', 'transactions', 'pendingReqs'));
    }
public function changePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'new_password'     => 'required|string|min:6|confirmed',
    ]);

    $user = auth()->user();

    if (!\Hash::check($request->current_password, $user->password)) {
        return back()->withErrors(['current_password' => 'Current password is incorrect.']);
    }

    $user->update(['password' => \Hash::make($request->new_password)]);

    return back()->with('password_success', '✅ Password changed successfully!');
}
    public function requestDeposit(Request $request)
    {
      $request->validate([
    'amount'                => 'required|numeric|min:1',
    'transaction_reference' => 'nullable|string|max:100',
    'screenshot'            => 'required|image|mimes:jpg,jpeg,png|max:5120',
]);

        $screenshotPath = $request->file('screenshot')->store('payment-screenshots', 'public');

        PaymentRequest::create([
            'user_id'               => auth()->id(),
            'amount'                => $request->amount,
            'screenshot_path'       => $screenshotPath,
            'transaction_reference' => $request->transaction_reference,
            'status'                => 'pending',
        ]);

        return back()->with('success', 'Payment request submitted! Admin will verify and credit your wallet soon.');
    }
}