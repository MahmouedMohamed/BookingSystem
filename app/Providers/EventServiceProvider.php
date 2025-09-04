<?php

namespace App\Providers;

use App\Modules\Bookings\Events\BookingCreated;
use App\Modules\Bookings\Listeners\SendBookingConfirmation;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        BookingCreated::class => [
            SendBookingConfirmation::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
