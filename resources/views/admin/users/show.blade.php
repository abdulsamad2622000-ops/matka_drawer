@extends('layouts.admin')

@section('title', $user->name)

@section('content')
<div class="page-header">
    <h1>👤 {{ $user->name }}</h1>
    <span class="subtitle">{{ $user->email }}</span>
</div>

<div class="stats-grid" style="margin-bottom:28px">
    <div class="stat-card revenue">
        <div class="stat-icon">💳</div>
        <div class="stat-info">
            <span class="stat-number">Rs. {{ number_format($user->wallet_balance, 0) }}</span>
            <span class="stat-label">Wallet Balance</span>
        </div>
    </div>
    <div class="stat-card primary">
        <div class="stat-icon">🎟️</div>
        <div class="stat-info">
            <span class="stat-number">{{ $tickets->count() }}</span>
            <span class="stat-label">Total Tickets</span>
        </div>
    </div>
    <div class="stat-card success">
        <div class="stat-icon">🏆</div>
        <div class="stat-info">
            <span class="stat-number">{{ $tickets->where('status','won')->count() }}</span>
            <span class="stat-label">Tickets Won</span>
        </div>
    </div>
</div>

<div class="two-col">
    {{-- Tickets --}}
    <div class="card">
        <div class="card-header"><h3>🎟️ Lottery Tickets</h3></div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Ticket #</th><th>Lottery</th><th>Paid</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse($tickets as $t)
                    <tr>
                        <td class="mono">{{ $t->ticket_number }}</td>
                        <td>{{ $t->lotteryPackage->name }}</td>
                        <td>Rs. {{ number_format($t->amount_paid, 0) }}</td>
                        <td><span class="badge {{ $t->status }}">{{ ucfirst($t->status) }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--text2);padding:20px">No tickets</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Transactions --}}
    <div class="card">
        <div class="card-header"><h3>💸 Wallet Transactions</h3></div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Type</th><th>Amount</th><th>Purpose</th><th>Date</th></tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                    <tr>
                        <td><span class="badge {{ $t->type }}">{{ ucfirst($t->type) }}</span></td>
                        <td class="{{ $t->type === 'credit' ? 'text-green' : 'text-red' }} bold">
                            {{ $t->type === 'credit' ? '+' : '-' }}Rs. {{ number_format($t->amount, 0) }}
                        </td>
                        <td>{{ ucfirst(str_replace('_', ' ', $t->purpose)) }}</td>
                        <td>{{ $t->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--text2);padding:20px">No transactions</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection