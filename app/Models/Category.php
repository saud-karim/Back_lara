<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    /**
     * The primary key type.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

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
        'image',
        'status',
        'sort_order',
        'parent_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the parent category.
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Get the child categories.
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Get the products for the category.
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get all descendants of the category.
     */
    public function descendants()
    {
        return $this->children()->with('descendants');
    }

    /**
     * Get all ancestors of the category.
     */
    public function ancestors()
    {
        return $this->parent()->with('ancestors');
    }

    /**
     * Check if category is a parent category.
     */
    public function isParent(): bool
    {
        return is_null($this->parent_id);
    }

    /**
     * Check if category is a child category.
     */
    public function isChild(): bool
    {
        return !is_null($this->parent_id);
    }

    /**
     * Get the full category path.
     */
    public function getFullPathAttribute(): string
    {
        $path = [$this->localized_name];
        $parent = $this->parent;

        while ($parent) {
            array_unshift($path, $parent->localized_name);
            $parent = $parent->parent;
        }

        return implode(' > ', $path);
    }

    /**
     * Get the category name based on locale.
     */
    public function getLocalizedNameAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->name_ar ?: $this->name) : ($this->name_en ?: $this->name);
    }

    /**
     * Get the category description based on locale.
     */
    public function getLocalizedDescriptionAttribute()
    {
        $locale = app()->getLocale();
        return $locale === 'ar' ? ($this->description_ar ?: '') : ($this->description_en ?: '');
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
    
    /**
     * Get the count of products in this category.
     */
    public function getProductsCountAttribute(): int
    {
        return $this->products()->where('status', 'active')->count();
    }
    
    /**
     * Get the count of all products (including inactive) in this category.
     */
    public function getAllProductsCountAttribute(): int
    {
        return $this->products()->count();
    }
} 