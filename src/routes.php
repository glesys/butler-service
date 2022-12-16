<?php

use Butler\Service\Http\Controllers\AboutController;
use Butler\Service\Http\Controllers\AuthController;
use Butler\Service\Http\Controllers\FailedJobsController;
use Butler\Service\Http\Controllers\GraphqlController;
use Butler\Service\Http\Controllers\HealthController;
use Butler\Service\Http\Controllers\HomeController;
use Butler\Service\Http\Controllers\TokensController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get('/', HomeController::class)->name('home');
    Route::get('/health', HealthController::class)->name('health');
    Route::get('/about', AboutController::class)->name('about');

    Route::controller(FailedJobsController::class)->group(function () {
        Route::get('failed-jobs', 'index')->name('failed-jobs.index');
        Route::get('failed-jobs/{id}', 'show')->name('failed-jobs.show');
        Route::post('failed-jobs/retry', 'retry')->name('failed-jobs.retry');
        Route::post('failed-jobs/forget', 'forget')->name('failed-jobs.forget');
    });

    Route::controller(TokensController::class)->group(function () {
        Route::get('tokens', 'index')->name('tokens.index');
        Route::post('tokens', 'store')->name('tokens.store');
        Route::delete('tokens', 'destroy')->name('tokens.delete');
    });

    if (config('butler.sso.enabled')) {
        Route::prefix('auth')->controller(AuthController::class)->group(function () {
            Route::get('redirect', 'redirect')->name('auth.redirect');
            Route::get('callback', 'callback')->name('auth.callback');
            Route::post('logout', 'logout')->name('auth.logout');
        });
    }
});

Route::middleware('api')->group(function () {
    Route::post('graphql', GraphqlController::class)
        ->middleware('auth:butler')
        ->name('graphql');
});
