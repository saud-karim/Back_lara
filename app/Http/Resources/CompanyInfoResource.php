<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyInfoResource extends JsonResource
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
            // Multilingual fields only (no duplicates)
            'company_name_ar' => $this->company_name_ar,
            'company_name_en' => $this->company_name_en,
            'company_description_ar' => $this->company_description_ar,
            'company_description_en' => $this->company_description_en,
            'mission_ar' => $this->mission_ar,
            'mission_en' => $this->mission_en,
            'vision_ar' => $this->vision_ar,
            'vision_en' => $this->vision_en,
            // Other fields
            'logo_text' => $this->logo_text,
            'founded_year' => $this->founded_year,
            'employees_count' => $this->employees_count,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s')
        ];
    }
}
