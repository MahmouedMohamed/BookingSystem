<?php

namespace App\Providers;

use App\Modules\Availabilities\Models\Availability;
use App\Modules\Availabilities\Models\AvailabilityOverride;
use App\Modules\Availabilities\Policies\AvailabilityOverridePolicy;
use App\Modules\Availabilities\Policies\AvailabilityPolicy;
use App\Modules\Bookings\Models\Booking;
use App\Modules\Bookings\Policies\BookingPolicy;
use App\Modules\Services\Models\Category;
use App\Modules\Services\Models\Service;
use App\Modules\Services\Policies\CategoryPolicy;
use App\Modules\Services\Policies\ServicePolicy;
use App\Modules\Users\Models\User;
use App\Modules\Users\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => UserPolicy::class,
        Category::class => CategoryPolicy::class,
        Service::class => ServicePolicy::class,
        Availability::class => AvailabilityPolicy::class,
        AvailabilityOverride::class => AvailabilityOverridePolicy::class,
        Booking::class => BookingPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}
