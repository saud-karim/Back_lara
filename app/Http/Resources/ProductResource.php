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
            'sale_price' => $this->sale_price,
            'rating' => $this->getAverageRatingSafe(),
            'reviews_count' => $this->getReviewsCountSafe(),
            'stock' => $this->stock,
            'status' => $this->status,
            'featured' => $this->featured,
            'images' => $this->parseImagesSafe(),
            'category' => new CategoryResource($this->whenLoaded('category')),
            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'is_in_stock' => $this->isInStock(),
            'has_low_stock' => $this->hasLowStock(),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Safely get average rating
     */
    private function getAverageRatingSafe()
    {
        try {
            // Try to get reviews from Review model (new system)
            if (class_exists('\App\Models\Review')) {
                $reviews = \App\Models\Review::where('product_id', $this->id)->where('status', 'approved');
                if ($reviews->count() > 0) {
                    return round($reviews->avg('rating'), 1);
                }
            }
            
            return 0;
        } catch (\Exception $e) {
            \Log::error('Error calculating average rating for product ' . $this->id . ': ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Safely get reviews count
     */
    private function getReviewsCountSafe()
    {
        try {
            // Try to get reviews from Review model (new system)
            if (class_exists('\App\Models\Review')) {
                return \App\Models\Review::where('product_id', $this->id)->where('status', 'approved')->count();
            }
            
            return 0;
        } catch (\Exception $e) {
            \Log::error('Error getting reviews count for product ' . $this->id . ': ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Safely parse images
     */
    private function parseImagesSafe()
    {
        try {
            $images = $this->images;
            
            // If images is already an array, return it
            if (is_array($images)) {
                return $images;
            }
            
            // If images is a JSON string, decode it
            if (is_string($images)) {
                $decoded = json_decode($images, true);
                return is_array($decoded) ? $decoded : [];
            }
            
            // Default fallback
            return [];
        } catch (\Exception $e) {
            \Log::error('Error parsing images for product ' . $this->id . ': ' . $e->getMessage());
            return [];
        }
    }
} 