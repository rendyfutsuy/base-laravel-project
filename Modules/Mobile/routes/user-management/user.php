<?php

use Illuminate\Support\Facades\Route;
use Modules\UserManagement\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Mobile Routing for User Management Module
|--------------------------------------------------------------------------
|
*/

Route::prefix('users')->middleware('auth:api')->group(function () {
    Route::get('/', [UserController::class, 'index'])
        ->middleware('permission')
        ->name('api.user-management.mobile.user.index');

    Route::post('/', [UserController::class, 'store'])
        ->middleware('permission')
        ->name('api.user-management.mobile.user.store');

    Route::get('/{id}', [UserController::class, 'show'])
        ->middleware('permission')
        ->name('api.user-management.mobile.user.show');

    Route::put('/{id}', [UserController::class, 'update'])
        ->middleware('permission')
        ->name('api.user-management.mobile.user.update');

    Route::delete('/{id}', [UserController::class, 'destroy'])
        ->middleware('permission')
        ->name('api.user-management.mobile.user.destroy');
});
