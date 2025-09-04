<?php

namespace App\Modules\Bookings\Interfaces;

use App\Modules\Bookings\Models\Booking;
use Illuminate\Pagination\LengthAwarePaginator;

interface BookingServiceInterface
{
    public function index($request): LengthAwarePaginator;

    public function store($request): Booking;

    public function find($id, $withTrashed = false): Booking;

    public function updateStatus($booking, $action): Booking;

    public function destroy($booking): bool;

    public function restore($booking): Booking;
}
