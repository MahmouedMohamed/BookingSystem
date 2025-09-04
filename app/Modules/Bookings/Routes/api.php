<?php

use App\Modules\Bookings\Controllers\SlotController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('providers/{provider}/services/{service}/slots', [SlotController::class, 'index']);
});
