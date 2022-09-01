<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Superadmin\StaffController;

/*
|--------------------------------------------------------------------------
| WEB Staff Routes
|--------------------------------------------------------------------------
|
*/

Route::prefix('staffs')->middleware('auth:api')->group(function() {
    Route::get('/', [StaffController::class, 'index'])
        ->middleware('permission:api.staff.index')
        ->name('api.staff.index');
        
    Route::get('/{id}', [StaffController::class, 'show'])
        ->middleware('permission:api.staff.show')
        ->name('api.staff.show');

    Route::put('/{id}', [StaffController::class, 'update'])
        ->middleware('permission:api.staff.update')
        ->name('api.staff.update');
        
    Route::delete('/{id}', [StaffController::class, 'destroy'])
        ->middleware('permission:api.staff.destroy')
        ->name('api.staff.destroy');

    Route::post('/', [StaffController::class, 'store'])
        ->middleware('permission:api.staff.store')
        ->name('api.staff.store');
});