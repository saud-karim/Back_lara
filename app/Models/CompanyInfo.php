<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyInfo extends Model
{
    protected $table = 'company_info';

    protected $fillable = [
        'company_name',
        'company_description',
        'mission',
        'vision',
        'company_name_ar',
        'company_name_en',
        'company_description_ar',
        'company_description_en',
        'mission_ar',
        'mission_en',
        'vision_ar',
        'vision_en',
        'logo_text',
        'founded_year',
        'employees_count'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get company info instance (singleton pattern)
     */
    public static function getInstance()
    {
        return static::first() ?? static::create([
            'company_name' => 'Construction Tools',
            'company_description' => 'نحن شركة رائدة في مجال توريد أدوات البناء والمعدات الصناعية في مصر والشرق الأوسط',
            'mission' => 'تقديم أفضل أدوات البناء بجودة عالية وأسعار منافسة',
            'vision' => 'أن نكون الشريك الأول والموثوق لكل مقاول ومهندس في المنطقة',
            'logo_text' => 'BS',
            'founded_year' => '2009',
            'employees_count' => '50+'
        ]);
    }
}
