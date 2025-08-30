<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'image' => $this->image,
            'status' => $this->status,
            'sort_order' => $this->sort_order,
            'products_count' => $this->products_count, // عدد المنتجات النشطة
            'full_path' => $this->full_path,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 