<?php

namespace App\Modules\Services\Controllers;

use App\Exceptions\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Modules\Services\Interfaces\ServiceInterface;
use App\Modules\Services\Models\Service;
use App\Modules\Services\Requests\StoreServiceRequest;
use App\Modules\Services\Requests\UpdateServiceRequest;
use App\Modules\Services\Resources\ServiceCollectionResource;
use App\Modules\Services\Resources\ServiceResource;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use ApiResponse;

    public function __construct(private ServiceInterface $service) {}

    public function index(Request $request)
    {
        try {
            $services = $this->service->index($request);

            return $this->sendSuccessResponse('Services retrieved successfully', new ServiceCollectionResource($services));
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to retrieve services: '.$e->getMessage());
        }
    }

    public function store(StoreServiceRequest $request)
    {
        try {
            $service = $this->service->store($request);

            return $this->sendSuccessResponse('Service created successfully', new ServiceResource($service), 'item', 201);
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to create service: '.$e->getMessage());
        }
    }

    public function show(Service $service)
    {
        try {
            $this->authorize('view', $service);

            return $this->sendSuccessResponse('Service retrieved successfully', new ServiceResource($service), 'item');
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Service not found', 404);
        }
    }

    public function update(UpdateServiceRequest $request, Service $service)
    {
        try {
            $this->authorize('update', $service);

            $service = $this->service->update($request, $service);

            return $this->sendSuccessResponse('Service updated successfully', new ServiceResource($service), 'item');
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to update service: '.$e->getMessage());
        }
    }

    public function destroy(Service $service)
    {
        try {
            $this->authorize('delete', $service);

            $this->service->destroy($service);

            return $this->sendSuccessResponse('Service deleted successfully', [], 'item');
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to delete service: '.$e->getMessage());
        }
    }

    public function restore(string $id)
    {
        try {
            $service = $this->service->find($id, true);

            $this->authorize('restore', $service);

            $this->service->restore($service);

            return $this->sendSuccessResponse('Service restored successfully', [], 'item');
        } catch (ModelNotFoundException $e) {
            throw $e;
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to restored service: '.$e->getMessage());
        }
    }
}
