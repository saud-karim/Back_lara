<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContactMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'email',
        'phone',
        'company',
        'subject',
        'message',
        'project_type',
        'status',
        'admin_notes',
    ];

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    // أنواع المشاريع
    const PROJECT_RESIDENTIAL = 'residential';
    const PROJECT_COMMERCIAL = 'commercial';
    const PROJECT_INDUSTRIAL = 'industrial';
    const PROJECT_OTHER = 'other';

    // حالات الرسالة
    const STATUS_NEW = 'new';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';

    // الـ Scopes
    public function scopeNew($query)
    {
        return $query->where('status', self::STATUS_NEW);
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', self::STATUS_IN_PROGRESS);
    }

    public function scopeResolved($query)
    {
        return $query->where('status', self::STATUS_RESOLVED);
    }

    public function scopeByProjectType($query, $type)
    {
        return $query->where('project_type', $type);
    }

    // Helper Methods
    public function isNew()
    {
        return $this->status === self::STATUS_NEW;
    }

    public function isInProgress()
    {
        return $this->status === self::STATUS_IN_PROGRESS;
    }

    public function isResolved()
    {
        return $this->status === self::STATUS_RESOLVED;
    }

    public function isClosed()
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function markAsInProgress()
    {
        $this->update(['status' => self::STATUS_IN_PROGRESS]);
    }

    public function markAsResolved()
    {
        $this->update(['status' => self::STATUS_RESOLVED]);
    }

    public function markAsClosed()
    {
        $this->update(['status' => self::STATUS_CLOSED]);
    }

    public function getProjectTypeNameAttribute()
    {
        $types = [
            self::PROJECT_RESIDENTIAL => __('Residential'),
            self::PROJECT_COMMERCIAL => __('Commercial'),
            self::PROJECT_INDUSTRIAL => __('Industrial'),
            self::PROJECT_OTHER => __('Other'),
        ];

        return $types[$this->project_type] ?? $this->project_type;
    }

    public function getStatusNameAttribute()
    {
        $statuses = [
            self::STATUS_NEW => __('New'),
            self::STATUS_IN_PROGRESS => __('In Progress'),
            self::STATUS_RESOLVED => __('Resolved'),
            self::STATUS_CLOSED => __('Closed'),
        ];

        return $statuses[$this->status] ?? $this->status;
    }

    /**
     * Boot method to generate ticket ID
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contactMessage) {
            if (empty($contactMessage->id)) {
                // إنشاء ticket ID مثل: TKT-2025-001
                $year = date('Y');
                $lastTicket = static::whereYear('created_at', $year)
                                   ->orderBy('created_at', 'desc')
                                   ->first();
                
                $nextNumber = 1;
                if ($lastTicket && preg_match('/TKT-\d{4}-(\d{3})/', $lastTicket->id, $matches)) {
                    $nextNumber = intval($matches[1]) + 1;
                }
                
                $contactMessage->id = sprintf('TKT-%s-%03d', $year, $nextNumber);
            }
        });
    }
} 