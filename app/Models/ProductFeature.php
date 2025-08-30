<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFeature extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'feature_ar',
        'feature_en',
        'sort_order'
    ];

    protected $casts = [
        'sort_order' => 'integer'
    ];

    /**
     * العلاقة مع المنتج
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
} 