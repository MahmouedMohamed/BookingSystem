<?php

namespace App\Modules\Bookings\Commands;

use App\Modules\Bookings\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanBookingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clean-bookings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean Bookings Command';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Booking::where('end_date', '<', Carbon::now())
            ->where('status', '=', 'CONFIRMED')
            ->update([
                'status' => 'COMPLETED',
            ]);

        Booking::where('end_date', '<', Carbon::now())
            ->where('status', '=', 'PENDING')
            ->update([
                'status' => 'CANCELLED',
                'cancelled_by_type' => 'SYSTEM',
                'cancellation_reason' => 'NO CONFIRMATION AND TIME PASSED',
            ]);
    }
}
