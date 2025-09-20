<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactInfo extends Model
{
    protected $table = 'contact_info';

    protected $fillable = [
        'main_phone',
        'secondary_phone',
        'toll_free',
        'main_email',
        'sales_email',
        'support_email',
        'address_street',
        'address_district',
        'address_city',
        'address_country',
        // New multilingual address fields
        'address_street_ar',
        'address_street_en',
        'address_district_ar',
        'address_district_en',
        'address_city_ar',
        'address_city_en',
        'address_country_ar',
        'address_country_en',
        // Working hours fields
        'working_hours_weekdays',
        'working_hours_friday',
        'working_hours_saturday',
        // New multilingual working hours fields
        'working_hours_weekdays_ar',
        'working_hours_weekdays_en',
        'working_hours_friday_ar',
        'working_hours_friday_en',
        'working_hours_saturday_ar',
        'working_hours_saturday_en',
        // Additional multilingual labels
        'emergency_phone_label_ar',
        'emergency_phone_label_en',
        'toll_free_label_ar',
        'toll_free_label_en',
        'whatsapp',
        'google_maps_url'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get contact info instance (singleton pattern)
     */
    public static function getInstance()
    {
        return static::first() ?? static::create([
            'main_phone' => '+20 123 456 7890',
            'secondary_phone' => '+20 987 654 3210',
            'toll_free' => '+20 800 123 456',
            'main_email' => 'info@bstools.com',
            'sales_email' => 'sales@bstools.com',
            'support_email' => 'support@bstools.com',
            'address_street' => 'شارع التحرير 123',
            'address_district' => 'وسط البلد',
            'address_city' => 'القاهرة',
            'address_country' => 'مصر',
            // Arabic multilingual fields
            'address_street_ar' => 'شارع التحرير، المعادي',
            'address_street_en' => 'Tahrir Street, Maadi',
            'address_district_ar' => 'المعادي',
            'address_district_en' => 'Maadi',
            'address_city_ar' => 'القاهرة',
            'address_city_en' => 'Cairo',
            'address_country_ar' => 'مصر',
            'address_country_en' => 'Egypt',
            // Working hours multilingual
            'working_hours_weekdays' => 'السبت - الخميس: 8:00 صباحاً - 6:00 مساءً',
            'working_hours_weekdays_ar' => 'الأحد - الخميس: 9:00 ص - 6:00 م',
            'working_hours_weekdays_en' => 'Sunday - Thursday: 9:00 AM - 6:00 PM',
            'working_hours_friday' => 'الجمعة: مغلق',
            'working_hours_friday_ar' => 'الجمعة: مغلق',
            'working_hours_friday_en' => 'Friday: Closed',
            'working_hours_saturday' => 'السبت: 9:00 صباحاً - 2:00 مساءً',
            'working_hours_saturday_ar' => 'السبت: 9:00 ص - 2:00 م',
            'working_hours_saturday_en' => 'Saturday: 9:00 AM - 2:00 PM',
            // Labels
            'emergency_phone_label_ar' => 'الطوارئ',
            'emergency_phone_label_en' => 'Emergency',
            'toll_free_label_ar' => 'الخط المجاني',
            'toll_free_label_en' => 'Toll Free',
            'whatsapp' => '+20 100 000 0001',
            'google_maps_url' => 'https://maps.google.com/maps?q=30.0444196,31.2357116&z=15&output=embed'
        ]);
    }

    /**
     * Get formatted address
     */
    public function getFullAddressAttribute()
    {
        return collect([
            $this->address_street,
            $this->address_district,
            $this->address_city,
            $this->address_country
        ])->filter()->implode(', ');
    }
}
