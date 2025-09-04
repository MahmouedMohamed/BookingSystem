<?php

use App\Modules\Analytics\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('analytics')->group(function () {
    Route::get('total-bookings', [AnalyticsController::class, 'totalBookings']);
    Route::get('bookings-rate', [AnalyticsController::class, 'bookingsRate']);
    Route::get('peak-hours', [AnalyticsController::class, 'peakHours']);
    Route::get('avg-bookings-duration', [AnalyticsController::class, 'averageBookingsDuration']);
});
