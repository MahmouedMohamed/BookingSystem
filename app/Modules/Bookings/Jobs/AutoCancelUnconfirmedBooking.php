<?php

namespace App\Modules\Bookings\Jobs;

use App\Modules\Bookings\Models\Booking;
use App\Traits\CacheHelper;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AutoCancelUnconfirmedBooking implements ShouldQueue
{
    use CacheHelper, Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private Booking $booking)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->booking->status != 'PENDING') {
            return;
        }
        $this->booking->update([
            'status' => 'CANCELLED',
            'cancelled_by_type' => 'SYSTEM',
            'cancellation_reason' => 'NO CONFIRMATION AND TIME PASSED',
        ]);
        $this->invalidateProviderSlots($this->booking->provider_id);
    }
}
