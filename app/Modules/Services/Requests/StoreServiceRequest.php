<?php

namespace App\Modules\Services\Requests;

use App\Http\Requests\FormRequest;
use App\Modules\Services\Models\Service;
use App\Modules\Users\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreServiceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $provider = $this->getProvider();

        return $provider && Gate::allows('create', [Service::class, $provider]);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $user = $this->user();
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:1',
            'duration' => 'required|integer|min:1',
            'category_id' => [
                'required',
                'integer',
                Rule::exists('categories', 'id')->whereNull('deleted_at'),
            ],
            'is_published' => 'sometimes|boolean',
        ];

        if ($user->role === 'admin') {
            $rules['provider_id'] = [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', 'provider'),
            ];
        }

        return $rules;
    }

    /**
     * Get the provider for which the service is being created.
     */
    protected function getProvider(): ?User
    {
        return User::where('id', $this->input('provider_id'))
            ->where('role', 'provider')
            ->first();
    }
}
