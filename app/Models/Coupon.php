<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'type',
        'value',
        'min_order_amount',
        'max_discount_amount',
        'usage_limit',
        'used_count',
        'valid_from',
        'valid_until',
        'status',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'max_discount_amount' => 'decimal:2',
        'usage_limit' => 'integer',
        'used_count' => 'integer',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
    ];

    // أنواع الكوبونات
    const TYPE_PERCENTAGE = 'percentage';
    const TYPE_FIXED = 'fixed';

    // حالات الكوبون
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_EXPIRED = 'expired';

    // الـ Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                    ->where(function ($q) {
                        $q->whereNull('valid_from')
                          ->orWhere('valid_from', '<=', now());
                    })
                    ->where(function ($q) {
                        $q->whereNull('valid_until')
                          ->orWhere('valid_until', '>=', now());
                    });
    }

    public function scopeAvailable($query)
    {
        return $query->active()
                    ->where(function ($q) {
                        $q->whereNull('usage_limit')
                          ->orWhereRaw('used_count < usage_limit');
                    });
    }

    // Helper Methods
    public function isValid()
    {
        return $this->status === self::STATUS_ACTIVE &&
               $this->isNotExpired() &&
               $this->hasUsageLeft();
    }

    public function isExpired()
    {
        if ($this->valid_until) {
            return Carbon::now()->gt($this->valid_until);
        }
        return false;
    }

    public function isNotExpired()
    {
        return !$this->isExpired();
    }

    public function hasUsageLeft()
    {
        if ($this->usage_limit) {
            return $this->used_count < $this->usage_limit;
        }
        return true;
    }

    public function canBeUsedForAmount($amount)
    {
        if ($this->min_order_amount) {
            return $amount >= $this->min_order_amount;
        }
        return true;
    }

    public function calculateDiscount($amount)
    {
        if (!$this->isValid() || !$this->canBeUsedForAmount($amount)) {
            return 0;
        }

        if ($this->type === self::TYPE_PERCENTAGE) {
            $discount = $amount * ($this->value / 100);
        } else {
            $discount = $this->value;
        }

        // تطبيق الحد الأقصى للخصم إذا وُجد
        if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
            $discount = $this->max_discount_amount;
        }

        return min($discount, $amount);
    }

    public function incrementUsage()
    {
        $this->increment('used_count');
    }

    public function getDiscountTextAttribute()
    {
        if ($this->type === self::TYPE_PERCENTAGE) {
            return $this->value . '%';
        } else {
            return number_format($this->value, 2) . ' EGP';
        }
    }
} 