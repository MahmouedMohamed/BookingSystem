<?php

namespace App\Modules\Analytics\Interfaces;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

interface BookingAnalyticsRepositoryInterface
{
    public function totalBookings($request, $withPagination = true, $timezone = null): LengthAwarePaginator|Builder;

    public function bookingsRate($request, $withPagination = true, $timezone = null): LengthAwarePaginator|Builder;

    public function peakHours($request, $timezone = null): array;

    public function averageBookingsDuration($request, $withPagination = true, $timezone = null): LengthAwarePaginator|Builder;
}
