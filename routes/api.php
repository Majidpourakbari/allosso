<?php

use App\Http\Controllers\Api\AuthApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('api.key')->group(function () {
    Route::post('/verify-allohash', [AuthApiController::class, 'verifyAllohash'])
        ->name('api.verify.allohash');
    
    Route::get('/check-allohash', [AuthApiController::class, 'checkAllohash'])
        ->name('api.check.allohash');
    
    Route::post('/external-auth', [AuthApiController::class, 'externalAuth'])
        ->name('api.external.auth');
});

