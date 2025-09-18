<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyStats extends Model
{
    protected $table = 'company_stats';

    protected $fillable = [
        'years_experience',
        'total_customers',
        'completed_projects',
        'support_availability'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get company stats instance (singleton pattern)
     */
    public static function getInstance()
    {
        return static::first() ?? static::create([
            'years_experience' => '15+',
            'total_customers' => '50K+',
            'completed_projects' => '1000+',
            'support_availability' => '24/7'
        ]);
    }
}
