<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlloSSO • Verify Access</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: dark;
            --color-primary-dark: #1f284b;
            --color-primary: #445c88;
            --color-accent: #ff7a00;
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

        .card {
            width: min(600px, 100%);
            background: linear-gradient(160deg, rgba(68, 92, 136, 0.14), rgba(31, 40, 75, 0.65));
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.07);
            backdrop-filter: blur(22px);
            box-shadow: 0 40px 70px rgba(6, 9, 20, 0.45);
            padding: clamp(36px, 5vw, 56px);
            display: grid;
            gap: 24px;
        }

        .badge {
            display: inline-flex;
            padding: 8px 16px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            color: rgba(255, 255, 255, 0.85);
            letter-spacing: 0.32em;
            text-transform: uppercase;
            font-size: 0.72rem;
        }

        h1 {
            margin: 0;
            font-size: clamp(1.9rem, 3vw, 2.2rem);
            font-weight: 600;
        }

        p {
            margin: 0;
            color: rgba(226, 232, 255, 0.74);
            line-height: 1.6;
        }

        .code-inputs {
            display: flex;
            gap: 16px;
            justify-content: center;
        }

        .code-inputs input {
            width: 56px;
            height: 64px;
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(9, 12, 24, 0.65);
            color: #f8fafc;
            font-size: 1.6rem;
            font-weight: 600;
            text-align: center;
            transition: border var(--duration) ease, box-shadow var(--duration) ease, transform var(--duration) ease;
        }

        .code-inputs input:focus {
            outline: none;
            border-color: rgba(255, 122, 0, 0.75);
            box-shadow: 0 0 0 4px rgba(255, 122, 0, 0.16);
            transform: translateY(-2px);
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

        .resend {
            text-align: center;
            color: rgba(226, 232, 255, 0.7);
            font-size: 0.92rem;
        }

        .resend button {
            background: none;
            border: none;
            color: #ff9b32;
            font-weight: 600;
            cursor: pointer;
            padding: 0;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid rgba(255, 100, 100, 0.3);
            background: rgba(255, 100, 100, 0.12);
            color: rgb(255, 196, 196);
            font-size: 0.9rem;
        }

        .status {
            padding: 12px 16px;
            border-radius: 12px;
            border: 1px solid rgba(76, 201, 240, 0.35);
            background: rgba(76, 201, 240, 0.16);
            color: rgba(220, 245, 255, 0.94);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="badge">Secure Sign-in</div>
        <div>
            <h1>Enter the 4-digit code</h1>
            <p>We sent a security code to <strong>{{ $email }}</strong>. Enter the digits below to continue.</p>
        </div>

        @if ($status)
            <div class="status">{{ $status }}</div>
        @endif

        @if ($errors->any())
            <div class="alert" role="alert">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form id="otp-form" action="{{ route('auth.verify') }}" method="POST" autocomplete="one-time-code">
            @csrf
            <input type="hidden" name="code" id="code-hidden">
            <div class="code-inputs" data-otp-inputs>
                @for ($i = 0; $i < 4; $i++)
                    <input type="text" inputmode="numeric" maxlength="1" aria-label="Digit {{ $i + 1 }}" required>
                @endfor
            </div>
            <button class="submit-button" type="submit">Verify and continue</button>
        </form>

        <div class="resend">
            Didn't get the code? <a href="{{ route('authenticate') }}" onclick="event.preventDefault(); document.getElementById('resend-form').submit();" style="color:#ff9b32; text-decoration:none; font-weight:600;">Resend email</a>
        </div>
        <form id="resend-form" action="{{ route('authenticate') }}" method="POST" style="display:none;">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
        </form>
        <form action="{{ route('auth.verify.cancel') }}" method="POST" style="display:flex; justify-content:center; margin-top:12px;">
            @csrf
            <button type="submit" style="background:none; border:none; color:rgba(226,232,255,0.7); font-size:0.9rem; cursor:pointer;">Use a different email</button>
        </form>
    </div>

    <script>
        const inputContainer = document.querySelector('[data-otp-inputs]');
        const inputs = Array.from(inputContainer.querySelectorAll('input'));
        const hiddenField = document.getElementById('code-hidden');

        function updateHiddenField() {
            hiddenField.value = inputs.map((input) => input.value).join('');
        }

        inputs.forEach((input, index) => {
            input.addEventListener('input', (event) => {
                const value = event.target.value.replace(/[^0-9]/g, '').slice(0, 1);
                event.target.value = value;
                if (value && inputs[index + 1]) {
                    inputs[index + 1].focus();
                }
                updateHiddenField();
            });

            input.addEventListener('keydown', (event) => {
                if (event.key === 'Backspace' && !input.value && inputs[index - 1]) {
                    inputs[index - 1].focus();
                }
            });
        });

        inputs[0]?.focus();
    </script>
</body>
</html>
