<?php

namespace App\Modules\Analytics\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;

interface AnalyticsServiceInterface
{
    public function totalBookings($request): LengthAwarePaginator;

    public function bookingsRate($request): LengthAwarePaginator;

    public function peakHours($request): array;

    public function averageBookingsDuration($request): LengthAwarePaginator;
}
