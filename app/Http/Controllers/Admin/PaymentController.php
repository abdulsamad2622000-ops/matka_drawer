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

        // User ka wallet credit karo
        $payment->user->creditWallet(
            $payment->amount,
            'deposit',
            'Payment approved by admin - Ref: #' . $payment->id
        );

        // Referrer ko 10% commission do
        $user = $payment->user;
        if ($user->referred_by) {
            $referrer = \App\Models\User::find($user->referred_by);
            if ($referrer) {
                $commission = round($payment->amount * 0.10, 2);
                $referrer->creditWallet(
                    $commission,
                    'referral_bonus',
                    "10% commission - {$user->name} ne Rs. " . number_format($payment->amount, 0) . " deposit kiya"
                );
                $referrer->increment('referral_bonus_balance', $commission);
            }
        }

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