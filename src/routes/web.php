<?php

use Butler\Service\Http\Controllers\AboutController;
use Butler\Service\Http\Controllers\AuthController;
use Butler\Service\Http\Controllers\FailedJobsController;
use Butler\Service\Http\Controllers\HealthController;
use Butler\Service\Http\Controllers\HomeController;
use Butler\Service\Http\Controllers\TokensController;
use Illuminate\Support\Facades\Route;

Route::get('/', config('butler.service.controllers.home', HomeController::class))->name('home');
Route::get('/health', config('butler.service.controllers.health', HealthController::class))->name('health');
Route::get('/about', config('butler.service.controllers.about', AboutController::class))->name('about');

Route::controller(config('butler.service.controllers.failed_jobs', FailedJobsController::class))->group(function () {
    Route::get('failed-jobs', 'index')->name('failed-jobs.index');
    Route::get('failed-jobs/{id}', 'show')->name('failed-jobs.show');
    Route::post('failed-jobs/retry', 'retry')->name('failed-jobs.retry');
    Route::post('failed-jobs/forget', 'forget')->name('failed-jobs.forget');
});

Route::controller(config('butler.service.controllers.tokens', TokensController::class))->group(function () {
    Route::get('tokens', 'index')->name('tokens.index');
    Route::post('tokens', 'store')->name('tokens.store');
    Route::delete('tokens', 'destroy')->name('tokens.delete');
});

if (config('butler.sso.enabled')) {
    Route::prefix('auth')->controller(config('butler.service.controllers.auth', AuthController::class))->group(function () {
        Route::get('redirect', 'redirect')->name('auth.redirect');
        Route::get('callback', 'callback')->name('auth.callback');
        Route::post('logout', 'logout')->name('auth.logout');
    });
}
