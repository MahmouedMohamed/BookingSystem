<?php

namespace App\Traits;

use Illuminate\Support\Facades\Cache;

trait CacheHelper
{
    /**
     * I use database for caching
     * database don't support tags...
     * That's why making this functionality
     * Track keys manually for database cache invalidation
     */
    private function trackSlotCacheKey(int $providerId, int $serviceId, string $key, $ttl)
    {
        // Track by provider
        $providerKeys = Cache::get('slots_provider_'.$providerId.'_keys', []);
        if (! in_array($key, $providerKeys)) {
            $providerKeys[] = $key;
            Cache::put('slots_provider_'.$providerId.'_keys', $providerKeys, $ttl);
        }

        // Track by service
        $serviceKeys = Cache::get('slots_service_'.$serviceId.'_keys', []);
        if (! in_array($key, $serviceKeys)) {
            $serviceKeys[] = $key;
            Cache::put('slots_service_'.$serviceId.'_keys', $serviceKeys, $ttl);
        }
    }

    /**
     * Invalidate cached slots for a provider
     */
    public function invalidateProviderSlots(int $providerId)
    {
        $keys = Cache::get('slots_provider_'.$providerId.'_keys', []);
        foreach ($keys as $key) {
            Cache::forget($key);
        }
        Cache::forget('slots_provider_'.$providerId.'_keys');
    }
}
