@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')

<style>
@keyframes blink {
    0%,100% { opacity:1; }
    50%      { opacity:0; }
}
.bet-card { background:var(--bg2);border:1px solid var(--border);border-radius:14px;overflow:hidden;transition:transform .2s,border-color .2s; }
.bet-card:hover { transform:translateY(-4px);border-color:rgba(0,184,148,.4); }
.bet-card-top { height:120px;display:flex;flex-direction:column;align-items:center;justify-content:center; }
.bet-card-bottom { padding:16px; }
.ticket-modal { display:none;position:fixed;inset:0;background:rgba(0,0,0,0.85);z-index:9998;align-items:center;justify-content:center;overflow-y:auto;padding:16px; }
.ticket-wrap { background:var(--bg2);border:2px solid var(--teal);border-radius:16px;width:100%;max-width:560px;max-height:92vh;overflow-y:auto; }
.ticket-header { background:var(--teal);padding:14px 20px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:10; }
.ticket-header span { font-family:'Rajdhani',sans-serif;font-size:1.2rem;font-weight:700;color:#fff; }
.ticket-body { padding:16px; }
.ticket-grid { display:grid;gap:10px;margin-bottom:12px;max-height:240px;overflow-y:auto;padding:6px; }

@keyframes brandingPulse {
    0%,100% { opacity:1; transform:scale(1); }
    50%      { opacity:0.7; transform:scale(1.03); }
}
@keyframes brandingGlow {
    0%,100% { text-shadow: 0 0 20px rgba(0,184,148,0.5), 0 0 40px rgba(0,184,148,0.3); }
    50%      { text-shadow: 0 0 40px rgba(0,184,148,0.9), 0 0 80px rgba(0,184,148,0.6); }
}
@keyframes tickerScroll {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
@keyframes floatDot {
    0%,100% { transform: translateY(0px); }
    50%      { transform: translateY(-8px); }
}
@keyframes slideWinners {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}

@media (max-width:768px) {
    .bet-cards-grid { grid-template-columns:repeat(2,1fr) !important; }
    .bet-card-top { height:90px !important; }
    .bet-card-top div:first-child { font-size:1.8rem !important; }
    .ticket-grid { max-height:200px; }
}
@media (max-width:480px) {
    .bet-cards-grid { grid-template-columns:repeat(2,1fr) !important; }
}

/* Video controls completely hidden */
video::-webkit-media-controls { display:none !important; }
video::-webkit-media-controls-enclosure { display:none !important; }
video::-webkit-media-controls-panel { display:none !important; }
video::--moz-media-controls { display:none !important; }
</style>

{{-- PAGE HEADER --}}
<div class="page-header" style="display:flex;align-items:center;justify-content:space-between">
    <h1>Welcome, {{ auth()->user()->name }}! 👋</h1>
    <div style="display:flex;align-items:center;gap:16px;font-family:'Rajdhani',sans-serif;font-size:1.1rem;font-weight:700">
        <span>A = 1</span>
        <span style="color:var(--text2)">&</span>
        <span>J = 0</span>
    </div>
</div>

{{-- WINNERS SLIDE --}}
@if($activeAnnouncement && $activeAnnouncement->show_winners_slide && $activeAnnouncement->winners_data && count($activeAnnouncement->winners_data) > 0)
<div style="margin-bottom:12px;background:var(--bg2);border:1px solid var(--border);border-radius:10px;padding:10px 14px;overflow:hidden">
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:8px">
        <span>🏆</span>
        <span style="font-weight:700;font-size:13px">Lucky Winners</span>
        <span style="background:rgba(255,193,7,0.15);color:#f59e0b;padding:1px 8px;border-radius:20px;font-size:10px;font-weight:700">LATEST DRAW</span>
    </div>
    <div style="overflow:hidden">
        <div id="winnersSlide" style="display:flex;gap:10px;animation:slideWinners 20s linear infinite;width:max-content">
            @foreach(array_merge($activeAnnouncement->winners_data, $activeAnnouncement->winners_data) as $winner)
            <div style="background:rgba(255,193,7,0.08);border:1px solid rgba(255,193,7,0.25);border-radius:8px;padding:6px 12px;min-width:160px;flex-shrink:0;display:flex;align-items:center;gap:8px">
                <span style="width:24px;height:24px;background:linear-gradient(135deg,#f59e0b,#d97706);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;color:#fff;font-weight:700;flex-shrink:0">
                    {{ substr($winner['name'], 0, 1) }}
                </span>
                <div>
                    <div style="font-weight:700;font-size:12px;display:flex;align-items:center;gap:4px">
                        {{ $winner['name'] }}
                        @if($winner['is_real'])
                        <span style="font-size:8px;background:var(--teal);color:#fff;padding:1px 4px;border-radius:6px">✓</span>
                        @endif
                    </div>
                    <div style="color:#f59e0b;font-family:'Rajdhani',sans-serif;font-size:13px;font-weight:700">
                        Rs. {{ number_format($winner['amount'], 0) }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- BRANDING BANNER --}}
<div id="brandingBanner" style="display:none;margin-bottom:20px;border-radius:12px;overflow:hidden;border:2px solid var(--teal);position:relative;background:linear-gradient(135deg,#0a1628 0%,#0d2137 40%,#0a2020 100%);height:400px;flex-direction:column;align-items:center;justify-content:center">
    <div style="position:absolute;inset:0;overflow:hidden;pointer-events:none">
        <div style="position:absolute;width:6px;height:6px;background:var(--teal);border-radius:50%;top:20%;left:10%;opacity:0.4;animation:floatDot 3s ease-in-out infinite"></div>
        <div style="position:absolute;width:4px;height:4px;background:var(--teal);border-radius:50%;top:60%;left:20%;opacity:0.3;animation:floatDot 4s ease-in-out infinite 1s"></div>
        <div style="position:absolute;width:8px;height:8px;background:var(--teal);border-radius:50%;top:30%;right:15%;opacity:0.3;animation:floatDot 3.5s ease-in-out infinite 0.5s"></div>
        <div style="position:absolute;width:5px;height:5px;background:#f59e0b;border-radius:50%;top:70%;right:25%;opacity:0.4;animation:floatDot 4.5s ease-in-out infinite 1.5s"></div>
        <div style="position:absolute;width:6px;height:6px;background:#f59e0b;border-radius:50%;bottom:25%;left:40%;opacity:0.3;animation:floatDot 3.8s ease-in-out infinite 0.8s"></div>
    </div>
    <div style="text-align:center;z-index:1;animation:brandingPulse 3s ease-in-out infinite">
        <div style="font-size:2.2rem;margin-bottom:4px">🎯</div>
        <div style="font-family:'Rajdhani',sans-serif;font-size:2.4rem;font-weight:700;color:var(--teal);letter-spacing:4px;animation:brandingGlow 2.5s ease-in-out infinite">
            MATKA CHAMPION
        </div>
        <div style="color:#aaa;font-size:12px;letter-spacing:6px;text-transform:uppercase;margin-top:4px">
            Pakistan's #1 Lottery Platform
        </div>
    </div>
    <div style="position:absolute;bottom:0;left:0;right:0;background:rgba(0,184,148,0.15);border-top:1px solid rgba(0,184,148,0.3);padding:6px 0;overflow:hidden">
        <div style="display:flex;white-space:nowrap;animation:tickerScroll 18s linear infinite;width:max-content">
            <span style="color:var(--teal);font-size:12px;font-weight:600;padding:0 40px">🎯 Place your bets now!</span>
            <span style="color:#f59e0b;font-size:12px;font-weight:600;padding:0 40px">🏆 Win big with 1x7000!</span>
            <span style="color:var(--teal);font-size:12px;font-weight:600;padding:0 40px">💰 Fast withdrawals guaranteed!</span>
            <span style="color:#f59e0b;font-size:12px;font-weight:600;padding:0 40px">🎯 Place your bets now!</span>
            <span style="color:var(--teal);font-size:12px;font-weight:600;padding:0 40px">🏆 Win big with 1x7000!</span>
            <span style="color:#f59e0b;font-size:12px;font-weight:600;padding:0 40px">💰 Fast withdrawals guaranteed!</span>
        </div>
    </div>
</div>

{{-- VIDEO BANNER --}}
@if($activeAnnouncement)
<div style="margin-bottom:20px;border-radius:12px;overflow:hidden;border:2px solid var(--teal);position:relative;background:#000;" id="videoBannerWrap">
    @if($activeAnnouncement->video_path && $activeAnnouncement->is_active)
    @php $playCount = $activeAnnouncement->video_play_count ?? 1; @endphp
    <video id="dashVideo"
        disablePictureInPicture
        oncontextmenu="return false"
        style="width:100%;display:block;height:400px;object-fit:contain;background:#000;">
        <source src="{{ asset('storage/' . $activeAnnouncement->video_path) }}" type="video/mp4">
    </video>
    {{-- Play/Pause Button - inside video top-left --}}
    <div style="position:absolute;top:12px;left:12px;z-index:10;display:flex;align-items:center;gap:8px">
        <button id="playPauseBtn" onclick="toggleVideoPlay()"
            style="background:rgba(0,0,0,0.65);border:none;color:#fff;border-radius:6px;width:38px;height:38px;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;">
            ⏸
        </button>
    </div>
    <script>
        (function() {
            const vid = document.getElementById('dashVideo');
            const totalPlays = {{ $playCount }};
            let playedCount = 0;
            if (vid) {
                vid.muted = true;
                vid.play().then(() => { vid.muted = false; }).catch(() => {});
                vid.addEventListener('ended', function() {
                    playedCount++;
                    if (playedCount < totalPlays) {
                        vid.currentTime = 0;
                        vid.play();
                    } else {
                        document.getElementById('videoBannerWrap').style.display = 'none';
                        document.getElementById('brandingBanner').style.display = 'flex';
                    }
                });
            }
        })();

        function toggleVideoPlay() {
            const vid = document.getElementById('dashVideo');
            const btn = document.getElementById('playPauseBtn');
            const txt = document.getElementById('videoStatusText');
            if (vid.paused) {
                vid.play();
                btn.textContent = '⏸';
                if(txt) txt.textContent = 'Playing';
            } else {
                vid.pause();
                btn.textContent = '▶';
                if(txt) txt.textContent = 'Paused';
            }
        }
    </script>
    @elseif($activeAnnouncement->winning_number)
    <div style="width:100%;min-height:220px;background:#000;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:8px;padding:30px 20px">
        <div style="color:#aaa;font-size:13px;letter-spacing:3px;text-transform:uppercase;font-weight:600">🏆 Winning Number</div>
        <div style="font-family:'Rajdhani',sans-serif;font-size:3.5rem;font-weight:700;color:var(--teal);letter-spacing:6px;text-align:center">
            {{ $activeAnnouncement->winning_number }}
        </div>
        <div style="color:#666;font-size:11px">Announced: {{ $activeAnnouncement->created_at->format('d M Y H:i') }}</div>
        @if($activeAnnouncement->next_draw_at)
        <div style="margin-top:16px;background:rgba(0,184,148,0.1);border:1px solid rgba(0,184,148,0.3);border-radius:10px;padding:12px 24px;text-align:center">
            <div style="color:#aaa;font-size:11px;letter-spacing:2px;text-transform:uppercase;margin-bottom:4px">🗓 Next Draw</div>
            <div style="font-family:'Rajdhani',sans-serif;font-size:1.4rem;font-weight:700;color:var(--teal)">{{ $activeAnnouncement->next_draw_at->format('d M Y') }}</div>
            <div style="font-family:'Rajdhani',sans-serif;font-size:1.1rem;color:#aaa">{{ $activeAnnouncement->next_draw_at->format('h:i A') }}</div>
        </div>
        @endif
    </div>
    @endif
    <div style="background:var(--bg2);padding:10px 16px;display:flex;align-items:center;border-top:1px solid var(--border)">
        <span style="font-family:'Rajdhani',sans-serif;font-weight:700;font-size:1rem">📢 {{ $activeAnnouncement->title }}</span>
    </div>
    @if($activeAnnouncement->extra_message)
    <div style="padding:10px 16px;background:rgba(0,184,148,0.08);border-top:1px solid var(--border)">
        <span style="font-size:13px;color:var(--teal)">💬 {{ $activeAnnouncement->extra_message }}</span>
    </div>
    @endif
</div>
@else
<script>
    document.getElementById('brandingBanner').style.display = 'flex';
</script>
@endif

{{-- STATS --}}
<div class="stats-grid mini">
    <a href="{{ route('user.bets.index') }}" style="text-decoration:none">
        <div class="stat-card" style="cursor:pointer">
            <div class="stat-icon">🎯</div>
            <div class="stat-info">
                <span class="stat-number">{{ auth()->user()->bets()->count() }}</span>
                <span class="stat-label">Total Bets</span>
            </div>
        </div>
    </a>
    <a href="{{ route('user.bets.index') }}" style="text-decoration:none">
        <div class="stat-card success" style="cursor:pointer">
            <div class="stat-icon">🏆</div>
            <div class="stat-info">
                <span class="stat-number">{{ auth()->user()->bets()->where('status','won')->count() }}</span>
                <span class="stat-label">Bets Won</span>
            </div>
        </div>
    </a>
    <a href="{{ route('user.wallet.index') }}" style="text-decoration:none">
        <div class="stat-card primary" style="cursor:pointer">
            <div class="stat-icon">💳</div>
            <div class="stat-info">
                <span class="stat-number">Rs. {{ number_format(auth()->user()->wallet_balance, 0) }}</span>
                <span class="stat-label">Wallet Balance</span>
            </div>
        </div>
    </a>
</div>

{{-- PLACE BET SECTION --}}
<div style="margin-top:24px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px">
        <h2>🎯 Place Your Bet</h2>
        <a href="{{ route('user.bets.index') }}" class="btn-sm">View All Bets</a>
    </div>
    <div class="bet-cards-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px">
        @foreach(['1x7' => [7,10], '1x70' => [70,100], '1x700' => [700,1000], '1x7000' => [7000,10000]] as $type => $config)
        @php [$multiplier, $total] = $config; @endphp
        <div class="bet-card">
            <div class="bet-card-top" style="background:
                @if($type === '1x7') linear-gradient(135deg,#0d2137,#1a3a52)
                @elseif($type === '1x70') linear-gradient(135deg,#1a2a0d,#2a4a1a)
                @elseif($type === '1x700') linear-gradient(135deg,#1a1a0d,#3a3a1a)
                @else linear-gradient(135deg,#2a1a0d,#4a2a1a)
                @endif">
                <div style="font-family:'Rajdhani',sans-serif;font-size:2.5rem;font-weight:700;color:var(--teal)">{{ $type }}</div>
                <div style="color:#aaa;font-size:12px;letter-spacing:3px;margin-top:4px">TICKETS</div>
            </div>
            <div class="bet-card-bottom">
                <button class="btn-primary full-width"
                    onclick="openTicketModal('{{ $type }}', {{ $multiplier }}, {{ $total }})">
                    🎫 SELECT NUMBER
                </button>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- TICKET MODAL --}}
<div class="ticket-modal" id="ticketModal">
    <div class="ticket-wrap">
        <div class="ticket-header">
            <span>🎫 Select Number — <span id="ticketType"></span></span>
            <button onclick="closeTicketModal()" style="background:rgba(0,0,0,0.2);border:none;color:#fff;font-size:18px;cursor:pointer;border-radius:6px;padding:2px 8px">✕</button>
        </div>
        <div class="ticket-body">
            <div style="text-align:center;margin-bottom:12px">
                <span style="font-size:11px;color:var(--text2);text-transform:uppercase;letter-spacing:2px">Selected Number</span>
                <div id="selectedNumDisplay"
                     style="font-family:'Rajdhani',sans-serif;font-size:2.5rem;font-weight:700;color:var(--teal);min-height:50px;line-height:1.2">
                    —
                </div>
            </div>
            <div style="margin-bottom:10px">
                <input type="text" id="ticketSearch" class="form-input"
                       placeholder="🔍 Type to search number..."
                       oninput="filterTickets(this.value)"
                       style="text-align:center;font-family:'Rajdhani',sans-serif;font-size:1rem;font-weight:700">
            </div>
            <div id="ticketGrid" class="ticket-grid"></div>
            <div class="form-group" style="margin-bottom:10px;margin-top:12px">
                <label>Bet Amount (Rs.) *</label>
                <input type="number" id="ticketAmount" class="form-input"
                       placeholder="Enter amount" min="1" oninput="calcTicketWin()">
            </div>
            <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:12px">
                @foreach([1000,2000,3000,4000,5000,6000] as $amt)
                <button type="button" onclick="setTicketAmount({{ $amt }})"
                    style="background:var(--bg3);border:1px solid var(--border);border-radius:8px;padding:8px;color:var(--text);font-weight:600;cursor:pointer;font-size:12px">
                    Rs. {{ number_format($amt,0) }}
                </button>
                @endforeach
            </div>
            <div style="background:rgba(0,184,148,0.05);border:1px solid rgba(0,184,148,.3);border-radius:10px;padding:12px;margin-bottom:14px">
                <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                    <span style="color:var(--text2);font-size:13px">Bet Amount:</span>
                    <span id="tDisplayAmount">Rs. 0</span>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                    <span style="color:var(--text2);font-size:13px">Multiplier:</span>
                    <span id="tDisplayMultiplier" style="color:var(--teal);font-weight:700">x0</span>
                </div>
                <div style="display:flex;justify-content:space-between;border-top:1px solid rgba(0,184,148,.2);padding-top:8px">
                    <span style="font-weight:700">Potential Win:</span>
                    <span id="tDisplayWin" style="color:var(--teal);font-family:'Rajdhani',sans-serif;font-size:1.2rem;font-weight:700">Rs. 0</span>
                </div>
            </div>
            <button type="button" onclick="submitTicketBet()" class="btn-primary full-width" style="font-size:1rem">
                🎯 Place Bet
            </button>
        </div>
    </div>
</div>

{{-- RECENT --}}
<div class="two-col" style="margin-top:24px">
    <div class="card">
        <div class="card-header">
            <h3>🎯 My Recent Bets</h3>
            <a href="{{ route('user.bets.index') }}" class="btn-sm">View All</a>
        </div>
        @if($myBets->isEmpty())
        <div class="empty-state"><span>🎯</span><p>No bets yet!</p></div>
        @else
        <div class="table-wrap">
            <table>
                <thead><tr><th>Number</th><th>Type</th><th>Amount</th><th>Win</th><th>Status</th></tr></thead>
                <tbody>
                    @foreach($myBets as $bet)
                    <tr>
                        <td class="mono" style="font-size:1.2rem;font-weight:700">{{ $bet->bet_number }}</td>
                        <td style="color:var(--teal);font-weight:700">{{ $bet->bet_type }}</td>
                        <td>Rs. {{ number_format($bet->bet_amount, 0) }}</td>
                        <td style="color:var(--teal)">Rs. {{ number_format($bet->potential_win, 0) }}</td>
                        <td><span class="badge {{ $bet->status }}">{{ ucfirst($bet->status) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
    <div class="card">
        <div class="card-header"><h3>💸 Recent Transactions</h3></div>
        @if($transactions->isEmpty())
        <div class="empty-state"><span>📋</span><p>No transactions yet.</p></div>
        @else
        <div class="table-wrap">
            <table>
                <thead><tr><th>Type</th><th>Amount</th><th>Purpose</th><th>Date</th></tr></thead>
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

{{-- WITHDRAWALS --}}
@if(isset($withdrawals) && $withdrawals->isNotEmpty())
<div class="card" style="margin-top:24px">
    <div class="card-header">
        <h3>🏧 Recent Withdrawals</h3>
        <a href="{{ route('user.withdrawal.index') }}" class="btn-sm">View All</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
                @foreach($withdrawals as $w)
                <tr>
                    <td class="bold">Rs. {{ number_format($w->amount, 0) }}</td>
                    <td>{{ strtoupper($w->method ?? 'BANK') }}</td>
                    <td>
                        @if($w->status === 'approved')
                        <span class="badge won">Approved ✓</span>
                        @elseif($w->status === 'rejected')
                        <span class="badge lost">Rejected ✗</span>
                        @else
                        <span class="badge pending">Pending ⏳</span>
                        @endif
                    </td>
                    <td>{{ $w->created_at->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

<script>
let ticketMultiplier = 7;
let ticketType = '';
let selectedNumber = null;

function openTicketModal(type, multiplier, total) {
    ticketMultiplier = multiplier;
    ticketType = type;
    selectedNumber = null;
    document.getElementById('ticketModal').style.display = 'flex';
    document.getElementById('ticketType').textContent = type;
    document.getElementById('tDisplayMultiplier').textContent = 'x' + multiplier;
    document.getElementById('selectedNumDisplay').textContent = '—';
    document.getElementById('ticketAmount').value = '';
    document.getElementById('tDisplayAmount').textContent = 'Rs. 0';
    document.getElementById('tDisplayWin').textContent = 'Rs. 0';
    document.getElementById('ticketSearch').value = '';
    const grid = document.getElementById('ticketGrid');
    grid.innerHTML = '';
    let cols, padLen;
    if (type === '1x7')         { cols = 5;  padLen = 1; }
    else if (type === '1x70')   { cols = 10; padLen = 2; }
    else if (type === '1x700')  { cols = 10; padLen = 3; }
    else                         { cols = 10; padLen = 4; }
    grid.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;
    for (let i = 0; i < total; i++) {
        const num = String(i).padStart(padLen, '0');
        const ticket = document.createElement('div');
        ticket.dataset.num = num;
        ticket.style.cssText = `position:relative;background:#f5a623;border:2px solid #c47d0e;border-radius:6px;padding:9px 4px;text-align:center;cursor:pointer;font-family:'Rajdhani',sans-serif;font-size:13px;font-weight:800;color:#1a0a00;transition:all .15s;user-select:none;overflow:visible;`;
        ticket.innerHTML = `<span style="position:absolute;top:50%;left:-5px;transform:translateY(-50%);width:10px;height:10px;background:var(--bg2);border-radius:50%;border:1.5px solid #c47d0e;display:block;z-index:1"></span>${num}<span style="position:absolute;top:50%;right:-5px;transform:translateY(-50%);width:10px;height:10px;background:var(--bg2);border-radius:50%;border:1.5px solid #c47d0e;display:block;z-index:1"></span>`;
        ticket.onmouseover = () => { if(!ticket.classList.contains('active')){ ticket.style.background='#ffb830'; ticket.style.transform='scale(1.07)'; } };
        ticket.onmouseout  = () => { if(!ticket.classList.contains('active')){ ticket.style.background='#f5a623'; ticket.style.transform='scale(1)'; } };
        ticket.onclick = () => selectTicket(num, ticket);
        grid.appendChild(ticket);
    }
}
function filterTickets(query) {
    document.querySelectorAll('#ticketGrid div').forEach(t => {
        t.style.display = (!query || t.dataset.num.includes(query)) ? '' : 'none';
    });
}
function selectTicket(num, el) {
    document.querySelectorAll('#ticketGrid div').forEach(t => {
        t.style.background='#f5a623'; t.style.borderColor='#c47d0e';
        t.style.color='#1a0a00'; t.style.transform='scale(1)';
        t.classList.remove('active');
    });
    el.style.background='#00b894'; el.style.borderColor='#007a63';
    el.style.color='#fff'; el.style.transform='scale(1.08)';
    el.classList.add('active');
    selectedNumber = num;
    document.getElementById('selectedNumDisplay').textContent = num;
    calcTicketWin();
}
function closeTicketModal() { document.getElementById('ticketModal').style.display = 'none'; }
function setTicketAmount(amt) { document.getElementById('ticketAmount').value = amt; calcTicketWin(); }
function calcTicketWin() {
    const amt = parseFloat(document.getElementById('ticketAmount').value) || 0;
    const win = amt * ticketMultiplier;
    document.getElementById('tDisplayAmount').textContent = 'Rs. ' + amt.toLocaleString();
    document.getElementById('tDisplayWin').textContent = 'Rs. ' + win.toLocaleString();
}
function submitTicketBet() {
    const amount = document.getElementById('ticketAmount').value;
    if (selectedNumber === null) { alert('Please select a number!'); return; }
    if (!amount || amount < 1)   { alert('Please enter a valid amount!'); return; }
    fetch('{{ route("user.bets.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
        body: JSON.stringify({ bet_number: selectedNumber, bet_amount: amount, bet_type: ticketType })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) { alert('✅ ' + data.message); closeTicketModal(); location.reload(); }
        else { alert('❌ ' + data.message); }
    })
    .catch(() => alert('❌ Something went wrong!'));
}
function copyReferral() {
    const input = document.getElementById('referralLink');
    input.select();
    document.execCommand('copy');
    alert('✅ Referral link copied!');
}
document.getElementById('ticketModal').addEventListener('click', function(e) {
    if (e.target === this) closeTicketModal();
});
</script>

{{-- REFERRAL SECTION --}}
<div style="margin-top:24px;background:var(--bg2);border:1px solid var(--border);border-radius:12px;padding:20px">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
        <h3 style="margin:0">🔗 Invite Friends & Earn Bonus</h3>
        <span style="font-size:12px;color:var(--text2)">
            Bonus per referral: <strong style="color:var(--teal)">10% of each deposit</strong>
        </span>
    </div>
    <p style="color:var(--text2);font-size:13px;margin-bottom:12px">
        Share your referral link — jab friend deposit kare, aapko us deposit ka 10% milega!
    </p>
    <div style="display:flex;gap:8px;align-items:center">
        <input type="text" id="referralLink" readonly
               value="{{ url('/register?ref=' . auth()->user()->referral_code) }}"
               class="form-input mono" style="font-size:12px">
        <button onclick="copyReferral()" class="btn-primary" style="white-space:nowrap;padding:10px 16px">
            📋 Copy
        </button>
    </div>
    @if(auth()->user()->referredUsers()->count() > 0)
    <p style="margin-top:10px;font-size:12px;color:var(--teal)">
        ✅ {{ auth()->user()->referredUsers()->count() }} friend(s) joined using your link!
    </p>
    @endif
</div>
@endsection