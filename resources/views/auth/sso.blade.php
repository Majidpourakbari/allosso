<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>AlloSSO • Access</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
</head>
<body>

<section class="login_register">
    <div class="content-place">
        <div class="form-place">
            @if(isset($platform) && $platform)
                <div style="text-align: center; margin-bottom: 24px;">
                    <img src="{{ asset('assets/allologos/' . $platform['logo']) }}" alt="{{ $platform['name'] }}" style="max-width: 200px; max-height: 80px; object-fit: contain; margin-bottom: 16px;">
                    <div style="color: #1b1b18; font-size: 1rem; font-weight: 500; margin-top: 12px;">
                        {{ $platform['message'] }}
                    </div>
                </div>
            @endif
            <div class="welcome">
                Welcome back
            </div>
            <a href="{{ route('auth.google') }}" class="login-attr">
                <img src="{{ asset('assets/images/login/google.svg') }}" alt="">
                Continue with google
            </a>
            <a href="{{ route('auth.apple') }}" class="login-attr">
                <img src="{{ asset('assets/images/login/dark-apple.svg') }}" alt="">
                Continue with Apple
            </a>
            <div class="or">
                <span>OR</span>
            </div>
            <form action="{{ route('authenticate') }}" method="POST" id="login_register_form">
                @csrf
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 no-padding">
                            <div class="input-place">
                                <label for="email">
                                    Email
                                </label>
                                <input type="email" name="email" id="email" value="{{ old('email', $email ?? '') }}" required placeholder="you@example.com" autofocus>
                            </div>
                        </div>
                        <div class="col-12 no-padding">
                            <div class="input-place">
                                <label for="security_code">
                                    Security Code
                                </label>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="padding: 8px 12px; background: #15423c; border: 1px solid #ddd; border-radius: 8px; min-width: 140px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                        <img src="{{ route('auth.captcha') }}?t={{ time() }}" alt="Security Code" onclick="this.src='{{ route('auth.captcha') }}?refresh=1&t='+Date.now()" style="cursor: pointer; max-width: 100%; height: auto;" title="Click to refresh">
                                    </div>
                                    <input type="text" name="security_code" id="security_code" required placeholder="Enter code" style="flex: 1; text-align: center; letter-spacing: 0.15em; font-size: 0.95rem; font-weight: 600; text-transform: uppercase;">
                                </div>
                            </div>
                        </div>
                        @error('email')
                            <div class="col-12 no-padding">
                                <div style="color: #F53003; font-size: 0.9rem; margin-top: 8px;">
                                    {{ $message }}
                                </div>
                            </div>
                        @enderror
                        @error('security_code')
                            <div class="col-12 no-padding">
                                <div style="color: #F53003; font-size: 0.9rem; margin-top: 8px;">
                                    {{ $message }}
                                </div>
                            </div>
                        @enderror
                        <div class="col-12 no-padding">
                            <button type="submit" class="btn-site btn--ripple btn-green">
                                Continue
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="img-back-place">
        <div class="cover-img"></div>
    </div>
</section>


<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/login.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
</body>
</html>
