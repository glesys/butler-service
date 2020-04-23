<?php

use Butler\Service\Http\Controllers\GraphqlController;
use Butler\Service\Http\Controllers\HealthController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::view(
        config('butler.service.routes.readme', '/'),
        'service::readme'
    )->name('readme');

    Route::view(
        config('butler.service.routes.schema', '/schema'),
        'service::schema',
        ['schema' => File::get(config('butler.graphql.schema'))]
    )->name('schema');

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
