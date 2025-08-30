<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'price' => $this->price,
            'original_price' => $this->original_price,
            'rating' => $this->rating,
            'reviews_count' => $this->reviews_count,
            'stock' => $this->stock,
            'status' => $this->status,
            'featured' => $this->featured,
            'images' => $this->images,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'is_in_stock' => $this->isInStock(),
            'has_low_stock' => $this->hasLowStock(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 