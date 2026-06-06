@extends('layouts.user')

@section('title', 'My Referrals')

@section('content')

<div class="page-header">
    <h1>🔗 My Referrals</h1>
    <span class="subtitle">Track your referral earnings</span>
</div>

{{-- STATS --}}
<div class="stats-grid mini" style="margin-bottom:24px">
    <div class="stat-card primary">
        <div class="stat-icon">👥</div>
        <div class="stat-info">
            <span class="stat-number">{{ $referredUsers->count() }}</span>
            <span class="stat-label">Friends Joined</span>
        </div>
    </div>
    <div class="stat-card success">
        <div class="stat-icon">💰</div>
        <div class="stat-info">
            <span class="stat-number">Rs. {{ number_format($totalEarned, 0) }}</span>
            <span class="stat-label">Total Earned</span>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🎁</div>
        <div class="stat-info">
            <span class="stat-number">Rs. {{ number_format($bonusPerUser, 0) }}</span>
            <span class="stat-label">Bonus Per Referral</span>
        </div>
    </div>
</div>

{{-- REFERRAL LINK --}}
<div style="background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:20px;margin-bottom:24px">
    <h3 style="margin:0 0 12px">📋 Your Referral Link</h3>
    <div style="display:flex;gap:8px;align-items:center">
        <input type="text" id="referralLink" readonly
               value="{{ url('/register?ref=' . $user->referral_code) }}"
               class="form-input mono" style="font-size:12px">
        <button onclick="copyReferral()" class="btn-primary" style="white-space:nowrap;padding:10px 16px">
            📋 Copy
        </button>
    </div>
</div>

{{-- REFERRED USERS TABLE --}}
<div class="card">
    <div class="card-header">
        <h3>👥 Friends Who Joined</h3>
    </div>
    @if($referredUsers->isEmpty())
    <div class="empty-state">
        <span>🔗</span>
        <p>No referrals yet. Share your link and earn bonus!</p>
    </div>
    @else
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Joined</th>
                    <th>Bonus Earned</th>
                </tr>
            </thead>
            <tbody>
                @foreach($referredUsers as $i => $ref)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $ref->name }}</td>
                    <td>{{ $ref->created_at->format('d M Y') }}</td>
                    <td style="color:var(--teal);font-weight:700">
                        +Rs. {{ number_format($bonusPerUser, 0) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

<script>
function copyReferral() {
    const input = document.getElementById('referralLink');
    input.select();
    document.execCommand('copy');
    alert('✅ Referral link copied!');
}
</script>

@endsection