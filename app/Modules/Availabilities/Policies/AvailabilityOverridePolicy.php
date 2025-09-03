<?php

namespace App\Modules\Availabilities\Policies;

use App\Modules\Availabilities\Models\AvailabilityOverride;
use App\Modules\Users\Models\User;

class AvailabilityOverridePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, User $provider): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->id == $provider->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, User $provider): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->id == $provider->id;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AvailabilityOverride $availabilityOverride): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->id === $availabilityOverride->provider_id;
    }

    /**
     */
    public function delete(User $user, AvailabilityOverride $availabilityOverride): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        return $user->id === $availabilityOverride->provider_id;
    }
}
