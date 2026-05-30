@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')

@if($activeAnnouncement && $activeAnnouncement->isVideoVisible())
<div id="videoOverlay" class="video-overlay active">
    <div class="video-modal">
        <div class="video-header">
            <span class="video-badge">📢 {{ $activeAnnouncement->title }}</span>
            <div class="video-timer">
                Closing in <span id="videoCountdown">{{ $activeAnnouncement->video_display_seconds }}</span>s
            </div>
        </div>
        <div class="video-container">
            <video id="announceVideo" autoplay controls>
                <source src="{{ asset('storage/' . $activeAnnouncement->video_path) }}"
                        type="video/mp4">
            </video>
        </div>
        <div class="video-footer">
            @if($activeAnnouncement->description)
            <p class="video-lottery">{{ $activeAnnouncement->description }}</p>
            @endif
            <p class="video-info">This announcement will close automatically after the timer ends.</p>
        </div>
    </div>
</div>

<script>
(function() {
    const overlay   = document.getElementById('videoOverlay');
    const countdown = document.getElementById('videoCountdown');
    let seconds     = {{ $activeAnnouncement->video_display_seconds }};

    const timer = setInterval(() => {
        seconds--;
        if (countdown) countdown.textContent = seconds;
        if (seconds <= 0) {
            clearInterval(timer);
            overlay.classList.remove('active');
            overlay.classList.add('hiding');
            setTimeout(() => overlay.remove(), 600);
        }
    }, 1000);
})();
</script>
@endif

<div class="page-header">
    <h1>Welcome, {{ auth()->user()->name }}! 👋</h1>
</div>

<div class="wallet-hero">
    <div>
        <span class="balance-label">💳 Wallet Balance</span>
        <span class="balance-amount">Rs. {{ number_format(auth()->user()->wallet_balance, 2) }}</span>
    </div>
    <div class="wallet-actions">
        <a href="{{ route('user.wallet.index') }}" class="btn-outline">Transaction History</a>
        <a href="{{ route('user.wallet.index') }}#deposit" class="btn-primary">+ Add Funds</a>
    </div>
</div>

<div class="stats-grid mini">
    <div class="stat-card">
        <div class="stat-icon">🎟️</div>
        <div class="stat-info">
            <span class="stat-number">{{ auth()->user()->lotteryTickets()->count() }}</span>
            <span class="stat-label">Total Tickets</span>
        </div>
    </div>
    <div class="stat-card success">
        <div class="stat-icon">🏆</div>
        <div class="stat-info">
            <span class="stat-number">{{ auth()->user()->lotteryTickets()->where('status','won')->count() }}</span>
            <span class="stat-label">Tickets Won</span>
        </div>
    </div>
    <div class="stat-card primary">
        <div class="stat-icon">⚡</div>
        <div class="stat-info">
            <span class="stat-number">{{ auth()->user()->lotteryTickets()->where('status','active')->count() }}</span>
            <span class="stat-label">Active Tickets</span>
        </div>
    </div>
</div>

{{-- Available Lotteries --}}
<div style="margin-top:32px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
        <h2>🎰 Available Lotteries</h2>
        <a href="{{ route('user.lotteries.index') }}" class="btn-sm">View All</a>
    </div>

    @php
        $availableLotteries = \App\Models\LotteryPackage::where('status','active')
            ->where('draw_date', '>=', now())
            ->orderBy('draw_date')
            ->take(4)
            ->get();
    @endphp

    <div class="lottery-grid">
        @forelse($availableLotteries as $lottery)
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
                <div class="lottery-price">
                    <div>
                        <span style="color:var(--text2);font-size:11px">Per Ticket</span>
                        <div class="price-tag">Rs. {{ number_format($lottery->price, 0) }}</div>
                    </div>
                    <a href="{{ route('user.lotteries.show', $lottery) }}" class="btn-primary">
                        Buy Now
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="empty-state" style="grid-column:1/-1">
            <span>🎰</span>
            <p>No active lotteries right now!</p>
        </div>
        @endforelse
    </div>
</div>

<div class="two-col" style="margin-top:32px">
    <div class="card">
        <div class="card-header">
            <h3>🎟️ My Recent Tickets</h3>
            <a href="{{ route('user.lotteries.index') }}" class="btn-sm">View All</a>
        </div>
        @if($myTickets->isEmpty())
        <div class="empty-state">
            <span>🎰</span>
            <p>No tickets yet! <a href="{{ route('user.lotteries.index') }}">Browse lotteries</a></p>
        </div>
        @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Ticket #</th><th>Lottery</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @foreach($myTickets as $ticket)
                    <tr>
                        <td class="mono">{{ $ticket->ticket_number }}</td>
                        <td>{{ $ticket->lotteryPackage->name }}</td>
                        <td>
                            <span class="badge {{ $ticket->status }}">
                                @if($ticket->status === 'won') 🏆 @endif
                                {{ ucfirst($ticket->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    <div class="card">
        <div class="card-header">
            <h3>💸 Recent Transactions</h3>
        </div>
        @if($transactions->isEmpty())
        <div class="empty-state">
            <span>📋</span>
            <p>No transactions yet.</p>
        </div>
        @else
        <div class="table-wrap">
            <table>
                <thead>
                    <tr><th>Type</th><th>Amount</th><th>Purpose</th><th>Date</th></tr>
                </thead>
                <tbody>
                    @foreach($transactions as $t)
                    <tr>
                        <td><span class="badge {{ $t->type }}">{{ ucfirst($t->type) }}</span></td>
                        <td class="{{ $t->type === 'credit' ? 'text-green' : 'text-red' }} bold">
                            {{ $t->type === 'credit' ? '+' : '-' }}Rs. {{ number_format($t->amount, 0) }}
                        </td>
                        <td>{{ ucfirst(str_replace('_', ' ', $t->purpose)) }}</td>
                        <td>{{ $t->created_at->format('d M') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection