<?php

namespace App\Modules\Bookings\Jobs;

use App\Modules\Bookings\Models\Booking;
use App\Modules\Services\Models\Service;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class InvalidateBookingsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(private Service $service)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Booking::where('service_id', $this->service->id)
            ->whereIn('status', ['PENDING', 'CONFIRMED'])
            ->update(['status' => 'CANCELLED', 'cancelled_by' => null, 'cancelled_by_type' => 'SYSTEM', 'cancellation_reason' => 'SERVICE RELATED CHANGED']);
    }
}
