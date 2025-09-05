<?php

namespace App\Modules\Availabilities\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Availabilities\Interfaces\AvailabilityServiceInterface;
use App\Modules\Availabilities\Models\Availability;
use App\Modules\Availabilities\Requests\StoreAvailabilityRequest;
use App\Modules\Availabilities\Requests\UpdateAvailabilityRequest;
use App\Modules\Availabilities\Resources\AvailabilityCollectionResource;
use App\Modules\Availabilities\Resources\AvailabilityResource;
use App\Modules\Users\Models\User;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    use ApiResponse;

    public function __construct(private AvailabilityServiceInterface $availabilityService) {}

    public function index(Request $request, User $provider)
    {
        try {
            $this->authorize('viewAny', [Availability::class, $provider]);

            $availabilities = $this->availabilityService->index($request, $provider);

            return $this->sendSuccessResponse('Availabilities retrieved successfully', new AvailabilityCollectionResource($availabilities));
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to retrieve availabilities: '.$e->getMessage());
        }
    }

    public function store(StoreAvailabilityRequest $request, User $provider)
    {
        try {
            $this->authorize('create', [Availability::class, $provider]);

            $availability = $this->availabilityService->store($request, $provider);

            return $this->sendSuccessResponse('Availability created successfully', new AvailabilityResource($availability), 'item', 201);
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to create availability: '.$e->getMessage());
        }
    }

    public function update(UpdateAvailabilityRequest $request, User $provider, Availability $availability)
    {
        try {
            $this->authorize('update', $availability);

            $availability = $this->availabilityService->update($request, $availability);

            return $this->sendSuccessResponse('Availability updated successfully', new AvailabilityResource($availability), 'item');
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to update availability: '.$e->getMessage());
        }
    }

    public function destroy(User $provider, Availability $availability)
    {
        try {
            $this->authorize('delete', $availability);

            $this->availabilityService->destroy($availability);

            return $this->sendSuccessResponse('Availability deleted successfully', [], 'item');
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to delete availability: '.$e->getMessage());
        }
    }
}
