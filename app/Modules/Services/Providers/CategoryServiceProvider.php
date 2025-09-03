<?php

namespace App\Modules\Services\Providers;

use App\Modules\Services\Interfaces\CategoryRepositoryInterface;
use App\Modules\Services\Interfaces\CategoryServiceInterface;
use App\Modules\Services\Repositories\CategoryRepository;
use App\Modules\Services\Services\CategoryService;
use Illuminate\Support\ServiceProvider;

class CategoryServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
