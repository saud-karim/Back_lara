<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name_ar',
        'name_en', 
        'description_ar',
        'description_en',
        'price',
        'sale_price',
        'sku',
        'category_id',
        'supplier_id',
        'brand_id',
        'stock',
        'status',
        'featured',
        'images',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'stock' => 'integer',
        'featured' => 'boolean',
        'images' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the supplier that owns the product.
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the order items for the product.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the brand for the product.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the reviews for the product (excluding soft deleted).
     */
    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    /**
     * Get all reviews including soft deleted.
     */
    public function allReviews()
    {
        return $this->hasMany(ProductReview::class)->withTrashed();
    }

    /**
     * Get the cart items for the product.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the wishlist items for the product.
     */
    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }

    /**
     * Check if product is in stock.
     */
    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    /**
     * Check if product has low stock.
     */
    public function hasLowStock(int $threshold = 10): bool
    {
        return $this->stock <= $threshold;
    }

    /**
     * Decrease stock by quantity.
     */
    public function decreaseStock(int $quantity): bool
    {
        if ($this->stock >= $quantity) {
            $this->decrement('stock', $quantity);
            return true;
        }
        return false;
    }

    /**
     * Increase stock by quantity.
     */
    public function increaseStock(int $quantity): void
    {
        $this->increment('stock', $quantity);
    }

    /**
     * Get the product name based on locale.
     */
    public function getLocalizedNameAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->name_ar ?: $this->name) : ($this->name_en ?: $this->name);
    }

    /**
     * Get the product description based on locale.
     */
    public function getLocalizedDescriptionAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->description_ar ?: $this->description) : ($this->description_en ?: $this->description);
    }

    /**
     * Product features relationship
     */
    public function features()
    {
        return $this->hasMany(ProductFeature::class);
    }

    /**
     * Product specifications relationship  
     */
    public function specifications()
    {
        return $this->hasMany(ProductSpecification::class);
    }

    /**
     * Calculate average rating for this product
     */
    public function calculateAverageRating()
    {
        try {
            // Try to get reviews from Review model (new system)
            if (class_exists('\App\Models\Review')) {
                $reviews = \App\Models\Review::where('product_id', $this->id)->where('status', 'approved');
                if ($reviews->count() > 0) {
                    return round($reviews->avg('rating'), 1);
                }
            }
            
            // Fallback to ProductReview model (old system)
            if ($this->reviews()->count() > 0) {
                return round($this->reviews()->avg('rating'), 1);
            }
            
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get reviews count for this product
     */
    public function getReviewsCount()
    {
        try {
            // Try to get reviews from Review model (new system)
            if (class_exists('\App\Models\Review')) {
                return \App\Models\Review::where('product_id', $this->id)->where('status', 'approved')->count();
            }
            
            // Fallback to ProductReview model (old system)
            return $this->reviews()->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
} 