<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'review',
        'status',
        'verified_purchase',
        'helpful_count',
        'images',
    ];

    protected $casts = [
        'rating' => 'integer',
        'verified_purchase' => 'boolean',
        'helpful_count' => 'integer',
        'images' => 'array',
    ];

    // الحالات المتاحة
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // الـ Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeVerifiedPurchase($query)
    {
        return $query->where('verified_purchase', true);
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    // Helper Methods
    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isVerifiedPurchase()
    {
        return $this->verified_purchase;
    }

    // إضافة إعجاب للتقييم
    public function incrementHelpful()
    {
        $this->increment('helpful_count');
    }
} 