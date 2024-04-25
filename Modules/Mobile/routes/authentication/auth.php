<?php

use Illuminate\Support\Facades\Route;
use Modules\Authentication\Http\Controllers\AuthController;
use Modules\Authentication\Http\Controllers\ProfileController;
use Modules\Authentication\Http\Controllers\RegisterController;
use Modules\Authentication\Http\Controllers\VerificationController;

/*
|--------------------------------------------------------------------------
| Mobile Routing for Authentication Module
|--------------------------------------------------------------------------
|
*/

Route::post('/login', [AuthController::class, 'login'])
    ->name('api.authentication.mobile.login');

Route::post('/register', [RegisterController::class, 'register'])
    ->name('api.authentication.mobile.register');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware(['auth:api'])
    ->name('api.authentication.mobile.logout');

Route::prefix('profile')->middleware('auth:api')->group(function () {
    Route::get('/', [ProfileController::class, 'profile'])
        ->name('api.authentication.mobile.profile.index');

    Route::put('/detail', [ProfileController::class, 'update'])
        ->middleware('permission')
        ->name('api.authentication.mobile.profile.update');

    Route::post('/password', [ProfileController::class, 'changePassword'])
        ->middleware('permission')
        ->name('api.authentication.mobile.profile.password');
});

Route::group(['middleware' => 'otp-token'], function () {
    Route::post('/verification/code/otp', [VerificationController::class, 'verify'])
        ->name('api.authentication.mobile.verification.otp');

    Route::post('/verification/resend/mail-otp', [VerificationController::class, 'resendMailOTP'])
        ->name('api.authentication.mobile.verification.resend.email.otp');
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/revoke', [AuthController::class, 'revokeToken'])
        ->name('api.authentication.mobile.revoke.token');
});

Route::middleware('refresh-token')
    ->post('/refresh/token', [AuthController::class, 'refreshMyToken'])
    ->name('api.authentication.mobile.refresh.token');
