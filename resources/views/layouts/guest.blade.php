<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'ANTERIN') }} - Autentikasi</title>
    <link rel="stylesheet" href="{{ asset('css/anterin.css') }}">
    <style>
        .auth-container {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: var(--bg-primary);
            padding: 20px;
        }
        .auth-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            backdrop-filter: blur(10px);
            box-shadow: var(--shadow);
            width: 100%;
            max-width: 400px;
            padding: 30px;
            text-align: center;
        }
        .auth-logo {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin-bottom: 24px;
            text-decoration: none;
        }
        .auth-logo-icon {
            width: 50px; height: 50px;
            background: linear-gradient(135deg, var(--accent), #0066CC);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px;
        }
        .auth-logo-text { font-weight: 800; font-size: 22px; color: var(--text-light); }
        .auth-logo-sub { font-size: 12px; color: var(--text-secondary); }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <a href="/" class="auth-logo">
                <div class="auth-logo-icon">🚌</div>
                <div>
                    <div class="auth-logo-text">ANTERIN</div>
                    <div class="auth-logo-sub">Smart City Transportation</div>
                </div>
            </a>
            
            {{ $slot }}
        </div>
    </div>
</body>
</html>
