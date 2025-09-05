<?php

namespace App\Modules\Analytics\Repositories;

use App\Modules\Analytics\Interfaces\BookingAnalyticsRepositoryInterface;
use App\Modules\Bookings\Models\Booking;
use App\Traits\TimeHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
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

    public function totalBookings($request, $withPagination = true, $timezone = null): LengthAwarePaginator|Builder
    {
        $query = Booking::query();
        $this->applyFilters($query, $request, $timezone ?? Auth::user()->timezone);

        $query = $query
            ->selectRaw('provider_id, COUNT(*) as total')
            ->groupBy('provider_id');

        if ($withPagination) {
            return $query->paginate($request->get('per_page', 15));
        }

        return $query;
    }

    public function bookingsRate($request, $withPagination = true, $timezone = null): LengthAwarePaginator|Builder
    {
        $query = Booking::query();
        $this->applyFilters($query, $request, $timezone ?? Auth::user()->timezone);

        $query = $query
            ->selectRaw("
                service_id,
                SUM(status = 'CONFIRMED') as confirmed_count,
                SUM(status = 'CANCELLED') as cancelled_count
            ")
            ->groupBy('service_id');

        if ($withPagination) {
            return $query->paginate($request->get('per_page', 15));
        }

        return $query;
    }

    public function peakHours($request, $timezone = null): array
    {
        $viewerTimezone = $timezone ?? Auth::user()->timezone;
        $mysqlTimezone = $this->getMySQLTimezoneString($viewerTimezone);

        $query = Booking::query();

        $startOfWeek = Carbon::now($viewerTimezone)->startOfDay();
        $endOfWeek = Carbon::now($viewerTimezone)->addDays(6)->endOfDay();

        if (! $request->date_from && ! $request->date_to) {
            $request->merge([
                'date_start' => $startOfWeek,
                'date_to' => $endOfWeek,
            ]);
        } else {
            $dateStart = Carbon::parse($request->date_from, $viewerTimezone)->startOfDay();
            $dateEnd = (clone $dateStart)->addDays(6)->endOfDay();
            $request->merge([
                'date_start' => $dateStart,
                'date_to' => $dateEnd,
            ]);
        }

        $this->applyFilters($query, $request, $viewerTimezone);

        $results = $query
            ->whereIn('status', ['CONFIRMED', 'COMPLETED'])
            ->selectRaw(
                'DAYNAME(CONVERT_TZ(start_date, "+00:00", ?)) as day_name,
                HOUR(CONVERT_TZ(start_date, "+00:00", ?)) as hour,
                COUNT(*) as booking_count',
                [$mysqlTimezone, $mysqlTimezone]
            )
            ->groupBy('day_name', 'hour')
            ->get();

        $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        $peaks = [];
        foreach ($daysOfWeek as $day) {
            $dayResults = $results->where('day_name', $day);

            if ($dayResults->isNotEmpty()) {
                $peak = $dayResults->sortByDesc('booking_count')->first();
                $peaks[] = [
                    'day_name' => $day,
                    'hour' => $peak->hour,
                    'booking_count' => $peak->booking_count,
                ];
            } else {
                $peaks[] = [
                    'day_name' => $day,
                    'hour' => null,
                    'booking_count' => 0,
                ];
            }
        }

        return $peaks;
    }

    public function averageBookingsDuration($request, $withPagination = true, $timezone = null): LengthAwarePaginator|Builder
    {
        $query = Booking::query();
        $this->applyFilters($query, $request, $timezone ?? Auth::user()->timezone);

        $query = $query
            ->selectRaw('customer_id, AVG(TIMESTAMPDIFF(MINUTE, start_date, end_date)) as avg_duration')
            ->groupBy('customer_id');

        if ($withPagination) {
            return $query
                ->paginate($request->get('per_page', 15));
        }

        return $query;
    }
}
