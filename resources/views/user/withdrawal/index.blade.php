@extends('layouts.user')

@section('title', 'Withdraw Funds')

@section('content')

<div class="page-header">
    <h1>💸 Withdraw Funds</h1>
    <span class="subtitle">Withdrawable Balance: <strong style="color:var(--teal)">Rs. {{ number_format(auth()->user()->wallet_balance - auth()->user()->referral_bonus_balance, 0) }}</strong></span>
</div>

@if(session('success'))
<div class="alert success" style="margin-bottom:20px">{{ session('success') }}</div>
@endif

<div class="two-col" style="align-items:start">

    {{-- WITHDRAWAL FORM --}}
    <div class="card">
        <div class="card-header">
            <h3>💸 New Withdrawal Request</h3>
        </div>
        <div style="padding:20px">
            {{-- 2% FEE NOTICE --}}
<div style="background:rgba(255,193,7,0.08);border:1px solid rgba(255,193,7,0.3);border-radius:10px;padding:12px 16px;margin-bottom:16px;display:flex;align-items:center;gap:10px">
    <span style="font-size:20px">ℹ️</span>
    <div>
        <div style="font-size:13px;font-weight:700;color:#f59e0b">2% Service Fee Applied</div>
        <div style="font-size:12px;color:var(--text2);margin-top:2px">
            A 2% service fee is deducted from your withdrawal amount. 
            Enter amount below to see exact calculation.
        </div>
    </div>
</div>
            <form action="{{ route('user.withdrawal.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Withdrawal Method *</label>
                    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:8px">
                        <label style="cursor:pointer">
                            <input type="radio" name="method" value="bank"
                                   onchange="showFields('bank')"
                                   {{ old('method') === 'bank' ? 'checked' : '' }}
                                   style="display:none" id="method_bank">
                            <div class="method-card" id="card_bank"
                                 style="border:2px solid var(--border);border-radius:10px;padding:14px;text-align:center;transition:all .2s">
                                <div style="font-size:24px">🏦</div>
                                <div style="font-size:13px;font-weight:600;margin-top:4px">Bank Transfer</div>
                            </div>
                        </label>
                        <label style="cursor:pointer">
                            <input type="radio" name="method" value="jazzcash"
                                   onchange="showFields('jazzcash')"
                                   {{ old('method') === 'jazzcash' ? 'checked' : '' }}
                                   style="display:none" id="method_jazzcash">
                            <div class="method-card" id="card_jazzcash"
                                 style="border:2px solid var(--border);border-radius:10px;padding:14px;text-align:center;transition:all .2s">
                                <div style="font-size:24px">📱</div>
                                <div style="font-size:13px;font-weight:600;margin-top:4px">JazzCash</div>
                            </div>
                        </label>
                        <label style="cursor:pointer">
                            <input type="radio" name="method" value="easypaisa"
                                   onchange="showFields('easypaisa')"
                                   {{ old('method') === 'easypaisa' ? 'checked' : '' }}
                                   style="display:none" id="method_easypaisa">
                            <div class="method-card" id="card_easypaisa"
                                 style="border:2px solid var(--border);border-radius:10px;padding:14px;text-align:center;transition:all .2s">
                                <div style="font-size:24px">💚</div>
                                <div style="font-size:13px;font-weight:600;margin-top:4px">EasyPaisa</div>
                            </div>
                        </label>
                    </div>
                    @error('method')<span class="error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
    <label>Amount (Rs.) *</label>
    <input type="number" name="amount" id="withdrawAmount" class="form-input"
           placeholder="Enter amount" min="1"
           max="{{ auth()->user()->wallet_balance - auth()->user()->referral_bonus_balance }}"
           value="{{ old('amount') }}"
           oninput="calcFee(this.value)">
    @error('amount')<span class="error">{{ $message }}</span>@enderror
</div>

{{-- FEE CALCULATOR --}}
<div id="feeInfo" style="display:none;background:rgba(0,184,148,0.05);border:1px solid rgba(0,184,148,.2);border-radius:8px;padding:12px;margin-bottom:12px">
    <div style="display:flex;justify-content:space-between;margin-bottom:6px">
        <span style="font-size:13px;color:var(--text2)">Requested Amount:</span>
        <span id="feeRequested" style="font-weight:700">Rs. 0</span>
    </div>
    <div style="display:flex;justify-content:space-between;margin-bottom:6px">
        <span style="font-size:13px;color:#ef4444">2% Fee:</span>
        <span id="feeCut" style="font-weight:700;color:#ef4444">- Rs. 0</span>
    </div>
    <div style="display:flex;justify-content:space-between;border-top:1px solid rgba(0,184,148,.2);padding-top:8px">
        <span style="font-size:13px;font-weight:700">You Will Receive:</span>
        <span id="feeReceive" style="font-weight:700;color:var(--teal);font-size:1.1rem">Rs. 0</span>
    </div>
</div>

                <div id="fields_bank" style="display:none">
                    <div class="form-group">
                        <label>Bank Name *</label>
                        <input type="text" name="bank_name" class="form-input"
                               placeholder="e.g. HBL, MCB, UBL" value="{{ old('bank_name') }}">
                        @error('bank_name')<span class="error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Account Title *</label>
                        <input type="text" name="account_title" class="form-input"
                               placeholder="Account holder name" value="{{ old('account_title') }}">
                        @error('account_title')<span class="error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Account Number *</label>
                        <input type="text" name="account_number" class="form-input"
                               placeholder="e.g. 1234567890123456" value="{{ old('account_number') }}">
                        @error('account_number')<span class="error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div id="fields_mobile" style="display:none">
                    <div class="form-group">
                        <label>Account Holder Name *</label>
                        <input type="text" name="account_holder" class="form-input"
                               placeholder="Full name" value="{{ old('account_holder') }}">
                        @error('account_holder')<span class="error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label>Mobile Number *</label>
                        <input type="text" name="mobile_number" class="form-input"
                               placeholder="03XXXXXXXXX" value="{{ old('mobile_number') }}">
                        @error('mobile_number')<span class="error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <button type="submit" class="btn-primary full-width" style="margin-top:8px">
                    💸 Submit Withdrawal Request
                </button>
            </form>
        </div>
    </div>

    {{-- WITHDRAWAL HISTORY --}}
    <div class="card">
        <div class="card-header">
            <h3>📋 Withdrawal History</h3>
        </div>
        @if($withdrawals->isEmpty())
        <div class="empty-state">
            <span>💸</span>
            <p>No withdrawal requests yet.</p>
        </div>
        @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($withdrawals as $w)
                    <tr>
                        <td style="font-weight:700">Rs. {{ number_format($w->amount, 0) }}</td>
                        <td>{{ strtoupper($w->method) }}</td>
                        <td><span class="badge {{ $w->status }}">{{ ucfirst($w->status) }}</span></td>
                        <td>{{ $w->created_at->format('d M Y') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>

<script>
function showFields(method) {
    ['bank','jazzcash','easypaisa'].forEach(m => {
        document.getElementById('card_' + m).style.border = '2px solid var(--border)';
        document.getElementById('card_' + m).style.background = 'transparent';
    });
    document.getElementById('card_' + method).style.border = '2px solid var(--teal)';
    document.getElementById('card_' + method).style.background = 'rgba(0,184,148,0.08)';
    document.getElementById('fields_bank').style.display   = method === 'bank' ? 'block' : 'none';
    document.getElementById('fields_mobile').style.display = (method === 'jazzcash' || method === 'easypaisa') ? 'block' : 'none';
}
function calcFee(val) {
    const amount = parseFloat(val) || 0;
    const fee    = Math.round(amount * 0.02);
    const receive = amount - fee;

    if (amount > 0) {
        document.getElementById('feeInfo').style.display = 'block';
        document.getElementById('feeRequested').textContent = 'Rs. ' + amount.toLocaleString();
        document.getElementById('feeCut').textContent = '- Rs. ' + fee.toLocaleString();
        document.getElementById('feeReceive').textContent = 'Rs. ' + receive.toLocaleString();
    } else {
        document.getElementById('feeInfo').style.display = 'none';
    }
}
@if(old('method'))
    showFields('{{ old('method') }}');
    document.getElementById('method_{{ old('method') }}').checked = true;
@endif
</script>

@endsection