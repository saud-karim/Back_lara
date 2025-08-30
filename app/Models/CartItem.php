<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'quantity',
    ];

    protected $casts = [
        'quantity' => 'integer',
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
    public function getTotalPriceAttribute()
    {
        return $this->quantity * $this->product->price;
    }

    public function increaseQuantity($amount = 1)
    {
        $this->increment('quantity', $amount);
    }

    public function decreaseQuantity($amount = 1)
    {
        $newQuantity = $this->quantity - $amount;
        
        if ($newQuantity <= 0) {
            $this->delete();
        } else {
            $this->decrement('quantity', $amount);
        }
    }

    public function updateQuantity($quantity)
    {
        if ($quantity <= 0) {
            $this->delete();
        } else {
            $this->update(['quantity' => $quantity]);
        }
    }
} 