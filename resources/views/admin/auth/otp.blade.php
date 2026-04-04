<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RB Admin - OTP Verify</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Oswald:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --gym-yellow: #ffdf00;
            --gym-dark: #121212;
            --gym-darker: #0a0a0a;
            --gym-card: #1e1e1e;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--gym-darker);
            color: #fff;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.8)), url('/assets/gym_bg_login.jpg');
            background-size: cover;
            background-position: center;
        }

        .otp-card {
            background: var(--gym-card);
            padding: 2.5rem;
            border-radius: 1.5rem;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.5);
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            text-align: center;
        }

        .otp-logo { margin-bottom: 2rem; }
        .otp-logo img { height: 50px; }

        .otp-title {
            font-family: 'Oswald', sans-serif;
            font-size: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 1rem;
        }

        .otp-subtitle {
            font-size: 0.875rem;
            color: rgba(255,255,255,0.6);
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        .otp-inputs {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-bottom: 2rem;
        }

        .otp-input {
            width: 100%;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 0.75rem;
            padding: 1rem;
            color: #fff;
            font-size: 1.5rem;
            text-align: center;
            letter-spacing: 0.5em;
            font-weight: 700;
        }

        .otp-input:focus {
            outline: none;
            border-color: var(--gym-yellow);
        }

        .btn-verify {
            width: 100%;
            background: var(--gym-yellow);
            color: #000;
            border: none;
            border-radius: 0.75rem;
            padding: 1rem;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            transition: all 0.2s;
            margin-bottom: 1.5rem;
        }

        .btn-verify:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(255, 223, 0, 0.2); }

        .resend-link {
            font-size: 0.875rem;
            color: rgba(255,255,255,0.4);
            text-decoration: none;
            transition: color 0.2s;
            background: none;
            border: none;
            cursor: pointer;
        }

        .resend-link:hover { color: var(--gym-yellow); }

        .error-message { color: #ff4d4d; font-size: 0.875rem; margin-top: 1rem; }
        .status-message { color: #4dff4d; font-size: 0.875rem; margin-top: 1rem; }
    </style>
</head>
<body>
    <div class="otp-card">
        <div class="otp-logo">
            <img src="/assets/logo.png" alt="RB Fitness">
        </div>
        <h1 class="otp-title">Verify <span>OTP</span></h1>
        <p class="otp-subtitle">A verification code has been sent to your registered email address. This code expires in 5 minutes.</p>
        
        <form action="{{ route('admin.otp.verify') }}" method="POST">
            @csrf
            <div class="form-group">
                <input type="text" name="otp" class="otp-input" maxlength="6" pattern="\d{6}" placeholder="······" required autofocus autocomplete="one-time-code">
            </div>
            
            @if($errors->any())
                <div class="error-message">{{ $errors->first() }}</div>
            @endif

            @if(session('status'))
                <div class="status-message">{{ session('status') }}</div>
            @endif

            <button type="submit" class="btn-verify">Verify & Login</button>
        </form>

        <form action="{{ route('admin.otp.resend') }}" method="POST">
            @csrf
            <button type="submit" class="resend-link">Didn't receive the code? Resend OTP</button>
        </form>
    </div>
</body>
</html>
