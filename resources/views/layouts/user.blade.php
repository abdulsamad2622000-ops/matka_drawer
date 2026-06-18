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
            .navbar-logo { font-size: 12px !important; }
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

        {{-- Profile Dropdown --}}
        <div style="position:relative">
            <div class="navbar-user" onclick="toggleProfileDrop()" style="cursor:pointer">
                <div class="navbar-avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
                <div style="display:flex;flex-direction:column;line-height:1.2">
                    <span style="font-size:13px;font-weight:600">{{ Str::limit(auth()->user()->name, 10) }}</span>
                    <span style="font-size:11px;color:var(--teal);font-weight:700">Rs. {{ number_format(auth()->user()->wallet_balance, 0) }}</span>
                </div>
                <span>▾</span>
            </div>

            <div id="profileDrop" style="display:none;position:absolute;top:110%;right:0;min-width:220px;background:var(--bg2);border:1px solid var(--border);border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,0.3);z-index:9999;overflow:hidden">

                <div style="padding:14px 16px;border-bottom:1px solid var(--border);background:var(--bg3)">
                    <div style="display:flex;align-items:center;gap:10px">
                        <div style="width:40px;height:40px;background:var(--teal);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:700;color:#fff">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div>
                            <div style="font-weight:700;font-size:14px">{{ auth()->user()->name }}</div>
                            <div style="font-size:11px;color:var(--text2)">{{ auth()->user()->email }}</div>
                        </div>
                    </div>
                </div>

                <div style="padding:12px 16px;border-bottom:1px solid var(--border)">
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                        <span style="font-size:12px;color:var(--text2)">💰 Total Balance</span>
                        <span style="font-weight:700;color:var(--teal)">Rs. {{ number_format(auth()->user()->wallet_balance, 0) }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                        <span style="font-size:12px;color:var(--text2)">💸 Withdrawable</span>
                        <span style="font-weight:700">Rs. {{ number_format(auth()->user()->wallet_balance - auth()->user()->referral_bonus_balance, 0) }}</span>
                    </div>
                    @if(auth()->user()->referral_bonus_balance > 0)
                    <div style="display:flex;justify-content:space-between">
                        <span style="font-size:12px;color:var(--text2)">🎁 Referral Bonus</span>
                        <span style="font-weight:700;color:#f59e0b">Rs. {{ number_format(auth()->user()->referral_bonus_balance, 0) }}</span>
                    </div>
                    @endif
                </div>

                <div style="padding:8px">
                    <a href="{{ route('user.wallet.index') }}"
                       style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;text-decoration:none;color:var(--text)"
                       onmouseover="this.style.background='var(--bg3)'" onmouseout="this.style.background='transparent'">
                        <span>💳</span> <span style="font-size:13px">Wallet & Transactions</span>
                    </a>
                    <a href="{{ route('user.wallet.index') }}#deposit"
                       style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;text-decoration:none;color:var(--text)"
                       onmouseover="this.style.background='var(--bg3)'" onmouseout="this.style.background='transparent'">
                        <span>➕</span> <span style="font-size:13px">Add Funds</span>
                    </a>
                    <a href="{{ route('user.referrals.index') }}"
                       style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;text-decoration:none;color:var(--text)"
                       onmouseover="this.style.background='var(--bg3)'" onmouseout="this.style.background='transparent'">
                        <span>🔗</span> <span style="font-size:13px">My Referrals</span>
                    </a>
                    <a href="{{ route('user.support.index') }}"
   style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:8px;text-decoration:none;color:var(--text)"
   onmouseover="this.style.background='var(--bg3)'" onmouseout="this.style.background='transparent'">
    <span>🎧</span> <span style="font-size:13px">Support</span>
</a>
                    <a href="{{ route('user.wallet.index') }}#change-password"
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
        <span class="bal-value">{{ auth()->user()->bets()->where('status','pending')->count() }}</span>
    </div>
</div>

<div class="app-wrapper">
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
           <a href="{{ route('user.support.index') }}" class="nav-item {{ request()->routeIs('user.support*') ? 'active' : '' }}">
    <span class="icon">🎧</span> Support
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

function toggleProfileDrop() {
    const drop = document.getElementById('profileDrop');
    drop.style.display = drop.style.display === 'block' ? 'none' : 'block';
}

document.addEventListener('click', function(e) {
    const drop = document.getElementById('profileDrop');
    if(drop && !e.target.closest('.navbar-user') && !e.target.closest('#profileDrop')) {
        drop.style.display = 'none';
    }
});
</script>

</body>
</html>