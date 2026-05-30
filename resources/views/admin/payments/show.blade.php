@extends('layouts.admin')

@section('title', 'Review Payment #' . $payment->id)

@section('content')
<div class="page-header">
    <h1>Review Payment #{{ $payment->id }}</h1>
    <span class="subtitle">Submitted {{ $payment->created_at->diffForHumans() }}</span>
</div>

<div class="two-col">
    <div class="card">
        <div class="card-header"><h3>📸 Payment Screenshot</h3></div>
        <div style="padding:20px">
            <img src="{{ asset('storage/' . $payment->screenshot_path) }}"
                 alt="Payment Screenshot"
                 style="max-width:100%;border-radius:10px;border:1px solid var(--border)">
        </div>
    </div>

    <div class="card">
        <div class="card-header"><h3>📋 Payment Details</h3></div>
        <div style="padding:20px">
            <div class="detail-row">
                <span class="detail-label">User</span>
                <span>{{ $payment->user->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email</span>
                <span>{{ $payment->user->email }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Amount</span>
                <span style="color:var(--gold);font-size:1.4rem;font-family:'Rajdhani',sans-serif;font-weight:700">
                    Rs. {{ number_format($payment->amount, 0) }}
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Reference</span>
                <span class="mono">{{ $payment->transaction_reference ?? '—' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status</span>
                <span class="badge {{ $payment->status }}">{{ ucfirst($payment->status) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Current Wallet</span>
                <span>Rs. {{ number_format($payment->user->wallet_balance, 0) }}</span>
            </div>

            @if($payment->status === 'pending')
            <hr style="border-color:var(--border);margin:20px 0">

            <form action="{{ route('admin.payments.approve', $payment) }}" method="POST"
                  style="margin-bottom:12px">
                @csrf
                <div class="form-group">
                    <label>Admin Note (optional)</label>
                    <textarea name="admin_note" class="form-input" rows="2"
                              placeholder="e.g. Verified via JazzCash"></textarea>
                </div>
                <button type="submit" class="btn-success full-width"
                        onclick="return confirm('Approve Rs.{{ number_format($payment->amount,0) }} and credit to user wallet?')">
                    ✅ Approve & Credit Wallet
                </button>
            </form>

            <form action="{{ route('admin.payments.reject', $payment) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Rejection Reason *</label>
                    <textarea name="admin_note" class="form-input" rows="2"
                              placeholder="Reason for rejection..." required></textarea>
                </div>
                <button type="submit" class="btn-danger full-width"
                        onclick="return confirm('Reject this payment request?')">
                    ❌ Reject Payment
                </button>
            </form>

            @else
            <div class="alert {{ $payment->status === 'approved' ? 'success' : 'error' }}"
                 style="margin-top:20px">
                {{ $payment->status === 'approved' ? '✅ Approved' : '❌ Rejected' }}
                by {{ $payment->approver->name ?? 'Admin' }}
                on {{ $payment->processed_at?->format('d M Y H:i') }}
                @if($payment->admin_note)
                    <br><em>Note: {{ $payment->admin_note }}</em>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection