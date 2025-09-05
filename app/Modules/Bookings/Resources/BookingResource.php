<?php

namespace App\Modules\Bookings\Resources;

use App\Modules\Services\Resources\ServiceResource;
use App\Modules\Users\Resources\UserResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $viewerTimezone = Auth::user()->timezone;

        return [
            'id' => $this->id,
            'customer' => UserResource::make($this->customer),
            'service' => ServiceResource::make($this->service),
            'start_date' => Carbon::parse($this->start_date)->setTimezone($viewerTimezone)->format('Y-m-d H:i:s'),
            'end_date' => Carbon::parse($this->end_date)->setTimezone($viewerTimezone)->format('Y-m-d H:i:s'),
            'status' => $this->status,
            'cancelled_by_type' => $this->cancelled_by_type,
            'cancelled_by' => $this->cancelledBy ? UserResource::make($this->cancelledBy) : null,
            'cancellation_reason' => $this->cancellation_reason,
        ];
    }
}
