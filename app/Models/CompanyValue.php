<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyValue extends Model
{
    protected $fillable = [
        'title_ar',
        'title_en',
        'description_ar',
        'description_en',
        'icon',
        'color',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Scope for active values
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered values
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Get title by language
     */
    public function getTitle($lang = 'ar')
    {
        return $lang === 'en' ? $this->title_en : $this->title_ar;
    }

    /**
     * Get description by language
     */
    public function getDescription($lang = 'ar')
    {
        return $lang === 'en' ? $this->description_en : $this->description_ar;
    }
}
