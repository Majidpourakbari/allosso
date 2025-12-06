<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>AlloSSO • Login</title>
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
                Enter Password
            </div>
            <div style="text-align: center; color: #706f6c; font-size: 0.9rem; margin-bottom: 24px;">
                {{ $email }}
            </div>
            <form action="{{ route('auth.password') }}" method="POST" id="login_register_form">
                @csrf
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12 no-padding">
                            <div class="input-place">
                                <label for="password">
                                    Password
                                </label>
                                <input type="password" name="password" id="password" required placeholder="Enter your password" autofocus>
                            </div>
                        </div>
                        @error('password')
                            <div class="col-12 no-padding">
                                <div style="color: #F53003; font-size: 0.9rem; margin-top: 8px;">
                                    {{ $message }}
                                </div>
                            </div>
                        @enderror
                        <div class="col-12 no-padding">
                            <button type="submit" class="btn-site btn--ripple btn-green">
                                Login
                            </button>
                            <div class="change-login">
                                <a href="{{ route('auth.show') }}">
                                    Back to login
                                </a>
                            </div>
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
