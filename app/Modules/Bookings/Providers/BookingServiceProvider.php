<?php

namespace App\Modules\Bookings\Providers;

use App\Modules\Bookings\Interfaces\BookingRepositoryInterface;
use App\Modules\Bookings\Interfaces\BookingServiceInterface;
use App\Modules\Bookings\Interfaces\SlotServiceInterface;
use App\Modules\Bookings\Services\BookingService;
use App\Modules\Bookings\Services\SlotService;
use App\Modules\Bookings\Repositories\BookingRepository;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class BookingServiceProvider extends ServiceProvider
{
    use ApiResponse;

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
