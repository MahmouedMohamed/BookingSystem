<?php

namespace App\Modules\Availabilities\Providers;

use App\Modules\Availabilities\Interfaces\AvailabilityRepositoryInterface;
use App\Modules\Availabilities\Interfaces\AvailabilityServiceInterface;
use App\Modules\Availabilities\Services\AvailabilityService;
use App\Modules\Availabilities\Repositories\AvailabilityRepository;
use Illuminate\Support\ServiceProvider;

class AvailabilityServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AvailabilityServiceInterface::class, AvailabilityService::class);
        $this->app->bind(AvailabilityRepositoryInterface::class, AvailabilityRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
