<?php

namespace Database\Factories;

use App\Modules\Services\Models\Category;
use App\Modules\Services\Models\Service;
use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Service::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider_id' => User::factory()->state(['role' => 'provider']),
            'category_id' => Category::factory(),
            'name' => fake()->name(),
            'description' => fake()->sentence(),
            'duration' => fake()->randomElement([30, 45, 60, 90, 120]),
            'price' => fake()->randomFloat(2, 1, 500),
            'is_published' => fake()->boolean(),
        ];
    }
}
