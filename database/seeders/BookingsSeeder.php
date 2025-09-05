<?php

namespace Database\Seeders;

use App\Modules\Availabilities\Models\Availability;
use App\Modules\Bookings\Interfaces\SlotServiceInterface;
use App\Modules\Bookings\Models\Booking;
use App\Modules\Services\Models\Service;
use App\Modules\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookingsSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::where('role', 'customer')->get();
        $services = Service::with('provider')->where('is_published', true)->get();

        $slotService = app(SlotServiceInterface::class);

        foreach ($services as $service) {
            $provider = $service->provider;

            $customersToBook = $customers->random(min(1, $customers->count()));

            foreach ($customersToBook as $customer) {
                $slotsGrouped = $slotService->index($provider, $service, $customer->timezone);
                $allSlots = $slotsGrouped->flatten(1);

                if ($allSlots->isEmpty()) {
                    continue; // skip if no availability
                }

                $slot = $allSlots->random();
                $slotStart = Carbon::parse($slot['start_at']);
                $slotEnd = Carbon::parse($slot['end_at']);

                Booking::create([
                    'customer_id' => $customer->id,
                    'provider_id' => $provider->id,
                    'service_id' => $service->id,
                    'start_date' => $slotStart->copy()->setTimezone('UTC'),
                    'end_date' => $slotEnd->copy()->setTimezone('UTC'),
                    'status' => fake()->randomElement(['PENDING', 'CONFIRMED']),
                ]);
            }
        }
    }
}
