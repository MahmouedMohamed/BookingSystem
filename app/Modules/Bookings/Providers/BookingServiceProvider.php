<?php

namespace App\Modules\Bookings\Providers;

use App\Modules\Bookings\Interfaces\SlotServiceInterface;
use App\Modules\Bookings\Services\SlotService;
use Illuminate\Support\ServiceProvider;

class BookingServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SlotServiceInterface::class, SlotService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
