<?php

use App\Modules\Availabilities\Controllers\AvailabilityController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('providers/{provider}/availabilities', AvailabilityController::class)->except(['show']);
});
