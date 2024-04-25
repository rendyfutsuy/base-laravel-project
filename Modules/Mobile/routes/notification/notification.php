<?php

use Illuminate\Support\Facades\Route;
use Modules\Notification\Http\Controllers\NotificationController;

/*
|--------------------------------------------------------------------------
| Mobile Routing for Notification Module
|--------------------------------------------------------------------------
|
*/

Route::group(['middleware' => 'auth:api'], function () {
    Route::get('', [NotificationController::class, 'index'])->name('api.notification.mobile.index');
    Route::get('read', [NotificationController::class, 'updateRead'])->name('api.notification.mobile.read');
    Route::delete('', [NotificationController::class, 'delete'])->name('api.notification.mobile.delete');
});

Route::group(['middleware' => 'auth:api'], function () {
    Route::group(['prefix' => 'firebase-token'], function () {
        Route::post('', [FirebaseTokenController::class, 'store'])->name('api.notification.mobile.firebase.store');
        Route::delete('', [FirebaseTokenController::class, 'delete'])->name('api.notification.mobile.firebase.destroy');
    });
});
