<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'cart_id',
        'product_id',
        'variant_id',
        'quantity',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    /**
     * Get the cart that owns the cart item.
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Get the product that owns the cart item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the variant if exists.
     * Note: ProductVariant model not implemented yet
     */
    public function variant()
    {
        return null; // ProductVariant model not implemented yet
    }

    /**
     * Get unit price
     */
    public function getUnitPriceAttribute()
    {
        if ($this->variant_id && $this->variant) {
            return $this->variant->price ?? $this->product->price;
        }
        return $this->product->price;
    }

    /**
     * Get total price
     */
    public function getTotalPriceAttribute()
    {
        return $this->quantity * $this->unit_price;
    }

    /**
     * Get stock available
     */
    public function getStockAvailableAttribute()
    {
        if ($this->variant_id && $this->variant) {
            return $this->variant->stock ?? $this->product->stock;
        }
        return $this->product->stock;
    }

    /**
     * Check if item is in stock
     */
    public function isInStock()
    {
        return $this->stock_available >= $this->quantity;
    }
}
