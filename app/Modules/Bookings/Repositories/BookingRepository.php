<?php

namespace App\Modules\Bookings\Repositories;

use App\Exceptions\ModelNotFoundException;
use App\Modules\Bookings\Exceptions\BookingException;
use App\Modules\Bookings\Interfaces\BookingRepositoryInterface;
use App\Modules\Bookings\Models\Booking;
use Carbon\Carbon;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingRepository implements BookingRepositoryInterface
{
    const STATUS_PENDING = 'PENDING';
    const STATUS_CONFIRMED = 'CONFIRMED';
    const STATUS_CANCELLED = 'CANCELLED';
    const STATUS_COMPLETED = 'COMPLETED';

    const TRANSACTIONS = [
        self::STATUS_PENDING => [self::STATUS_CONFIRMED, self::STATUS_CANCELLED],
        self::STATUS_CONFIRMED => [self::STATUS_CANCELLED],
        self::STATUS_CANCELLED => [],
        self::STATUS_COMPLETED => [],
    ];

    public function canTransitionTo(string $newStatus, $oldStatus): bool
    {
        return in_array($newStatus, self::TRANSACTIONS[$oldStatus]);
    }

    public function index($request): LengthAwarePaginator
    {
        $query = Booking::with([
            'customer',
            'service.provider',
            'service.category',
            'cancelledBy'
        ])->when(Auth::user()->role == 'admin', function ($query) use ($request) {
            if ($request->get('provider_id')) {
                $query->provider($request->get('provider_id'));
            }
            if ($request->get('customer_id')) {
                $query->customer($request->get('customer_id'));
            }
        })->when(Auth::user()->role == 'provider', function ($query) {
            return $query->provider(Auth::user()->id);
        })->when(Auth::user()->role == 'customer', function ($query) {
            return $query->customer(Auth::user()->id);
        });

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($request->get('per_page', 15));
    }

    public function checkForBookingOverlapping($provider, $start, $end)
    {
        $overlap = Booking::where('provider_id', $provider->id)
            ->where('start_date', '=', $start)
            ->where('end_date', '=', $end)
            ->whereIn('status', ['PENDING', 'CONFIRMED'])
            ->exists();

        if ($overlap) {
            throw new BookingException('This slot is already booked.', 422);
        }
    }

    public function checkForAvailability($slots, $viewerStart, $viewerEnd, $start)
    {
        $slots = $slots[$viewerStart->toDateString()] ?? null;

        $pickedSlot = $slots->where('start_at', $viewerStart->toIso8601String())
            ->where('end_at', $viewerEnd->toIso8601String())
            ->first();

        if (!$slots || !$pickedSlot) {
            throw new BookingException('There\'s no slots at this time', 422);
        }

        if ($start->isPast()) {
            throw new BookingException('Cannot book in the past.', 422);
        }
    }

    public function store($request, $service, $slots): Booking
    {
        try {
            $provider = $service->provider;
            $viewerTimezone = Auth::user()->timezone;

            $viewerStart = Carbon::parse($request->start_date, $viewerTimezone);
            $viewerEnd = Carbon::parse($request->start_date, $viewerTimezone)->addMinutes($service->duration);

            $start = Carbon::parse($request->start_date, $viewerTimezone)->utc();
            $end = Carbon::parse($request->start_date, $viewerTimezone)->addMinutes($service->duration)->utc();

            // For Race Condition
            DB::beginTransaction();

            $this->checkForBookingOverlapping($provider, $start, $end);

            $this->checkForAvailability($slots, $viewerStart, $viewerEnd, $start);

            $booking = Booking::create([
                'customer_id' => Auth::user()->role == 'customer' ? Auth::id() : $request->input('customer_id'),
                'provider_id' => $provider->id,
                'service_id' => $service->id,
                'start_date' => $start,
                'end_date' => $end,
                'status' => 'PENDING',
            ]);
            DB::commit();
            return $booking;
        } catch (Exception $ex) {
            DB::rollBack();
            throw $ex;
        }
    }

    public function find($id, $withTrashed = false): Booking
    {
        $model = Booking::where('id', $id)->withTrashed($withTrashed)->first();
        if (empty($model)) {
            throw new ModelNotFoundException;
        }

        return $model;
    }

    public function updateStatus($booking, $action): Booking
    {
        $newStatus = $action == 'confirm' ? 'CONFIRMED' : 'CANCELLED';
        if(Carbon::parse($booking->start_date)->isPast()){
            throw new BookingException('Can\'t change status for past booking', 403);
        }
        if(!$this->canTransitionTo($newStatus, $booking->status)){
            throw new BookingException('Can\'t change status for booking', 403);
        }
        $booking->update([
           'status' => $newStatus
        ]);

        return $booking->fresh();
    }

    public function destroy($booking): bool
    {
        return $booking->delete();
    }

    public function restore($booking): Booking
    {
        $booking->restore();

        return $booking->fresh();
    }
}
