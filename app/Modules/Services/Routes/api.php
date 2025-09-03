<?php

use App\Modules\Services\Controllers\CategoryController;
use App\Modules\Services\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('categories', CategoryController::class);
    Route::prefix('categories')->group(function () {
        Route::patch('{id}/restore', [CategoryController::class, 'restore']);
    });
    Route::apiResource('services', ServiceController::class);
    Route::prefix('services')->group(function () {
        Route::patch('{id}/restore', [ServiceController::class, 'restore']);
    });
});
