<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    /**
     * Get the user that owns the cart.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the cart.
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Alias for items() relationship
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get items count
     */
    public function getItemsCountAttribute()
    {
        return $this->items()->sum('quantity');
    }

    /**
     * Get subtotal
     */
    public function getSubtotalAttribute()
    {
        return $this->items->sum(function ($item) {
            // Safe access to product price
            if (!$item->product) {
                return 0;
            }
            return $item->quantity * ($item->product->price ?? 0);
        });
    }

    /**
     * Get or create cart for user
     */
    public static function getOrCreateForUser($userId = null, $sessionId = null)
    {
        if ($userId) {
            return static::firstOrCreate(['user_id' => $userId]);
        } elseif ($sessionId) {
            return static::firstOrCreate(['session_id' => $sessionId]);
        }

        return null;
    }

    /**
     * Clear expired carts
     */
    public static function clearExpired()
    {
        return static::where('expires_at', '<', now())
                     ->where('user_id', null)
                     ->delete();
    }
}
