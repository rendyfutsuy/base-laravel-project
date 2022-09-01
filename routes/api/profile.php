<?php

use App\Http\Controllers\API\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| WEB User Routes
|--------------------------------------------------------------------------
|
*/

Route::prefix('profile')->middleware('auth:api')->group(function() {
    Route::get('/', [ProfileController::class, 'profile'])
        ->name('api.profile.index');

    Route::put('/detail', [ProfileController::class, 'update'])
        ->middleware('permission:api.profile.update')
        ->name('api.profile.update');

    Route::post('/password', [ProfileController::class, 'changePassword'])
        ->middleware('permission:api.profile.password')
        ->name('api.profile.password');
});