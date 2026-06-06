<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Matka Champion')</title>
    <link rel="stylesheet" href="/css/app.css">
    <style>
        .sidebar {
            transition: transform .3s ease, width .3s ease;
        }
        .sidebar.hidden {
            transform: translateX(-220px);
            width: 0;
        }
        .main-content {
            transition: margin-left .3s ease;
        }
        .main-content.expanded {
            margin-left: 0 !important;
        }
        .sidebar {
        transition: transform .3s ease, width .3s ease;
    }
    .sidebar.hidden {
        transform: translateX(-220px);
        width: 0;
    }
    .main-content {
        transition: margin-left .3s ease;
        overflow-x: hidden;
        min-width: 0;
    }
    .main-content.expanded {
        margin-left: 0 !important;
    }
    .app-wrapper {
        overflow-x: hidden;
    }
    @media (max-width: 768px) {
        .app-wrapper {
            overflow-x: hidden;
        }
        .main-content {
            width: 100%;
            max-width: 100vw;
            overflow-x: hidden;
        }
    }
    </style>
</head>
<body>

<!-- TOP NAVBAR -->
<nav class="top-navbar">
    <div class="navbar-left">
        <button class="hamburger" id="hamburgerBtn">☰</button>
        <span class="navbar-logo">Matka Champion</span>
    </div>
    <div class="navbar-right">
        <div class="navbar-bl">
            <span>B: {{ number_format(auth()->user()->wallet_balance, 0) }}</span>
            &nbsp;|&nbsp;
            <span>L: 0</span>
        </div>
{{-- Cart Icon --}}
<a href="{{ route('user.cart.index') }}" style="position:relative;color:var(--text);font-size:20px;text-decoration:none">
    🛒
    @php $cartCount = \App\Models\CartItem::where('user_id', auth()->id())->sum('quantity'); @endphp
    @if($cartCount > 0)
    <span style="position:absolute;top:-6px;right:-8px;background:var(--teal);color:#fff;font-size:10px;font-weight:700;width:16px;height:16px;border-radius:50%;display:flex;align-items:center;justify-content:center">
        {{ $cartCount }}
    </span>
    @endif
</a>
        <div class="navbar-user">

            <div class="navbar-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
            <span>{{ Str::limit(auth()->user()->name, 10) }}</span>
            <span>▾</span>
        </div>
    </div>
</nav>

<!-- BALANCE BAR -->
<div class="balance-bar">
    <div class="bal-item">
        <span class="bal-label">Credit:</span>
        <span class="bal-value">0</span>
    </div>
    <div class="bal-item">
        <span class="bal-label">Balance:</span>
        <span class="bal-value green">{{ number_format(auth()->user()->wallet_balance, 0) }}</span>
    </div>
    <div class="bal-item">
        <span class="bal-label">Liable:</span>
        <span class="bal-value">0</span>
    </div>
    <div class="bal-item">
        <span class="bal-label">Active Bets:</span>
        <span class="bal-value">{{ auth()->user()->bets()->where('status','pending')->count() }}</span>
    </div>
</div>

<div class="app-wrapper">
    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
        <nav class="sidebar-nav">
            <a href="{{ route('user.dashboard') }}" class="nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <span class="icon">🏠</span> Dashboard
            </a>
            <a href="{{ route('user.bets.index') }}" class="nav-item {{ request()->routeIs('user.bets*') ? 'active' : '' }}">
                <span class="icon">🎯</span> Place Bet
            </a>
        @if(Route::has('user.cart.index'))
<a href="{{ route('user.cart.index') }}" class="nav-item {{ request()->routeIs('user.cart*') ? 'active' : '' }}">
    <span class="icon">🛒</span> My Cart
    @php $cartCount = \App\Models\CartItem::where('user_id', auth()->id())->sum('quantity'); @endphp
    @if($cartCount > 0)
    <span class="badge active" style="margin-left:auto;font-size:10px">{{ $cartCount }}</span>
    @endif
</a>
@endif
            <a href="{{ route('user.wallet.index') }}" class="nav-item {{ request()->routeIs('user.wallet*') ? 'active' : '' }}">
                <span class="icon">💳</span> Wallet
                <span style="margin-left:auto;font-size:11px;color:var(--teal)">
                    Rs. {{ number_format(auth()->user()->wallet_balance, 0) }}
                </span>
            </a>
            <a href="{{ route('user.referrals.index') }}" class="nav-item {{ request()->routeIs('user.referrals*') ? 'active' : '' }}">
    <span class="icon">🔗</span> Referrals
    @if(auth()->user()->referredUsers()->count() > 0)
    <span style="margin-left:auto;font-size:11px;color:var(--teal)">
        {{ auth()->user()->referredUsers()->count() }}
    </span>
    @endif
</a>
        </nav>
        <div class="sidebar-bottom">
            <div class="user-chip">
                <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                <div>
                    <div style="font-size:13px;font-weight:600">{{ auth()->user()->name }}</div>
                    <div style="font-size:11px;color:var(--text2)">{{ auth()->user()->email }}</div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-outline" style="width:100%;font-size:12px">Logout</button>
            </form>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content" id="mainContent">
        @if(session('success'))
            <div class="alert success" style="margin-bottom:16px">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert error" style="margin-bottom:16px">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</div>

<script>
    const btn     = document.getElementById('hamburgerBtn');
    const sidebar = document.getElementById('sidebar');
    const main    = document.getElementById('mainContent');

    btn.addEventListener('click', function() {
        sidebar.classList.toggle('hidden');
        main.classList.toggle('expanded');
    });
</script>

</body>
</html>
