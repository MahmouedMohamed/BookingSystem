<?php

namespace App\Modules\Users\Requests;

use App\Http\Requests\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->user() && $this->user()->role !== 'admin') {
            // Force role to current user's role
            $this->merge([
                'role' => $this->user()->role,
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
        return [
            'name' => 'sometimes|required|string|max:255',
            'password' => 'sometimes|required|string|min:8|max:255',
            'role' => 'sometimes|string|in:admin,provider,customer',
            'timezone' => 'sometimes|numeric|min:-12|max:12'
        ];
    }
}
