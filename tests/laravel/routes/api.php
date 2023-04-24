<?php

use Illuminate\Support\Facades\Route;

Route::get('/dummy-api-route', fn () => true)->name('dummy-api-route');
