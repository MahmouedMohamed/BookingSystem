<?php

namespace App\Modules\Analytics\Providers;

use App\Modules\Analytics\Interfaces\AnalyticsServiceInterface;
use App\Modules\Analytics\Interfaces\BookingAnalyticsRepositoryInterface;
use App\Modules\Analytics\Services\AnalyticsService;
use App\Modules\Analytics\Repositories\BookingAnalyticsRepository;
use Illuminate\Support\ServiceProvider;

class AnalyticsServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AnalyticsServiceInterface::class, AnalyticsService::class);
        $this->app->bind(BookingAnalyticsRepositoryInterface::class, BookingAnalyticsRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
