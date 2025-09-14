<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReviewStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'review_status_history';
    
    public $timestamps = false; // Only created_at is used

    protected $fillable = [
        'review_id',
        'old_status',
        'new_status',
        'changed_by',
        'reason',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    // Status constants (matching Review model)
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // Relationships
    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Scopes
    public function scopeByReview($query, $reviewId)
    {
        return $query->where('review_id', $reviewId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('new_status', $status);
    }

    public function scopeByAdmin($query, $adminId)
    {
        return $query->where('changed_by', $adminId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Helper methods
    public function getStatusChangeLabel(): string
    {
        if (!$this->old_status) {
            return "Created as {$this->new_status}";
        }
        
        return "Changed from {$this->old_status} to {$this->new_status}";
    }

    public function wasApproved(): bool
    {
        return $this->new_status === self::STATUS_APPROVED;
    }

    public function wasRejected(): bool
    {
        return $this->new_status === self::STATUS_REJECTED;
    }

    public function wasPending(): bool
    {
        return $this->new_status === self::STATUS_PENDING;
    }

    public function isStatusUpgrade(): bool
    {
        if (!$this->old_status) return true;
        
        $statusOrder = [
            self::STATUS_PENDING => 1,
            self::STATUS_APPROVED => 2,
            self::STATUS_REJECTED => 0
        ];
        
        return ($statusOrder[$this->new_status] ?? 0) > ($statusOrder[$this->old_status] ?? 0);
    }
}
