<?php

use Butler\Service\Http\Controllers\GraphqlController;
use Illuminate\Support\Facades\Route;

Route::post('graphql', config('butler.service.controllers.graphql', GraphqlController::class))->name('graphql');
