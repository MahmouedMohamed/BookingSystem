<?php

namespace App\Modules\Availabilities\Requests;

use App\Http\Requests\FormRequest;
use App\Traits\TimeHelper;

class BaseAvailabilityOverrideRequest extends FormRequest
{
    use TimeHelper;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('date')) {
            // If date is provided → reset weekday/recurring fields
            $this->merge([
                'weekday' => null,
                'date_start' => null,
                'recurring' => false,
                'number_of_recurring' => 0,
            ]);
        } else {
            // If no date → ensure these fields exist with defaults
            $this->merge([
                'date' => null,
                'weekday' => $this->input('weekday'),
                'date_start' => $this->input('date_start'),
                'recurring' => $this->boolean('recurring', false),
                'number_of_recurring' => $this->input('number_of_recurring', 0),
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [];
    }
}
