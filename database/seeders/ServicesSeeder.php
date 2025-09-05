<?php

namespace Database\Seeders;

use App\Modules\Services\Models\Category;
use App\Modules\Services\Models\Service;
use App\Modules\Users\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $providers = User::where('role', 'provider')->get();
        $categories = Category::all();

        foreach ($providers as $provider) {
            Service::factory()->count(5)->create([
                'provider_id' => $provider->id,
                'category_id' => $categories->random()->id,
            ]);
        }
    }
}
