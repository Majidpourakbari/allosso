<?php

use App\Http\Controllers\AuthController;
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

Route::get('/dashboard', function () {
    return view('dashboard', [
        'user' => auth()->user(),
    ]);
})->middleware('auth')->name('dashboard');
