<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReviewImage extends Model
{
    use HasFactory;

    protected $table = 'review_images';
    
    public $timestamps = false; // Only created_at is used

    protected $fillable = [
        'review_id',
        'image_path',
        'thumbnail_path',
        'alt_text',
        'order_index',
    ];

    protected $casts = [
        'order_index' => 'integer',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    // Accessors
    public function getImageUrlAttribute(): string
    {
        return $this->image_path ? asset('storage/' . $this->image_path) : '';
    }

    public function getThumbnailUrlAttribute(): string
    {
        return $this->thumbnail_path ? asset('storage/' . $this->thumbnail_path) : $this->image_url;
    }

    // Helper methods
    public function getFullImagePath(): string
    {
        return storage_path('app/public/' . $this->image_path);
    }

    public function getFullThumbnailPath(): string
    {
        return $this->thumbnail_path 
            ? storage_path('app/public/' . $this->thumbnail_path)
            : $this->getFullImagePath();
    }

    public function exists(): bool
    {
        return file_exists($this->getFullImagePath());
    }

    public function thumbnailExists(): bool
    {
        return $this->thumbnail_path && file_exists($this->getFullThumbnailPath());
    }
}
