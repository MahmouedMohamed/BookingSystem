<?php

namespace App\Modules\Availabilities\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailabilityOverrideResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'date' => $this->date?->format('Y-m-d'),
            'weekday' => $this->when($this->weekday != null, __('app.weekdays.'.$this->weekday), null),
            'start' => $this->start,
            'end' => $this->end,
            'recurring' => $this->recurring,
            'number_of_recurring' => $this->number_of_recurring,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
