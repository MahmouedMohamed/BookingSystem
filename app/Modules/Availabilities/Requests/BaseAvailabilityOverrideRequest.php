<?php

namespace App\Modules\Availabilities\Requests;

use App\Http\Requests\FormRequest;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class BaseAvailabilityOverrideRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Calculate the target date for a weekday override
     */
    protected function calculateTargetDate(int $weekday, string $dateStart, ): string
    {
        $startDate = Carbon::parse($dateStart);
        $currentWeekday = $startDate->dayOfWeek;

        if ($weekday >= $currentWeekday) {
            $daysToAdd = $weekday - $currentWeekday;
        } else {
            $daysToAdd = 7 - $currentWeekday + $weekday;
        }

        return $startDate->addDays($daysToAdd)->format('Y-m-d');
    }

    protected function getTimeConflictRule(int $providerId, string $type)
    {
        $availabilityOverride = $this->route('availabilities_override');

        return Rule::unique('provider_availabilities_overrides')->where(function ($query) use ($providerId, $type) {
            $query->where('provider_id', $providerId)
                ->where(function ($query) use ($type) {
                    if ($type === 'date') {
                        $query->where('date', $this->input('date'));
                    } else {
                        // Use date_start for weekday-based overrides
                        $dateStart = $this->input('date_start');
                        $weekday = $this->input('weekday');

                        // Only calculate target date if date_start is provided
                        if ($dateStart) {
                            $targetDate = $this->calculateTargetDate($weekday, $dateStart);

                            if ($this->input('recurring', false)) {
                                $numberOfRecurring = $this->input('number_of_recurring', 0);
                                $dateEnd = Carbon::parse($targetDate)->addWeeks($numberOfRecurring);

                                $query->where(function ($q) use ($targetDate, $dateEnd) {
                                    // Check one-time overrides that fall within the recurrence period
                                    $q->where(function ($innerQ) use ($targetDate, $dateEnd) {
                                        $innerQ->where('recurring', false)
                                            ->where('date', '>=', $targetDate)
                                            ->where('date', '<=', $dateEnd);
                                    })
                                    // Check recurring overrides that overlap with this recurrence period
                                    ->orWhere(function ($innerQ) use ($targetDate, $dateEnd) {
                                        $innerQ->where('recurring', true)
                                            ->where('date', '<=', $dateEnd)
                                            ->whereRaw("DATE_ADD(date, INTERVAL number_of_recurring WEEK) >= ?", [$targetDate]);
                                    });
                                });
                            } else {
                                $query->where('date', $targetDate);
                            }
                        }

                        $query->where('weekday', $weekday);
                    }
                })
                ->where(function ($query) {
                    $query->where('start', '<', $this->input('end'))
                        ->where('end', '>', $this->input('start'));
                });
        })->when(isset($availabilityOverride), function ($query) use ($availabilityOverride) {
            $query->ignore($availabilityOverride->id);
        });
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $provider = $this->route('provider');

        return [
            // Either date OR weekday must be provided, but not both
            /**
             * ? Know that if I have availability every monday from 10:00 to 12:00
I can override a specific date so I don't need weekday, recurring would be false and number_of_recurring would be 0
Else If I want to override weekday then I can specify if it's gonna be repeated for specific weeks on the same day
             */
            'date' => [
                'sometimes',
                'required_without:weekday',
                'date',
                'after_or_equal:today',
                $this->getTimeConflictRule($provider->id, 'date')
            ],
            // Sunday => 0 ... Saturday => 6
            'weekday' => [
                'sometimes',
                'required_without:date',
                'integer',
                'min:0',
                'max:6',
                $this->getTimeConflictRule($provider->id, 'weekday')
            ],
            'date_start' => 'required_with:weekday|date|after_or_equal:today',
            'start' => 'required|date_format:H:i',
            'end' => 'required|date_format:H:i|after:start',
            'recurring' => 'sometimes|boolean',
            'number_of_recurring' => [
                'sometimes',
                'integer',
                'min:0',
                'max:52',
                Rule::requiredIf(function () {
                    return $this->input('recurring', false) === true;
                })
            ],
        ];
    }
}
