<?php

namespace App\Modules\Analytics\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;

interface BookingAnalyticsRepositoryInterface
{
    public function totalBookings($request): LengthAwarePaginator;

    public function bookingsRate($request): LengthAwarePaginator;

    public function peakHours($request): LengthAwarePaginator;

    public function averageBookingsDuration($request): LengthAwarePaginator;
}
