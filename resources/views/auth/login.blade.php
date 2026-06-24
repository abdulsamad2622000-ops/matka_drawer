<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Matka Champion</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            min-height: 100vh;
            background: #1a0000;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }

        .login-wrapper {
            display: flex;
            width: 900px;
            min-height: 580px;
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }

        .left-panel {
            width: 55%;
            background: linear-gradient(135deg, #1a0000 0%, #3d0000 50%, #1a0000 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .left-panel::before {
            content: '';
            position: absolute;
            width: 350px;
            height: 350px;
            background: rgba(255,0,0,0.05);
            border-radius: 50%;
            top: -80px;
            left: -80px;
        }

        .left-panel::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            background: rgba(255,0,0,0.04);
            border-radius: 50%;
            bottom: -40px;
            right: -40px;
        }

        .scene {
            position: relative;
            width: 280px;
            height: 340px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .matka {
            position: absolute;
            bottom: 40px;
            left: 50%;
            transform: translateX(-50%);
            animation: matkaShake 0.5s ease-in-out infinite alternate;
        }

        @keyframes matkaShake {
            0%   { transform: translateX(-50%) rotate(-4deg); }
            100% { transform: translateX(-50%) rotate(4deg); }
        }

        .matka svg {
            width: 160px;
            height: 180px;
            filter: drop-shadow(0 10px 30px rgba(255,0,0,0.4));
        }

        .dollar {
            position: absolute;
            font-size: 22px;
            animation: flyOut 1.5s ease-in-out infinite;
            opacity: 0;
        }

        .dollar:nth-child(1) { left: 50%; top: 30px; animation-delay: 0s; }
        .dollar:nth-child(2) { left: 30%; top: 20px; animation-delay: 0.3s; }
        .dollar:nth-child(3) { left: 65%; top: 25px; animation-delay: 0.6s; }
        .dollar:nth-child(4) { left: 20%; top: 50px; animation-delay: 0.9s; }
        .dollar:nth-child(5) { left: 72%; top: 50px; animation-delay: 1.2s; }

        @keyframes flyOut {
            0%   { opacity: 0; transform: translateY(80px) scale(0.5); }
            30%  { opacity: 1; transform: translateY(20px) scale(1); }
            70%  { opacity: 1; transform: translateY(-20px) scale(1.1); }
            100% { opacity: 0; transform: translateY(-60px) scale(0.8); }
        }

        .glow-ring {
            position: absolute;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            width: 170px;
            height: 30px;
            background: radial-gradient(ellipse, rgba(255,50,50,0.4) 0%, transparent 70%);
            border-radius: 50%;
            animation: glowPulse 1s ease-in-out infinite alternate;
        }

        @keyframes glowPulse {
            0%   { opacity: 0.4; transform: translateX(-50%) scaleX(0.8); }
            100% { opacity: 1; transform: translateX(-50%) scaleX(1.2); }
        }

        .matka-label {
            position: absolute;
            bottom: 8px;
            left: 50%;
            transform: translateX(-50%);
            color: rgba(255,100,100,0.7);
            font-size: 12px;
            letter-spacing: 3px;
            text-transform: uppercase;
            font-weight: 600;
            white-space: nowrap;
        }

        .right-panel {
            width: 45%;
            padding: 50px 45px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #fff;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 40px;
        }

        .logo-icon {
            width: 22px;
            height: 22px;
            background: linear-gradient(135deg, #cc0000, #ff4444);
            clip-path: polygon(0 0, 60% 0, 100% 40%, 100% 100%, 40% 100%, 0 60%);
        }

        .logo-text {
            font-size: 18px;
            font-weight: 700;
            color: #1a0000;
            letter-spacing: 2px;
        }

        .login-title {
            font-size: 32px;
            font-weight: 700;
            color: #1a0000;
            margin-bottom: 8px;
        }

        .login-sub {
            font-size: 13px;
            color: #888;
            margin-bottom: 32px;
        }

        .form-group { margin-bottom: 20px; }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .input-wrap { position: relative; }

        .form-group input {
            width: 100%;
            padding: 13px 16px;
            border: 1.5px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            color: #333;
            outline: none;
            transition: border .2s;
            background: #fff;
        }

        .form-group input:focus {
            border-color: #cc0000;
            box-shadow: 0 0 0 3px rgba(204,0,0,0.1);
        }

        .eye-btn {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: #aaa;
            font-size: 16px;
        }

        .alert-error {
            background: #fff0f0;
            border: 1px solid #ffcdd2;
            color: #c62828;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 16px;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #cc0000, #ff2222);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity .2s, transform .1s;
            margin-top: 4px;
            letter-spacing: 1px;
        }

        .btn-login:hover { opacity: 0.9; }
        .btn-login:active { transform: scale(0.98); }

        .form-footer {
            margin-top: 20px;
            font-size: 13px;
            color: #888;
        }

        .form-footer a {
            color: #cc0000;
            text-decoration: none;
            font-weight: 600;
        }

        .form-footer a:hover { text-decoration: underline; }

        .forgot {
            display: block;
            margin-top: 8px;
            font-size: 13px;
            color: #cc0000;
            text-decoration: none;
        }

        @media (max-width: 700px) {
            .login-wrapper { flex-direction: column; width: 95%; }
            .left-panel { width: 100%; height: 280px; }
            .right-panel { width: 100%; padding: 30px 24px; }
        }
    </style>
</head>
<body>
<div class="login-wrapper">

    <!-- LEFT PANEL -->
    <div class="left-panel">
        <div class="scene">

            <div class="dollar">💵</div>
            <div class="dollar">💰</div>
            <div class="dollar">💵</div>
            <div class="dollar">💸</div>
            <div class="dollar">💵</div>

            <div class="matka">
                <svg viewBox="0 0 200 180" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <linearGradient id="potGrad" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0%" stop-color="#d4622a"/>
                            <stop offset="40%" stop-color="#b5451a"/>
                            <stop offset="100%" stop-color="#7a2a0a"/>
                        </linearGradient>
                        <linearGradient id="shineGrad" x1="0" y1="0" x2="1" y2="0">
                            <stop offset="0%" stop-color="rgba(255,180,120,0.4)"/>
                            <stop offset="50%" stop-color="rgba(255,255,255,0.15)"/>
                            <stop offset="100%" stop-color="rgba(0,0,0,0)"/>
                        </linearGradient>
                    </defs>

                    <path d="M30 90 Q15 80 18 110 Q22 145 100 158 Q178 145 182 110 Q185 80 170 90 Q160 55 100 48 Q40 55 30 90 Z" fill="url(#potGrad)"/>
                    <path d="M38 85 Q28 105 32 130 Q36 148 55 155" stroke="rgba(255,160,90,0.35)" stroke-width="10" stroke-linecap="round" fill="none"/>
                    <path d="M72 48 Q68 32 72 22 Q100 14 128 22 Q132 32 128 48 Z" fill="#c45520"/>
                    <ellipse cx="100" cy="22" rx="28" ry="9" fill="#a03d10"/>
                    <ellipse cx="100" cy="21" rx="20" ry="6" fill="#3a1000"/>
                    <ellipse cx="100" cy="48" rx="30" ry="10" fill="#c45520"/>
                    <path d="M42 82 Q100 72 158 82" stroke="rgba(255,255,255,0.7)" stroke-width="3" fill="none" stroke-linecap="round"/>
                    <path d="M52 95 L58 88 M55 97 L63 91 M50 100 L57 95" stroke="rgba(255,255,255,0.6)" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M142 95 L148 88 M145 97 L153 91 M140 100 L147 95" stroke="rgba(255,255,255,0.6)" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M94 90 L100 83 M97 92 L105 86 M92 95 L99 90" stroke="rgba(255,255,255,0.6)" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M28 112 Q100 100 172 112" stroke="rgba(255,255,255,0.7)" stroke-width="3" fill="none" stroke-linecap="round"/>
                    <path d="M32 122 Q50 117 68 122 Q86 127 104 122 Q122 117 140 122 Q158 127 170 122" stroke="rgba(255,255,255,0.6)" stroke-width="2" fill="none" stroke-linecap="round"/>
                    <path d="M34 130 Q52 125 70 130 Q88 135 106 130 Q124 125 142 130 Q158 135 168 130" stroke="rgba(255,255,255,0.5)" stroke-width="1.5" fill="none" stroke-linecap="round"/>
                    <path d="M32 140 Q100 130 168 140" stroke="rgba(255,255,255,0.6)" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                    <path d="M48 148 L54 148 M62 150 L68 150 M76 151 L82 151" stroke="rgba(255,255,255,0.5)" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M118 151 L124 151 M132 150 L138 150 M146 148 L152 148" stroke="rgba(255,255,255,0.5)" stroke-width="1.5" stroke-linecap="round"/>
                    <ellipse cx="100" cy="157" rx="55" ry="12" fill="#8B3510"/>
                    <ellipse cx="100" cy="165" rx="50" ry="6" fill="rgba(0,0,0,0.2)"/>
                </svg>
            </div>

            <div class="glow-ring"></div>
            <div class="matka-label">Matka Champion</div>

        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="right-panel">
        <div class="logo">
            <div class="logo-icon"></div>
            <span class="logo-text">Matka Champion</span>
        </div>

        <h2 class="login-title">Log In</h2>
        <p class="login-sub">Enter your phone number and password to login to your dashboard.</p>

        @if(session('error'))
            <div class="alert-error">{{ session('error') }}</div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>Phone Number / Username</label>
                <div class="input-wrap">
                    <input type="text" name="email" placeholder="Phone Number/Username"
                           value="{{ old('email') }}" required>
                </div>
                @error('email')<span style="color:#c62828;font-size:12px">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label>Password</label>
                <div class="input-wrap">
                    <input type="password" name="password" id="passInput"
                           placeholder="Enter your Password" required>
                    <button type="button" class="eye-btn" onclick="togglePass()">👁</button>
                </div>
                @error('password')<span style="color:#c62828;font-size:12px">{{ $message }}</span>@enderror
            </div>

            <button type="submit" class="btn-login">🔑 Sign In</button>
        </form>

        <div class="form-footer">
            Don't have an account? <a href="{{ route('register') }}">Sign Up</a>
<a href="{{ route('password.request') }}" class="forgot">Forget Password?</a>     </div>
    </div>
</div>

<script>
function togglePass() {
    const input = document.getElementById('passInput');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>