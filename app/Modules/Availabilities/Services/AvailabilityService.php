<?php

namespace App\Modules\Availabilities\Services;

use App\Modules\Availabilities\Interfaces\AvailabilityRepositoryInterface;
use App\Modules\Availabilities\Interfaces\AvailabilityServiceInterface;
use App\Modules\Availabilities\Models\Availability;
use Illuminate\Pagination\LengthAwarePaginator;

class AvailabilityService implements AvailabilityServiceInterface
{
    public function __construct(private AvailabilityRepositoryInterface $availabilityRepositoryInterface) {}

    public function index($request, $provider): LengthAwarePaginator
    {
        return $this->availabilityRepositoryInterface->index($request, $provider);
    }

    public function store($request, $provider): Availability
    {
        return $this->availabilityRepositoryInterface->store($request, $provider);
    }

    public function update($request, $availability): Availability
    {
        return $this->availabilityRepositoryInterface->update($request, $availability);
    }

    public function destroy($availability): bool
    {
        return $this->availabilityRepositoryInterface->destroy($availability);
    }
}
