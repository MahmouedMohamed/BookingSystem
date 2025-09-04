<?php

namespace App\Modules\Bookings\Interfaces;

use Illuminate\Support\Collection;

interface SlotServiceInterface
{
    public function index($provider, $service): Collection;
}
