<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlloSSO • Login</title>
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

        .email-display {
            text-align: center;
            color: rgba(255, 122, 0, 0.9);
            font-weight: 500;
            margin-top: 8px;
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

        input[type="password"] {
            width: 100%;
            padding: 16px 20px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.16);
            background: rgba(6, 9, 20, 0.58);
            color: #f1f3ff;
            font-size: 1rem;
            transition: border var(--duration) ease, background var(--duration) ease, box-shadow var(--duration) ease;
        }

        input[type="password"]:focus {
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
            transition: transform var(--duration) ease, box-shadow var(--duration) ease;
            width: 100%;
        }

        .submit-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 32px rgba(255, 122, 0, 0.26);
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
            <div class="brand" style="text-align: center; margin-bottom: 24px;">
                <h1 style="font-size: clamp(2rem, 3.8vw, 2.7rem); font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; margin: 0;">Allo<span style="color: var(--color-accent);">SSO</span></h1>
            </div>
            <header class="panel-header">
                <h2>Enter Password</h2>
                <p class="email-display">{{ $email }}</p>
            </header>

            <form action="{{ route('auth.password') }}" method="POST" autocomplete="on">
                @csrf
                <div class="form-field-wrapper">
                    <div class="form-field-container">
                        <input id="password" name="password" type="password" placeholder="Password" required autofocus>
                    </div>
                </div>

                <div class="form-field-wrapper">
                    <button class="submit-button form-field-container" type="submit">
                        Continue
                    </button>
                </div>
            </form>

            @error('password')
                <div class="alert" role="alert">
                    {{ $message }}
                </div>
            @enderror
        </section>
    </div>
</body>
</html>

