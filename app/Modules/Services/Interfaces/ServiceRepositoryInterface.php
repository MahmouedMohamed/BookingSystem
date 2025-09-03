<?php

namespace App\Modules\Services\Interfaces;

use App\Modules\Services\Models\Service;
use Illuminate\Pagination\LengthAwarePaginator;

interface ServiceRepositoryInterface
{
    public function index($request, $with = []): LengthAwarePaginator;

    public function store($request): Service;

    public function find($id, $withTrashed = false): Service;

    public function update($request, $service): Service;

    public function destroy($service): bool;

    public function restore($id): Service;
}
