<?php

namespace App\Providers;

use App\Modules\Availabilities\Providers\AvailabilityServiceProvider;
use App\Modules\Bookings\Providers\BookingServiceProvider;
use App\Modules\Services\Providers\CategoryServiceProvider;
use App\Modules\Services\Providers\ServiceProvider;
use App\Modules\Users\Providers\UserServiceProvider;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class AppServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(UserServiceProvider::class);
        $this->app->register(CategoryServiceProvider::class);
        $this->app->register(ServiceProvider::class);
        $this->app->register(AvailabilityServiceProvider::class);
        $this->app->register(BookingServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
