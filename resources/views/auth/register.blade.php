<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlloSSO • Register</title>
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

        input[type="text"],
        input[type="email"],
        input[type="tel"],
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

        input:focus {
            outline: none;
            border-color: rgba(255, 122, 0, 0.75);
            background: rgba(10, 16, 34, 0.92);
            box-shadow: 0 0 0 4px rgba(255, 122, 0, 0.16);
        }

        .password-strength {
            margin-top: 8px;
            height: 4px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 2px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .password-strength.weak .password-strength-bar {
            width: 33%;
            background: linear-gradient(90deg, #ff4444, #ff6666);
        }

        .password-strength.medium .password-strength-bar {
            width: 66%;
            background: linear-gradient(90deg, #ffaa00, #ffcc00);
        }

        .password-strength.strong .password-strength-bar {
            width: 100%;
            background: linear-gradient(90deg, #00ff88, #00cc66);
        }

        .password-requirements {
            margin-top: 8px;
            font-size: 0.8rem;
            color: rgba(226, 232, 255, 0.6);
        }

        .password-requirements span {
            display: block;
            margin: 4px 0;
            transition: color 0.3s ease;
        }

        .password-requirements span.valid {
            color: #00ff88;
        }

        .password-requirements span.invalid {
            color: rgba(226, 232, 255, 0.4);
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

        .email-display {
            text-align: center;
            color: rgba(255, 122, 0, 0.9);
            font-weight: 500;
            margin-bottom: 8px;
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
                <h2>Complete Registration</h2>
                <p class="email-display">{{ $email }}</p>
            </header>

            <form action="{{ route('auth.register') }}" method="POST" id="registerForm">
                @csrf
                
                <div class="form-field-wrapper">
                    <div class="form-field-container">
                        <label for="first_name">First Name *</label>
                        <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" placeholder="First Name" required autofocus>
                    </div>
                </div>

                <div class="form-field-wrapper">
                    <div class="form-field-container">
                        <label for="last_name">Last Name *</label>
                        <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" placeholder="Last Name" required>
                    </div>
                </div>

                <div class="form-field-wrapper">
                    <div class="form-field-container">
                        <label for="phone">Phone Number</label>
                        <input id="phone" name="phone" type="tel" value="{{ old('phone') }}" placeholder="Phone Number (optional)">
                    </div>
                </div>

                <div class="form-field-wrapper">
                    <div class="form-field-container">
                        <label for="password">Password *</label>
                        <input id="password" name="password" type="password" placeholder="Password" required>
                        <div class="password-strength" id="passwordStrength">
                            <div class="password-strength-bar"></div>
                        </div>
                        <div class="password-requirements" id="passwordRequirements">
                            <span id="req-length" class="invalid">At least 8 characters</span>
                            <span id="req-upper" class="invalid">One uppercase letter</span>
                            <span id="req-lower" class="invalid">One lowercase letter</span>
                            <span id="req-number" class="invalid">One number</span>
                            <span id="req-special" class="invalid">One special character</span>
                        </div>
                    </div>
                </div>

                <div class="form-field-wrapper">
                    <div class="form-field-container">
                        <label for="password_confirmation">Confirm Password *</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" placeholder="Confirm Password" required>
                        <div id="passwordMatch" style="margin-top: 8px; font-size: 0.8rem; color: rgba(226, 232, 255, 0.4);"></div>
                    </div>
                </div>

                <div class="form-field-wrapper">
                    <button class="submit-button form-field-container" type="submit" id="submitButton" disabled>
                        Register
                    </button>
                </div>
            </form>

            @if ($errors->any())
                <div class="alert" role="alert">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @enderror
        </section>
    </div>

    <script>
        const passwordInput = document.getElementById('password');
        const passwordConfirmInput = document.getElementById('password_confirmation');
        const passwordStrength = document.getElementById('passwordStrength');
        const submitButton = document.getElementById('submitButton');
        const registerForm = document.getElementById('registerForm');

        function checkPasswordStrength(password) {
            let strength = 0;
            const requirements = {
                length: password.length >= 8,
                upper: /[A-Z]/.test(password),
                lower: /[a-z]/.test(password),
                number: /[0-9]/.test(password),
                special: /[^A-Za-z0-9]/.test(password)
            };

            // Update requirement indicators
            document.getElementById('req-length').className = requirements.length ? 'valid' : 'invalid';
            document.getElementById('req-upper').className = requirements.upper ? 'valid' : 'invalid';
            document.getElementById('req-lower').className = requirements.lower ? 'valid' : 'invalid';
            document.getElementById('req-number').className = requirements.number ? 'valid' : 'invalid';
            document.getElementById('req-special').className = requirements.special ? 'valid' : 'invalid';

            // Calculate strength
            if (requirements.length) strength++;
            if (requirements.upper) strength++;
            if (requirements.lower) strength++;
            if (requirements.number) strength++;
            if (requirements.special) strength++;

            // Update strength bar
            passwordStrength.className = 'password-strength';
            if (strength <= 2) {
                passwordStrength.classList.add('weak');
            } else if (strength <= 4) {
                passwordStrength.classList.add('medium');
            } else {
                passwordStrength.classList.add('strong');
            }

            return strength === 5;
        }

        function checkPasswordMatch() {
            const matchDiv = document.getElementById('passwordMatch');
            if (passwordConfirmInput.value === '') {
                matchDiv.textContent = '';
                matchDiv.style.color = 'rgba(226, 232, 255, 0.4)';
                return false;
            }
            
            if (passwordInput.value === passwordConfirmInput.value) {
                matchDiv.textContent = '✓ Passwords match';
                matchDiv.style.color = '#00ff88';
                return true;
            } else {
                matchDiv.textContent = '✗ Passwords do not match';
                matchDiv.style.color = '#ff4444';
                return false;
            }
        }

        function validateForm() {
            const firstName = document.getElementById('first_name').value.trim();
            const lastName = document.getElementById('last_name').value.trim();
            const isPasswordStrong = checkPasswordStrength(passwordInput.value);
            const passwordsMatch = checkPasswordMatch();

            if (firstName && lastName && isPasswordStrong && passwordsMatch) {
                submitButton.disabled = false;
            } else {
                submitButton.disabled = true;
            }
        }

        passwordInput.addEventListener('input', () => {
            checkPasswordStrength(passwordInput.value);
            checkPasswordMatch();
            validateForm();
        });

        passwordConfirmInput.addEventListener('input', () => {
            checkPasswordMatch();
            validateForm();
        });

        document.getElementById('first_name').addEventListener('input', validateForm);
        document.getElementById('last_name').addEventListener('input', validateForm);
    </script>
</body>
</html>

