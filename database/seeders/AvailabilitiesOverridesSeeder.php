<?php

namespace Database\Seeders;

use App\Modules\Availabilities\Models\AvailabilityOverride;
use App\Modules\Users\Models\User;
use Illuminate\Database\Seeder;

class AvailabilitiesOverridesSeeder extends Seeder
{
    public function run(): void
    {
        $providers = User::where('role', 'provider')->get();

        foreach ($providers as $provider) {
            AvailabilityOverride::factory()
                ->count(rand(1, 2))
                ->create([
                    'provider_id' => $provider->id,
                ]);
        }
    }
}
