<?php

namespace App\Modules\Users\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollectionResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => UserResource::collection($this->collection),
            'links' => [
                'self' => url()->current(),
            ],
            'meta' => [
                'pagination' => [
                    'total' => $this->total(),
                    'per_page' => $this->perPage(),
                    'current_page' => $this->currentPage(),
                    'last_page' => $this->lastPage(),
                    'from' => $this->firstItem(),
                    'to' => $this->lastItem(),
                ],
            ],
        ];
    }
}
