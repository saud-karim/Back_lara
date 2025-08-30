<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'name',
        'rating',
        'certifications',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'decimal:1',
        'certifications' => 'array',
    ];

    /**
     * Get the user that owns the supplier.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the products for the supplier.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Check if supplier has good rating.
     */
    public function hasGoodRating(float $threshold = 4.0): bool
    {
        return $this->rating >= $threshold;
    }

    /**
     * Get certification count.
     */
    public function getCertificationCountAttribute(): int
    {
        return count($this->certifications ?? []);
    }
} 