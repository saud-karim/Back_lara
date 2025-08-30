<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        // اكتشاف اللغة من الطلب
        $lang = $request->get('lang', 'en');
        
        return [
            'id' => $this->id,
            'name' => $lang === 'ar' ? ($this->name_ar ?: $this->name_en) : ($this->name_en ?: $this->name_ar),
            'description' => $lang === 'ar' ? ($this->description_ar ?: $this->description_en) : ($this->description_en ?: $this->description_ar),
            'email' => $this->email,
            'phone' => $this->phone,
            'rating' => $this->rating,
            'certifications' => $this->certifications,
            'certification_count' => count($this->certifications ?? []),
            'verified' => $this->verified,
            'status' => $this->status,
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 