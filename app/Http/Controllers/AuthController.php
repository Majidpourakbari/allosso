<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AppleAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * Detect platform from referrer or query parameter
     */
    private function detectPlatform(Request $request): ?array
    {
        // Check query parameter first
        $platformParam = $request->query('platform');
        if ($platformParam) {
            return $this->getPlatformInfo($platformParam);
        }

        // Check referrer
        $referer = $request->header('referer');
        if ($referer) {
            $refererHost = parse_url($referer, PHP_URL_HOST);
            if ($refererHost) {
                // Remove www. prefix
                $refererHost = preg_replace('/^www\./', '', $refererHost);
                
                // Map domains to platforms
                if (strpos($refererHost, 'allolancer.com') !== false) {
                    return $this->getPlatformInfo('allolancer');
                } elseif (strpos($refererHost, 'alloai.com') !== false || strpos($refererHost, 'allo-ai.com') !== false || strpos($refererHost, 'allo-ai.io') !== false) {
                    return $this->getPlatformInfo('alloai');
                }
            }
        }

        return null;
    }

    /**
     * Get platform information
     */
    private function getPlatformInfo(string $platform): array
    {
        $platforms = [
            'allolancer' => [
                'name' => 'AlloLancer',
                'domain' => 'allolancer.com',
                'logo' => 'allolaner.jpg', // Note: actual filename is allolaner.jpg
                'message' => 'To access AlloLancer, please login or register',
            ],
            'alloai' => [
                'name' => 'AlloAI',
                'domain' => 'allo-ai.io',
                'logo' => 'alloai.jpg',
                'message' => 'To access AlloAI, please login or register',
            ],
        ];

        return $platforms[strtolower($platform)] ?? null;
    }

    public function show(Request $request): View|RedirectResponse
    {
        try {
            if (Auth::check()) {
                return redirect()->route('dashboard');
            }

            // Always generate new random security code on page load
            $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // Exclude confusing characters
            $securityCode = '';
            for ($i = 0; $i < 5; $i++) {
                $securityCode .= $characters[random_int(0, strlen($characters) - 1)];
            }
            $request->session()->put('security_code', $securityCode);

            // Store platform in session if detected
            $platform = $this->detectPlatform($request);
            if ($platform) {
                $request->session()->put('platform', $platform);
            } else {
                $platform = $request->session()->get('platform');
            }

            return view('auth.sso', [
                'email' => old('email', ''),
                'platform' => $platform,
            ]);
        } catch (\Exception $e) {
            \Log::error('Auth show error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            // Return a simple error view instead of crashing
            // Return error view
            return response()->view('errors.500', [
                'message' => 'An error occurred. Please try again later.',
            ], 500);
        }
    }

    public function captcha(Request $request): Response
    {
        // Always generate new random code when refresh is requested
        if ($request->has('refresh')) {
            $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // Exclude confusing characters
            $securityCode = '';
            for ($i = 0; $i < 5; $i++) {
                $securityCode .= $characters[random_int(0, strlen($characters) - 1)];
            }
            $request->session()->put('security_code', $securityCode);
        }
        
        // Get code from session or generate new one
        $code = $request->session()->get('security_code');
        if (!$code) {
            $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
            $code = '';
            for ($i = 0; $i < 5; $i++) {
                $code .= $characters[random_int(0, strlen($characters) - 1)];
            }
            $request->session()->put('security_code', $code);
        }
        
        // Create image
        $width = 140;
        $height = 50;
        $image = imagecreatetruecolor($width, $height);
        
        // Colors
        $bgColor = imagecolorallocate($image, 21, 66, 60);
        $textColor = imagecolorallocate($image, 255, 255, 255);
        $lineColor = imagecolorallocatealpha($image, 255, 255, 255, 80);
        $noiseColor = imagecolorallocatealpha($image, 255, 255, 255, 60);
        
        // Fill background
        imagefill($image, 0, 0, $bgColor);
        
        // Add noise dots
        for ($i = 0; $i < 100; $i++) {
            imagesetpixel($image, rand(0, $width), rand(0, $height), $noiseColor);
        }
        
        // Add random lines
        for ($i = 0; $i < 5; $i++) {
            imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $lineColor);
        }
        
        // Font settings
        $fontSize = 5;
        $x = 20;
        $y = 30;
        
        // Draw each character with slight rotation and offset
        for ($i = 0; $i < strlen($code); $i++) {
            $char = $code[$i];
            $angle = rand(-15, 15);
            $charX = $x + ($i * 24);
            $charY = $y + rand(-3, 3);
            
            // Draw character
            imagestring($image, $fontSize, $charX, $charY, $char, $textColor);
            
            // Add some distortion with small lines
            imageline($image, $charX - 2, $charY - 2, $charX + 18, $charY + 2, $noiseColor);
        }
        
        // Output image
        ob_start();
        imagepng($image);
        $imageData = ob_get_contents();
        ob_end_clean();
        imagedestroy($image);
        
        return response($imageData)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'security_code' => ['required', 'string', 'size:5'],
        ]);

        // Verify security code
        $sessionCode = $request->session()->get('security_code');
        if (!$sessionCode || strtoupper($validated['security_code']) !== strtoupper($sessionCode)) {
            return back()
                ->withInput()
                ->withErrors(['security_code' => 'The security code is incorrect.']);
        }

        // Regenerate security code after successful verification
        $request->session()->forget('security_code');

        $email = strtolower($validated['email']);

        // Check if user exists
        $user = User::where('email', $email)->first();

        if (!$user) {
            // New user - redirect to registration
            $request->session()->put('registration_email', $email);
            return redirect()->route('auth.register.show');
        }

        // Existing user - redirect to password login
        $request->session()->put('login_email', $email);
        return redirect()->route('auth.password.show');
    }

    public function showPasswordLogin(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $email = $request->session()->get('login_email');
        if (!$email) {
            return redirect()->route('auth.show');
        }

        // Get platform from session
        $platform = $request->session()->get('platform');

        return view('auth.password', [
            'email' => $email,
            'platform' => $platform,
        ]);
    }

    public function passwordLogin(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string'],
        ]);

        $email = $request->session()->get('login_email');
        if (!$email) {
            return redirect()->route('auth.show');
        }

        $user = User::where('email', $email)->first();
        if (!$user) {
            return redirect()->route('auth.show');
        }

        // Check password
        if (!Hash::check($validated['password'], $user->password)) {
            return back()
                ->withInput()
                ->withErrors(['password' => 'The password is incorrect.']);
        }

        $request->session()->forget('login_email');

        // Login user
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('status', 'Welcome back to AlloSSO!');
    }

    public function showRegister(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $email = $request->session()->get('registration_email');
        if (!$email) {
            return redirect()->route('auth.show');
        }

        // Get platform from session
        $platform = $request->session()->get('platform');

        return view('auth.register', [
            'email' => $email,
            'platform' => $platform,
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $email = $request->session()->get('registration_email');
        if (!$email) {
            return redirect()->route('auth.show');
        }

        // Generate unique allohash
        do {
            $uniqueCode = Str::random(32) . time() . random_int(1000, 9999);
            $allohash = Hash::make($uniqueCode);
        } while (User::where('allohash', $allohash)->exists());

        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $email,
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'allohash' => $allohash,
        ]);

        $request->session()->forget('registration_email');

        // Login user directly
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('status', 'Registration complete. Welcome to AlloSSO!');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.show');
    }

    public function saveAllolancerAccountType(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'account_type' => ['required', 'string', 'in:freelancer,employer'],
        ]);

        $user = Auth::user();
        if ($user) {
            $user->allolancer_account_type = $validated['account_type'];
            $user->save();
        }

        return redirect()->route('dashboard')->with('status', 'Account type saved successfully!');
    }

    /**
     * Redirect to Google authorization page
     */
    public function redirectToGoogle(Request $request): RedirectResponse
    {
        // Store platform in session if exists
        $platform = $this->detectPlatform($request);
        if ($platform) {
            $request->session()->put('platform', $platform);
        }

        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email'])
            ->redirect();
    }

    /**
     * Handle Google callback
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $googleId = $googleUser->getId();
            $email = strtolower($googleUser->getEmail());
            $name = $googleUser->getName();
            $avatar = $googleUser->getAvatar();

            // Find or create user
            $user = User::where('google_id', $googleId)->first();

            if (!$user && $email) {
                // Try to find by email
                $user = User::where('email', $email)->first();
                
                if ($user) {
                    // Link Google ID to existing user
                    $user->google_id = $googleId;
                    if (!$user->name && $name) {
                        $user->name = $name;
                    }
                    $user->save();
                }
            }

            if (!$user) {
                if (!$email) {
                    return redirect()->route('auth.show')
                        ->withErrors(['error' => 'Unable to retrieve email from Google account.']);
                }

                // Generate unique allohash
                do {
                    $uniqueCode = Str::random(32) . time() . random_int(1000, 9999);
                    $allohash = Hash::make($uniqueCode);
                } while (User::where('allohash', $allohash)->exists());

                $user = User::create([
                    'name' => $name ?? 'Google User',
                    'email' => $email,
                    'google_id' => $googleId,
                    'password' => Hash::make(Str::random(32)), // Random password since Google auth doesn't use password
                    'allohash' => $allohash,
                ]);
            }

            // Login user
            Auth::login($user);
            $request->session()->regenerate();

            return redirect()->route('dashboard')->with('status', 'Welcome to AlloSSO!');

        } catch (\Exception $e) {
            return redirect()->route('auth.show')
                ->withErrors(['error' => 'Google authentication failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Redirect to Apple authorization page
     */
    public function redirectToApple(Request $request): RedirectResponse
    {
        try {
            // Check if Apple is configured
            if (!config('services.apple.client_id') || !config('services.apple.team_id')) {
                \Log::warning('Apple Sign-In not configured');
                return redirect()->route('auth.show')
                    ->withErrors(['error' => 'Apple Sign-In is not configured. Please contact administrator.']);
            }

            $state = bin2hex(random_bytes(16));
            $request->session()->put('apple_state', $state);
            
            // Store platform in session if exists
            $platform = $this->detectPlatform($request);
            if ($platform) {
                $request->session()->put('platform', $platform);
            }

            $appleAuth = new AppleAuthService();
            $authUrl = $appleAuth->getAuthorizationUrl($state);

            return redirect()->away($authUrl);
        } catch (\Exception $e) {
            \Log::error('Apple redirect error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->route('auth.show')
                ->withErrors(['error' => 'Apple authentication error: ' . $e->getMessage()]);
        }
    }

    /**
     * Handle Apple callback
     */
    public function handleAppleCallback(Request $request): RedirectResponse
    {
        try {
            // Apple sends data via POST
            $code = $request->input('code');
            $state = $request->input('state');
            $userData = $request->input('user'); // Only on first authorization

            // Log for debugging
            \Log::info('Apple Callback', [
                'has_code' => !empty($code),
                'has_state' => !empty($state),
                'has_user_data' => !empty($userData),
                'session_id' => $request->session()->getId(),
            ]);

            // Verify state - but be more lenient for first time users
            $sessionState = $request->session()->get('apple_state');
            if ($sessionState && $state && $state !== $sessionState) {
                \Log::warning('Apple state mismatch', [
                    'session_state' => $sessionState,
                    'received_state' => $state,
                ]);
                // Don't fail immediately, continue if we have a code
                if (!$code) {
                    return redirect()->route('auth.show')
                        ->withErrors(['error' => 'Invalid state parameter.']);
                }
            }

            if ($sessionState) {
                $request->session()->forget('apple_state');
            }

            if (!$code) {
                $error = $request->input('error');
                $errorDescription = $request->input('error_description');
                \Log::error('Apple callback error', [
                    'error' => $error,
                    'error_description' => $errorDescription,
                ]);
                return redirect()->route('auth.show')
                    ->withErrors(['error' => $errorDescription ?? $error ?? 'Apple authentication failed.']);
            }

            $appleAuth = new AppleAuthService();
            
            // Exchange code for tokens
            $tokens = $appleAuth->getTokens($code);
            $idToken = $tokens['id_token'] ?? null;

            if (!$idToken) {
                \Log::error('No ID token from Apple', ['tokens' => $tokens]);
                throw new \Exception('No ID token received from Apple');
            }

            // Get user info from ID token
            $userInfo = $appleAuth->getUserInfo($idToken);
            $appleId = $userInfo['apple_id'];
            $email = $userInfo['email'];

            \Log::info('Apple user info', [
                'apple_id' => $appleId,
                'has_email' => !empty($email),
            ]);

            // Parse user data if provided (only on first authorization)
            $name = null;
            if ($userData) {
                $userDataDecoded = json_decode($userData, true);
                if (isset($userDataDecoded['name'])) {
                    $firstName = $userDataDecoded['name']['firstName'] ?? '';
                    $lastName = $userDataDecoded['name']['lastName'] ?? '';
                    $name = trim($firstName . ' ' . $lastName);
                }
            }

            // Find or create user
            $user = User::where('apple_id', $appleId)->first();

            if (!$user && $email) {
                // Try to find by email
                $user = User::where('email', $email)->first();
                
                if ($user) {
                    // Link Apple ID to existing user
                    $user->apple_id = $appleId;
                    if (!$user->name && $name) {
                        $user->name = $name;
                    }
                    $user->save();
                }
            }

            if (!$user) {
                // Create new user
                if (!$email) {
                    // Apple didn't provide email (user chose to hide it)
                    // We need to ask for email or use a placeholder
                    $request->session()->put('apple_id', $appleId);
                    $request->session()->put('apple_name', $name);
                    return redirect()->route('auth.apple.email');
                }

                // Generate unique allohash
                do {
                    $uniqueCode = Str::random(32) . time() . random_int(1000, 9999);
                    $allohash = Hash::make($uniqueCode);
                } while (User::where('allohash', $allohash)->exists());

                $user = User::create([
                    'name' => $name ?? 'Apple User',
                    'email' => $email,
                    'apple_id' => $appleId,
                    'password' => Hash::make(Str::random(32)), // Random password since Apple auth doesn't use password
                    'allohash' => $allohash,
                ]);

                \Log::info('Created new Apple user', ['user_id' => $user->id]);
            }

            // Login user
            Auth::login($user);
            $request->session()->regenerate();

            \Log::info('Apple login successful', ['user_id' => $user->id]);

            return redirect()->route('dashboard')->with('status', 'Welcome to AlloSSO!');

        } catch (\Exception $e) {
            \Log::error('Apple callback exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return redirect()->route('auth.show')
                ->withErrors(['error' => 'Apple authentication failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Show email form for Apple users without email
     */
    public function showAppleEmail(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $appleId = $request->session()->get('apple_id');
        if (!$appleId) {
            return redirect()->route('auth.show');
        }

        $platform = $request->session()->get('platform');

        return view('auth.apple-email', [
            'apple_name' => $request->session()->get('apple_name'),
            'platform' => $platform,
        ]);
    }

    /**
     * Handle email submission for Apple users
     */
    public function handleAppleEmail(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
        ]);

        $appleId = $request->session()->get('apple_id');
        if (!$appleId) {
            return redirect()->route('auth.show');
        }

        $email = strtolower($validated['email']);
        $name = $request->session()->get('apple_name', 'Apple User');

        // Check if email is already taken
        $existingUser = User::where('email', $email)->first();
        if ($existingUser) {
            return back()
                ->withInput()
                ->withErrors(['email' => 'This email is already registered. Please login with password instead.']);
        }

        // Generate unique allohash
        do {
            $uniqueCode = Str::random(32) . time() . random_int(1000, 9999);
            $allohash = Hash::make($uniqueCode);
        } while (User::where('allohash', $allohash)->exists());

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'apple_id' => $appleId,
            'password' => Hash::make(Str::random(32)),
            'allohash' => $allohash,
        ]);

        $request->session()->forget('apple_id');
        $request->session()->forget('apple_name');

        // Login user
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')->with('status', 'Welcome to AlloSSO!');
    }
}