<?php

namespace App\Modules\Bookings\Policies;

use App\Modules\Bookings\Models\Booking;
use App\Modules\Users\Models\User;

class BookingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role == 'admin' || $user->role == 'customer';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Booking $model): bool
    {
        return $user->role === 'admin' ||
            $model->customer->id == $user->id ||
            $model->service->provider->id == $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Booking $model): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return $user->role === 'admin';
    }
}
