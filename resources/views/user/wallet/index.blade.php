@extends('layouts.user')

@section('title', 'My Wallet')

@section('content')
<div class="page-header">
    <h1>💳 My Wallet</h1>
</div>

<div class="wallet-hero large">
    <div>
        <span class="balance-label">Current Balance</span>
        <span class="balance-amount huge">Rs. {{ number_format($user->wallet_balance, 2) }}</span>
    </div>
    @if($pendingReqs > 0)
    <div class="pending-badge">⏳ {{ $pendingReqs }} pending request(s)</div>
    @endif
</div>

{{-- BALANCE BREAKDOWN --}}
@if($user->referral_bonus_balance > 0)
<div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:20px">
    <div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:16px;text-align:center">
        <div style="font-size:12px;color:var(--text2);margin-bottom:4px">💰 Withdrawable Balance</div>
        <div style="font-family:'Rajdhani',sans-serif;font-size:1.6rem;font-weight:700;color:var(--teal)">
            Rs. {{ number_format($user->wallet_balance - $user->referral_bonus_balance, 0) }}
        </div>
        <div style="font-size:11px;color:var(--text2);margin-top:4px">Can be withdrawn</div>
    </div>
    <div style="background:var(--bg2);border:1px solid rgba(255,193,7,0.4);border-radius:12px;padding:16px;text-align:center">
        <div style="font-size:12px;color:var(--text2);margin-bottom:4px">🎁 Referral Bonus</div>
        <div style="font-family:'Rajdhani',sans-serif;font-size:1.6rem;font-weight:700;color:#f59e0b">
            Rs. {{ number_format($user->referral_bonus_balance, 0) }}
        </div>
        <div style="font-size:11px;color:var(--text2);margin-top:4px">Bet only • Not withdrawable</div>
    </div>
</div>
@endif

<div class="two-col" id="deposit">
    <div class="card">
        <div class="card-header"><h3>➕ Add Funds to Wallet</h3></div>

        {{-- PAYMENT DETAILS FROM ADMIN --}}
        @php
            $payTitle  = \App\Models\Setting::get('payment_account_title', '');
            $payNumber = \App\Models\Setting::get('payment_account_number', '');
            $payType   = \App\Models\Setting::get('payment_account_type', '');
            $payNote   = \App\Models\Setting::get('payment_instructions', '');
        @endphp
        <div style="background:var(--bg3);border:1px solid var(--teal);border-radius:12px;padding:18px;margin:16px 20px 0">
            <h4 style="margin:0 0 12px;color:var(--teal)">📲 Send Payment To:</h4>
            <div style="display:grid;gap:10px">
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:var(--bg2);border-radius:8px">
                    <span style="color:var(--text2);font-size:13px">Account Type</span>
                    <strong>{{ $payType ?: 'N/A' }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:var(--bg2);border-radius:8px">
                    <span style="color:var(--text2);font-size:13px">Account Title</span>
                    <strong>{{ $payTitle ?: 'N/A' }}</strong>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 14px;background:var(--bg2);border-radius:8px;cursor:pointer"
                     onclick="copyText('{{ $payNumber }}')">
                    <span style="color:var(--text2);font-size:13px">Account Number</span>
                    <div style="display:flex;align-items:center;gap:8px">
                        <strong style="font-family:monospace;font-size:15px;color:var(--teal)">{{ $payNumber ?: 'N/A' }}</strong>
                        <span style="font-size:12px;color:var(--text2)">📋</span>
                    </div>
                </div>
                @if($payNote)
                <div style="padding:10px 14px;background:rgba(0,184,148,0.08);border-radius:8px;font-size:13px;color:var(--text2)">
                    ℹ️ {{ $payNote }}
                </div>
                @endif
            </div>
            <p style="margin:12px 0 0;font-size:12px;color:var(--text2)">
                1️⃣ Send payment to above account &nbsp;
                2️⃣ Take screenshot &nbsp;
                3️⃣ Fill form below & upload screenshot &nbsp;
                4️⃣ Admin will verify & credit your wallet
            </p>
        </div>

        @if(session('success'))
        <div class="alert success" style="margin:16px 20px 0">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert error" style="margin:16px 20px 0">❌ {{ session('error') }}</div>
        @endif

        <form action="{{ route('user.wallet.deposit') }}" method="POST"
              enctype="multipart/form-data" style="padding:20px">
            @csrf
            <div class="form-group">
                <label>Amount (Rs.) *</label>
                <input type="number" name="amount" class="form-input"
                       placeholder="e.g. 1000" min="1"
                       required value="{{ old('amount') }}">
                @error('amount')<span class="error">{{ $message }}</span>@enderror
            </div>
            <div class="form-group">
                <label>Payment Screenshot *</label>
                <div class="file-drop">
                    <input type="file" name="screenshot" accept="image/*"
                           required id="screenshotFile">
                    <div class="file-drop-ui">
                        <span class="file-icon">📸</span>
                        <span class="file-text">Upload your payment screenshot</span>
                        <span class="file-name" id="ssName">No file chosen</span>
                    </div>
                </div>
                <div id="imagePreview" style="display:none;margin-top:10px">
                    <img id="previewImg"
                         style="max-width:100%;border-radius:8px;border:1px solid var(--border)">
                </div>
                @error('screenshot')<span class="error">{{ $message }}</span>@enderror
            </div>
            <button type="submit" class="btn-primary full-width">
                📤 Submit Payment Request
            </button>
        </form>
    </div>

    <div class="card">
        <div class="card-header"><h3>📋 Transaction History</h3></div>
        @if($transactions->isEmpty())
        <div class="empty-state">
            <span>📭</span>
            <p>No transactions yet.</p>
        </div>
        @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Purpose</th>
                        <th>Amount</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $t)
                    <tr>
                        <td>{{ $t->created_at->format('d M Y') }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $t->purpose)) }}</td>
                        <td class="{{ $t->type === 'credit' ? 'text-green' : 'text-red' }} bold">
                            {{ $t->type === 'credit' ? '+' : '-' }}Rs. {{ number_format($t->amount, 0) }}
                        </td>
                        <td>Rs. {{ number_format($t->balance_after, 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:16px">{{ $transactions->links() }}</div>
        @endif
    </div>
</div>

{{-- CHANGE PASSWORD --}}
<div style="margin-top:24px">
    <div class="card">
        <div class="card-header">
            <h3>🔐 Change Password</h3>
        </div>
        <div style="padding:20px">
            @if(session('password_success'))
            <div class="alert success" style="margin-bottom:16px">{{ session('password_success') }}</div>
            @endif
            @if($errors->has('current_password'))
            <div class="alert error" style="margin-bottom:16px">❌ {{ $errors->first('current_password') }}</div>
            @endif

            <form method="POST" action="{{ route('user.wallet.change-password') }}">
                @csrf
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
                    <div>
                        <label style="display:block;margin-bottom:6px;font-size:13px;color:var(--text2)">
                            Current Password
                        </label>
                        <input type="password" name="current_password"
                               class="form-input" placeholder="Enter current password">
                        @error('current_password')
                            <span style="color:red;font-size:12px">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:6px;font-size:13px;color:var(--text2)">
                            New Password
                        </label>
                        <input type="password" name="new_password"
                               class="form-input" placeholder="Min 6 characters">
                        @error('new_password')
                            <span style="color:red;font-size:12px">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label style="display:block;margin-bottom:6px;font-size:13px;color:var(--text2)">
                            Confirm New Password
                        </label>
                        <input type="password" name="new_password_confirmation"
                               class="form-input" placeholder="Repeat new password">
                    </div>
                </div>
                <button type="submit" class="btn-primary" style="margin-top:16px;padding:10px 28px">
                    🔐 Change Password
                </button>
            </form>
        </div>
    </div>
</div>

<script>
const ssFile     = document.getElementById('screenshotFile');
const ssName     = document.getElementById('ssName');
const preview    = document.getElementById('imagePreview');
const previewImg = document.getElementById('previewImg');

ssFile.addEventListener('change', () => {
    if (ssFile.files.length) {
        ssName.textContent = ssFile.files[0].name;
        const reader = new FileReader();
        reader.onload = e => {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(ssFile.files[0]);
    }
});

function copyText(text) {
    navigator.clipboard.writeText(text);
    alert('✅ Account number copied!');
}
</script>

@endsection