<?php

namespace App\Modules\Bookings\Observers;

use App\Modules\Bookings\Events\BookingCreated;
use App\Modules\Bookings\Jobs\AutoCancelUnconfirmedBooking;
use App\Modules\Bookings\Models\Booking;
use App\Traits\CacheHelper;
use Carbon\Carbon;

class BookingObserver
{
    use CacheHelper;

    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        BookingCreated::dispatch($booking);
        AutoCancelUnconfirmedBooking::dispatch($booking)->delay(Carbon::now()->addMinutes(10));
        $this->invalidateProviderSlots($booking->provider_id);
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        if ($booking->isDirty('status')) {
            $originalStatus = $booking->getOriginal('status');
            $newStatus = $booking->status;
            if ($originalStatus == 'PENDING' && $newStatus == 'CONFIRMED') {
                $this->invalidateProviderSlots($booking->provider_id);
            }
            if ($originalStatus == 'CONFIRMED' && $newStatus == 'CANCELLED') {
                $this->invalidateProviderSlots($booking->provider_id);
            }
        }
    }

    /**
     * Handle the Booking "deleted" event.
     */
    public function deleted(Booking $booking): void
    {
        $this->invalidateProviderSlots($booking->provider_id);
    }

    /**
     * Handle the Booking "restored" event.
     */
    public function restored(Booking $booking): void
    {
        $this->invalidateProviderSlots($booking->provider_id);
    }

    /**
     * Handle the Booking "force deleted" event.
     */
    public function forceDeleted(Booking $booking): void
    {
        $this->invalidateProviderSlots($booking->provider_id);
    }
}
