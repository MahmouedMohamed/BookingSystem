<?php

namespace Database\Factories;

use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'role' => fake()->randomElement(['admin', 'provider', 'customer']),
            'password' => static::$password ??= Hash::make('password'),
            'timezone' => fake()->numberBetween(-12, 12),
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): Factory
    {
        return $this->state(fn () => [
            'role' => 'admin',
        ]);
    }

    public function provider(): Factory
    {
        return $this->state(fn () => [
            'role' => 'provider',
        ]);
    }

    public function customer(): Factory
    {
        return $this->state(fn () => [
            'role' => 'customer',
        ]);
    }
}
