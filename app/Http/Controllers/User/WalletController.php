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

    public function requestDeposit(Request $request)
    {
        $request->validate([
            'amount'                => 'required|numeric|min:100|max:100000',
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