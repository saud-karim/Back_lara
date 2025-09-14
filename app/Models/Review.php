<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'product_id', 
        'order_id',
        'rating',
        'review',
        'review_ar',
        'review_en', 
        'status',
        'verified_purchase',
        'helpful_count',
        'admin_response',
        'admin_response_by',
        'admin_response_at',
    ];

    protected $casts = [
        'verified_purchase' => 'boolean',
        'helpful_count' => 'integer',
        'rating' => 'integer',
        'admin_response_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    public static function getStatuses()
    {
        return [
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_APPROVED => __('Approved'),
            self::STATUS_REJECTED => __('Rejected'),
        ];
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ReviewImage::class)->orderBy('order_index');
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(ReviewStatusHistory::class)->orderBy('created_at', 'desc');
    }

    public function helpfulUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'review_helpful')
            ->withTimestamps(['created_at']);
    }

    public function adminResponse(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_response_by');
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeVerified($query)
    {
        return $query->where('verified_purchase', true);
    }

    public function scopeWithImages($query)
    {
        return $query->has('images');
    }

    public function scopeHelpful($query)
    {
        return $query->where('helpful_count', '>', 0);
    }

    public function scopeByRating($query, $rating)
    {
        return $query->where('rating', $rating);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('review', 'LIKE', "%{$search}%")
              ->orWhere('review_ar', 'LIKE', "%{$search}%")
              ->orWhere('review_en', 'LIKE', "%{$search}%");
        });
    }

    public function scopeByProduct($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeDateRange($query, $from, $to)
    {
        return $query->whereBetween('created_at', [$from, $to]);
    }

    // Helper methods
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isRejected(): bool
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function hasImages(): bool
    {
        return $this->images()->exists();
    }

    public function isHelpful(): bool
    {
        return $this->helpful_count > 0;
    }

    public function getStatusNameAttribute(): string
    {
        return self::getStatuses()[$this->status] ?? $this->status;
    }

    public function updateStatus(string $status, ?string $reason = null, ?int $adminId = null): void
    {
        $oldStatus = $this->status;
        
        $this->update([
            'status' => $status,
            'admin_response_by' => $adminId,
            'admin_response_at' => now(),
        ]);

        // Log status change
        $this->statusHistory()->create([
            'old_status' => $oldStatus,
            'new_status' => $status,
            'changed_by' => $adminId,
            'reason' => $reason,
        ]);
    }

    public function markAsHelpful(int $userId): bool
    {
        if (!$this->helpfulUsers()->where('user_id', $userId)->exists()) {
            $this->helpfulUsers()->attach($userId);
            $this->increment('helpful_count');
            return true;
        }
        
        return false;
    }

    public function unmarkAsHelpful(int $userId): bool
    {
        if ($this->helpfulUsers()->where('user_id', $userId)->exists()) {
            $this->helpfulUsers()->detach($userId);
            $this->decrement('helpful_count');
            return true;
        }
        
        return false;
    }
}
