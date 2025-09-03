<?php

use App\Modules\Availabilities\Controllers\AvailabilityController;
use App\Modules\Availabilities\Controllers\AvailabilityOverrideController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('providers/{provider}/availabilities', AvailabilityController::class)->except(['show']);
    Route::apiResource('providers/{provider}/availabilities-overrides', AvailabilityOverrideController::class)->except(['show']);
});
