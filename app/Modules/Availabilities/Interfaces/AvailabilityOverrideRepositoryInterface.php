<?php

namespace App\Modules\Availabilities\Interfaces;

use App\Modules\Availabilities\Models\AvailabilityOverride;
use Illuminate\Pagination\LengthAwarePaginator;

interface AvailabilityOverrideRepositoryInterface
{
    public function index($request, $provider): LengthAwarePaginator;

    public function store($request, $provider): AvailabilityOverride;

    public function update($request, $availability): AvailabilityOverride;

    public function destroy($availability): bool;
}
