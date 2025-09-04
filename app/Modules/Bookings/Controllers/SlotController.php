<?php

namespace App\Modules\Bookings\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Bookings\Interfaces\SlotServiceInterface;
use App\Modules\Services\Models\Service;
use App\Modules\Bookings\Requests\SlotRequest;
use App\Modules\Bookings\Exceptions\InvalidServiceException;
use App\Modules\Users\Models\User;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;

class SlotController extends Controller
{
    use ApiResponse;

    public function __construct(private SlotServiceInterface $slotServiceInterface) {}

    public function index(Request $request, User $provider, Service $service)
    {
        try {
            $slots = $this->slotServiceInterface->index($provider, $service);

            return $this->sendSuccessResponse('Slots retrieved successfully', $slots);
        } catch (InvalidServiceException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to retrieve slots: '.$e->getMessage());
        }
    }
}
