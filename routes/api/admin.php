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
        ->middleware('permission:api.superadmin.index')
        ->name('api.superadmin.index');

    Route::get('/{id}', [SuperadminController::class, 'show'])
        ->middleware('permission:api.superadmin.show')
        ->name('api.superadmin.show');

    Route::put('/{id}', [SuperadminController::class, 'update'])
        ->middleware('permission:api.superadmin.update')
        ->name('api.superadmin.update');

    Route::delete('/{id}', [SuperadminController::class, 'destroy'])
        ->middleware('permission:api.superadmin.destroy')
        ->name('api.superadmin.destroy');

    Route::post('/', [SuperadminController::class, 'store'])
        ->middleware('permission:api.superadmin.store')
        ->name('api.superadmin.store');
});
