<?php

namespace App\Modules\Analytics\Exports;

use App\Modules\Analytics\Interfaces\BookingAnalyticsRepositoryInterface;
use Illuminate\Http\Request;

class PeakHoursExport extends BaseExport
{
    public function data(?int $take = null, ?int $skip = null)
    {
        /**
         * @var BookingAnalyticsRepositoryInterface $bookingAnalyticsRepositoryInterface
         */
        $bookingAnalyticsRepositoryInterface = app()->make(BookingAnalyticsRepositoryInterface::class);

        $query = $bookingAnalyticsRepositoryInterface->peakHours(new Request($this->request), $this->requester->timezone);

        if (! $skip && ! $take) {
            return count($query);
        }

        return collect($query);
    }

    public function generate(bool $all = false, bool $allBasedOnCount = false): void
    {
        parent::generate();
    }

    public function map($data): array
    {
        return [
            $data['day_name'],
            $data['hour'],
            $data['booking_count'],
        ];
    }

    public function headings(): array
    {
        $headers = [
            'Day',
            'Peak Hour',
            'Number of Bookings',
        ];

        return $headers;
    }
}
