<?php

namespace App\Modules\Users\Providers;

use App\Modules\Users\Interfaces\AuthServiceInterface;
use App\Modules\Users\Interfaces\UserRepositoryInterface;
use App\Modules\Users\Interfaces\UserServiceInterface;
use App\Modules\Users\Repositories\UserRepository;
use App\Modules\Users\Services\AuthService;
use App\Modules\Users\Services\UserService;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
