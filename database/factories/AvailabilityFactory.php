<?php

namespace Database\Factories;

use App\Modules\Availabilities\Models\Availability;
use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Availability>
 */
class AvailabilityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Availability::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider_id' => User::factory(),
            'weekday' => fake()->numberBetween(0, 6), // 0=Sunday, 6=Saturday
            'start' => fake()->randomElement(['09:00:00', '10:00:00', '11:00:00']),
            'end' => fake()->randomElement(['17:00:00', '18:00:00']),
        ];
    }
}
