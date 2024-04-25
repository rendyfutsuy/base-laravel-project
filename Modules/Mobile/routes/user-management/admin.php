<?php

use Illuminate\Support\Facades\Route;
use Modules\UserManagement\Http\Controllers\SuperadminController;

/*
|--------------------------------------------------------------------------
| Mobile Routing for User Management Module
|--------------------------------------------------------------------------
|
*/

Route::prefix('superadmins')->middleware('auth:api')->group(function () {
    Route::get('/', [SuperadminController::class, 'index'])
        ->middleware('permission')
        ->name('api.user-management.mobile.superadmin.index');

    Route::get('/{id}', [SuperadminController::class, 'show'])
        ->middleware('permission')
        ->name('api.user-management.mobile.superadmin.show');

    Route::put('/{id}', [SuperadminController::class, 'update'])
        ->middleware('permission')
        ->name('api.user-management.mobile.superadmin.update');

    Route::delete('/{id}', [SuperadminController::class, 'destroy'])
        ->middleware('permission')
        ->name('api.user-management.mobile.superadmin.destroy');

    Route::post('/', [SuperadminController::class, 'store'])
        ->middleware('permission')
        ->name('api.user-management.mobile.superadmin.store');
});
