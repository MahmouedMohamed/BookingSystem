<?php

namespace App\Modules\Availabilities\Requests;

use App\Http\Requests\FormRequest;
use Illuminate\Validation\Rule;

class StoreAvailabilityRequest extends FormRequest
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
            // Sunday => 0 ... Saturday => 6
            'weekday' => [
                'required',
                'integer',
                'min:0',
                'max:6',
                Rule::unique('provider_availabilities')
                    ->where('provider_id', $provider->id),
            ],
            'start' => 'required|date_format:H:i',
            'end' => 'required|date_format:H:i|after:start',
        ];
    }
}
