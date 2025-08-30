<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'tracking_number' => $this->tracking_number,
            'estimated_delivery' => $this->estimated_delivery,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 