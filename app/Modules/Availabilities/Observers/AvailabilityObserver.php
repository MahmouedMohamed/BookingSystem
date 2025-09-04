<?php

namespace App\Modules\Availabilities\Observers;

use App\Modules\Availabilities\Models\Availability;
use App\Traits\CacheHelper;
use Illuminate\Support\Facades\Cache;

class AvailabilityObserver
{
    use CacheHelper;

    /**
     * Handle the Availability "created" event.
     */
    public function created(Availability $availability): void
    {
        $this->invalidateProviderSlots($availability->provider_id);
    }

    /**
     * Handle the Availability "updated" event.
     */
    public function updated(Availability $availability): void
    {
        $this->invalidateProviderSlots($availability->provider_id);
    }

    /**
     * Handle the Availability "deleted" event.
     */
    public function deleted(Availability $availability): void
    {
        $this->invalidateProviderSlots($availability->provider_id);
    }

    /**
     * Handle the Availability "restored" event.
     */
    public function restored(Availability $availability): void
    {
        //
    }
}
