<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'admin_id',
        'note',
        'type',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    // Scopes
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Type constants
    const TYPE_GENERAL = 'general';
    const TYPE_SUPPORT = 'support';
    const TYPE_WARNING = 'warning';

    public static function getNoteTypes()
    {
        return [
            self::TYPE_GENERAL => 'عام',
            self::TYPE_SUPPORT => 'دعم فني',
            self::TYPE_WARNING => 'تحذير',
        ];
    }

    // Helper methods
    public static function addNote($customerId, $adminId, $note, $type = self::TYPE_GENERAL)
    {
        return self::create([
            'customer_id' => $customerId,
            'admin_id' => $adminId,
            'note' => $note,
            'type' => $type,
        ]);
    }

    public function getTypeNameAttribute()
    {
        return self::getNoteTypes()[$this->type] ?? $this->type;
    }
}
