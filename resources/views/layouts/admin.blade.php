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
        <span class="bal-value">{{ auth()->user()->lotteryTickets()->where('status','active')->count() }}</span>
    </div>
</div>

<div class="app-wrapper">
    <!-- SIDEBAR -->
    <aside class="sidebar" id="sidebar">
     <nav class="sidebar-nav">
    <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <span class="icon">🏠</span> Dashboard
    </a>
    <a href="{{ route('admin.payments.index') }}" class="nav-item {{ request()->routeIs('admin.payments*') ? 'active' : '' }}">
        <span class="icon">💳</span> Payments
    </a>

    <a href="{{ route('admin.withdrawals.index') }}" class="nav-item {{ request()->routeIs('admin.withdrawals*') ? 'active' : '' }}">
    <span class="icon">💸</span> Withdrawals
</a>
   
    <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
        <span class="icon">👥</span> Users
    </a>
    <a href="{{ route('admin.announcements.index') }}" class="nav-item {{ request()->routeIs('admin.announcements*') ? 'active' : '' }}">
        <span class="icon">📢</span> Announcements
    </a>
    <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
        <span class="icon">⚙️</span> Settings
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
