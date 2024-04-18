<?php

use Illuminate\Support\Facades\Route;
use Modules\Hierarchy\Http\Controllers\RoleController;
use Modules\Hierarchy\Http\Controllers\PermissionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:api'])->group(function () {
    Route::prefix('roles')->group(function () {
        Route::get('/', [RoleController::class, 'index'])
            ->middleware('permission')
            ->name('api.hierarchy.role.index');
        Route::get('/{id}', [RoleController::class, 'show'])
            ->middleware('permission')
            ->name('api.hierarchy.role.show');
        Route::post('/', [RoleController::class, 'store'])
            ->middleware('permission')
            ->name('api.hierarchy.role.store');
        Route::post('sync/{role}', [RoleController::class, 'sync'])
            ->middleware('permission')
            ->name('api.hierarchy.role.permission.sync');

        Route::post('user/resync/{user}/{role}', [RoleController::class, 'resync'])
            ->middleware('permission')
            ->name('api.hierarchy.role.user.resync');
    });

    Route::prefix('permissions')->group(function () {
        Route::get('/', [PermissionController::class, 'index'])
            ->middleware('permission')
            ->name('api.hierarchy.permission.index');
        Route::get('/{id}', [PermissionController::class, 'show'])
            ->middleware('permission')
            ->name('api.hierarchy.permission.show');
        Route::post('/', [PermissionController::class, 'store'])
            ->middleware('permission')
            ->name('api.hierarchy.permission.store');
        Route::post('resync/{permission}', [PermissionController::class, 'resync'])
            ->middleware('permission')
            ->name('api.hierarchy.permission.resync');

        Route::post('resync/{permission}/to-user', [PermissionController::class, 'resyncToUser'])
            ->middleware('permission')
            ->name('api.hierarchy.permission.resync.to.users');
    });
});
