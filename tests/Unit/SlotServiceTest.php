<?php

namespace Tests\Unit;

use App\Modules\Availabilities\Models\Availability;
use App\Modules\Availabilities\Models\AvailabilityOverride;
use App\Modules\Bookings\Exceptions\InvalidServiceException;
use App\Modules\Bookings\Interfaces\SlotServiceInterface;
use App\Modules\Bookings\Models\Booking;
use App\Modules\Services\Models\Service;
use App\Modules\Users\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SlotServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected SlotServiceInterface $slotService;

    protected User $provider;

    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->slotService = app(SlotServiceInterface::class);

        $this->provider = User::factory()->create([
            'timezone' => 0,
        ]);

        $this->service = Service::factory()->create([
            'provider_id' => $this->provider->id,
            'is_published' => true,
            'duration' => 60,
        ]);

        $viewer = User::factory()->create(['timezone' => 0]);
        Auth::login($viewer);

        Cache::flush();
        Carbon::setTestNow(Carbon::parse('2025-09-05 09:00:00', 'UTC'));
    }

    #[Test]
    public function it_throws_if_service_not_published()
    {
        $this->service->update(['is_published' => false]);

        $this->expectException(InvalidServiceException::class);

        $this->slotService->index($this->provider, $this->service, Auth::user()->timezone);
    }

    #[Test]
    public function it_returns_available_slots_without_bookings_or_overrides()
    {
        Availability::create([
            'provider_id' => $this->provider->id,
            'weekday' => Carbon::now()->dayOfWeek,
            'start' => '09:00:00',
            'end' => '12:00:00',
        ]);

        $slots = $this->slotService->index($this->provider, $this->service, Auth::user()->timezone);

        $this->assertNotEmpty($slots);
        $this->assertEquals('2025-09-05', $slots->keys()->first());
    }

    #[Test]
    public function it_skips_slots_conflicting_with_bookings()
    {
        Availability::create([
            'provider_id' => $this->provider->id,
            'weekday' => Carbon::now()->dayOfWeek,
            'start' => '09:00:00',
            'end' => '12:00:00',
        ]);

        Booking::create([
            'customer_id' => User::factory()->create()->id,
            'provider_id' => $this->provider->id,
            'service_id' => $this->service->id,
            'start_date' => Carbon::parse('2025-09-05 09:00:00'),
            'end_date' => Carbon::parse('2025-09-05 10:00:00'),
            'status' => 'CONFIRMED',
        ]);

        $slots = $this->slotService->index($this->provider, $this->service, Auth::user()->timezone);

        $firstSlot = $slots->first()->first();
        $this->assertStringContainsString('10:00', $firstSlot['start_at']);
    }

    #[Test]
    public function it_skips_slots_conflicting_with_overrides()
    {
        Availability::create([
            'provider_id' => $this->provider->id,
            'weekday' => Carbon::now()->dayOfWeek,
            'start' => '09:00:00',
            'end' => '12:00:00',
        ]);

        AvailabilityOverride::create([
            'provider_id' => $this->provider->id,
            'date' => '2025-09-05',
            'start' => '09:00:00',
            'end' => '10:00:00',
            'recurring' => false,
            'number_of_recurring' => 0,
        ]);

        $slots = $this->slotService->index($this->provider, $this->service, Auth::user()->timezone);

        $firstSlot = $slots->first()->first();
        $this->assertStringContainsString('10:00', $firstSlot['start_at']);
    }
}
