<?php

use Illuminate\Support\Facades\Route;

Route::get('/dummy-web-route', fn () => true)->name('dummy-web-route');
