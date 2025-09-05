<?php

namespace App\Modules\Analytics\Exports;

use App\Modules\Analytics\Interfaces\BookingAnalyticsRepositoryInterface;
use Illuminate\Http\Request;

class TotalBookingsExport extends BaseExport
{
    public function data(?int $take = null, ?int $skip = null)
    {
        /**
         * @var BookingAnalyticsRepositoryInterface $bookingAnalyticsRepositoryInterface
         */
        $bookingAnalyticsRepositoryInterface = app()->make(BookingAnalyticsRepositoryInterface::class);

        $query = $bookingAnalyticsRepositoryInterface->totalBookings(new Request($this->request), false, $this->requester->timezone);

        if ($take) {
            $query = $query->take($take);
        }

        if ($skip) {
            $query = $query->skip($skip);
        }

        if (! $skip && ! $take) {
            return $query->count();
        }

        return $query->get();
    }

    public function generate(bool $all = false, bool $allBasedOnCount = false): void
    {
        parent::generate();
    }

    public function map($data): array
    {
        return [
            $data['provider_id'],
            $data['total'],
        ];
    }

    public function headings(): array
    {
        $headers = [
            'Provider ID',
            'Total',
        ];

        return $headers;
    }
}
