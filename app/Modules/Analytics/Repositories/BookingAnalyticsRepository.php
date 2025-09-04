<?php

namespace App\Modules\Analytics\Repositories;

use App\Modules\Analytics\Interfaces\BookingAnalyticsRepositoryInterface;
use App\Modules\Bookings\Models\Booking;
use App\Traits\TimeHelper;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class BookingAnalyticsRepository implements BookingAnalyticsRepositoryInterface
{
    use TimeHelper;

    private function applyFilters(&$query, $request, $viewerTimezone)
    {
        $query->when($request->provider_id, function ($query) use ($request) {
            $query->where('provider_id', $request->provider_id);
        })
            ->when($request->service_id, function ($query) use ($request) {
                $query->where('service_id', $request->service_id);
            })
            ->when($request->date_from && $request->date_to, function ($query) use ($request, $viewerTimezone) {
                $dateStartUTC = Carbon::parse($request->date_from, $viewerTimezone)->startOfDay();
                $dateEndUTC = Carbon::parse($request->date_to, $viewerTimezone)->endOfDay();
                $query->whereBetween('start_date', [$dateStartUTC, $dateEndUTC]);
            });
    }

    public function totalBookings($request): LengthAwarePaginator
    {
        $query = Booking::query();
        $this->applyFilters($query, $request, Auth::user()->timezone);
        return $query
            ->selectRaw('provider_id, COUNT(*) as total')
            ->groupBy('provider_id')
            ->paginate($request->get('per_page', 15));
    }

    public function bookingsRate($request): LengthAwarePaginator
    {
        $query = Booking::query();
        $this->applyFilters($query, $request, Auth::user()->timezone);
        return $query
            ->selectRaw("
                service_id,
                SUM(status = 'CONFIRMED') as confirmed_count,
                SUM(status = 'CANCELLED') as cancelled_count
            ")
            ->groupBy('service_id')
            ->paginate($request->get('per_page', 15));
    }

    public function peakHours($request): LengthAwarePaginator
    {
        $viewerTimezone = Auth::user()->timezone;
        $mysqlTimezone = $this->getMySQLTimezoneString($viewerTimezone);
        $query = Booking::query();
        $this->applyFilters($query, $request, $viewerTimezone);

        return $query
            ->whereIn('status', ['CONFIRMED', 'COMPLETED'])
            ->selectRaw(
                'DAYNAME(CONVERT_TZ(start_date, "+00:00", ?)) as day_name,
                DAY(CONVERT_TZ(start_date, "+00:00", ?)) as day,
                HOUR(CONVERT_TZ(start_date, "+00:00", ?)) as hour,
                COUNT(*) as booking_count',
                [$mysqlTimezone, $mysqlTimezone, $mysqlTimezone]
            )
            ->groupBy('day', 'day_name', 'hour')
            ->orderBy('booking_count', 'desc')
            ->paginate($request->get('per_page', 15));
    }

    public function averageBookingsDuration($request): LengthAwarePaginator
    {
        $query = Booking::query();
        $this->applyFilters($query, $request, Auth::user()->timezone);
        return $query
            ->selectRaw('customer_id, AVG(TIMESTAMPDIFF(MINUTE, start_date, end_date)) as avg_duration')
            ->groupBy('customer_id')
            ->paginate($request->get('per_page', 15));
    }
}
