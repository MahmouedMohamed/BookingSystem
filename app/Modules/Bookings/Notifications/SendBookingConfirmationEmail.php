<?php

namespace App\Modules\Bookings\Notifications;

use App\Modules\Bookings\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SendBookingConfirmationEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(private Booking $booking)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $confirmationUrl = env('APP_URL')."/api/bookings/{$this->booking->id}/confirm";

        return (new MailMessage)
            ->subject('Booking Submitted Successfully')
            ->line('Please confirm your booking (in 10 mins) by clicking the button below.')
            ->action('Confirm Booking', $confirmationUrl)
            ->line('Thank you for using our application!');
    }
}
