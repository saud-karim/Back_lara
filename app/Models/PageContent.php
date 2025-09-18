<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageContent extends Model
{
    protected $table = 'page_content';

    protected $fillable = [
        'about_page',
        'contact_page'
    ];

    protected $casts = [
        'about_page' => 'array',
        'contact_page' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get page content instance (singleton pattern)
     */
    public static function getInstance()
    {
        return static::first() ?? static::create([
            'about_page' => [
                'badge_ar' => 'من نحن',
                'badge_en' => 'About Us',
                'title_ar' => 'نبني المستقبل معاً',
                'title_en' => 'Building the Future Together',
                'subtitle_ar' => 'شركة رائدة في مجال أدوات ومواد البناء منذ أكثر من 15 عاماً',
                'subtitle_en' => 'A leading company in construction tools and materials for over 15 years'
            ],
            'contact_page' => [
                'badge_ar' => 'تواصل معنا',
                'badge_en' => 'Contact Us',
                'title_ar' => 'اتصل بنا',
                'title_en' => 'Contact Us',
                'subtitle_ar' => 'نحن هنا لمساعدتك. تواصل معنا في أي وقت',
                'subtitle_en' => 'We\'re here to help you. Contact us anytime'
            ]
        ]);
    }

    /**
     * Get content by page and key
     */
    public function getContent($page, $key, $lang = 'ar')
    {
        $pageContent = $this->{$page . '_page'} ?? [];
        $fullKey = $key . '_' . ($lang === 'en' ? 'en' : 'ar');
        return $pageContent[$fullKey] ?? $pageContent[$key] ?? null;
    }
}
