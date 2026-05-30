@extends('layouts.user')

@section('title', 'Lotteries')

@section('content')
<div class="page-header">
    <h1>🎰 Available Lotteries</h1>
    <span class="subtitle">Wallet: <strong style="color:var(--gold)">Rs. {{ number_format(auth()->user()->wallet_balance, 0) }}</strong></span>
</div>

@if(session('success'))
<div class="alert success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert error">{{ session('error') }}</div>
@endif

<div class="lottery-grid">
    @forelse($lotteries as $lottery)
    <div class="lottery-card">
        <div class="lottery-img">
            @if($lottery->image)
                <img src="{{ asset('storage/' . $lottery->image) }}" alt="{{ $lottery->name }}">
            @else
                🎰
            @endif
        </div>
        <div class="lottery-body">
            <div class="lottery-name">{{ $lottery->name }}</div>
            <div style="color:var(--text2);font-size:12px;margin-bottom:8px">
                {{ $lottery->description }}
            </div>
            <div style="margin-bottom:8px">
                <span style="color:var(--text2);font-size:11px">🏆 PRIZE POOL</span>
                <div class="lottery-prize">Rs. {{ number_format($lottery->prize_amount, 0) }}</div>
            </div>
            <div class="lottery-meta">
                <span>📅 {{ $lottery->draw_date->format('d M Y') }}</span>
                <span>🎟️ {{ $lottery->availableTickets() }} left</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill"
                     style="width:{{ min(100, ($lottery->sold_tickets / $lottery->total_tickets) * 100) }}%">
                </div>
            </div>
            <div style="font-size:11px;color:var(--text2);margin-bottom:12px">
                {{ $lottery->sold_tickets }}/{{ $lottery->total_tickets }} tickets sold
            </div>
            <div class="lottery-price">
                <div>
                    <span style="color:var(--text2);font-size:11px">Per Ticket</span>
                    <div class="price-tag">Rs. {{ number_format($lottery->price, 0) }}</div>
                </div>
                <a href="{{ route('user.lotteries.show', $lottery) }}" class="btn-primary">
                    Buy Ticket
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="empty-state" style="grid-column:1/-1">
        <span>🎰</span>
        <p>No active lotteries available right now. Check back soon!</p>
    </div>
    @endforelse
</div>

@if($myTickets->isNotEmpty())
<div style="margin-top:40px">
    <h2 style="margin-bottom:20px">🎟️ My Tickets</h2>
    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Ticket #</th>
                        <th>Lottery</th>
                        <th>Paid</th>
                        <th>Draw Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($myTickets as $t)
                    <tr>
                        <td class="mono">{{ $t->ticket_number }}</td>
                        <td>{{ $t->lotteryPackage->name }}</td>
                        <td>Rs. {{ number_format($t->amount_paid, 0) }}</td>
                        <td>{{ $t->lotteryPackage->draw_date->format('d M Y') }}</td>
                        <td>
                            <span class="badge {{ $t->status }}">{{ ucfirst($t->status) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:16px">{{ $myTickets->links() }}</div>
    </div>
</div>
@endif
@endsection