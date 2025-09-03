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

        // Define services for each category
        $categoryServices = [
            'Items Care' => ['Laundry', 'Cleaning', 'Ironing'],
            'Personal Care' => ['Shaving', 'Haircut', 'Massage']
        ];

        foreach ($providers as $provider) {
            foreach ($categories as $category) {
                if (isset($categoryServices[$category->name])) {
                    foreach ($categoryServices[$category->name] as $serviceName) {
                        Service::factory()->create([
                            'name' => $serviceName,
                            'category_id' => $category->id,
                            'provider_id' => $provider->id,
                            'description' => Str::ucfirst($serviceName) . ' service',
                        ]);
                    }
                }
            }
        }
    }
}
