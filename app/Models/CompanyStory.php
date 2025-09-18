<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyStory extends Model
{
    protected $table = 'company_story';

    protected $fillable = [
        'paragraph1_ar',
        'paragraph1_en',
        'paragraph2_ar',
        'paragraph2_en',
        'paragraph3_ar',
        'paragraph3_en',
        'features'
    ];

    protected $casts = [
        'features' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get company story instance (singleton pattern)
     */
    public static function getInstance()
    {
        return static::first() ?? static::create([
            'paragraph1_ar' => 'منذ تأسيسنا في عام 2009، ونحن نسعى لتقديم أفضل أدوات البناء والمعدات الصناعية في مصر والشرق الأوسط.',
            'paragraph1_en' => 'Since our foundation in 2009, we have been striving to provide the best construction tools and industrial equipment in Egypt and the Middle East.',
            'paragraph2_ar' => 'فريقنا المتخصص يعمل على توفير حلول شاملة لجميع احتياجات البناء والتشييد بأعلى معايير الجودة.',
            'paragraph2_en' => 'Our specialized team works to provide comprehensive solutions for all construction and building needs with the highest quality standards.',
            'paragraph3_ar' => 'نفخر بثقة عملائنا وشراكتنا الطويلة معهم في تحقيق مشاريعهم بنجاح وكفاءة عالية.',
            'paragraph3_en' => 'We are proud of our clients\' trust and our long-term partnership in achieving their projects successfully and efficiently.',
            'features' => [
                ['name_ar' => 'منتجات عالية الجودة', 'name_en' => 'Premium Quality Products'],
                ['name_ar' => 'معايير الأمان العالمية', 'name_en' => 'International Safety Standards'],
                ['name_ar' => 'الابتكار والتطوير', 'name_en' => 'Innovation & Development'],
                ['name_ar' => 'التميز في الخدمة', 'name_en' => 'Service Excellence']
            ]
        ]);
    }

    /**
     * Get paragraph by number and language
     */
    public function getParagraph($number, $lang = 'ar')
    {
        $field = "paragraph{$number}_" . ($lang === 'en' ? 'en' : 'ar');
        return $this->$field;
    }
}
