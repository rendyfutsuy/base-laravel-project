<?php

use Illuminate\Support\Facades\Route;
use Modules\Notification\Http\Controllers\NotificationController;

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

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('', [NotificationController::class, 'index'])->name('api.notification.index');
    Route::get('read', [NotificationController::class, 'updateRead'])->name('api.notification.read');
    Route::delete('', [NotificationController::class, 'delete'])->name('api.notification.delete');
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::group(['prefix' => 'firebase-token'], function () {
        Route::post('', [FirebaseTokenController::class, 'store'])->name('api.notification.firebase.store');
        Route::delete('', [FirebaseTokenController::class, 'delete'])->name('api.notification.firebase.destroy');
    });
});
