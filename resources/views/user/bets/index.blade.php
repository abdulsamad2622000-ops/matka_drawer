@extends('layouts.user')

@section('title', 'Place Your Bet')

@section('content')

<style>
html, body { overflow-x: hidden; max-width: 100%; }
.main-content { overflow-x: hidden !important; max-width: 100% !important; }
.bet-card { background:var(--bg2);border:1px solid var(--border);border-radius:14px;overflow:hidden;transition:transform .2s,border-color .2s;min-width:0;box-sizing:border-box; }
.bet-card:hover { transform:translateY(-4px);border-color:rgba(0,184,148,.4); }
.bet-card-top { height:120px;display:flex;flex-direction:column;align-items:center;justify-content:center; }
.bet-card-bottom { padding:16px; }
.ticket-modal { display:none;position:fixed;inset:0;background:rgba(0,0,0,0.85);z-index:9998;align-items:center;justify-content:center;overflow-y:auto;padding:16px; }
.ticket-wrap { background:var(--bg2);border:2px solid var(--teal);border-radius:16px;width:100%;max-width:560px;max-height:92vh;overflow-y:auto; }
.ticket-header { background:var(--teal);padding:14px 20px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:10; }
.ticket-header span { font-family:'Rajdhani',sans-serif;font-size:1.2rem;font-weight:700;color:#fff; }
.ticket-body { padding:16px; }
.ticket-grid { display:grid;gap:10px;margin-bottom:12px;max-height:240px;overflow-y:auto;padding:6px; }
.bet-cards-grid { box-sizing:border-box;width:100%;max-width:100%; }

@media (max-width:768px) {
    .bet-cards-grid { grid-template-columns:1fr 1fr !important;gap:10px !important; }
    .bet-card-top { height:90px !important; }
    .bet-card-top div:first-child { font-size:1.5rem !important; }
    .bet-card-bottom { padding:10px !important; }
    .bet-card-bottom button { font-size:11px !important;padding:8px 4px !important; }
    .ticket-grid { max-height:180px; }
}
@media (max-width:480px) {
    .bet-cards-grid { grid-template-columns:1fr 1fr !important;gap:8px !important; }
}
</style>

<div class="page-header">
    <h1>🎯 Place Your Bet</h1>
    <span class="subtitle">Wallet: <strong style="color:var(--teal)">Rs. {{ number_format(auth()->user()->wallet_balance, 0) }}</strong></span>
</div>

{{-- BET TYPE CARDS --}}
<div class="bet-cards-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:32px;width:100%;box-sizing:border-box">
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

{{-- MY BETS --}}
@if($myBets->isNotEmpty())
<h2 style="margin-bottom:16px">🎯 My Bets</h2>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Number</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Potential Win</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($myBets as $bet)
                <tr>
                    <td><span class="mono" style="font-size:1.2rem;font-weight:700">{{ $bet->bet_number }}</span></td>
                    <td><span style="color:var(--teal);font-weight:700">{{ $bet->bet_type }}</span></td>
                    <td>Rs. {{ number_format($bet->bet_amount, 0) }}</td>
                    <td style="color:var(--teal);font-weight:700">Rs. {{ number_format($bet->potential_win, 0) }}</td>
                    <td><span class="badge {{ $bet->status }}">{{ ucfirst($bet->status) }}</span></td>
                    <td>{{ $bet->created_at->format('d M Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div style="padding:16px">{{ $myBets->links() }}</div>
</div>
@endif

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
                <div id="selectedNumDisplay" style="font-family:'Rajdhani',sans-serif;font-size:2.5rem;font-weight:700;color:var(--teal);min-height:50px;line-height:1.2">—</div>
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
                <input type="number" id="ticketAmount" class="form-input" placeholder="Enter amount" min="1" oninput="calcTicketWin()">
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
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                <button type="button" onclick="submitTicketBet()" class="btn-primary full-width" style="font-size:1rem">
                    🎯 Place Bet
                </button>
                <button type="button" onclick="addToCart()" style="background:rgba(0,184,148,0.1);border:2px solid var(--teal);color:var(--teal);border-radius:10px;padding:12px;font-size:1rem;font-weight:700;cursor:pointer;width:100%">
                    🛒 Add to Cart
                </button>
            </div>
        </div>
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
            position:relative;background:#f5a623;border:2px solid #c47d0e;
            border-radius:6px;padding:9px 4px;text-align:center;cursor:pointer;
            font-family:'Rajdhani',sans-serif;font-size:13px;font-weight:800;
            color:#1a0a00;transition:all .15s;user-select:none;overflow:visible;
        `;
        ticket.innerHTML = `
            <span style="position:absolute;top:50%;left:-5px;transform:translateY(-50%);width:10px;height:10px;background:var(--bg2);border-radius:50%;border:1.5px solid #c47d0e;display:block;z-index:1"></span>
            ${num}
            <span style="position:absolute;top:50%;right:-5px;transform:translateY(-50%);width:10px;height:10px;background:var(--bg2);border-radius:50%;border:1.5px solid #c47d0e;display:block;z-index:1"></span>
        `;
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

document.getElementById('ticketModal').addEventListener('click', function(e) {
    if (e.target === this) closeTicketModal();
});

function addToCart() {
    const amount = document.getElementById('ticketAmount').value;
    if (selectedNumber === null) { alert('Please select a number!'); return; }
    if (!amount || amount < 1)   { alert('Please enter a valid amount!'); return; }

    fetch('{{ route("user.cart.add-bet") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
        body: JSON.stringify({ bet_number: selectedNumber, bet_amount: amount, bet_type: ticketType })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('✅ Added to cart! Cart has ' + data.cart_count + ' bet(s).');
            closeTicketModal();
            updateCartCount();
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(() => alert('❌ Something went wrong!'));
}
</script>

@endsection