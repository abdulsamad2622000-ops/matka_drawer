<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function index()
    {
        $pending  = WithdrawalRequest::with('user')->where('status', 'pending')->latest()->get();
        $processed = WithdrawalRequest::with('user')->whereIn('status', ['approved','rejected'])->latest()->take(20)->get();

        return view('admin.withdrawals.index', compact('pending', 'processed'));
    }

    public function approve(WithdrawalRequest $withdrawal)
    {
        if (!$withdrawal->isPending()) {
            return back()->with('error', 'Request already processed.');
        }

        $withdrawal->update([
            'status'       => 'approved',
            'processed_at' => now(),
        ]);

        return back()->with('success', '✅ Withdrawal approved successfully!');
    }

    public function reject(Request $request, WithdrawalRequest $withdrawal)
    {
        if (!$withdrawal->isPending()) {
            return back()->with('error', 'Request already processed.');
        }

        // Refund to wallet
        $withdrawal->user->creditWallet(
            $withdrawal->amount,
            'refund',
            'Withdrawal rejected — amount refunded to wallet'
        );

        $withdrawal->update([
            'status'       => 'rejected',
            'admin_note'   => $request->admin_note,
            'processed_at' => now(),
        ]);

        return back()->with('success', '↩️ Withdrawal rejected & amount refunded!');
    }
}