@extends('layouts.user')

@section('title', 'My Cart')

@section('content')
<div class="page-header">
    <h1>🛒 My Cart</h1>
    <span class="subtitle">Review your selected lotteries before checkout</span>
</div>

@if(session('success'))
<div class="alert success">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert error">{{ session('error') }}</div>
@endif

@if($cartItems->isEmpty())
<div class="empty-state" style="margin-top:60px">
    <span>🛒</span>
    <p>Your cart is empty!</p>
    <a href="{{ route('user.lotteries.index') }}" class="btn-primary" style="margin-top:16px;display:inline-block">
        Browse Lotteries
    </a>
</div>
@else
<div class="two-col">
    <div class="card">
        <div class="card-header"><h3>Cart Items</h3></div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Lottery</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cartItems as $item)
                    <tr>
                        <td>
                            <div style="font-weight:600">{{ $item->lotteryPackage->name }}</div>
                            <div style="font-size:11px;color:var(--text2)">
                                Draw: {{ $item->lotteryPackage->draw_date->format('d M Y') }}
                            </div>
                        </td>
                        <td>Rs. {{ number_format($item->price_per_ticket, 0) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td style="color:var(--teal);font-weight:700">
                            Rs. {{ number_format($item->total_price, 0) }}
                        </td>
                        <td>
                            <form action="{{ route('user.cart.remove', $item) }}"
                                  method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-xs"
                                    style="background:rgba(239,68,68,.15);color:var(--red)">
                                    Remove
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card" style="height:fit-content">
        <div class="card-header"><h3>Order Summary</h3></div>
        <div style="padding:20px">
            <div class="detail-row">
                <span class="detail-label">Total Items</span>
                <span>{{ $cartItems->count() }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total Tickets</span>
                <span>{{ $cartItems->sum('quantity') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Wallet Balance</span>
                <span style="color:var(--teal);font-weight:700">
                    Rs. {{ number_format(auth()->user()->wallet_balance, 0) }}
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total Amount</span>
                <span style="color:var(--teal);font-size:1.4rem;font-family:'Rajdhani',sans-serif;font-weight:700">
                    Rs. {{ number_format($total, 0) }}
                </span>
            </div>

            @if(auth()->user()->wallet_balance < $total)
            <div class="alert error" style="margin-top:16px">
                Insufficient balance! Please
                <a href="{{ route('user.wallet.index') }}">top up your wallet</a>.
            </div>
            @else
            <form action="{{ route('user.cart.checkout') }}" method="POST" style="margin-top:16px">
                @csrf
                <button type="submit" class="btn-primary full-width"
                        onclick="return confirm('Confirm checkout for Rs.{{ number_format($total,0) }}?')">
                    ✅ Checkout Now
                </button>
            </form>
            @endif

            <a href="{{ route('user.lotteries.index') }}"
               class="btn-outline full-width" style="margin-top:10px;text-align:center;display:block">
                + Add More
            </a>
        </div>
    </div>
</div>
@endif
@endsection