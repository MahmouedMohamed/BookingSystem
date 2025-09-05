<?php

namespace Database\Factories;

use App\Modules\Availabilities\Models\AvailabilityOverride;
use App\Modules\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AvailabilityOverride>
 */
class AvailabilityOverrideFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AvailabilityOverride::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $date = Carbon::now()->addDays(fake()->numberBetween(1, 14));

        return [
            'provider_id' => User::factory(),
            'date' => $date->toDateString(),
            'weekday' => $date->dayOfWeek,
            'recurring' => fake()->boolean(),
            'number_of_recurring' => fake()->numberBetween(0, 4),
            'start' => '09:00:00',
            'end'   => '12:00:00',
        ];
    }
}
