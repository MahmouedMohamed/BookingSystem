<?php

namespace App\Modules\Availabilities\Rules;

use App\Modules\Availabilities\Models\AvailabilityOverride;
use App\Traits\TimeHelper;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AvailabilityOverrideConflict implements ValidationRule
{
    use TimeHelper;

    protected int $providerId;

    protected ?int $ignoreId;

    protected string $type;

    protected string $start;

    protected string $end;

    public function __construct(int $providerId, string $type, string $start, string $end, ?int $ignoreId = null)
    {
        $this->providerId = $providerId;
        $this->type = $type;
        $this->start = $start;
        $this->end = $end;
        $this->ignoreId = $ignoreId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = AvailabilityOverride::where('provider_id', $this->providerId)
            ->when($this->ignoreId, fn ($q) => $q->where('id', '!=', $this->ignoreId))
            ->where('start', '<', $this->end)
            ->where('end', '>', $this->start);

        if ($this->type === 'date') {
            $targetDate = $value;

            $query->where(function ($q) use ($targetDate) {
                $q->where(function ($sub) use ($targetDate) {
                    $sub->where('recurring', false)
                        ->whereDate('date', '=', $targetDate);
                })
                    ->orWhere(function ($sub) use ($targetDate) {
                        $sub->where('recurring', true)
                            ->whereDate('date', '<=', $targetDate)
                            ->whereRaw('DATE_ADD(date, INTERVAL (number_of_recurring - 1) WEEK) >= ?', [$targetDate]);
                    });
            });
        } else {
            $weekday = $value;
            $dateStart = request()->input('date_start');

            if ($dateStart) {
                $targetDate = $this->calculateNextWeekday($dateStart, $weekday);

                if (request()->boolean('recurring')) {
                    $numberOfRecurring = request()->integer('number_of_recurring', 0);
                    $dateEnd = Carbon::parse($targetDate)->addWeeks($numberOfRecurring - 1);

                    $query->where(function ($q) use ($targetDate, $dateEnd) {
                        $q->where(function ($sub) use ($targetDate, $dateEnd) {
                            $sub->where('recurring', false)
                                ->whereBetween('date', [$targetDate, $dateEnd]);
                        })
                            ->orWhere(function ($sub) use ($targetDate, $dateEnd) {
                                $sub->where('recurring', true)
                                    ->where('date', '<=', $dateEnd)
                                    ->whereRaw('DATE_ADD(date, INTERVAL (number_of_recurring - 1) WEEK) >= ?', [$targetDate]);
                            });
                    });
                } else {
                    $query->whereDate('date', $targetDate);
                }
            }

            $query->where('weekday', $weekday);
        }

        if ($query->exists()) {
            $fail('An override already exists that conflicts');
        }
    }
}
