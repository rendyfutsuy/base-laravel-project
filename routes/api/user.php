<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Superadmin\UserController;

/*
|--------------------------------------------------------------------------
| WEB User Routes
|--------------------------------------------------------------------------
|
*/

Route::prefix('users')->middleware('auth:api')->group(function () {
    Route::get('/', [UserController::class, 'index'])
        ->middleware('permission:api.user.index')
        ->name('api.user.index');

    Route::get('/{id}', [UserController::class, 'show'])
        ->middleware('permission:api.user.show')
        ->name('api.user.show');

    Route::put('/{id}', [UserController::class, 'update'])
        ->middleware('permission:api.user.update')
        ->name('api.user.update');

    Route::delete('/{id}', [UserController::class, 'destroy'])
        ->middleware('permission:api.user.destroy')
        ->name('api.user.destroy');

    Route::post('/', [UserController::class, 'store'])
        ->middleware('permission:api.user.store')
        ->name('api.user.store');
});
