<?php

namespace App\Modules\Availabilities\Requests;

use App\Modules\Availabilities\Rules\AvailabilityOverrideConflict;
use App\Modules\Availabilities\Rules\AvailabilityOverrideRule;
use Illuminate\Validation\Rule;

class UpdateAvailabilityOverrideRequest extends BaseAvailabilityOverrideRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $provider = $this->route('provider');
        $availabilityOverride = $this->route('availabilities_override');

        return [
            // Either date OR weekday must be provided, but not both
            /**
             * ? Know that if I have availability every monday from 10:00 to 12:00
I can override a specific date so I don't need weekday, recurring would be false and number_of_recurring would be 0
Else If I want to override weekday then I can specify if it's gonna be repeated for specific weeks on the same day
             */
            'date' => [
                'sometimes',
                'nullable',
                'required_without:weekday',
                'date',
                'after_or_equal:today',
                new AvailabilityOverrideRule(
                    $provider->id,
                    $this->input('weekday'),
                    $this->input('date_start'),
                    $this->input('start'),
                    $this->input('end'),
                    'date',
                ),
                new AvailabilityOverrideConflict($provider->id, 'date', $this->input('start'), $this->input('end'), $availabilityOverride->id)
            ],
            // Sunday => 0 ... Saturday => 6
            'weekday' => [
                'sometimes',
            'nullable',
                'required_without:date',
                'integer',
                'min:0',
                'max:6',
                new AvailabilityOverrideRule(
                    $provider->id,
                    $this->input('weekday'),
                    $this->input('date_start'),
                    $this->input('start'),
                    $this->input('end'),
                    'weekday',
                ),
                new AvailabilityOverrideConflict($provider->id, 'weekday', $this->input('start'), $this->input('end'), $availabilityOverride->id)
            ],
            'date_start' => 'required_with:weekday|nullable|date|after_or_equal:today',
            'start' => 'required|date_format:H:i',
            'end' => 'required|date_format:H:i|after:start',
            'recurring' => 'sometimes|boolean|nullable',
            'number_of_recurring' => [
                'sometimes',
                'nullable',
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
