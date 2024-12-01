<?php

use App\Http\Controllers\Api\AudiogramController;
use App\Http\Controllers\Api\LoginController;
use App\Http\Controllers\Api\PasswordlessAuthController;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\SocialAuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/health', function () {
    return ['success' => true, 'timestamp' => now()];
});

Route::versioned()->name('api.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::post('/auth/providers/{provider}/callback', SocialAuthController::class)
            ->name('auth.social.callback')
            ->whereIn('provider', ['google', 'github']);

        Route::post('/auth/passwordless/token', [PasswordlessAuthController::class, 'sendToken'])
            ->name('auth.passwordless.token');

        Route::post('/auth/passwordless/verify', [PasswordlessAuthController::class, 'verifyToken'])
            ->name('auth.passwordless.verify');

        Route::post('/auth/login', LoginController::class)
            ->name('auth.login');

        Route::post('/auth/register', RegisterController::class)
            ->name('auth.register');
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::resource('audiograms', AudiogramController::class)->only('index', 'store', 'show');

        Route::get('profile', [UserController::class, 'show'])->name('profile.show');
        Route::put('profile', [UserController::class, 'update'])->name('profile.update');
    });
});
