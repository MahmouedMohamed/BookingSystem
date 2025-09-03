<?php

namespace App\Modules\Availabilities\Requests;

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
