<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyMilestone extends Model
{
    protected $fillable = [
        'year',
        'event_ar',
        'event_en',
        'description_ar',
        'description_en',
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
     * Scope for active milestones
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered milestones
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('year', 'asc')->orderBy('order', 'asc');
    }

    /**
     * Get event by language
     */
    public function getEvent($lang = 'ar')
    {
        return $lang === 'en' ? $this->event_en : $this->event_ar;
    }

    /**
     * Get description by language
     */
    public function getDescription($lang = 'ar')
    {
        return $lang === 'en' ? $this->description_en : $this->description_ar;
    }
}
