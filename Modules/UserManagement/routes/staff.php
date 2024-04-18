<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Superadmin\StaffController;

/*
|--------------------------------------------------------------------------
| WEB Staff Routes
|--------------------------------------------------------------------------
|
*/

Route::prefix('staffs')->middleware('auth:api')->group(function () {
    Route::get('/', [StaffController::class, 'index'])
        ->middleware('permission')
        ->name('api.user-management.staff.index');

    Route::get('/{id}', [StaffController::class, 'show'])
        ->middleware('permission')
        ->name('api.user-management.staff.show');

    Route::put('/{id}', [StaffController::class, 'update'])
        ->middleware('permission')
        ->name('api.user-management.staff.update');

    Route::delete('/{id}', [StaffController::class, 'destroy'])
        ->middleware('permission')
        ->name('api.user-management.staff.destroy');

    Route::post('/', [StaffController::class, 'store'])
        ->middleware('permission')
        ->name('api.user-management.staff.store');
});
