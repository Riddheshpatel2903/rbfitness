<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #0a0a0a; color: #ffffff; padding: 20px; }
        .container { max-width: 600px; margin: 0 auto; background: #1a1a1a; padding: 40px; border-radius: 10px; border: 1px solid #ffc107; text-align: center; }
        .otp { font-size: 32px; font-weight: bold; color: #ffc107; letter-spacing: 5px; margin: 30px 0; padding: 20px; background: rgba(255,193,7,0.1); border-radius: 5px; border: 1px dashed #ffc107; }
        .footer { margin-top: 30px; font-size: 12px; opacity: 0.5; }
    </style>
</head>
<body>
    <div class="container">
        <h1 style="color: #ffc107; text-transform: uppercase;">RB Fitness Admin Login</h1>
        <p>A login attempt was made for your administrative account.</p>
        <p>Please use the following 6-digit One-Time Password to verify your identity:</p>
        
        <div class="otp">{{ $otp }}</div>
        
        <p>This code will expire in <strong>10 minutes</strong>.</p>
        <p>If you did not attempt to log in, please secure your account immediately.</p>
        
        <div class="footer">
            &copy; {{ date('Y') }} RB Fitness Club. All Rights Reserved.
        </div>
    </div>
</body>
</html>
