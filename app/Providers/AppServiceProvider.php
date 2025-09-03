<?php

namespace App\Providers;

use App\Modules\Services\Providers\CategoryServiceProvider;
use App\Modules\Users\Providers\UserServiceProvider;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(UserServiceProvider::class);
        $this->app->register(CategoryServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
