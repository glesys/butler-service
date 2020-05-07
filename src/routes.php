<?php

use Butler\Service\Http\Controllers\GraphqlController;
use Butler\Service\Http\Controllers\HealthController;
use Butler\Service\Http\Controllers\FrontController;
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
});

Route::middleware(['api', 'auth'])->group(function () {
    Route::post(
        config('butler.service.routes.graphql', '/graphql'),
        GraphqlController::class
    )->name('graphql');
});
