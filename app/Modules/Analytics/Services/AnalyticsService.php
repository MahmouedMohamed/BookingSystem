<?php

namespace App\Modules\Analytics\Services;

use App\Modules\Analytics\Interfaces\AnalyticsServiceInterface;
use App\Modules\Analytics\Interfaces\BookingAnalyticsRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class AnalyticsService implements AnalyticsServiceInterface
{
    public function __construct(private BookingAnalyticsRepositoryInterface $bookingAnalyticsRepositoryInterface) {}

    public function totalBookings($request): LengthAwarePaginator
    {
        return $this->bookingAnalyticsRepositoryInterface->totalBookings($request);
    }

    public function bookingsRate($request): LengthAwarePaginator
    {
        return $this->bookingAnalyticsRepositoryInterface->bookingsRate($request);
    }

    public function peakHours($request): LengthAwarePaginator
    {
        return $this->bookingAnalyticsRepositoryInterface->peakHours($request);
    }

    public function averageBookingsDuration($request): LengthAwarePaginator
    {
        return $this->bookingAnalyticsRepositoryInterface->averageBookingsDuration($request);
    }
}
