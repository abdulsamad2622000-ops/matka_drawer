<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LottoApp')</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
<div class="app-wrapper">
    <aside class="sidebar">
        <div class="sidebar-logo">
            <span>🎰 LottoApp</span>
            <small>Player Portal</small>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('user.dashboard') }}" class="nav-item {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <span class="icon">🏠</span> Dashboard
            </a>
            <a href="{{ route('user.lotteries.index') }}" class="nav-item {{ request()->routeIs('user.lotteries*') ? 'active' : '' }}">
                <span class="icon">🎰</span> Lotteries
            </a>
            <a href="{{ route('user.wallet.index') }}" class="nav-item {{ request()->routeIs('user.wallet*') ? 'active' : '' }}">
                <span class="icon">💳</span> Wallet
                <span style="margin-left:auto;font-size:11px;color:var(--gold)">
                    Rs. {{ number_format(auth()->user()->wallet_balance, 0) }}
                </span>
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
            <form action="{{ route('logout') }}" method="POST" style="margin-top:12px">
                @csrf
                <button type="submit" class="btn-outline" style="width:100%;font-size:13px">Logout</button>
            </form>
        </div>
    </aside>

    <main class="main-content">
        @if(session('success'))
            <div class="alert success" style="margin-bottom:20px">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert error" style="margin-bottom:20px">{{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>