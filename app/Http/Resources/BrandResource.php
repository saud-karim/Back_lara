<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BrandResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // اكتشاف اللغة من الطلب
        $lang = $request->get('lang', 'en');
        
        return [
            'id' => $this->id,
            'name' => $lang === 'ar' ? ($this->name_ar ?: $this->name_en) : ($this->name_en ?: $this->name_ar),
            'description' => $lang === 'ar' ? ($this->description_ar ?: $this->description_en) : ($this->description_en ?: $this->description_ar),
            'logo' => $this->logo,
            'website' => $this->website,
            'status' => $this->status,
            'featured' => $this->featured,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
