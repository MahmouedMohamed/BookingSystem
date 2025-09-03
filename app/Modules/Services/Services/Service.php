<?php

namespace App\Modules\Services\Services;

use App\Modules\Services\Interfaces\ServiceInterface;
use App\Modules\Services\Interfaces\ServiceRepositoryInterface;
use App\Modules\Services\Models\Service as ServiceModel;
use Illuminate\Pagination\LengthAwarePaginator;

class Service implements ServiceInterface
{
    public function __construct(private ServiceRepositoryInterface $serviceRepository) {}

    public function index($request): LengthAwarePaginator
    {
        return $this->serviceRepository->index($request, ['provider', 'category']);
    }

    public function find($id, bool $withTrashed = false): ServiceModel
    {
        return $this->serviceRepository->find($id, $withTrashed);
    }

    public function store($request): ServiceModel
    {
        return $this->serviceRepository->store($request);
    }

    public function update($request, $service): ServiceModel
    {
        return $this->serviceRepository->update($request, $service);
    }

    public function destroy($service): bool
    {
        return $this->serviceRepository->destroy($service);
    }

    public function restore($service): ServiceModel
    {
        return $this->serviceRepository->restore($service);
    }
}
