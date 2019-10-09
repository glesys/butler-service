<?php

use Butler\Service\Http\Controllers\GraphqlController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::view(
        config('service.routes.readme', '/'),
        'service::readme'
    )->name('readme');

    Route::view(
        config('service.routes.schema', '/schema'),
        'service::schema',
        ['schema' => File::get(app_path('Http/Graphql/schema.graphql'))]
    )->name('schema');
});

Route::middleware(['api', 'auth'])->group(function () {
    Route::post(
        config('service.routes.graphql', '/graphql'),
        GraphqlController::class
    )->name('graphql');
});
