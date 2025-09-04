<?php

namespace App\Modules\Bookings\Listeners;

use App\Modules\Bookings\Events\BookingCreated;
use App\Modules\Bookings\Models\Booking;
use App\Modules\Bookings\Notifications\SendBookingConfirmationEmail;
use App\Modules\Bookings\Notifications\SendBookingSubmittedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendBookingConfirmation implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BookingCreated $event): void
    {
        /**
         * @var Booking $booking
         */
        $booking = $event->booking;

        $booking->customer->notify(new SendBookingConfirmationEmail($booking));

        $booking->service->provider->notify(new SendBookingSubmittedNotification($booking));
    }
}
