<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

Route::post('/login', [AuthController::class, 'login'])
    ->name('api.auth.login');

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware(['auth:api'])
    ->name('api.auth.logout');
