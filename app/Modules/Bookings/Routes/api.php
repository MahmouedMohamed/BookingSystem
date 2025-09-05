<?php

use App\Modules\Bookings\Controllers\BookingController;
use App\Modules\Bookings\Controllers\SlotController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('providers/{provider}/services/{service}/slots', [SlotController::class, 'index']);
    Route::group(['middleware' => 'throttle:bookings'], function () {
        Route::apiResource('bookings', BookingController::class)->except(['show', 'update']);
        Route::group(['prefix' => 'bookings'], function () {
            Route::patch('/{booking}/restore', [BookingController::class, 'restore']);
            Route::patch('/{booking}/{action}', [BookingController::class, 'updateStatus'])->where('action', 'confirm|cancel');
        });
    });
});
