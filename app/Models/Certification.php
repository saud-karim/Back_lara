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
        'issuer_ar',
        'issuer_en',
        'issue_date',
        'expiry_date',
        'image',
        'order',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'issue_date' => 'date',
        'expiry_date' => 'date',
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

    /**
     * Get issuer by language
     */
    public function getIssuer($lang = 'ar')
    {
        return $lang === 'en' ? $this->issuer_en : $this->issuer_ar;
    }
}
