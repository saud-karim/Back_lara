<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    protected $fillable = [
        'name_ar',
        'name_en',
        'role_ar',
        'role_en',
        'experience_ar',
        'experience_en',
        'specialty_ar',
        'specialty_en',
        'image',
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
     * Scope for active team members
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for ordered team members
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
     * Get role by language
     */
    public function getRole($lang = 'ar')
    {
        return $lang === 'en' ? $this->role_en : $this->role_ar;
    }

    /**
     * Get experience by language
     */
    public function getExperience($lang = 'ar')
    {
        return $lang === 'en' ? $this->experience_en : $this->experience_ar;
    }

    /**
     * Get specialty by language
     */
    public function getSpecialty($lang = 'ar')
    {
        return $lang === 'en' ? $this->specialty_en : $this->specialty_ar;
    }
}
