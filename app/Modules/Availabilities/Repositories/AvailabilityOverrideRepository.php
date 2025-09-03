<?php

namespace App\Modules\Availabilities\Repositories;

use App\Modules\Availabilities\Interfaces\AvailabilityOverrideRepositoryInterface;
use App\Modules\Availabilities\Models\AvailabilityOverride;
use App\Traits\TimeHelper;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class AvailabilityOverrideRepository implements AvailabilityOverrideRepositoryInterface
{
    use TimeHelper;

    public function index($request, $provider): LengthAwarePaginator
    {
        $query = AvailabilityOverride::where('provider_id', '=', $provider->id);

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($request->get('per_page', 15));
    }

    public function store($request, $provider): AvailabilityOverride
    {
        $data = $request->validated();

        $data['provider_id'] = $provider->id;

        if(isset($data['weekday']) && isset($data['date_start'])) {
            $data['date'] = $this->calculateNextWeekday($data['date_start'], $data['weekday']);
        }

        return AvailabilityOverride::create($data);
    }

    public function update($request, $availability): AvailabilityOverride
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
