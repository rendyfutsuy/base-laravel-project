<?php

use Illuminate\Support\Facades\Route;
use Modules\UserManagement\Http\Controllers\StaffController;

/*
|--------------------------------------------------------------------------
| Mobile Routing for User Management Module
|--------------------------------------------------------------------------
|
*/

Route::prefix('staffs')->middleware('auth:api')->group(function () {
    Route::get('/', [StaffController::class, 'index'])
        ->middleware('permission')
        ->name('api.user-management.mobile.staff.index');

    Route::get('/{id}', [StaffController::class, 'show'])
        ->middleware('permission')
        ->name('api.user-management.mobile.staff.show');

    Route::put('/{id}', [StaffController::class, 'update'])
        ->middleware('permission')
        ->name('api.user-management.mobile.staff.update');

    Route::delete('/{id}', [StaffController::class, 'destroy'])
        ->middleware('permission')
        ->name('api.user-management.mobile.staff.destroy');

    Route::post('/', [StaffController::class, 'store'])
        ->middleware('permission')
        ->name('api.user-management.mobile.staff.store');
});
