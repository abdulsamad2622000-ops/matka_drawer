<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — LottoApp</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-box">
        <div class="auth-logo">
            <span>🎰 LottoApp</span>
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
                <label>Email Address</label>
                <input type="email" name="email" class="form-input"
                       placeholder="you@example.com" value="{{ old('email') }}" required>
                @error('email')<span class="error">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>Phone <span class="hint">(optional)</span></label>
                <input type="text" name="phone" class="form-input"
                       placeholder="03001234567" value="{{ old('phone') }}">
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