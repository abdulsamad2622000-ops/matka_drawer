@extends('layouts.user')

@section('title', $lottery->name)

@section('content')
<div class="page-header">
    <h1>{{ $lottery->name }}</h1>
    <span class="subtitle">Draw Date: {{ $lottery->draw_date->format('d M Y') }}</span>
</div>

@if(session('success'))
<div class="alert success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert error">{{ session('error') }}</div>
@endif

<div class="two-col">
    <div class="card">
        <div class="card-header"><h3>🎰 Lottery Details</h3></div>
        <div style="padding:20px">
            @if($lottery->image)
            <img src="{{ asset('storage/' . $lottery->image) }}"
                 style="width:100%;border-radius:10px;margin-bottom:16px">
            @endif

            <p style="color:var(--text2);margin-bottom:20px">{{ $lottery->description }}</p>

            <div class="detail-row">
                <span class="detail-label">Ticket Price</span>
                <span style="color:var(--teal);font-weight:700;font-size:1.2rem">
                    Rs. {{ number_format($lottery->price, 0) }}
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Prize Pool</span>
                <span style="color:var(--teal);font-weight:700;font-size:1.4rem">
                    Rs. {{ number_format($lottery->prize_amount, 0) }}
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Draw Date</span>
                <span>{{ $lottery->draw_date->format('d M Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Available Tickets</span>
                <span>{{ $lottery->availableTickets() }} / {{ $lottery->total_tickets }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Your Wallet</span>
                <span style="color:var(--teal);font-weight:700">
                    Rs. {{ number_format(auth()->user()->wallet_balance, 0) }}
                </span>
            </div>

            <div class="progress-bar" style="margin-top:16px">
                <div class="progress-fill"
                     style="width:{{ min(100, ($lottery->sold_tickets / $lottery->total_tickets) * 100) }}%">
                </div>
            </div>
            <p style="font-size:11px;color:var(--text2);margin-top:4px">
                {{ $lottery->sold_tickets }}/{{ $lottery->total_tickets }} tickets sold
            </p>
        </div>
    </div>

    <div>
        @if($lottery->status === 'active' && !$lottery->isSoldOut())

        {{-- Buy Now --}}
        <div class="card" style="margin-bottom:16px">
            <div class="card-header"><h3>🎟️ Buy Tickets</h3></div>
            <form action="{{ route('user.lotteries.buy', $lottery) }}"
                  method="POST" style="padding:20px">
                @csrf
                <div class="form-group">
                    <label>Quantity (max 10)</label>
                    <input type="number" name="quantity" class="form-input"
                           value="1" min="1" max="10" id="qty" required>
                </div>
                <div class="prize-summary">
                    <span>Total Cost:</span>
                    <strong id="totalCost" style="color:var(--teal)">
                        Rs. {{ number_format($lottery->price, 0) }}
                    </strong>
                </div>
                <button type="submit" class="btn-primary full-width"
                        onclick="return confirm('Buy ' + document.getElementById('qty').value + ' ticket(s)?')">
                    🎟️ Buy Now
                </button>
            </form>
        </div>

        {{-- Add to Cart --}}
        <div class="card" style="margin-bottom:16px">
            <div class="card-header"><h3>🛒 Add to Cart</h3></div>
            <form action="{{ route('user.cart.add', $lottery) }}"
                  method="POST" style="padding:20px">
                @csrf
                <div class="form-group">
                    <label>Quantity (max 10)</label>
                    <input type="number" name="quantity" class="form-input"
                           value="1" min="1" max="10" id="cartQty">
                </div>
                <div class="prize-summary">
                    <span>Total:</span>
                    <strong id="cartTotal" style="color:var(--teal)">
                        Rs. {{ number_format($lottery->price, 0) }}
                    </strong>
                </div>
                <button type="submit" class="btn-outline full-width">
                    🛒 Add to Cart
                </button>
            </form>
        </div>

        @elseif($lottery->isSoldOut())
        <div class="alert error">🚫 This lottery is sold out!</div>
        @else
        <div class="alert info">ℹ️ This lottery is {{ $lottery->status }}.</div>
        @endif

        @if($userTickets->isNotEmpty())
        <div class="card">
            <div class="card-header"><h3>My Tickets for this Lottery</h3></div>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>Ticket #</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($userTickets as $t)
                        <tr>
                            <td class="mono">{{ $t->ticket_number }}</td>
                            <td><span class="badge {{ $t->status }}">{{ ucfirst($t->status) }}</span></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
const price = {{ $lottery->price }};

const qty = document.getElementById('qty');
const totalCost = document.getElementById('totalCost');
if(qty) {
    qty.addEventListener('input', () => {
        totalCost.textContent = 'Rs. ' + (price * (parseInt(qty.value) || 1)).toLocaleString();
    });
}

const cartQty = document.getElementById('cartQty');
const cartTotal = document.getElementById('cartTotal');
if(cartQty) {
    cartQty.addEventListener('input', () => {
        cartTotal.textContent = 'Rs. ' + (price * (parseInt(cartQty.value) || 1)).toLocaleString();
    });
}
</script>
@endsection