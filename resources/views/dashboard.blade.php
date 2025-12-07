<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>AlloSSO • Dashboard</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
    <style>
        @media screen and (max-width: 991.98px) {
            .login_register .content-place {
                width: 100% !important;
                padding: 0 !important;
            }
            .login_register .content-place .form-place {
                width: 100% !important;
                padding: 2rem 1rem !important;
                margin: 0 !important;
                max-width: 100% !important;
            }
        }
        @media screen and (max-width: 767.98px) {
            .login_register .content-place {
                width: 100% !important;
                height: 100vh !important;
                position: fixed !important;
                left: 0 !important;
                top: 0 !important;
                transform: none !important;
                padding: 0 !important;
            }
            .login_register .content-place .form-place {
                width: 100% !important;
                padding: 2rem 1rem !important;
                margin: 0 !important;
                max-width: 100% !important;
            }
        }
        @media screen and (max-width: 575.98px) {
            .login_register .content-place .form-place {
                width: 100% !important;
                padding: 2rem 1rem !important;
            }
        }
        .access-list {
            list-style: none;
            padding: 0;
            margin: 24px 0 0;
            display: grid;
            gap: 16px;
        }
        .access-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 18px 20px;
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            background: #fff;
            transition: all 0.2s ease;
        }
        .access-item:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transform: translateY(-1px);
        }
        .access-label {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1rem;
            font-weight: 500;
            color: #1b1b18;
        }
        .access-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: rgba(0, 0, 0, 0.05);
        }
        .access-action {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 8px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            background: #1b1b18;
            color: #fff;
            font-size: 0.9rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s ease;
            user-select: none;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
        }
        .access-action:hover {
            background: #2a2a27;
            transform: translateY(-1px);
        }
        .badge-exclusive {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 999px;
            background: rgba(255, 122, 0, 0.1);
            color: #ff7a00;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            font-weight: 600;
            margin-left: 8px;
        }
        .welcome-section {
            margin-bottom: 32px;
        }
        .welcome-section h2 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1b1b18;
            margin: 0 0 8px 0;
        }
        .welcome-section p {
            color: #706f6c;
            font-size: 0.9rem;
            margin: 0;
        }
        .logout-btn {
            position: absolute;
            top: 20px;
            right: 20px;
        }
        .logout-btn form {
            display: inline;
        }
        .logout-btn button {
            padding: 10px 20px;
            background: #1b1b18;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .logout-btn button:hover {
            background: #2a2a27;
        }
        @if (session('status'))
        .status-message {
            padding: 12px 16px;
            background: rgba(255, 122, 0, 0.1);
            border: 1px solid rgba(255, 122, 0, 0.2);
            border-radius: 8px;
            color: #ff7a00;
            font-size: 0.9rem;
            margin-bottom: 24px;
        }
        @endif
        .explore-more-section {
            margin-top: 48px;
            padding: 24px;
            background: #fff;
            border-radius: 12px;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }
        .explore-more-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1b1b18;
            margin-bottom: 20px;
            text-align: center;
        }
        .logo-slider-container {
            position: relative;
            overflow: hidden;
            width: 100%;
            padding: 20px 0;
        }
        .logo-slider-track {
            display: flex;
            gap: 24px;
            animation: slide 30s linear infinite;
        }
        .logo-slider-track:hover {
            animation-play-state: paused;
        }
        .logo-slide-item {
            flex-shrink: 0;
            width: 120px;
            height: 80px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.2s ease;
            border-radius: 8px;
            padding: 12px;
            background: rgba(0, 0, 0, 0.02);
        }
        .logo-slide-item:hover {
            transform: scale(1.05);
            background: rgba(0, 0, 0, 0.05);
        }
        .logo-slide-item img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        @keyframes slide {
            0% {
                transform: translateX(0);
            }
            100% {
                transform: translateX(-50%);
            }
        }
        .logo-slider-track.duplicate {
            animation: slide 30s linear infinite;
        }
    </style>
</head>
<body>

<section class="login_register">
    <div class="content-place" style="position: relative;">
        <div class="logout-btn">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit">Sign Out</button>
            </form>
        </div>
        <div class="form-place">
            <div class="welcome-section">
                <h2>Access Overview</h2>
                <p>Unified access fabric for every workspace.</p>
            </div>
            @if (session('status'))
                <div class="status-message">
                    {{ session('status') }}
                </div>
            @endif
            @if(isset($platform) && $platform)
                <div style="text-align: center; margin-bottom: 24px;">
                    <img src="{{ asset('assets/allologos/' . $platform['logo']) }}" alt="{{ $platform['name'] }}" style="max-width: 200px; max-height: 80px; object-fit: contain; margin-bottom: 16px;">
                    <div style="color: #1b1b18; font-size: 1rem; font-weight: 500; margin-top: 12px;">
                        {{ $platform['message'] }}
                    </div>
                </div>
            @endif
            <ul class="access-list">
                @php
                    $showAll = !isset($platform) || !$platform;
                    $platformDomain = isset($platform) && $platform ? $platform['domain'] : null;
                @endphp

                @if($showAll || $platformDomain === 'allolancer.com')
                <li class="access-item">
                    <div class="access-label">
                        <span class="access-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 6H4v12h16V6Z" stroke="#ff7a00" stroke-width="1.5" stroke-linejoin="round"/>
                                <path d="M4 10h16" stroke="#ff7a00" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </span>
                        allolancer.com
                    </div>
                    <a class="access-action" href="#" onclick="handleLogin('https://allolancer.com/allosso.php', event); return false;">Login</a>
                </li>
                @endif

                @if($showAll)
                <li class="access-item">
                    <div class="access-label">
                        <span class="access-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 3 4 9v12h6v-6h4v6h6V9l-8-6Z" stroke="#8ce9b6" stroke-width="1.5" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        allo-ai.io
                    </div>
                    <a class="access-action" href="#" onclick="handleLogin('https://allo-ai.io', event); return false;">Login</a>
                </li>
                <li class="access-item">
                    <div class="access-label">
                        <span class="access-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M5 6h14v4H5V6Zm0 6h14v6H5v-6Z" stroke="#9cc9ff" stroke-width="1.5" stroke-linejoin="round"/>
                                <path d="M9 6v10" stroke="#9cc9ff" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </span>
                        alloacademy.se
                    </div>
                    <a class="access-action" href="#" onclick="handleLogin('https://alloacademy.se', event); return false;">Login</a>
                </li>
                <li class="access-item">
                    <div class="access-label">
                        <span class="access-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 3 4 9v12h6v-6h4v6h6V9l-8-6Z" stroke="#8ce9b6" stroke-width="1.5" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        allomeet.com
                    </div>
                    <a class="access-action" href="#" onclick="handleLogin('https://allomeet.com', event); return false;">Login</a>
                </li>
                @endif

                @if($showAll && ($user->access_erp ?? false))
                <li class="access-item">
                    <div class="access-label">
                        <span class="access-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M4 4h16v12H4V4Z" stroke="#ff7a00" stroke-width="1.5" stroke-linejoin="round"/>
                                <path d="M4 12h16" stroke="#ff7a00" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </span>
                        ERP
                        <span class="badge-exclusive">Exclusive Access</span>
                    </div>
                    <a class="access-action" href="#" onclick="handleLogin('https://allo-sso.com/erp/allosso.php', event); return false;">Login</a>
                </li>
                @endif

                @if($showAll && ($user->access_admin_portal ?? false))
                <li class="access-item">
                    <div class="access-label">
                        <span class="access-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6 6h12v12H6V6Z" stroke="#ff7a00" stroke-width="1.5" stroke-linejoin="round"/>
                                <path d="M9 9h6v6H9V9Z" stroke="#ff7a00" stroke-width="1.5" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        Admin Portal
                        <span class="badge-exclusive">Exclusive Access</span>
                    </div>
                    <a class="access-action" href="#" onclick="handleLogin('https://admin.allosso.com', event); return false;">Login</a>
                </li>
                @endif

                @if($showAll && ($user->access_ai_developer ?? false))
                <li class="access-item">
                    <div class="access-label">
                        <span class="access-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 4v16M4 12h16" stroke="#ff7a00" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </span>
                        AI Developer
                        <span class="badge-exclusive">Exclusive Access</span>
                    </div>
                    <a class="access-action" href="#" onclick="handleLogin('https://ai-dev.allosso.com', event); return false;">Login</a>
                </li>
                @endif

                @if(isset($platform) && $platform && $platformDomain === 'alloai.com')
                <li class="access-item">
                    <div class="access-label">
                        <span class="access-icon" aria-hidden="true">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 4v16M4 12h16" stroke="#ff7a00" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </span>
                        AlloAI
                    </div>
                    <a class="access-action" href="#" onclick="handleLogin('https://alloai.com', event); return false;">Login</a>
                </li>
                @endif
            </ul>

            @if(isset($platform) && $platform)
                @php
                    $allPlatforms = [
                        [
                            'name' => 'AlloLancer',
                            'domain' => 'allolancer.com',
                            'logo' => 'allolaner.jpg',
                            'url' => 'https://allolancer.com/allosso.php'
                        ],
                        [
                            'name' => 'AlloAI',
                            'domain' => 'alloai.com',
                            'logo' => 'alloai.jpg',
                            'url' => 'https://allo-ai.io'
                        ],
                        [
                            'name' => 'AlloAcademy',
                            'domain' => 'alloacademy.se',
                            'logo' => 'alloacademy.jpg',
                            'url' => 'https://alloacademy.se'
                        ],
                        [
                            'name' => 'AlloMeet',
                            'domain' => 'allomeet.com',
                            'logo' => 'allomeet.jpg',
                            'url' => 'https://allomeet.com'
                        ]
                    ];
                    
                    $currentDomain = $platform['domain'];
                    // Filter out current platform - match by domain
                    $otherPlatforms = array_filter($allPlatforms, function($p) use ($currentDomain) {
                        // Match exact domain or check if domains are related (e.g., alloai.com vs allo-ai.io)
                        if ($p['domain'] === $currentDomain) {
                            return false;
                        }
                        // Handle alloai.com and allo-ai.io as same platform
                        if (($p['domain'] === 'alloai.com' || $p['domain'] === 'allo-ai.io') && 
                            ($currentDomain === 'alloai.com' || $currentDomain === 'allo-ai.io')) {
                            return false;
                        }
                        return true;
                    });
                @endphp

                @if(count($otherPlatforms) > 0)
                <div class="explore-more-section">
                    <div class="explore-more-title">Explore More</div>
                    <div class="logo-slider-container">
                        <div class="logo-slider-track">
                            @foreach($otherPlatforms as $otherPlatform)
                                <div class="logo-slide-item" onclick="handleLogin('{{ $otherPlatform['url'] }}', event);" title="{{ $otherPlatform['name'] }}">
                                    <img src="{{ asset('assets/allologos/' . $otherPlatform['logo']) }}" alt="{{ $otherPlatform['name'] }}">
                                </div>
                            @endforeach
                            @foreach($otherPlatforms as $otherPlatform)
                                <div class="logo-slide-item" onclick="handleLogin('{{ $otherPlatform['url'] }}', event);" title="{{ $otherPlatform['name'] }}">
                                    <img src="{{ asset('assets/allologos/' . $otherPlatform['logo']) }}" alt="{{ $otherPlatform['name'] }}">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
    <div class="img-back-place">
        <div class="cover-img"></div>
    </div>
</section>

@php
    $showAccountTypeModal = false;
    if (isset($platform) && $platform && $platform['domain'] === 'allolancer.com') {
        $user = auth()->user();
        // Show modal if user hasn't selected account type yet
        if ($user && !$user->allolancer_account_type) {
            $showAccountTypeModal = true;
        }
    }
@endphp

@if($showAccountTypeModal)
<!-- Account Type Selection Modal -->
<div id="accountTypeModal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 10000; display: flex; align-items: center; justify-content: center;">
    <div style="background: #fff; border-radius: 12px; padding: 32px; max-width: 500px; width: 90%; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);">
        <h2 style="font-size: 1.5rem; font-weight: 600; color: #1b1b18; margin-bottom: 16px; text-align: center;">Select Your Account Type</h2>
        <p style="color: #706f6c; font-size: 0.9rem; margin-bottom: 24px; text-align: center;">Please choose your account type to continue</p>
        
        <form action="{{ route('allolancer.account-type') }}" method="POST" id="accountTypeForm">
            @csrf
            <div style="display: grid; gap: 16px; margin-bottom: 24px;">
                <label style="display: flex; align-items: center; padding: 20px; border: 2px solid #e9efec; border-radius: 12px; cursor: pointer; transition: all 0.2s ease; background: #fff;">
                    <input type="radio" name="account_type" value="freelancer" required style="margin-right: 12px; width: 20px; height: 20px; cursor: pointer;">
                    <div>
                        <div style="font-weight: 600; color: #1b1b18; margin-bottom: 4px;">Freelancer</div>
                        <div style="font-size: 0.85rem; color: #706f6c;">I want to offer my services</div>
                    </div>
                </label>
                
                <label style="display: flex; align-items: center; padding: 20px; border: 2px solid #e9efec; border-radius: 12px; cursor: pointer; transition: all 0.2s ease; background: #fff;">
                    <input type="radio" name="account_type" value="employer" required style="margin-right: 12px; width: 20px; height: 20px; cursor: pointer;">
                    <div>
                        <div style="font-weight: 600; color: #1b1b18; margin-bottom: 4px;">Employer</div>
                        <div style="font-size: 0.85rem; color: #706f6c;">I want to hire freelancers</div>
                    </div>
                </label>
            </div>
            
            <button type="submit" style="width: 100%; padding: 14px; background: #1b1b18; color: #fff; border: none; border-radius: 8px; font-size: 1rem; font-weight: 600; cursor: pointer; transition: all 0.2s ease;">
                Continue
            </button>
        </form>
    </div>
</div>

<style>
    #accountTypeModal label:hover {
        border-color: #1b1b18;
        background: rgba(27, 27, 24, 0.02);
    }
    #accountTypeModal input[type="radio"]:checked + div {
        color: #1b1b18;
    }
    #accountTypeModal label:has(input[type="radio"]:checked) {
        border-color: #1b1b18;
        background: rgba(27, 27, 24, 0.05);
    }
    #accountTypeModal button:hover {
        background: #2a2a27;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('accountTypeForm');
        const modal = document.getElementById('accountTypeModal');
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => {
                if (response.ok) {
                    modal.style.display = 'none';
                    window.location.reload();
                } else {
                    alert('An error occurred. Please try again.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    });
</script>
@endif

<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/login.js') }}"></script>
<script src="{{ asset('assets/js/jquery.validate.min.js') }}"></script>
<script>
    function handleLogin(url, event) {
        const allohash = '{{ $user->allohash }}';
        
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        
        const separator = url.includes('?') ? '&' : '?';
        const finalUrl = url + separator + 'allohash=' + encodeURIComponent(allohash);
        
        window.location.href = finalUrl;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const accessActions = document.querySelectorAll('.access-action');
        accessActions.forEach(function(action) {
            action.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                return false;
            });
        });
    });
</script>
</body>
</html>
