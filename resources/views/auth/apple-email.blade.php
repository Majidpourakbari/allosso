<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlloSSO • Complete Apple Sign-In</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: dark;
            --color-primary-dark: #1f284b;
            --color-primary: #445c88;
            --color-accent: #ff7a00;
            --color-surface: rgba(255, 255, 255, 0.08);
            --color-surface-hover: rgba(255, 255, 255, 0.14);
            --color-border: rgba(255, 255, 255, 0.12);
            --radius-lg: 24px;
            --radius-md: 16px;
            --duration: 180ms;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
            background: radial-gradient(circle at top left, rgba(68, 92, 136, 0.45), transparent 55%),
                        radial-gradient(circle at bottom right, rgba(255, 122, 0, 0.35), transparent 50%),
                        linear-gradient(135deg, var(--color-primary-dark), rgb(14, 19, 39));
            color: #f5f7ff;
        }

        .auth-shell {
            width: min(500px, 100%);
            display: grid;
            grid-template-columns: 1fr;
            background: linear-gradient(160deg, rgba(68, 92, 136, 0.14), rgba(31, 40, 75, 0.65));
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: var(--radius-lg);
            backdrop-filter: blur(22px);
            box-shadow: 0 40px 70px rgba(6, 9, 20, 0.45);
            overflow: hidden;
        }

        .auth-panel {
            position: relative;
            padding: clamp(32px, 5vw, 64px);
            background: rgba(9, 12, 24, 0.55);
        }

        .panel-header {
            text-align: center;
        }

        .panel-header h2 {
            margin: 0;
            font-size: clamp(1.2rem, 2vw, 1.4rem);
            font-weight: 600;
            white-space: nowrap;
        }

        .panel-header p {
            margin: 8px 0 0;
            color: rgba(226, 232, 255, 0.72);
            font-size: 0.9rem;
        }

        .apple-info {
            text-align: center;
            margin: 24px 0;
            padding: 16px;
            background: rgba(255, 122, 0, 0.1);
            border: 1px solid rgba(255, 122, 0, 0.2);
            border-radius: 12px;
            color: rgba(255, 196, 150, 0.9);
            font-size: 0.9rem;
        }

        form {
            margin: 32px 0 0;
            display: grid;
            gap: 18px;
            align-items: center;
        }

        .form-field-wrapper {
            display: flex;
            justify-content: center;
        }

        .form-field-container {
            width: 100%;
            max-width: 400px;
        }

        label {
            font-size: 0.9rem;
            color: rgba(233, 239, 255, 0.86);
            font-weight: 500;
            display: block;
            margin-bottom: 8px;
        }

        input[type="email"] {
            width: 100%;
            padding: 16px 20px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.16);
            background: rgba(6, 9, 20, 0.58);
            color: #f1f3ff;
            font-size: 1rem;
            transition: border var(--duration) ease, background var(--duration) ease, box-shadow var(--duration) ease;
        }

        input:focus {
            outline: none;
            border-color: rgba(255, 122, 0, 0.75);
            background: rgba(10, 16, 34, 0.92);
            box-shadow: 0 0 0 4px rgba(255, 122, 0, 0.16);
        }

        .submit-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 16px 24px;
            border-radius: 16px;
            border: none;
            background: linear-gradient(120deg, var(--color-accent), #ff9b32);
            color: #10142b;
            font-size: 1.02rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform var(--duration) ease, box-shadow var(--duration) ease, opacity var(--duration) ease;
            width: 100%;
        }

        .submit-button:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 18px 32px rgba(255, 122, 0, 0.26);
        }

        .submit-button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .alert {
            margin: 28px 0 0;
            padding: 14px 18px;
            border-radius: 12px;
            border: 1px solid rgba(255, 100, 100, 0.3);
            background: rgba(255, 100, 100, 0.12);
            color: rgb(255, 196, 196);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="auth-shell">
        <section class="auth-panel">
            @if(isset($platform) && $platform)
                <div style="text-align: center; margin-bottom: 24px;">
                    <img src="{{ asset('assets/allologos/' . $platform['logo']) }}" alt="{{ $platform['name'] }}" style="max-width: 200px; max-height: 80px; object-fit: contain; margin-bottom: 16px;">
                    <div style="color: rgba(226, 232, 255, 0.9); font-size: 1rem; font-weight: 500; margin-top: 12px;">
                        {{ $platform['message'] }}
                    </div>
                </div>
            @else
                <div class="brand" style="text-align: center; margin-bottom: 24px;">
                    <h1 style="font-size: clamp(2rem, 3.8vw, 2.7rem); font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; margin: 0;">Allo<span style="color: var(--color-accent);">SSO</span></h1>
                </div>
            @endif
            <header class="panel-header">
                <h2>Complete Apple Sign-In</h2>
                <p>Please provide your email address to complete registration</p>
            </header>

            @if(isset($apple_name) && $apple_name)
                <div class="apple-info">
                    Welcome, {{ $apple_name }}! 🍎
                </div>
            @endif

            <form action="{{ route('auth.apple.email.submit') }}" method="POST">
                @csrf
                
                <div class="form-field-wrapper">
                    <div class="form-field-container">
                        <label for="email">Email Address *</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" placeholder="your@email.com" required autofocus>
                    </div>
                </div>

                <div class="form-field-wrapper">
                    <button class="submit-button form-field-container" type="submit">
                        Continue
                    </button>
                </div>
            </form>

            @if ($errors->any())
                <div class="alert" role="alert">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
</body>
</html>

