<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Matka Champion</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-box">
       <div class="auth-logo">
    <div style="display:flex;align-items:center;justify-content:center;gap:10px;margin-bottom:4px">
        <svg viewBox="0 0 80 80" width="48" height="48" xmlns="http://www.w3.org/2000/svg">
            <defs>
                <linearGradient id="rPotGrad" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="#cc0000"/>
                    <stop offset="100%" stop-color="#7a2a0a"/>
                </linearGradient>
            </defs>
            <!-- Neck -->
            <path d="M32 22 Q30 14 33 10 Q40 6 47 10 Q50 14 48 22 Z" fill="#c45520"/>
            <!-- Rim -->
            <ellipse cx="40" cy="22" rx="10" ry="4" fill="#a03d10"/>
            <!-- Body -->
            <path d="M22 32 Q12 42 14 56 Q18 72 40 74 Q62 72 66 56 Q68 42 58 32 Q52 24 40 22 Q28 24 22 32 Z" fill="url(#rPotGrad)"/>
            <!-- Shine -->
            <path d="M24 34 Q18 46 20 58" stroke="rgba(255,150,80,0.3)" stroke-width="4" stroke-linecap="round" fill="none"/>
            <!-- White stripes -->
            <path d="M20 44 Q40 38 60 44" stroke="rgba(255,255,255,0.6)" stroke-width="1.5" fill="none"/>
            <path d="M18 54 Q40 48 62 54" stroke="rgba(255,255,255,0.5)" stroke-width="1.5" fill="none"/>
            <!-- Bottom -->
            <ellipse cx="40" cy="73" rx="22" ry="5" fill="#6b0000"/>
            <!-- Opening glow -->
            <ellipse cx="40" cy="22" rx="8" ry="3" fill="rgba(255,200,0,0.3)"/>
        </svg>
        <span style="font-size:1.4rem;font-weight:700">Matka Champion</span>
    </div>
    <small>Create your account</small>
</div>
        @if(session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" class="form-input"
                       placeholder="Ali Hassan" value="{{ old('name') }}" required>
                @error('name')<span class="error">{{ $message }}</span>@enderror
            </div>

           <div class="form-group">
    <label>Phone Number/Username</label>
    <input type="text" name="email" class="form-input"
           placeholder="03001234567" value="{{ old('email') }}" required>
    @error('email')<span class="error">{{ $message }}</span>@enderror
</div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-input"
                       placeholder="Min 6 characters" required>
                @error('password')<span class="error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-input"
                       placeholder="Repeat password" required>
            </div>

            <div class="form-group">
                <label>Referral Code <span class="hint">(optional)</span></label>
                <input type="text" name="referral_code" class="form-input mono"
                       placeholder="e.g. ABC12345"
                       value="{{ old('referral_code', $referralCode ?? '') }}">
                @error('referral_code')<span class="error">{{ $message }}</span>@enderror
                @if($referralCode ?? false)
                <p class="helper-text" style="color:var(--teal)">✅ Referral code applied!</p>
                @endif
            </div>

            <button type="submit" class="btn-primary full-width">
                Create Account →
            </button>
        </form>

        <div class="auth-footer">
            Already have an account? <a href="{{ route('login') }}">Login here</a>
        </div>
    </div>
</div>
</body>
</html>
