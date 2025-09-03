<?php

namespace App\Modules\Availabilities\Repositories;

use App\Modules\Availabilities\Interfaces\AvailabilityRepositoryInterface;
use App\Modules\Availabilities\Models\Availability;
use Illuminate\Pagination\LengthAwarePaginator;

class AvailabilityRepository implements AvailabilityRepositoryInterface
{
    public function index($request, $provider): LengthAwarePaginator
    {
        $query = Availability::where('provider_id', '=', $provider->id);

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($request->get('per_page', 15));
    }

    public function store($request, $provider): Availability
    {
        $data = $request->validated();

        $data['provider_id'] = $provider->id;

        return Availability::create($data);
    }

    public function update($request, $availability): Availability
    {
        $data = $request->validated();

        $availability->update($data);

        return $availability->fresh();
    }

    public function destroy($availability): bool
    {
        return $availability->delete();
    }
}
