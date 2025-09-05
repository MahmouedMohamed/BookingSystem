<?php

namespace Database\Factories;

use App\Modules\Availabilities\Models\Availability;
use App\Modules\Bookings\Interfaces\SlotServiceInterface;
use App\Modules\Bookings\Models\Booking;
use App\Modules\Services\Models\Service;
use App\Modules\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Booking>
 */
class BookingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Booking::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $provider = User::query()->where('role', 'provider')->inRandomOrder()->first()
            ?? User::factory()->create(['role' => 'provider']);

        $service = Service::where('provider_id', $provider->id)->inRandomOrder()->first()
            ?? Service::factory()->create(['provider_id' => $provider->id]);

        $slotService = app(SlotServiceInterface::class);
        $slotsGrouped = $slotService->index($provider, $service, 'UTC');

        $allSlots = $slotsGrouped->flatten(1);
        if ($allSlots->isEmpty()) {
            return [];
        } else {
            $slot = $allSlots->random();
            $slotStart = Carbon::parse($slot['start_at']);
            $slotEnd = Carbon::parse($slot['end_at']);
        }

        // Convert to UTC for saving in DB
        $slotStartUTC = $slotStart->copy()->setTimezone('UTC');
        $slotEndUTC = $slotEnd->copy()->setTimezone('UTC');

        return [
            'customer_id' => User::factory()->create(['role' => 'customer']),
            'provider_id' => $provider->id,
            'service_id' => $service->id,
            'start_date' => $slotStartUTC,
            'end_date' => $slotEndUTC,
            'status' => $this->faker->randomElement(['PENDING', 'CONFIRMED', 'CANCELLED', 'COMPLETED']),
        ];
    }
}
