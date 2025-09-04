<?php

namespace App\Modules\Services\Observers;

use App\Modules\Bookings\Jobs\InvalidateBookingsJob;
use App\Modules\Services\Models\Service;
use App\Traits\CacheHelper;

class ServiceObserver
{
    use CacheHelper;
    /**
     * Handle the Service "updated" event.
     */
    public function updated(Service $service): void
    {
        if ($service->isDirty(['duration', 'is_published'])) {
            $this->invalidateProviderSlots($service->provider_id);
            InvalidateBookingsJob::dispatch($service);
        }
    }

    /**
     * Handle the Service "deleted" event.
     */
    public function deleted(Service $service): void
    {
            $this->invalidateProviderSlots($service->provider_id);
            InvalidateBookingsJob::dispatch($service);
    }

    /**
     * Handle the Service "force deleted" event.
     */
    public function forceDeleted(Service $service): void
    {
            $this->invalidateProviderSlots($service->provider_id);
    }
}
