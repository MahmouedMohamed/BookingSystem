<?php

namespace App\Modules\Availabilities\Services;

use App\Modules\Availabilities\Interfaces\AvailabilityOverrideRepositoryInterface;
use App\Modules\Availabilities\Interfaces\AvailabilityOverrideServiceInterface;
use App\Modules\Availabilities\Models\AvailabilityOverride;
use Illuminate\Pagination\LengthAwarePaginator;

class AvailabilityOverrideService implements AvailabilityOverrideServiceInterface
{
    public function __construct(private AvailabilityOverrideRepositoryInterface $availabilityOverrideRepositoryInterface) {}

    public function index($request, $provider): LengthAwarePaginator
    {
        return $this->availabilityOverrideRepositoryInterface->index($request, $provider);
    }

    public function store($request, $provider): AvailabilityOverride
    {
        return $this->availabilityOverrideRepositoryInterface->store($request, $provider);
    }

    public function update($request, $availabilityOverride): AvailabilityOverride
    {
        return $this->availabilityOverrideRepositoryInterface->update($request, $availabilityOverride);
    }

    public function destroy($availabilityOverride): bool
    {
        return $this->availabilityOverrideRepositoryInterface->destroy($availabilityOverride);
    }
}
