<?php

namespace App\Modules\Analytics\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Analytics\Interfaces\AnalyticsServiceInterface;
use App\Modules\Analytics\Resources\AnalyticsCollectionResource;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    use ApiResponse;

    public function __construct(private AnalyticsServiceInterface $analyticsServiceInterface) {}

    public function totalBookings(Request $request)
    {
        try {
            $analytics = $this->analyticsServiceInterface->totalBookings($request);

            return $this->sendSuccessResponse('Analytics retrieved successfully', new AnalyticsCollectionResource($analytics));
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to retrieve analytics: '.$e->getMessage());
        }
    }

    public function bookingsRate(Request $request)
    {
        try {
            $analytics = $this->analyticsServiceInterface->bookingsRate($request);

            return $this->sendSuccessResponse('Analytics retrieved successfully', $analytics);
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to retrieve analytics: '.$e->getMessage());
        }
    }

    public function peakHours(Request $request)
    {
        try {
            $analytics = $this->analyticsServiceInterface->peakHours($request);

            return $this->sendSuccessResponse('Analytics retrieved successfully', $analytics);
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to retrieve analytics: '.$e->getMessage());
        }
    }

    public function averageBookingsDuration(Request $request)
    {
        try {
            $analytics = $this->analyticsServiceInterface->averageBookingsDuration($request);

            return $this->sendSuccessResponse('Analytics retrieved successfully', $analytics);
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to retrieve analytics: '.$e->getMessage());
        }
    }
}
