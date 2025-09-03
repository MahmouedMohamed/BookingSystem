<?php

namespace App\Modules\Services\Interfaces;

use App\Modules\Services\Models\Service;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Naming may be misleading but best practice is not to name it like this
 * ServiceServiceInterface
 * Can be Also named as ServiceManagerInterface, ServiceCatalogInterface or Just like I named it.
 * We can Differ it from model from the namespace
 */
interface ServiceInterface
{
    public function index($request): LengthAwarePaginator;

    public function find($id, bool $withTrashed = false): Service;

    public function store($request): Service;

    public function update($request, $service): Service;

    public function destroy($service): bool;

    public function restore($service): Service;
}
