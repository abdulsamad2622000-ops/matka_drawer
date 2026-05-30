<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PaymentRequest;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = PaymentRequest::with('user')->latest()->paginate(20);
        return view('admin.payments.index', compact('payments'));
    }

    public function show(PaymentRequest $payment)
    {
        return view('admin.payments.show', compact('payment'));
    }

    public function approve(Request $request, PaymentRequest $payment)
    {
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Already processed!');
        }

        $payment->user->creditWallet(
            $payment->amount,
            'deposit',
            'Payment approved by admin - Ref: #' . $payment->id
        );

        $payment->update([
            'status'       => 'approved',
            'admin_note'   => $request->admin_note,
            'approved_by'  => auth()->id(),
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Payment approved! Rs. ' . $payment->amount . ' added to user wallet.');
    }

    public function reject(Request $request, PaymentRequest $payment)
    {
        $request->validate(['admin_note' => 'required|string|max:500']);

        $payment->update([
            'status'       => 'rejected',
            'admin_note'   => $request->admin_note,
            'approved_by'  => auth()->id(),
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Payment rejected.');
    }
}