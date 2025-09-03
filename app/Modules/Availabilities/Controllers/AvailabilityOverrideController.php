<?php

namespace App\Modules\Availabilities\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Availabilities\Interfaces\AvailabilityOverrideServiceInterface;
use App\Modules\Availabilities\Models\AvailabilityOverride;
use App\Modules\Availabilities\Requests\StoreAvailabilityOverrideRequest;
use App\Modules\Availabilities\Requests\UpdateAvailabilityOverrideRequest;
use App\Modules\Availabilities\Resources\AvailabilityOverrideCollectionResource;
use App\Modules\Availabilities\Resources\AvailabilityOverrideResource;
use App\Modules\Users\Models\User;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class AvailabilityOverrideController extends Controller
{
    use ApiResponse;

    public function __construct(private AvailabilityOverrideServiceInterface $availabilityOverrideService) {}


    public function index(Request $request, User $provider)
    {
        try {
            $this->authorize('viewAny', [AvailabilityOverride::class, $provider]);

            $availabilitiesOverrides = $this->availabilityOverrideService->index($request, $provider);

            return $this->sendSuccessResponse('AvailabilitiesOverrides retrieved successfully', new AvailabilityOverrideCollectionResource($availabilitiesOverrides));
        } catch(AuthorizationException $e){
            throw $e;
        }catch (Exception $e) {
            return $this->sendErrorResponse('Failed to retrieve availabilitiesOverrides: '.$e->getMessage());
        }
    }

    public function store(StoreAvailabilityOverrideRequest $request, User $provider)
    {
        try {
            $this->authorize('create', [AvailabilityOverride::class, $provider]);

            $availabilityOverride = $this->availabilityOverrideService->store($request, $provider);

            return $this->sendSuccessResponse('AvailabilityOverride created successfully', new AvailabilityOverrideResource($availabilityOverride), 'item', 201);
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to create availabilityOverride: '.$e->getMessage());
        }
    }

    public function update(UpdateAvailabilityOverrideRequest $request, User $provider, AvailabilityOverride $availabilitiesOverride)
    {
        try {
            $this->authorize('update', $availabilitiesOverride);

            $availabilityOverride = $this->availabilityOverrideService->update($request, $availabilitiesOverride);

            return $this->sendSuccessResponse('Availability updated successfully', new AvailabilityOverrideResource($availabilityOverride), 'item');
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to update availabilityOverride: '.$e->getMessage());
        }
    }

    public function destroy(User $provider, AvailabilityOverride $availabilitiesOverride)
    {
        try {
            $this->authorize('delete', $availabilitiesOverride);

            $this->availabilityOverrideService->destroy($availabilitiesOverride);

            return $this->sendSuccessResponse('AvailabilityOverride deleted successfully', [], 'item');
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to delete availabilityOverride: '.$e->getMessage());
        }
    }
}
