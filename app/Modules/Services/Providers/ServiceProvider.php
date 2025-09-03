<?php

namespace App\Modules\Services\Providers;

use App\Modules\Services\Interfaces\ServiceInterface;
use App\Modules\Services\Interfaces\ServiceRepositoryInterface;
use App\Modules\Services\Repositories\ServiceRepository;
use App\Modules\Services\Services\Service;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ServiceInterface::class, Service::class);
        $this->app->bind(ServiceRepositoryInterface::class, ServiceRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
