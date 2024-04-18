<?php

use Illuminate\Support\Facades\Route;
use Modules\Authentication\Http\Controllers\AuthController;
use Modules\Authentication\Http\Controllers\ProfileController;
use Modules\Authentication\Http\Controllers\RegisterController;
use Modules\Authentication\Http\Controllers\VerificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthController::class, 'login'])
    ->name('api.authentication.login');

Route::post('/register', [RegisterController::class, 'register'])
    ->name('api.authentication.register');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware(['auth:api'])
    ->name('api.authentication.logout');

Route::prefix('profile')->middleware('auth:api')->group(function () {
    Route::get('/', [ProfileController::class, 'profile'])
        ->name('api.authentication.profile.index');

    Route::put('/detail', [ProfileController::class, 'update'])
        ->middleware('permission')
        ->name('api.authentication.profile.update');

    Route::post('/password', [ProfileController::class, 'changePassword'])
        ->middleware('permission')
        ->name('api.authentication.profile.password');
});

Route::group(['middleware' => 'otp-token'], function () {
    Route::post('/verification/code/otp', [VerificationController::class, 'verify'])
        ->name('api.authentication.verification.otp');

    Route::post('/verification/resend/mail-otp', [VerificationController::class, 'resendMailOTP'])
        ->name('api.authentication.verification.resend.email.otp');
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::post('/revoke', [AuthController::class, 'revokeToken'])
        ->name('api.authentication.revoke.token');
});

Route::middleware('refresh-token')
    ->post('/refresh/token', [AuthController::class, 'refreshMyToken'])
    ->name('api.authentication.refresh.token');
