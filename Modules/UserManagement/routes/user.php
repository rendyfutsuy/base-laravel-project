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
        ->middleware('permission')
        ->name('api.user-management.user.index');

    Route::post('/', [UserController::class, 'store'])
        ->middleware('permission')
        ->name('api.user-management.user.store');

    Route::get('/{id}', [UserController::class, 'show'])
        ->middleware('permission')
        ->name('api.user-management.user.show');

    Route::put('/{id}', [UserController::class, 'update'])
        ->middleware('permission')
        ->name('api.user-management.user.update');

    Route::delete('/{id}', [UserController::class, 'destroy'])
        ->middleware('permission')
        ->name('api.user-management.user.destroy');
});
