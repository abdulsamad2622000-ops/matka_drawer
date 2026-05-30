<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — LottoApp</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-box">
        <div class="auth-logo">
            <span>🎰 LottoApp</span>
            <small>Sign in to your account</small>
        </div>

        @if(session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" class="form-input"
                       placeholder="you@example.com" value="{{ old('email') }}" required>
                @error('email')<span class="error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-input"
                       placeholder="••••••••" required>
                @error('password')<span class="error">{{ $message }}</span>@enderror
            </div>

            <button type="submit" class="btn-primary full-width">
                Login →
            </button>
        </form>

        <div class="auth-footer">
            Don't have an account? <a href="{{ route('register') }}">Register here</a>
        </div>
    </div>
</div>
</body>
</html>