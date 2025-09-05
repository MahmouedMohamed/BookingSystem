<?php

namespace App\Modules\Bookings\Requests;

use App\Http\Requests\FormRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreBookingRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $creatorTimezone = Auth::user()->timezone;

        return [
            'customer_id' => [
                'sometimes',
                'integer',
                Rule::exists('users', 'id')->where('role', 'customer')->whereNull('deleted_at'),
            ],
            'service_id' => [
                'required',
                'integer',
                Rule::exists('services', 'id')->where('is_published', true)->whereNull('deleted_at'),
            ],
            'start_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($creatorTimezone) {
                    $start = Carbon::parse($value, $creatorTimezone);
                    if ($start->isPast()) {
                        $fail('The start date must be in the future in your timezone.');
                    }
                },
            ],
            'end_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) use ($creatorTimezone) {
                    $start = Carbon::parse($this->start_date, $creatorTimezone);
                    $end = Carbon::parse($value, $creatorTimezone);
                    if ($end->lte($start)) {
                        $fail('End date must be after start date in your timezone.');
                    }
                },
            ],
        ];
    }
}
