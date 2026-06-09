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

@media (max-width:768px) {
    .bet-cards-grid { grid-template-columns:repeat(2,1fr) !important; }
    .bet-card-top { height:90px !important; }
    .bet-card-top div:first-child { font-size:1.8rem !important; }
    .ticket-grid { max-height:200px; }
}
@media (max-width:480px) {
    .bet-cards-grid { grid-template-columns:repeat(2,1fr) !important; }
}
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
@if($activeAnnouncement && $activeAnnouncement->winners_data && count($activeAnnouncement->winners_data) > 0)
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
<style>
@keyframes slideWinners {
    0%   { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
</style>
@endif

{{-- VIDEO BANNER --}}
@if($activeAnnouncement)
<div style="margin-bottom:20px;border-radius:12px;overflow:hidden;border:2px solid var(--teal);position:relative;background:#000">
    @if($activeAnnouncement->isVideoVisible())
    <video id="dashVideo" controls
        controlsList="nodownload noremoteplayback noplaybackrate"
        disablePictureInPicture oncontextmenu="return false"
        style="width:100%;display:block;height:220px;object-fit:contain;background:#000">
        <source src="{{ asset('storage/' . $activeAnnouncement->video_path) }}" type="video/mp4">
    </video>
    <script>
        const vid = document.getElementById('dashVideo');
        if(vid) { vid.muted=true; vid.play().then(()=>{vid.muted=false;}).catch(()=>{}); }
    </script>
    <div style="position:absolute;top:12px;left:12px;display:flex;align-items:center;gap:6px;background:rgba(0,0,0,0.6);padding:4px 10px;border-radius:10px">
        <span style="width:8px;height:8px;background:red;border-radius:50%;display:inline-block;animation:blink 1s infinite"></span>
        <span style="color:#fff;font-size:11px;font-weight:600">LIVE</span>
    </div>
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
</div>
@endif


{{-- STATS --}}
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

            {{-- Selected Number Display --}}
            <div style="text-align:center;margin-bottom:12px">
                <span style="font-size:11px;color:var(--text2);text-transform:uppercase;letter-spacing:2px">Selected Number</span>
                <div id="selectedNumDisplay"
                     style="font-family:'Rajdhani',sans-serif;font-size:2.5rem;font-weight:700;color:var(--teal);min-height:50px;line-height:1.2">
                    —
                </div>
            </div>

            {{-- Search Box --}}
            <div style="margin-bottom:10px">
                <input type="text" id="ticketSearch"
                       class="form-input"
                       placeholder="🔍 Type to search number..."
                       oninput="filterTickets(this.value)"
                       style="text-align:center;font-family:'Rajdhani',sans-serif;font-size:1rem;font-weight:700">
            </div>

            {{-- Number Grid --}}
            <div id="ticketGrid" class="ticket-grid"></div>

            {{-- Amount --}}
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

            {{-- Summary --}}
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
        ticket.style.cssText = `
            position:relative;
            background:#f5a623;
            border:2px solid #c47d0e;
            border-radius:6px;
            padding:9px 4px;
            text-align:center;
            cursor:pointer;
            font-family:'Rajdhani',sans-serif;
            font-size:13px;
            font-weight:800;
            color:#1a0a00;
            transition:all .15s;
            user-select:none;
            overflow:visible;
        `;
        ticket.innerHTML = `
            <span style="position:absolute;top:50%;left:-5px;transform:translateY(-50%);width:10px;height:10px;background:var(--bg2);border-radius:50%;border:1.5px solid #c47d0e;display:block;z-index:1"></span>
            ${num}
            <span style="position:absolute;top:50%;right:-5px;transform:translateY(-50%);width:10px;height:10px;background:var(--bg2);border-radius:50%;border:1.5px solid #c47d0e;display:block;z-index:1"></span>
        `;
        ticket.onmouseover = () => {
            if (!ticket.classList.contains('active')) {
                ticket.style.background = '#ffb830';
                ticket.style.transform = 'scale(1.07)';
            }
        };
        ticket.onmouseout = () => {
            if (!ticket.classList.contains('active')) {
                ticket.style.background = '#f5a623';
                ticket.style.transform = 'scale(1)';
            }
        };
        ticket.onclick = () => selectTicket(num, ticket);
        grid.appendChild(ticket);
    }
}

function filterTickets(query) {
    const tickets = document.querySelectorAll('#ticketGrid div');
    tickets.forEach(t => {
        const num = t.dataset.num || '';
        t.style.display = (!query || num.includes(query)) ? '' : 'none';
    });
}

function selectTicket(num, el) {
    document.querySelectorAll('#ticketGrid div').forEach(t => {
        t.style.background = '#f5a623';
        t.style.borderColor = '#c47d0e';
        t.style.color = '#1a0a00';
        t.style.transform = 'scale(1)';
        t.classList.remove('active');
    });
    el.style.background = '#00b894';
    el.style.borderColor = '#007a63';
    el.style.color = '#fff';
    el.style.transform = 'scale(1.08)';
    el.classList.add('active');
    selectedNumber = num;
    document.getElementById('selectedNumDisplay').textContent = num;
    calcTicketWin();
}

function closeTicketModal() {
    document.getElementById('ticketModal').style.display = 'none';
}

function setTicketAmount(amt) {
    document.getElementById('ticketAmount').value = amt;
    calcTicketWin();
}

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
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
        },
        body: JSON.stringify({
            bet_number: selectedNumber,
            bet_amount: amount,
            bet_type:   ticketType
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('✅ ' + data.message);
            closeTicketModal();
            location.reload();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(err => {
        alert('❌ Something went wrong!');
        console.error(err);
    });
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
            Bonus per referral: <strong style="color:var(--teal)">Rs. {{ \App\Models\Setting::get('referral_bonus', 100) }}</strong>
        </span>
    </div>
    <p style="color:var(--text2);font-size:13px;margin-bottom:12px">
        Share your referral link — when your friend signs up, you get a bonus in your wallet!
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