<?php

namespace App\Modules\Bookings\Services;

use App\Modules\Bookings\Interfaces\BookingRepositoryInterface;
use App\Modules\Bookings\Interfaces\BookingServiceInterface;
use App\Modules\Bookings\Interfaces\SlotServiceInterface;
use App\Modules\Bookings\Models\Booking;
use App\Modules\Services\Interfaces\ServiceInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class BookingService implements BookingServiceInterface
{
    public function __construct(
        private BookingRepositoryInterface $bookingRepository,
        private ServiceInterface $service,
        private SlotServiceInterface $slotService
    ) {}

    public function index($request): LengthAwarePaginator
    {
        return $this->bookingRepository->index($request);
    }

    public function find($id, $withTrashed = false): Booking
    {
        return $this->bookingRepository->find($id, $withTrashed);
    }

    public function store($request): Booking
    {
        $service = $this->service->find($request->input('service_id'));
        $slots = $this->slotService->index($service->provider, $service, Auth::user()->timezone);

        return $this->bookingRepository->store($request, $service, $slots);
    }

    public function updateStatus($booking, $action, $reason = null): Booking
    {
        return $this->bookingRepository->updateStatus($booking, $action, $reason);
    }

    public function destroy($booking): bool
    {
        return $this->bookingRepository->destroy($booking);
    }

    public function restore($booking): Booking
    {
        return $this->bookingRepository->restore($booking);
    }
}
