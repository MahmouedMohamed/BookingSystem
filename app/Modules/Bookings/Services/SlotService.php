<?php

namespace App\Modules\Bookings\Services;

use App\Modules\Availabilities\Models\Availability;
use App\Modules\Availabilities\Models\AvailabilityOverride;
use App\Modules\Bookings\Exceptions\InvalidServiceException;
use App\Modules\Bookings\Interfaces\SlotServiceInterface;
use App\Modules\Bookings\Models\Booking;
use App\Traits\CacheHelper;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Carbon\CarbonTimeZone;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SlotService implements SlotServiceInterface
{
    use CacheHelper;

    public function __construct() {}

    public function index($provider, $service, $viewerTimezone): Collection
    {
        if (! $service->is_published || $service->provider_id !== $provider->id) {
            throw new InvalidServiceException;
        }

        $providerTimezone = CarbonTimeZone::create($provider->timezone);
        $viewerTimezone = CarbonTimeZone::createFromHourOffset($viewerTimezone);

        $viewerDateStart = Carbon::now()->timezone($viewerTimezone);

        /**
         * this from is used in queries related to provider so we need to convert the start coming from
         * viewer timezone to the provider timezone as we save things in database with provider timezone
         *
         * NOTE: This wouldn't affect if timezone of provider is +
         * o.w it will get the previous day
         * */
        $from = Carbon::parse($viewerDateStart, $viewerTimezone)->setTimezone($providerTimezone)->startOfDay();
        $days = 7; // Only Next Week
        $to = (clone $from)->addDays($days - 1)->endOfDay();

        $cacheKey = 'slots_provider_'.$provider->id.
            '_service_'.$service->id.
            '_from_'.$from->toDateString().
            '_timezone_'.$viewerTimezone;

        $ttl = 7 * 60 * 60;

        // Track key manually for database cache
        $this->trackSlotCacheKey($provider->id, $service->id, $cacheKey, $ttl);

        return Cache::remember(
            $cacheKey,
            $ttl,
            function () use ($provider, $service, $providerTimezone, $viewerTimezone, $from, $to) {
                $availabilities = Availability::where('provider_id', $provider->id)
                    ->get(['weekday', 'start', 'end'])
                    ->groupBy('weekday');

                // We can't compare with from here cause some overrides may be before the start date
                // but it still recursive
                $availabilitiesOverrides = AvailabilityOverride::where('provider_id', $provider->id)
                    ->whereDate('date', '<=', $to->toDateString())
                    ->get(['date', 'start', 'end', 'recurring', 'number_of_recurring']);

                $availabilitiesOverridesWithRecursive = $this->calculateAvailabilitiesOverridesWithRecursive($availabilitiesOverrides, $providerTimezone, $from, $to);

                $bookings = Booking::where('provider_id', $provider->id)
                    ->whereBetween('start_date', [$from, $to])
                    ->whereIn('status', ['PENDING', 'CONFIRMED'])
                    ->get(['start_date', 'end_date'])
                    ->map(fn ($booking) => [
                        'start' => Carbon::parse($booking->start_date)->setTimezone($providerTimezone),
                        'end' => Carbon::parse($booking->end_date)->setTimezone($providerTimezone),
                    ]);

                $slots = collect();

                foreach (CarbonPeriod::create($from, '1 day', $to) as $day) {
                    $weekday = $day->dayOfWeek;
                    $dateKey = $day->toDateString();

                    $availabilitiesDays = $availabilities->get($weekday, collect())
                        ->map(fn ($r) => [
                            'start' => Carbon::parse($dateKey.' '.$r->start, $providerTimezone),
                            'end' => Carbon::parse($dateKey.' '.$r->end, $providerTimezone),
                        ])
                        ->values();

                    $blocking = $availabilitiesOverridesWithRecursive->get($dateKey, collect());

                    foreach ($availabilitiesDays as $availabilitiesDay) {
                        $timeCursor = (clone $availabilitiesDay['start']);

                        $this->getSlotsInTime($slots, $timeCursor, $service, $availabilitiesDay, $bookings, $blocking, $viewerTimezone);
                    }
                }

                return $slots->groupBy('date');
            }
        );
    }

    private function calculateAvailabilitiesOverridesWithRecursive($availabilitiesOverrides, $providerTimezone, $from, $to)
    {
        $availabilitiesOverridesWithRecursive = collect();

        foreach ($availabilitiesOverrides as $availabilityOverride) {
            $date = Carbon::parse($availabilityOverride->date, $providerTimezone)->startOfDay();
            $dateKey = $date->copy()->toDateString();
            $availabilitiesOverridesWithRecursive->push([
                'date' => $dateKey,
                'start' => Carbon::parse($dateKey.' '.$availabilityOverride->start, $providerTimezone),
                'end' => Carbon::parse($dateKey.' '.$availabilityOverride->end, $providerTimezone),
            ]);
            if ($availabilityOverride->recurring) {
                for ($index = 0; $index < $availabilityOverride->number_of_recurring; $index++) {
                    $occurrence = $date->copy()->addWeeks($index);

                    // only add occurrences in range
                    if ($occurrence->between($from, $to)) {
                        $dateKey = $occurrence->toDateString();
                        $availabilitiesOverridesWithRecursive->push([
                            'date' => $dateKey,
                            'start' => Carbon::parse($dateKey.' '.$availabilityOverride->start, $providerTimezone),
                            'end' => Carbon::parse($dateKey.' '.$availabilityOverride->end, $providerTimezone),
                        ]);
                    }
                }
            }
        }

        return $availabilitiesOverridesWithRecursive->groupBy('date');
    }

    private function getSlotsInTime(&$slots, $timeCursor, $service, $w, $bookings, $blocking, $viewerTimezone)
    {
        while ($timeCursor->copy()->addMinutes($service->duration)->lte($w['end'])) {
            $slotStart = $timeCursor->copy();
            $slotEnd = $timeCursor->copy()->addMinutes($service->duration);

            $conflictsBooking = $bookings->first(fn ($booking) => $slotStart->lt($booking['end']) && $slotEnd->gt($booking['start']));

            // Blocked by override?
            $conflictsOverride = $blocking->first(fn ($blocking) => $slotStart->lt($blocking['end']) && $slotEnd->gt($blocking['start']));

            $nowInViewerTz = Carbon::now($viewerTimezone);

            if (! $conflictsBooking && ! $conflictsOverride && $slotStart->copy()->setTimezone($viewerTimezone)->gte($nowInViewerTz)) {
                $slots->push([
                    'start_at' => $slotStart->clone()->setTimezone($viewerTimezone)->toIso8601String(),
                    'end_at' => $slotEnd->clone()->setTimezone($viewerTimezone)->toIso8601String(),
                    'date' => $slotStart->clone()->setTimezone($viewerTimezone)->toDateString(),
                ]);

                $timeCursor->addMinutes($service->duration);
            } else {
                if (isset($conflictsBooking)) {
                    $timeCursor = Carbon::parse($conflictsBooking['end'])->copy();
                } elseif (isset($conflictsOverride)) {
                    $timeCursor = Carbon::parse($conflictsOverride['end'])->copy();
                } else {
                    $timeCursor->addMinutes($service->duration);
                }
            }
        }
    }
}
