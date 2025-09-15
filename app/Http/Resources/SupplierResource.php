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
            'certifications' => $this->getCertificationsSafe(),
            'certification_count' => $this->getCertificationCountSafe(),
            'verified' => $this->verified,
            'status' => $this->status,
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Safely get certifications
     */
    private function getCertificationsSafe()
    {
        try {
            $certifications = $this->certifications;
            
            // If certifications is already an array, return it
            if (is_array($certifications)) {
                return $certifications;
            }
            
            // If certifications is a JSON string, decode it
            if (is_string($certifications)) {
                $decoded = json_decode($certifications, true);
                return is_array($decoded) ? $decoded : [];
            }
            
            // Default fallback
            return [];
        } catch (\Exception $e) {
            \Log::error('Error parsing certifications for supplier ' . $this->id . ': ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Safely get certification count
     */
    private function getCertificationCountSafe()
    {
        try {
            $certifications = $this->getCertificationsSafe();
            return is_countable($certifications) ? count($certifications) : 0;
        } catch (\Exception $e) {
            \Log::error('Error counting certifications for supplier ' . $this->id . ': ' . $e->getMessage());
            return 0;
        }
    }
} 