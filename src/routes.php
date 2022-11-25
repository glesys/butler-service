<?php

use Butler\Health\Controller as HealthController;
use Butler\Service\Http\Controllers\AuthController;
use Butler\Service\Http\Controllers\FrontController;
use Butler\Service\Http\Controllers\GraphqlController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get(
        config('butler.service.routes.front', '/'),
        FrontController::class
    )->name('front');

    Route::get(
        config('butler.service.routes.health', '/health'),
        HealthController::class
    )->name('health');

    if (config('butler.sso.enabled')) {
        Route::prefix('auth')->controller(AuthController::class)->group(function () {
            Route::get('redirect', 'redirect')->name('auth.redirect');
            Route::get('callback', 'callback')->name('auth.callback');
            Route::post('logout', 'logout')->name('auth.logout')->middleware('auth');
        });
    }
});

Route::middleware(['api', 'auth:butler'])->group(function () {
    Route::post(
        config('butler.service.routes.graphql', '/graphql'),
        GraphqlController::class
    )->name('graphql');
});
