<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlloSSO • Access</title>
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
            font-size: 0.95rem;
            line-height: 1.6;
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
        }

        input[type="email"],
        input[type="text"] {
            width: 100%;
            padding: 16px 20px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.16);
            background: rgba(6, 9, 20, 0.58);
            color: #f1f3ff;
            font-size: 1rem;
            transition: border var(--duration) ease, background var(--duration) ease, box-shadow var(--duration) ease;
        }

        input[type="email"]:focus,
        input[type="text"]:focus {
            outline: none;
            border-color: rgba(255, 122, 0, 0.75);
            background: rgba(10, 16, 34, 0.92);
            box-shadow: 0 0 0 4px rgba(255, 122, 0, 0.16);
        }

        .security-code-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            margin: 18px 0;
        }

        .security-code-box {
            padding: 8px 12px;
            background: rgba(6, 9, 20, 0.8);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            min-width: 140px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .security-code-box img {
            max-width: 100%;
            height: auto;
            image-rendering: crisp-edges;
        }

        .security-code-input {
            flex: 1;
            max-width: 200px;
        }

        .security-code-label {
            font-size: 0.8rem;
            color: rgba(233, 239, 255, 0.86);
            text-align: center;
            margin-bottom: 6px;
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

        .divider {
            margin: 36px 0 24px;
            display: flex;
            align-items: center;
            gap: 14px;
            color: rgba(226, 232, 255, 0.58);
            font-size: 0.82rem;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            height: 1px;
            background: rgba(255, 255, 255, 0.12);
        }

        .social-buttons {
            display: grid;
            gap: 12px;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .social-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(12, 18, 32, 0.55);
            color: rgba(238, 242, 255, 0.92);
            font-size: 0.92rem;
            font-weight: 500;
            cursor: pointer;
            transition: border var(--duration) ease, background var(--duration) ease, transform var(--duration) ease;
            text-decoration: none;
        }

        .social-button:hover {
            border-color: rgba(255, 122, 0, 0.8);
            background: rgba(22, 31, 54, 0.8);
            transform: translateY(-1px);
        }

        .social-icon {
            width: 18px;
            height: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        @media (max-width: 940px) {
            .auth-panel {
                padding: 42px 28px;
            }
        }

        @media (max-width: 560px) {
            .social-buttons {
                grid-template-columns: 1fr;
            }
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
                <h2>Access your workspace</h2>
            </header>

            <form action="{{ route('authenticate') }}" method="POST" autocomplete="on">
                @csrf
                <div class="form-field-wrapper">
                    <div class="form-field-container">
                        <input id="email" name="email" type="email" inputmode="email" value="{{ old('email', $email ?? '') }}" placeholder="you@example.com" required autofocus>
                    </div>
                </div>

                <div class="form-field-wrapper">
                    <div class="form-field-container" style="display: flex; align-items: center; gap: 12px;">
                        <div class="security-code-box">
                            <img src="{{ route('auth.captcha') }}?t={{ time() }}" alt="Security Code" onclick="this.src='{{ route('auth.captcha') }}?refresh=1&t='+Date.now()" style="cursor: pointer;" title="Click to refresh">
                        </div>
                        <input id="security_code" name="security_code" type="text" placeholder="Enter code" required style="text-align: center; letter-spacing: 0.15em; font-size: 0.95rem; font-weight: 600; text-transform: uppercase; flex: 1; max-width: 200px;">
                    </div>
                </div>

                <div class="form-field-wrapper">
                    <button class="submit-button form-field-container" type="submit">
                        Continue
                    </button>
                </div>
            </form>

            @error('email')
                <div class="alert" role="alert">
                    {{ $message }}
                </div>
            @enderror

            @error('security_code')
                <div class="alert" role="alert">
                    {{ $message }}
                </div>
            @enderror

            <div class="divider" role="separator" aria-label="Other sign-in options">Or</div>

            <div class="social-buttons">
                <a class="social-button" href="#google" aria-label="Continue with Google">
                    <span class="social-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21.35 11.1H12v2.8h5.34c-.23 1.44-1.6 4.23-5.34 4.23a4.91 4.91 0 0 1 0-9.82 4.66 4.66 0 0 1 3.3 1.28l2.25-2.17A8.06 8.06 0 0 0 12 4a8 8 0 1 0 0 16c4.62 0 7.68-3.25 7.68-7.82 0-.52-.06-1.02-.18-1.48Z" fill="#ffffff" fill-opacity="0.78"/>
                        </svg>
                    </span>
                    Google
                </a>
                <a class="social-button" href="#apple" aria-label="Continue with Apple">
                    <span class="social-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M16.48 12.57c-.02-2.03 1.67-3 1.74-3.04-0.96-1.4-2.45-1.6-2.97-1.62-1.27-.13-2.48.75-3.12.75-.65 0-1.64-.73-2.7-.71-1.39.02-2.69.83-3.41 2.11-1.45 2.51-.37 6.22 1.04 8.26.7 1 1.52 2.13 2.6 2.09 1.04-.04 1.43-.68 2.69-.68 1.26 0 1.61.68 2.7.66 1.11-.02 1.82-1.03 2.5-2.03.79-1.15 1.12-2.27 1.14-2.33-.02-.01-2.19-.84-2.19-3.47Z" fill="#ffffff" fill-opacity="0.78"/>
                            <path d="M14.89 6.64c.54-.66.91-1.58.81-2.5-.78.03-1.73.52-2.29 1.18-.5.58-.95 1.52-.83 2.41.88.07 1.77-.46 2.31-1.09Z" fill="#ffffff" fill-opacity="0.78"/>
                        </svg>
                    </span>
                    Apple
                </a>
                <a class="social-button" href="#yahoo" aria-label="Continue with Yahoo">
                    <span class="social-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 6.5h-3.68L14.5 13l-.16 4.5h-2.65L11.5 13 8.66 6.5H5l4.53 9-2.27 4h2.89l1.92-3.7.14 3.7H15l.14-3.69L21 6.5Z" fill="#ffffff" fill-opacity="0.78"/>
                        </svg>
                    </span>
                    Yahoo
                </a>
                <a class="social-button" href="#x" aria-label="Continue with X">
                    <span class="social-icon" aria-hidden="true">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18.23 4h2.33l-5.1 5.82 5.97 8.18h-4.67l-3.65-4.79-4.18 4.79H6.6l5.45-6.24L6.34 4h4.8l3.27 4.37L18.23 4Z" fill="#ffffff" fill-opacity="0.78"/>
                        </svg>
                    </span>
                    X
                </a>
            </div>
        </section>
    </div>
</body>
</html>
