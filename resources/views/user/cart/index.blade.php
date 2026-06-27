@extends('layouts.user')

@section('title', 'My Cart')

@section('content')
<div class="page-header">
    <h1>🛒 My Cart</h1>
    <span class="subtitle">Review your bets before placing</span>
</div>

@if(session('success'))
<div class="alert success" style="margin-bottom:16px">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="alert error" style="margin-bottom:16px">{{ session('error') }}</div>
@endif

@if($cartItems->isEmpty())
<div class="empty-state" style="margin-top:60px">
    <span>🛒</span>
    <p>Your cart is empty!</p>
    <a href="{{ route('user.bets.index') }}" class="btn-primary" style="margin-top:16px;display:inline-block">
        Place Bets
    </a>
</div>
@else
<div class="two-col">

    {{-- CART ITEMS --}}
    <div class="card">
        <div class="card-header">
            <h3>🎯 Bet Items</h3>
            <span style="font-size:12px;color:var(--text2)">{{ $cartItems->count() }} bet(s)</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bet Number</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Potential Win</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cartItems as $i => $item)
                    <tr>
                        <td style="color:var(--text2);font-size:12px">{{ $i + 1 }}</td>
                        <td>
                            <span style="font-family:'Rajdhani',sans-serif;font-size:1.8rem;font-weight:700;color:var(--teal)">
                                {{ $item->bet_number }}
                            </span>
                        </td>
                        <td>
                            <span style="background:rgba(0,184,148,0.1);border:1px solid rgba(0,184,148,0.3);padding:3px 10px;border-radius:20px;font-size:12px;font-weight:700;color:var(--teal)">
                                {{ $item->bet_type }}
                            </span>
                        </td>
                        <td style="font-weight:700">Rs. {{ number_format($item->bet_amount, 0) }}</td>
                        <td style="color:var(--teal);font-weight:700">Rs. {{ number_format($item->potential_win, 0) }}</td>
                        <td>
                            <form action="{{ route('user.cart.remove', $item) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-xs"
                                    style="background:rgba(239,68,68,.15);color:#ef4444;border:1px solid rgba(239,68,68,0.3)">
                                    🗑️ Remove
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ORDER SUMMARY --}}
    <div class="card" style="height:fit-content">
        <div class="card-header"><h3>📋 Summary</h3></div>
        <div style="padding:20px">
            <div style="display:flex;justify-content:space-between;margin-bottom:12px">
                <span style="color:var(--text2);font-size:13px">Total Bets</span>
                <span style="font-weight:700">{{ $cartItems->count() }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:12px">
                <span style="color:var(--text2);font-size:13px">Wallet Balance</span>
                <span style="color:var(--teal);font-weight:700">
                    Rs. {{ number_format(auth()->user()->wallet_balance, 0) }}
                </span>
            </div>
            <div style="display:flex;justify-content:space-between;margin-bottom:12px">
                <span style="color:var(--text2);font-size:13px">Total Potential Win</span>
                <span style="color:#f59e0b;font-weight:700">
                    Rs. {{ number_format($cartItems->sum('potential_win'), 0) }}
                </span>
            </div>
            <div style="display:flex;justify-content:space-between;padding:12px 0;border-top:1px solid var(--border);border-bottom:1px solid var(--border);margin-bottom:16px">
                <span style="font-weight:700">Total Amount</span>
                <span style="color:var(--teal);font-size:1.4rem;font-family:'Rajdhani',sans-serif;font-weight:700">
                    Rs. {{ number_format($total, 0) }}
                </span>
            </div>

            @if(auth()->user()->wallet_balance < $total)
            <div class="alert error" style="margin-bottom:16px">
                ❌ Insufficient balance! Please
                <a href="{{ route('user.wallet.index') }}" style="color:var(--teal)">top up your wallet</a>.
            </div>
            @else
            <form action="{{ route('user.cart.checkout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-primary full-width"
                        onclick="return confirm('Place all {{ $cartItems->count() }} bet(s) for Rs. {{ number_format($total,0) }}?')">
                    ✅ Place All Bets
                </button>
            </form>
            @endif

            <a href="{{ route('user.bets.index') }}"
               class="btn-outline full-width" style="margin-top:10px;text-align:center;display:block">
                + Add More Bets
            </a>
        </div>
    </div>

</div>
@endif
@endsection