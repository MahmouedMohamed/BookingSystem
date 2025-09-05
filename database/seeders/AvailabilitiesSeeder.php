<?php

namespace Database\Seeders;

use App\Modules\Availabilities\Models\Availability;
use App\Modules\Services\Models\Category;
use App\Modules\Users\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AvailabilitiesSeeder extends Seeder
{
    public function run(): void
    {
        $providers = User::where('role', 'provider')->get();

        foreach ($providers as $provider) {
            foreach ([1, 3, 5] as $weekday) { // Monday, Wednesday, Friday
                Availability::factory()->create([
                    'provider_id' => $provider->id,
                    'weekday' => $weekday,
                    'start' => '09:00:00',
                    'end'   => '17:00:00',
                ]);
            }
        }
    }
}
