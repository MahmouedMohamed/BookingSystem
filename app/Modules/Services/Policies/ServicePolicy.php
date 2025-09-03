<?php

namespace App\Modules\Services\Policies;

use App\Modules\Services\Models\Service;
use App\Modules\Users\Models\User;

class ServicePolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Service $service): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'provider' && $user->id === $service->provider_id) {
            return true;
        }

        if ($user->role === 'customer') {
            return $service->is_published;
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, User $provider): bool
    {
        return $user->role === 'admin' || ($user->role === 'provider' && $user->id == $provider->id);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Service $service): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'provider') {
            return $user->id === $service->provider_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Service $service): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'provider') {
            return $user->id === $service->provider_id;
        }

        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Service $service): bool
    {
        if ($user->role === 'admin') {
            return true;
        }

        if ($user->role === 'provider') {
            return $user->id === $service->provider_id;
        }

        return false;
    }
}
