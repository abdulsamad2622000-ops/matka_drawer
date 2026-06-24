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
            overflow-x: hidden;
        }
        .main-content.expanded {
            margin-left: 0 !important;
        }
        .app-wrapper {
            overflow-x: hidden;
        }
        @media (max-width: 768px) {
            .sidebar {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                height: 100vh !important;
                z-index: 1000 !important;
                width: 220px !important;
                transform: translateX(-220px) !important;
                transition: transform .3s ease !important;
            }
            .sidebar.mobile-open {
                transform: translateX(0px) !important;
            }
            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
                max-width: 100vw !important;
            }
            .navbar-bl { display: none !important; }
            .balance-bar { font-size: 11px !important; padding: 4px 8px !important; }
        }
        #mobileOverlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        #mobileOverlay.active { display: block; }
    </style>
</head>
<body>

<div id="mobileOverlay" onclick="closeMobileSidebar()"></div>

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



        {{-- NOTIFICATION BELL --}}
@php
    $notifPayments    = \App\Models\PaymentRequest::where('status','pending')->count();
    $notifWithdrawals = \App\Models\WithdrawalRequest::where('status','pending')->count();
    $notifSupport     = \App\Models\SupportTicket::where('status','open')->count();
    $notifPassReset   = \App\Models\PasswordResetRequest::where('status','pending')->count();

    if(request()->routeIs('admin.payments*'))        $notifPayments    = 0;
    if(request()->routeIs('admin.withdrawals*'))     $notifWithdrawals = 0;
    if(request()->routeIs('admin.support*'))         $notifSupport     = 0;
    if(request()->routeIs('admin.password-resets*')) $notifPassReset   = 0;

    $totalNotif = $notifPayments + $notifWithdrawals + $notifSupport + $notifPassReset;
@endphp
<div style="position:relative">
    <button onclick="toggleBell()" style="background:none;border:none;cursor:pointer;position:relative;font-size:20px;padding:4px">
        🔔
        @if($totalNotif > 0)
        <span style="position:absolute;top:-4px;right:-6px;background:#ef4444;color:#fff;font-size:10px;font-weight:700;width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center">
            {{ $totalNotif }}
        </span>
        @endif
    </button>

    <div id="bellDrop" style="display:none;position:absolute;top:110%;right:0;min-width:280px;background:var(--bg2);border:1px solid var(--border);border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,0.3);z-index:9999;overflow:hidden">
        <div style="padding:12px 16px;border-bottom:1px solid var(--border);font-weight:700;font-size:14px">
            🔔 Notifications
            @if($totalNotif > 0)
            <span style="background:#ef4444;color:#fff;font-size:10px;padding:2px 7px;border-radius:10px;margin-left:6px">{{ $totalNotif }}</span>
            @endif
        </div>

        <div style="padding:8px">
            @if($notifPayments > 0)
            <a href="{{ route('admin.payments.index') }}" style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;text-decoration:none;color:var(--text);margin-bottom:4px;background:rgba(255,193,7,0.06);border:1px solid rgba(255,193,7,0.2)"
               onmouseover="this.style.background='var(--bg3)'" onmouseout="this.style.background='rgba(255,193,7,0.06)'">
                <span style="font-size:18px">💰</span>
                <div style="flex:1">
                    <div style="font-size:13px;font-weight:600">Pending Payments</div>
                    <div style="font-size:11px;color:var(--text2)">{{ $notifPayments }} request(s) waiting</div>
                </div>
                <span style="background:#f59e0b;color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:10px">{{ $notifPayments }}</span>
            </a>
            @endif

            @if($notifWithdrawals > 0)
            <a href="{{ route('admin.withdrawals.index') }}" style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;text-decoration:none;color:var(--text);margin-bottom:4px;background:rgba(99,102,241,0.06);border:1px solid rgba(99,102,241,0.2)"
               onmouseover="this.style.background='var(--bg3)'" onmouseout="this.style.background='rgba(99,102,241,0.06)'">
                <span style="font-size:18px">💸</span>
                <div style="flex:1">
                    <div style="font-size:13px;font-weight:600">Pending Withdrawals</div>
                    <div style="font-size:11px;color:var(--text2)">{{ $notifWithdrawals }} request(s) waiting</div>
                </div>
                <span style="background:#6366f1;color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:10px">{{ $notifWithdrawals }}</span>
            </a>
            @endif

            @if($notifSupport > 0)
            <a href="{{ route('admin.support.index') }}" style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;text-decoration:none;color:var(--text);margin-bottom:4px;background:rgba(0,184,148,0.06);border:1px solid rgba(0,184,148,0.2)"
               onmouseover="this.style.background='var(--bg3)'" onmouseout="this.style.background='rgba(0,184,148,0.06)'">
                <span style="font-size:18px">🎧</span>
                <div style="flex:1">
                    <div style="font-size:13px;font-weight:600">Open Support Tickets</div>
                    <div style="font-size:11px;color:var(--text2)">{{ $notifSupport }} ticket(s) open</div>
                </div>
                <span style="background:var(--teal);color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:10px">{{ $notifSupport }}</span>
            </a>
            @endif

            @if($notifPassReset > 0)
            <a href="{{ route('admin.password-resets.index') }}" style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;text-decoration:none;color:var(--text);background:rgba(239,68,68,0.06);border:1px solid rgba(239,68,68,0.2)"
               onmouseover="this.style.background='var(--bg3)'" onmouseout="this.style.background='rgba(239,68,68,0.06)'">
                <span style="font-size:18px">🔑</span>
                <div style="flex:1">
                    <div style="font-size:13px;font-weight:600">Password Reset Requests</div>
                    <div style="font-size:11px;color:var(--text2)">{{ $notifPassReset }} request(s) pending</div>
                </div>
                <span style="background:#ef4444;color:#fff;font-size:11px;font-weight:700;padding:2px 8px;border-radius:10px">{{ $notifPassReset }}</span>
            </a>
            @endif

            @if($totalNotif === 0)
            <div style="padding:20px;text-align:center;color:var(--text2);font-size:13px">
                ✅ No pending notifications
            </div>
            @endif
        </div>
    </div>
</div>
        {{-- Profile Dropdown --}}
        <div style="position:relative">
            <div class="navbar-user" onclick="toggleAdminDrop()" style="cursor:pointer">
                <div class="navbar-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                <div style="display:flex;flex-direction:column;line-height:1.2">
                    <span style="font-size:13px;font-weight:600">{{ Str::limit(auth()->user()->name, 10) }}</span>
                    <span style="font-size:11px;color:var(--teal);font-weight:700">Admin</span>
                </div>
                <span>▾</span>
            </div>

            <div id="adminDrop" style="display:none;position:absolute;top:110%;right:0;min-width:200px;background:var(--bg2);border:1px solid var(--border);border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,0.3);z-index:9999;overflow:hidden">

                <div style="padding:14px 16px;border-bottom:1px solid var(--border);background:var(--bg3)">
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="width:38px;height:38px;background:var(--teal);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;font-weight:700;color:#fff">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:13px">{{ auth()->user()->name }}</div>
                            <div style="font-size:11px;color:var(--text2)">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                </div>

                <div style="padding:8px">
                    <a href="{{ route('admin.settings.index') }}"
                       style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;text-decoration:none;color:var(--text)"
                       onmouseover="this.style.background='var(--bg3)'" onmouseout="this.style.background='transparent'">
                        <span>⚙️</span> <span style="font-size:13px">Settings</span>
                    </a>
                    <a href="{{ route('admin.settings.index') }}"
                       style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;text-decoration:none;color:var(--text)"
                       onmouseover="this.style.background='var(--bg3)'" onmouseout="this.style.background='transparent'">
                        <span>🔐</span> <span style="font-size:13px">Change Password</span>
                    </a>
                </div>

                <div style="padding:8px;border-top:1px solid var(--border)">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit"
                            style="width:100%;display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;background:rgba(239,68,68,0.1);border:none;color:#ef4444;cursor:pointer;font-size:13px;font-weight:600">
                            <span>🚪</span> Logout
                        </button>
                    </form>
                </div>
            </div>
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
            <a href="{{ route('admin.support.index') }}" class="nav-item {{ request()->routeIs('admin.support*') ? 'active' : '' }}">
    <span class="icon">🎧</span> Support
    @php $openTickets = \App\Models\SupportTicket::where('status','open')->count(); @endphp
    @if($openTickets > 0)
    <span style="margin-left:auto;background:#ef4444;color:#fff;font-size:10px;font-weight:700;padding:1px 7px;border-radius:10px">
        {{ $openTickets }}
    </span>
    @endif
</a>
            <a href="{{ route('admin.settings.index') }}" class="nav-item {{ request()->routeIs('admin.settings*') ? 'active' : '' }}">
                <span class="icon">⚙️</span> Settings
            </a>
           <a href="{{ route('admin.password-resets.index') }}" class="nav-item {{ request()->routeIs('admin.password-resets*') ? 'active' : '' }}">
    <span class="icon">🔑</span> Password Resets
    @php $prCount = \App\Models\PasswordResetRequest::where('status','pending')->count(); @endphp
    @if($prCount > 0)
    <span style="margin-left:auto;background:#ef4444;color:#fff;font-size:10px;font-weight:700;padding:1px 7px;border-radius:10px">
        {{ $prCount }}
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
    const overlay = document.getElementById('mobileOverlay');

    function isMobile() { return window.innerWidth <= 768; }

    btn.addEventListener('click', function() {
        if (isMobile()) {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('active');
        } else {
            sidebar.classList.toggle('hidden');
            main.classList.toggle('expanded');
        }
    });

    function closeMobileSidebar() {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
    }

    document.querySelectorAll('.sidebar .nav-item').forEach(function(link) {
        link.addEventListener('click', function() {
            if (isMobile()) closeMobileSidebar();
        });
    });

    function toggleAdminDrop() {
        const drop = document.getElementById('adminDrop');
        const bell = document.getElementById('bellDrop');
        bell.style.display = 'none';
        drop.style.display = drop.style.display === 'block' ? 'none' : 'block';
    }

    function toggleBell() {
        const bell = document.getElementById('bellDrop');
        const drop = document.getElementById('adminDrop');
        drop.style.display = 'none';
        bell.style.display = bell.style.display === 'block' ? 'none' : 'block';
    }

    document.addEventListener('click', function(e) {
        const drop = document.getElementById('adminDrop');
        const bell = document.getElementById('bellDrop');

        if(drop && !e.target.closest('.navbar-user') && !e.target.closest('#adminDrop')) {
            drop.style.display = 'none';
        }
        if(bell && !e.target.closest('#bellDrop') && !e.target.closest('button[onclick="toggleBell()"]')) {
            bell.style.display = 'none';
        }
    });
</script>

</body>
</html>