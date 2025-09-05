<?php

use App\Modules\Analytics\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->prefix('analytics')->group(function () {
    Route::get('total-bookings', [AnalyticsController::class, 'totalBookings']);
    Route::get('bookings-rate', [AnalyticsController::class, 'bookingsRate']);
    Route::get('peak-hours', [AnalyticsController::class, 'peakHours']);
    Route::get('avg-bookings-duration', [AnalyticsController::class, 'averageBookingsDuration']);
    Route::get('total-bookings/export', [AnalyticsController::class, 'exportTotalBookings']);
    Route::get('bookings-rate/export', [AnalyticsController::class, 'exportBookingsRate']);
    Route::get('peak-hours/export', [AnalyticsController::class, 'exportPeakHours']);
    Route::get('avg-bookings-duration/export', [AnalyticsController::class, 'exportAverageBookingsDuration']);
});
