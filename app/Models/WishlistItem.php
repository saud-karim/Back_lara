<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WishlistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
    ];

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
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Helper Methods
    public function moveToCart($quantity = 1)
    {
        // إضافة إلى السلة
        CartItem::updateOrCreate(
            [
                'user_id' => $this->user_id,
                'product_id' => $this->product_id,
            ],
            [
                'quantity' => $quantity,
            ]
        );

        // حذف من قائمة الأمنيات
        $this->delete();
    }
} 