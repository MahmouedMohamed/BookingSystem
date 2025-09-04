<?php

namespace App\Modules\Bookings\Providers;

use App\Modules\Bookings\Interfaces\BookingRepositoryInterface;
use App\Modules\Bookings\Interfaces\BookingServiceInterface;
use App\Modules\Bookings\Interfaces\SlotServiceInterface;
use App\Modules\Bookings\Services\BookingService;
use App\Modules\Bookings\Services\SlotService;
use App\Modules\Bookings\Repositories\BookingRepository;
use App\Traits\ApiResponse;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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
        $this->app->bind(BookingServiceInterface::class, BookingService::class);
        $this->app->bind(BookingRepositoryInterface::class, BookingRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Rate Limiting for Bookings Endpoints
        // I made it 10/min for debugging purpose, increase it if you want
        RateLimiter::for('bookings', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip())->response(function () {
                return $this->sendErrorResponse('Too many booking requests. Please try again later.', 429);
            });
        });
    }
}
