<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExampleController;
use App\Http\Controllers\RepositoryExampleController;

// for example testing only
Route::group(['prefix' => 'test/example'], function () {
    Route::get('/', [ExampleController::class, 'index'])
        ->name('api.example.index');

    Route::get('/restful', [ExampleController::class, 'getFromApi'])
        ->name('api.example.index.3rd');

    Route::post('/restful', [ExampleController::class, 'postToApi'])
        ->name('api.example.post.3rd');
});

Route::group(['prefix' => 'repository/example'], function () {
    Route::get('/', [RepositoryExampleController::class, 'all'])
        ->name('api.example.repository.index');

    Route::post('/', [RepositoryExampleController::class, 'store'])
        ->name('api.example.repository.store');

    Route::delete('/{id}', [RepositoryExampleController::class, 'delete'])
        ->name('api.example.repository.delete');

    Route::put('/{id}', [RepositoryExampleController::class, 'update'])
        ->name('api.example.repository.update');
});