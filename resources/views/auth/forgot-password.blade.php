<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password — Matka Champion</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-box">
        <div class="auth-logo">
            <div style="display:flex;align-items:center;justify-content:center;gap:10px;margin-bottom:4px">
                <svg viewBox="0 0 80 80" width="48" height="48" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="potGrad" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0%" stop-color="#cc0000"/>
                            <stop offset="100%" stop-color="#7a2a0a"/>
                        </linearGradient>
                    </defs>
                    <path d="M32 22 Q30 14 33 10 Q40 6 47 10 Q50 14 48 22 Z" fill="#c45520"/>
                    <ellipse cx="40" cy="22" rx="10" ry="4" fill="#a03d10"/>
                    <path d="M22 32 Q12 42 14 56 Q18 72 40 74 Q62 72 66 56 Q68 42 58 32 Q52 24 40 22 Q28 24 22 32 Z" fill="url(#potGrad)"/>
                    <path d="M24 34 Q18 46 20 58" stroke="rgba(255,150,80,0.3)" stroke-width="4" stroke-linecap="round" fill="none"/>
                    <path d="M20 44 Q40 38 60 44" stroke="rgba(255,255,255,0.6)" stroke-width="1.5" fill="none"/>
                    <path d="M18 54 Q40 48 62 54" stroke="rgba(255,255,255,0.5)" stroke-width="1.5" fill="none"/>
                    <ellipse cx="40" cy="73" rx="22" ry="5" fill="#6b0000"/>
                    <ellipse cx="40" cy="22" rx="8" ry="3" fill="rgba(255,200,0,0.3)"/>
                </svg>
                <span style="font-size:1.4rem;font-weight:700">Matka Champion</span>
            </div>
            <small>Forgot Password</small>
        </div>

        @if(session('success'))
        <div style="background:rgba(0,184,148,0.1);border:1px solid rgba(0,184,148,0.4);border-radius:10px;padding:20px;margin-bottom:16px;text-align:center">
            <div style="font-size:32px;margin-bottom:8px">✅</div>
          <div style="color:var(--teal);font-weight:600;font-size:15px">Password Reset Request Submitted!</div>
<div style="color:var(--text2);font-size:13px;margin-top:8px;line-height:1.6">
    Your request has been received. Our support team will review your request and send your password to your registered email address. Please check your inbox and spam folder.
</div>
<div style="margin-top:12px;background:rgba(0,184,148,0.05);border:1px solid rgba(0,184,148,0.2);border-radius:8px;padding:10px 14px;font-size:12px;color:var(--text2)">
    📧 Please check your <strong style="color:var(--text)">email inbox</strong> and <strong style="color:var(--text)">spam/junk folder</strong> for the password.
</div>
        </div>
        <a href="{{ route('login') }}" class="btn-primary full-width" style="text-align:center;display:block;margin-top:8px">
            ← Back to Login
        </a>

        @else

        @if($errors->any())
        <div class="alert error" style="margin-bottom:16px">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('password.request.store')}}" method="POST">
            @csrf
<div class="form-group">
    <label>User Name  *</label>
    <input type="text" name="phone" class="form-input"
           placeholder="03001234567"
           value="{{ old('phone') }}" required>
    @error('phone')<span class="error">{{ $message }}</span>@enderror
</div>
          
            <div class="form-group">
                <label>Reason <span class="hint">(optional)</span></label>
                <textarea name="reason" class="form-input" rows="3"
                          placeholder="e.g. I forgot my password, please help..."
                          style="resize:vertical">{{ old('reason') }}</textarea>
                @error('reason')<span class="error">{{ $message }}</span>@enderror
            </div>

            <button type="submit" class="btn-primary full-width">
                📧 Send Request
            </button>
        </form>

        <div class="auth-footer" style="margin-top:16px;text-align:center">
            <a href="{{ route('login') }}">← Back to Login</a>
        </div>

        @endif
    </div>
</div>
</body>
</html>