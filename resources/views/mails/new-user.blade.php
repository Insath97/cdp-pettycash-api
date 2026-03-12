<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ config('app.name') }}</title>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #f8fafc; color: #1e293b; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); }
        .header { background-color: #298c77; padding: 30px; text-align: center; color: #ffffff; }
        .content { padding: 40px; line-height: 1.6; }
        .greeting { font-size: 18px; font-weight: 700; margin-bottom: 20px; }
        .credential-box { background-color: #f1f5f9; border-radius: 12px; padding: 24px; margin-bottom: 30px; }
        .credential-item { display: flex; margin-bottom: 12px; }
        .credential-label { width: 100px; font-weight: 600; color: #64748b; font-size: 13px; }
        .credential-value { font-weight: 700; color: #1e293b; font-size: 14px; }
        .btn { display: inline-block; background: #298c77; color: #ffffff; text-align: center; padding: 14px 28px; border-radius: 8px; text-decoration: none; font-weight: 700; margin-top: 10px; }
        .footer { padding: 20px; background-color: #f8fafc; text-align: center; font-size: 12px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2 style="margin:0;">ACCOUNT CREATED</h2>
        </div>
        <div class="content">
            <p class="greeting">Hello {{ $user['name'] }},</p>
            <p>Your account has been successfully created for the <strong>{{ config('app.name') }}</strong> portal. You can now log in using the credentials below:</p>

            <div class="credential-box">
                <div class="credential-item">
                    <div class="credential-label">Username</div>
                    <div class="credential-value">{{ $user['username'] }}</div>
                </div>
                <div class="credential-item">
                    <div class="credential-label">Email</div>
                    <div class="credential-value">{{ $user['email'] }}</div>
                </div>
                <div class="credential-item" style="margin-bottom: 0;">
                    <div class="credential-label">Password</div>
                    <div class="credential-value" style="color: #298c77;">{{ $password }}</div>
                </div>
            </div>

            @if (isset($role) && !empty($role))
            <p><strong>Assigned Role:</strong> {{ $role }}</p>
            @endif

            <a href="{{ $login_url ?? config('app.url') }}" class="btn">Login to Your Account</a>
            
            <p style="margin-top: 30px; font-size: 13px; color: #64748b;">
                <strong>Note:</strong> We recommend changing your password immediately after your first login for security purposes.
            </p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>Environment: {{ config('app.env') }}</p>
        </div>
    </div>
</body>
</html>
