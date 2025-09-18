<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactInfoResource extends JsonResource
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
            // Phone numbers (no multilingual needed)
            'main_phone' => $this->main_phone,
            'secondary_phone' => $this->secondary_phone,
            'toll_free' => $this->toll_free,
            // Emails (no multilingual needed)
            'main_email' => $this->main_email,
            'sales_email' => $this->sales_email,
            'support_email' => $this->support_email,
            // WhatsApp
            'whatsapp' => $this->whatsapp,
            // Address - multilingual ONLY (no legacy fields)
            'address' => [
                'street_ar' => $this->address_street_ar,
                'street_en' => $this->address_street_en,
                'district_ar' => $this->address_district_ar,
                'district_en' => $this->address_district_en,
                'city_ar' => $this->address_city_ar,
                'city_en' => $this->address_city_en,
                'country_ar' => $this->address_country_ar,
                'country_en' => $this->address_country_en
            ],
            // Working hours - multilingual ONLY (no legacy fields)
            'working_hours' => [
                'weekdays_ar' => $this->working_hours_weekdays_ar,
                'weekdays_en' => $this->working_hours_weekdays_en,
                'friday_ar' => $this->working_hours_friday_ar,
                'friday_en' => $this->working_hours_friday_en,
                'saturday_ar' => $this->working_hours_saturday_ar,
                'saturday_en' => $this->working_hours_saturday_en
            ],
            // Labels - multilingual
            'labels' => [
                'emergency_ar' => $this->emergency_phone_label_ar,
                'emergency_en' => $this->emergency_phone_label_en,
                'toll_free_ar' => $this->toll_free_label_ar,
                'toll_free_en' => $this->toll_free_label_en
            ],
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s')
        ];
    }
}
