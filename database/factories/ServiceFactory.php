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
            'provider_id' => User::factory(),
            'name' => fake()->name(),
            'description' => fake()->text(),
            'category_id' => Category::factory(),
            'duration' => fake()->numberBetween(1, 720), // From minute to 12 hours
            'price' => fake()->randomFloat(2, 1, 500),
            'is_published' => fake()->boolean(),
        ];
    }
}
