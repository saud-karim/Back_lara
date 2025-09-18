<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyStoryResource extends JsonResource
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
            'paragraph1_ar' => $this->paragraph1_ar,
            'paragraph1_en' => $this->paragraph1_en,
            'paragraph2_ar' => $this->paragraph2_ar,
            'paragraph2_en' => $this->paragraph2_en,
            'paragraph3_ar' => $this->paragraph3_ar,
            'paragraph3_en' => $this->paragraph3_en,
            'features' => $this->features ?? [],
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s')
        ];
    }
}
