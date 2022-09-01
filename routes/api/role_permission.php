<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Superadmin\RoleController;
use App\Http\Controllers\Superadmin\PermissionController;

/*
|--------------------------------------------------------------------------
| WEB Role Permission Routes
|--------------------------------------------------------------------------
|
*/

Route::middleware(['auth:api'])->group(function () {
    Route::prefix('roles')->group(function() {
        Route::get('/', [RoleController::class, 'index'])
            ->middleware('permission:api.role.index')
            ->name('api.role.index');
        Route::get('/{id}', [RoleController::class, 'show'])
            ->middleware('permission:api.role.show')
            ->name('api.role.show');
        Route::post('/', [RoleController::class, 'store'])
            ->middleware('permission:api.role.store')
            ->name('api.role.store');        
        Route::post('sync/{role}', [RoleController::class, 'sync'])
            ->middleware('permission:api.role.permission.sync')
            ->name('api.role.permission.sync');

        Route::post('user/resync/{user}/{role}', [RoleController::class, 'resync'])
            ->middleware('permission:api.role.user.resync')
            ->name('api.role.user.resync');
    });

    Route::prefix('permissions')->group(function() {
        Route::get('/', [PermissionController::class, 'index'])
            ->middleware('permission:api.permission.index')
            ->name('api.permission.index');
        Route::get('/{id}', [PermissionController::class, 'show'])
            ->middleware('permission:api.permission.show')
            ->name('api.permission.show');
        Route::post('/', [PermissionController::class, 'store'])
            ->middleware('permission:api.permission.store')
            ->name('api.permission.store');
        Route::post('resync/{permission}', [PermissionController::class, 'resync'])
            ->middleware('permission:api.permission.resync')
            ->name('api.permission.resync');
    });
});