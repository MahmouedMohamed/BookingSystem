<?php

namespace App\Modules\Bookings\Controllers;

use App\Exceptions\ModelNotFoundException;
use App\Http\Controllers\Controller;
use App\Modules\Bookings\Exceptions\BookingException;
use App\Modules\Bookings\Interfaces\BookingServiceInterface;
use App\Modules\Bookings\Models\Booking;
use App\Modules\Bookings\Requests\StoreBookingRequest;
use App\Modules\Bookings\Requests\UpdateBookingRequest;
use App\Modules\Bookings\Resources\BookingCollectionResource;
use App\Modules\Bookings\Resources\BookingResource;
use App\Traits\ApiResponse;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    use ApiResponse;

    public function __construct(private BookingServiceInterface $bookingService) {}

    public function index(Request $request)
    {
        try {
            $bookings = $this->bookingService->index($request);

            return $this->sendSuccessResponse('Bookings retrieved successfully', new BookingCollectionResource($bookings));
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to retrieve bookings: '.$e->getMessage());
        }
    }

    public function store(StoreBookingRequest $request)
    {
        try {
            $this->authorize('create', Booking::class);

            $booking = $this->bookingService->store($request);

            return $this->sendSuccessResponse('Booking created successfully', new BookingResource($booking), 'item');
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (BookingException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to create booking: '.$e->getMessage());
        }
    }

    public function updateStatus(Request $request, Booking $booking, string $action)
    {
        try {
            $this->authorize('update', $booking);

            $booking = $this->bookingService->updateStatus($booking, $action, $request->input('reason'));

            return $this->sendSuccessResponse('Booking updated successfully', new BookingResource($booking), 'item');
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (BookingException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to update booking: '.$e->getMessage());
        }
    }

    public function destroy(Booking $booking)
    {
        try {
            $this->authorize('delete', $booking);

            $this->bookingService->destroy($booking);

            return $this->sendSuccessResponse('Booking deleted successfully', [], 'item');
        } catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to delete booking: '.$e->getMessage());
        }
    }

    public function restore(string $id)
    {
        try {
            $booking = $this->bookingService->find($id, true);

            $this->authorize('restore', $booking);

            $this->bookingService->restore($booking);

            return $this->sendSuccessResponse('Booking restored successfully', [], 'item');
        } catch (ModelNotFoundException $e) {
            throw $e;
        }catch (AuthorizationException $e) {
            throw $e;
        } catch (Exception $e) {
            return $this->sendErrorResponse('Failed to restored booking: '.$e->getMessage());
        }
    }
}
