<?php

namespace App\Modules\Availabilities\Interfaces;

use App\Modules\Availabilities\Models\Availability;
use Illuminate\Pagination\LengthAwarePaginator;

interface AvailabilityRepositoryInterface
{
    public function index($request, $provider): LengthAwarePaginator;

    public function store($request, $provider): Availability;

    public function update($request, $availability): Availability;

    public function destroy($availability): bool;
}
