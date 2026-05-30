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

<div class="two-col" id="deposit">
    <div class="card">
        <div class="card-header"><h3>➕ Add Funds to Wallet</h3></div>

        <div class="payment-methods">
            <h4>How to Add Funds</h4>
            <div class="pm-list">
                <div class="pm-item">
                    <span class="pm-icon">🔵</span>
                    <div>
                        <strong>Pay via Payoneer</strong>
                        <p style="font-size:12px;color:var(--text2);margin-bottom:8px">
                            Click below to make payment via Payoneer
                        </p>
                        <a href="https://payoneer.com/dummy-payment-link"
                           target="_blank"
                           class="btn-primary"
                           style="font-size:13px;padding:8px 16px;display:inline-block">
                            💳 Pay Now via Payoneer
                        </a>
                    </div>
                </div>
            </div>
            <p class="pm-note">
                <strong>Steps:</strong><br>
                1️⃣ Click "Pay Now" button above<br>
                2️⃣ Complete payment on Payoneer<br>
                3️⃣ Take screenshot of confirmation<br>
                4️⃣ Fill form below & upload screenshot<br>
                5️⃣ Admin will verify & credit your wallet
            </p>
        </div>

        @if(session('success'))
        <div class="alert success" style="margin:0 20px">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="alert error" style="margin:0 20px">❌ {{ session('error') }}</div>
        @endif

        <form action="{{ route('user.wallet.deposit') }}" method="POST"
              enctype="multipart/form-data" style="padding:20px">
            @csrf

            <div class="form-group">
                <label>Amount (Rs.) *</label>
                <input type="number" name="amount" class="form-input"
                       placeholder="e.g. 1000" min="100" max="100000"
                       required value="{{ old('amount') }}">
                @error('amount')<span class="error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>Payoneer Transaction ID <span class="hint">(optional)</span></label>
                <input type="text" name="transaction_reference" class="form-input"
                       placeholder="e.g. PAY-XXXXXXXXXX"
                       value="{{ old('transaction_reference') }}">
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
</script>
@endsection