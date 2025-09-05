<?php

namespace App\Modules\Availabilities\Observers;

use App\Modules\Availabilities\Models\AvailabilityOverride;
use App\Traits\CacheHelper;

class AvailabilityOverrideObserver
{
    use CacheHelper;

    /**
     * Handle the AvailabilityOverride "created" event.
     */
    public function created(AvailabilityOverride $availabilityOverride): void
    {
        $this->invalidateProviderSlots($availabilityOverride->provider_id);
    }

    /**
     * Handle the AvailabilityOverride "updated" event.
     */
    public function updated(AvailabilityOverride $availabilityOverride): void
    {
        $this->invalidateProviderSlots($availabilityOverride->provider_id);
    }

    /**
     * Handle the AvailabilityOverride "deleted" event.
     */
    public function deleted(AvailabilityOverride $availabilityOverride): void
    {
        $this->invalidateProviderSlots($availabilityOverride->provider_id);
    }
}
