@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="page-header">
    <h1>⚙️ Settings</h1>
    <span class="subtitle">Manage application settings</span>
</div>

@if(session('success'))
<div class="alert success" style="background:var(--green-light,#d1fae5);color:#065f46;padding:12px 16px;border-radius:8px;margin-bottom:20px">
    {{ session('success') }}
</div>
@endif

<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    @method('PUT')

    {{-- PAYMENT DETAILS --}}
    <div class="card" style="margin-bottom:20px">
        <div class="card-header">
            <h3>💳 Payment Account Details</h3>
        </div>
        <div style="padding:20px">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div>
                    <label style="display:block;margin-bottom:6px;font-size:13px;color:var(--text2)">Account Title</label>
                    <input type="text" name="payment_account_title"
                           value="{{ old('payment_account_title', $settings['payment_account_title']) }}"
                           class="form-input" placeholder="e.g. Muhammad Ali">
                    @error('payment_account_title')
                        <span style="color:red;font-size:12px">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label style="display:block;margin-bottom:6px;font-size:13px;color:var(--text2)">Account Number / Phone</label>
                    <input type="text" name="payment_account_number"
                           value="{{ old('payment_account_number', $settings['payment_account_number']) }}"
                           class="form-input" placeholder="e.g. 03001234567">
                    @error('payment_account_number')
                        <span style="color:red;font-size:12px">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label style="display:block;margin-bottom:6px;font-size:13px;color:var(--text2)">Account Type</label>
                  <input type="text" name="payment_account_type"
       value="{{ old('payment_account_type', $settings['payment_account_type']) }}"
       class="form-input" placeholder="e.g. JazzCash, EasyPaisa, HBL">
                </div>
                <div>
                    <label style="display:block;margin-bottom:6px;font-size:13px;color:var(--text2)">Payment Instructions (optional)</label>
                    <input type="text" name="payment_instructions"
                           value="{{ old('payment_instructions', $settings['payment_instructions']) }}"
                           class="form-input" placeholder="e.g. Send payment and upload screenshot">
                </div>
            </div>
        </div>
    </div>

    {{-- REFERRAL SETTINGS --}}
    <div class="card" style="margin-bottom:20px">
        <div class="card-header">
            <h3>🔗 Referral Settings</h3>
        </div>
        <div style="padding:20px">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div>
                    <label style="display:block;margin-bottom:6px;font-size:13px;color:var(--text2)">
                        Referral Bonus Amount (Rs.)
                    </label>
                    <input type="number" name="referral_bonus"
                           value="{{ old('referral_bonus', $settings['referral_bonus']) }}"
                           class="form-input" min="0" step="1">
                    @error('referral_bonus')
                        <span style="color:red;font-size:12px">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- WALLET SETTINGS --}}
    <div class="card" style="margin-bottom:20px">
        <div class="card-header">
            <h3>💰 Wallet Settings</h3>
        </div>
        <div style="padding:20px">
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
                <div>
                    <label style="display:block;margin-bottom:6px;font-size:13px;color:var(--text2)">Min Deposit (Rs.)</label>
                    <input type="number" name="min_deposit"
                           value="{{ old('min_deposit', $settings['min_deposit']) }}"
                           class="form-input" min="0">
                    @error('min_deposit')
                        <span style="color:red;font-size:12px">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label style="display:block;margin-bottom:6px;font-size:13px;color:var(--text2)">Min Withdrawal (Rs.)</label>
                    <input type="number" name="min_withdrawal"
                           value="{{ old('min_withdrawal', $settings['min_withdrawal']) }}"
                           class="form-input" min="0">
                    @error('min_withdrawal')
                        <span style="color:red;font-size:12px">{{ $message }}</span>
                    @enderror
                </div>
                <div>
                    <label style="display:block;margin-bottom:6px;font-size:13px;color:var(--text2)">Max Withdrawal (Rs.)</label>
                    <input type="number" name="max_withdrawal"
                           value="{{ old('max_withdrawal', $settings['max_withdrawal']) }}"
                           class="form-input" min="0">
                    @error('max_withdrawal')
                        <span style="color:red;font-size:12px">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    {{-- GENERAL SETTINGS --}}
    <div class="card" style="margin-bottom:20px">
        <div class="card-header">
            <h3>🌐 General Settings</h3>
        </div>
        <div style="padding:20px">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div>
                    <label style="display:block;margin-bottom:6px;font-size:13px;color:var(--text2)">Site Name</label>
                    <input type="text" name="site_name"
                           value="{{ old('site_name', $settings['site_name']) }}"
                           class="form-input">
                    @error('site_name')
                        <span style="color:red;font-size:12px">{{ $message }}</span>
                    @enderror
                </div>
                <div style="display:flex;align-items:center;gap:10px;margin-top:24px">
                    <input type="checkbox" name="maintenance_mode" id="maintenance_mode"
                           value="1" {{ $settings['maintenance_mode'] ? 'checked' : '' }}
                           style="width:18px;height:18px">
                    <label for="maintenance_mode" style="font-size:14px">
                        🔧 Maintenance Mode
                    </label>
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn-primary" style="padding:12px 32px;font-size:15px">
        💾 Save Settings
    </button>
</form>

</form>

{{-- CHANGE PASSWORD --}}
<div class="card" style="margin-top:24px">
    <div class="card-header">
        <h3>🔐 Change Password</h3>
    </div>
    <div style="padding:20px">
        @if($errors->has('current_password'))
        <div class="alert error" style="margin-bottom:16px">❌ {{ $errors->first('current_password') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.settings.change-password') }}">
            @csrf
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px">
                <div>
                    <label style="display:block;margin-bottom:6px;font-size:13px;color:var(--text2)">
                        Current Password
                    </label>
                    <input type="password" name="current_password"
                           class="form-input" placeholder="Enter current password">
                </div>
                <div>
                    <label style="display:block;margin-bottom:6px;font-size:13px;color:var(--text2)">
                        New Password
                    </label>
                    <input type="password" name="new_password"
                           class="form-input" placeholder="Min 6 characters">
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
@endsection