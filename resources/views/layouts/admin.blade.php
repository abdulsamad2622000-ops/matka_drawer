<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin — @yield('title', 'LottoApp')</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
<div class="app-wrapper">
    <aside class="sidebar">
        <div class="sidebar-logo">
            <span>🎰 LottoApp</span>
            <small>Admin Panel</small>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <span class="icon">📊</span> Dashboard
            </a>
            <a href="{{ route('admin.payments.index') }}" class="nav-item {{ request()->routeIs('admin.payments*') ? 'active' : '' }}">
                <span class="icon">💳</span> Payments
                @php $pending = \App\Models\PaymentRequest::where('status','pending')->count() @endphp
                @if($pending > 0)
                    <span class="badge warning" style="margin-left:auto">{{ $pending }}</span>
                @endif
            </a>
            <a href="{{ route('admin.lotteries.index') }}" class="nav-item {{ request()->routeIs('admin.lotteries*') ? 'active' : '' }}">
                <span class="icon">🎰</span> Lotteries
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <span class="icon">👥</span> Users
            </a>
            <a href="{{ route('admin.announcements.index') }}" class="nav-item {{ request()->routeIs('admin.announcements*') ? 'active' : '' }}">
                <span class="icon">📢</span> Announcements
            </a>
        </nav>
        <div class="sidebar-bottom">
            <div class="user-chip">
                <div class="user-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                <div>
                    <div style="font-size:13px;font-weight:600">{{ auth()->user()->name }}</div>
                    <span class="admin-badge">ADMIN</span>
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
            <div class="alert success" style="margin-bottom:20px">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert error" style="margin-bottom:20px">❌ {{ session('error') }}</div>
        @endif

        @yield('content')
    </main>
</div>
</body>
</html>