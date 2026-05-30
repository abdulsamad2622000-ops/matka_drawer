@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="page-header">
    <h1>Dashboard</h1>
    <span class="subtitle">Welcome back, {{ auth()->user()->name }}</span>
</div>

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

<div class="two-col">
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
@endsection
