<?php

namespace App\Modules\Analytics\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Analytics\Exports\AverageBookingsDurationExport;
use App\Modules\Analytics\Exports\BookingsRateExport;
use App\Modules\Analytics\Exports\PeakHoursExport;
use App\Modules\Analytics\Exports\TotalBookingsExport;
use App\Modules\Analytics\Interfaces\AnalyticsServiceInterface;
use App\Modules\Analytics\Jobs\BaseExportShutterJob;
use App\Modules\Analytics\Resources\AnalyticsCollectionResource;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Ramsey\Uuid\Uuid;

class AnalyticsController extends Controller
{
    use ApiResponse;

    public function __construct(private AnalyticsServiceInterface $analyticsServiceInterface)
    {
        // Can be middleware but I kept "Role-based access control using Laravel Policies or Gates"
        Gate::allowIf(function ($user) {
            return $user->role == 'admin';
        });
    }

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

            return $this->sendSuccessResponse('Analytics retrieved successfully', new AnalyticsCollectionResource($analytics));
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

            return $this->sendSuccessResponse('Analytics retrieved successfully', new AnalyticsCollectionResource($analytics));
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to retrieve analytics: '.$e->getMessage());
        }
    }

    public function exportTotalBookings(Request $request)
    {
        try {
            $uuid = Uuid::uuid4()->toString();
            $name = 'Total Bookings';
            BaseExportShutterJob::dispatch(
                $uuid,
                $name,
                TotalBookingsExport::class,
                Auth::user(),
                ['request' => $request->toArray()]
            );

            return $this->sendSuccessResponse('Analytics Exported successfully');
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to export analytics: '.$e->getMessage());
        }
    }

    public function exportBookingsRate(Request $request)
    {
        try {
            $uuid = Uuid::uuid4()->toString();
            $name = 'Bookings Rate';
            BaseExportShutterJob::dispatch(
                $uuid,
                $name,
                BookingsRateExport::class,
                Auth::user(),
                ['request' => $request->toArray()]
            );

            return $this->sendSuccessResponse('Analytics Exported successfully');
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to export analytics: '.$e->getMessage());
        }
    }

    public function exportPeakHours(Request $request)
    {
        try {
            $uuid = Uuid::uuid4()->toString();
            $name = 'Peak Hours';
            BaseExportShutterJob::dispatch(
                $uuid,
                $name,
                PeakHoursExport::class,
                Auth::user(),
                ['request' => $request->toArray()]
            );

            return $this->sendSuccessResponse('Analytics Exported successfully');
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to export analytics: '.$e->getMessage());
        }
    }

    public function exportAverageBookingsDuration(Request $request)
    {
        try {
            $uuid = Uuid::uuid4()->toString();
            $name = 'Average Bookings Durations';
            BaseExportShutterJob::dispatch(
                $uuid,
                $name,
                AverageBookingsDurationExport::class,
                Auth::user(),
                ['request' => $request->toArray()]
            );

            return $this->sendSuccessResponse('Analytics Exported successfully');
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to export analytics: '.$e->getMessage());
        }
    }
}
