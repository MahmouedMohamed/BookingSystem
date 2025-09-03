<?php

namespace App\Modules\Availabilities\Rules;

use App\Modules\Availabilities\Models\Availability;
use App\Traits\TimeHelper;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AvailabilityOverrideRule implements ValidationRule
{
    use TimeHelper;

    protected int $providerId;
    protected ?int $weekday;
    protected ?string $dateStart;
    protected string $start;
    protected string $end;
    protected string $type;

    public function __construct(int $providerId, ?int $weekday, ?string $dateStart, string $start, string $end, string $type)
    {
        $this->providerId = $providerId;
        $this->weekday = $weekday;
        $this->dateStart = $dateStart;
        $this->start = $start;
        $this->end = $end;
        $this->type = $type;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->type == 'date') {
            $date = $value;
        } else {
            $date = null;
        }

        if (!$date && $this->weekday !== null && $this->dateStart) {
            $date = $this->calculateNextWeekday($this->dateStart, $this->weekday);
        }

        if (!$date) {
            return;
        }

        $date = Carbon::parse($date);
        $weekday = $date->dayOfWeek;
        $availabilities = Availability::where('provider_id', $this->providerId)
            ->where('weekday', $weekday)
            ->get();

        if ($availabilities->isEmpty()) {
            $fail("Provider has no availability on this date/weekday to block.");
            return;
        }

        $withinAvailabilities = $availabilities->contains(function ($availability) use ($date) {
            $availabilityStart = Carbon::parse($date->toDateString() . ' ' . $availability->start);
            $availabilityEnd = Carbon::parse($date->toDateString() . ' ' . $availability->end);

            $currentStart = Carbon::parse($date->toDateString() . ' ' . $this->start);
            $currentEnd = Carbon::parse($date->toDateString() . ' ' . $this->end);

            return $currentStart->gte($availabilityStart) && $currentEnd->lte($availabilityEnd);
        });

        if (!$withinAvailabilities) {
            $fail("Override {$this->start}-{$this->end} must fall within provider availability on {$date->toDateString()}.");
        }
    }
}
