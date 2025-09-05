<?php

namespace App\Modules\Bookings\Notifications;

use App\Modules\Bookings\Models\Booking;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SendBookingSubmittedNotification extends Notification implements ShouldBroadcast, ShouldQueue
{
    use Queueable;

    public function __construct(private Booking $booking) {}

    // Add this to tell Laravel which channels to use
    public function via($notifiable)
    {
        return ['broadcast'];
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('booking.'.$this->booking->service->provider->id),
        ];
    }

    public function broadcastWith(): array
    {
        return [
            'booking_id' => $this->booking->id,
            'status' => $this->booking->status,
            'customer_name' => $this->booking->customer->name,
            'service_name' => $this->booking->service->name,
        ];
    }

    public function toArray($notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'status' => $this->booking->status,
            'customer_name' => $this->booking->customer->name,
            'service_name' => $this->booking->service->name,
        ];
    }

    public function broadcastAs(): string
    {
        return 'BookingSubmitted';
    }
}
