<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Superadmin\SuperadminController;

/*
|--------------------------------------------------------------------------
| WEB Admin Routes
|--------------------------------------------------------------------------
|
*/

Route::prefix('superadmins')->middleware('auth:api')->group(function () {
    Route::get('/', [SuperadminController::class, 'index'])
        ->middleware('permission')
        ->name('api.user-management.superadmin.index');

    Route::get('/{id}', [SuperadminController::class, 'show'])
        ->middleware('permission')
        ->name('api.user-management.superadmin.show');

    Route::put('/{id}', [SuperadminController::class, 'update'])
        ->middleware('permission')
        ->name('api.user-management.superadmin.update');

    Route::delete('/{id}', [SuperadminController::class, 'destroy'])
        ->middleware('permission')
        ->name('api.user-management.superadmin.destroy');

    Route::post('/', [SuperadminController::class, 'store'])
        ->middleware('permission')
        ->name('api.user-management.superadmin.store');
});
