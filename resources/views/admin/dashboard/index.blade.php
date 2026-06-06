@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')

<div class="page-header">
    <h1>Dashboard</h1>
    <span class="subtitle">Welcome back, {{ auth()->user()->name }}</span>
</div>

{{-- STATS --}}
<div class="stats-grid">
    <div class="stat-card primary">
        <div class="stat-icon">👥</div>
        <div class="stat-info">
            <span class="stat-number">{{ $stats['total_users'] }}</span>
            <span class="stat-label">Total Users</span>
        </div>
    </div>
    <div class="stat-card warning">
        <div class="stat-icon">⏳</div>
        <div class="stat-info">
            <span class="stat-number">{{ $stats['pending_payments'] }}</span>
            <span class="stat-label">Pending Payments</span>
        </div>
    </div>
    <div class="stat-card success">
        <div class="stat-icon">🎰</div>
        <div class="stat-info">
            <span class="stat-number">{{ $stats['active_lotteries'] }}</span>
            <span class="stat-label">Active Lotteries</span>
        </div>
    </div>
    <div class="stat-card info">
        <div class="stat-icon">🎟️</div>
        <div class="stat-info">
            <span class="stat-number">{{ $stats['total_tickets_sold'] }}</span>
            <span class="stat-label">Tickets Sold</span>
        </div>
    </div>
    <div class="stat-card revenue">
        <div class="stat-icon">💰</div>
        <div class="stat-info">
            <span class="stat-number">Rs. {{ number_format($stats['total_revenue'], 0) }}</span>
            <span class="stat-label">Total Revenue</span>
        </div>
    </div>
</div>

{{-- LIVE BETS MONITOR --}}
<div class="card" style="margin-top:24px">
    <div class="card-header" style="display:flex;align-items:center;justify-content:space-between">
        <div style="display:flex;align-items:center;gap:10px">
            <h3 style="margin:0">🎯 Live Bets Monitor</h3>
            <span style="display:flex;align-items:center;gap:5px;background:rgba(255,59,59,0.15);border:1px solid rgba(255,59,59,0.3);padding:3px 10px;border-radius:20px;font-size:11px;color:#ff3b3b;font-weight:700">
                <span style="width:7px;height:7px;background:#ff3b3b;border-radius:50%;display:inline-block;animation:blink 1s infinite"></span>
                LIVE
            </span>
        </div>
        <button onclick="refreshBets()" class="btn-sm">🔄 Refresh</button>
    </div>

    <div id="betsTableWrap" class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>User</th>
                    <th>Bet Number</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Potential Win</th>
                    <th>Status</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody id="betsTableBody">
                @forelse($recentBets as $i => $bet)
                <tr>
                    <td style="color:var(--text2);font-size:12px">{{ $i + 1 }}</td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px">
                            <div style="width:28px;height:28px;background:var(--teal);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff">
                                {{ substr($bet->user->name, 0, 1) }}
                            </div>
                            <span style="font-weight:600">{{ $bet->user->name }}</span>
                        </div>
                    </td>
                    <td>
                        <span style="font-family:'Rajdhani',sans-serif;font-size:1.8rem;font-weight:700;color:var(--teal)">
                            {{ str_pad($bet->bet_number, 2, '0', STR_PAD_LEFT) }}
                        </span>
                    </td>
                    <td>
                        <span style="background:rgba(0,184,148,0.1);border:1px solid rgba(0,184,148,0.3);padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700;color:var(--teal)">
                            {{ $bet->bet_type }}
                        </span>
                    </td>
                    <td style="font-weight:700">Rs. {{ number_format($bet->bet_amount, 0) }}</td>
                    <td style="color:var(--teal);font-weight:700">Rs. {{ number_format($bet->potential_win, 0) }}</td>
                    <td><span class="badge {{ $bet->status }}">{{ ucfirst($bet->status) }}</span></td>
                    <td style="color:var(--text2);font-size:12px">{{ $bet->created_at->diffForHumans() }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;color:var(--text2);padding:40px">
                        🎯 No bets yet
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- PAYMENTS --}}
<div class="two-col" style="margin-top:24px">
    <div class="card">
        <div class="card-header">
            <h3>Recent Payment Requests</h3>
            <a href="{{ route('admin.payments.index') }}" class="btn-sm">View All</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>User</th><th>Amount</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @forelse($recentPayments as $p)
                    <tr>
                        <td>{{ $p->user->name }}</td>
                        <td>Rs. {{ number_format($p->amount, 0) }}</td>
                        <td><span class="badge {{ $p->status }}">{{ ucfirst($p->status) }}</span></td>
                        <td><a href="{{ route('admin.payments.show', $p) }}" class="btn-xs">Review</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--text2)">No payments yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Recent Ticket Purchases</h3>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>User</th><th>Lottery</th><th>Ticket #</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse($recentTickets as $t)
                    <tr>
                        <td>{{ $t->user->name }}</td>
                        <td>{{ $t->lotteryPackage->name }}</td>
                        <td class="mono">{{ $t->ticket_number }}</td>
                        <td><span class="badge {{ $t->status }}">{{ ucfirst($t->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--text2)">No tickets yet</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
@keyframes blink {
    0%,100% { opacity:1; }
    50%      { opacity:0; }
}
</style>

<script>
function refreshBets() {
    location.reload();
}

// Auto refresh har 30 second mein
setTimeout(() => location.reload(), 30000);
</script>

@endsection