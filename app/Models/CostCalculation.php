<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CostCalculation extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'area',
        'materials',
        'total_cost',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'area' => 'decimal:2',
        'materials' => 'array',
        'total_cost' => 'decimal:2',
    ];

    /**
     * Get the user that owns the cost calculation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get materials count.
     */
    public function getMaterialsCountAttribute(): int
    {
        return count($this->materials ?? []);
    }

    /**
     * Calculate cost per square meter.
     */
    public function getCostPerSquareMeterAttribute(): float
    {
        if ($this->area > 0) {
            return $this->total_cost / $this->area;
        }
        return 0;
    }
} 