<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'show'])
    ->name('auth.show');

Route::get('/captcha', [AuthController::class, 'captcha'])
    ->name('auth.captcha');

Route::post('/authenticate', [AuthController::class, 'authenticate'])
    ->name('authenticate');

Route::get('/password', [AuthController::class, 'showPasswordLogin'])
    ->name('auth.password.show');

Route::post('/password', [AuthController::class, 'passwordLogin'])
    ->name('auth.password');

Route::get('/register', [AuthController::class, 'showRegister'])
    ->name('auth.register.show');

Route::post('/register', [AuthController::class, 'register'])
    ->name('auth.register');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::post('/allolancer/account-type', [AuthController::class, 'saveAllolancerAccountType'])
    ->middleware('auth')
    ->name('allolancer.account-type');

Route::get('/auth/apple', [AuthController::class, 'redirectToApple'])
    ->name('auth.apple');

Route::match(['get', 'post'], '/auth/apple/callback', [AuthController::class, 'handleAppleCallback'])
    ->name('auth.apple.callback');

Route::get('/auth/apple/email', [AuthController::class, 'showAppleEmail'])
    ->name('auth.apple.email');

Route::post('/auth/apple/email', [AuthController::class, 'handleAppleEmail'])
    ->name('auth.apple.email.submit');

Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])
    ->name('auth.google');

Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])
    ->name('auth.google.callback');

Route::get('/dashboard', function (Request $request) {
    // Get platform from session
    $platform = $request->session()->get('platform');
    
    return view('dashboard', [
        'user' => auth()->user(),
        'platform' => $platform,
    ]);
})->middleware('auth')->name('dashboard');
