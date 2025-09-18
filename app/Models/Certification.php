<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'icon',
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
     * Scope for active certifications
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered certifications
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc');
    }

    /**
     * Get name by language
     */
    public function getName($lang = 'ar')
    {
        return $lang === 'en' ? $this->name_en : $this->name_ar;
    }

    /**
     * Get description by language
     */
    public function getDescription($lang = 'ar')
    {
        return $lang === 'en' ? $this->description_en : $this->description_ar;
    }
}
