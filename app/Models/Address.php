<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'type',
        'name',
        'phone',
        'street',
        'city',
        'state',
        'postal_code',
        'country',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    // أنواع العناوين
    const TYPE_HOME = 'home';
    const TYPE_WORK = 'work';
    const TYPE_OTHER = 'other';

    // العلاقات
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'shipping_address_id');
    }

    // الـ Scopes
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helper Methods
    public function isDefault()
    {
        return $this->is_default;
    }

    public function makeDefault()
    {
        // إزالة الافتراضي من باقي العناوين للمستخدم (فقط النشطة وليس المحذوفة soft deleted)
        $this->user->addresses()->update(['is_default' => false]);
        
        // جعل هذا العنوان افتراضي
        $this->update(['is_default' => true]);
        
        // إعادة تحميل النموذج للحصول على أحدث البيانات
        $this->refresh();
    }

    // الحصول على العنوان المنسق
    public function getFormattedAddressAttribute()
    {
        return trim($this->street . ', ' . $this->city . ', ' . $this->state . ', ' . $this->country);
    }

    // التحقق من اكتمال العنوان
    public function isComplete()
    {
        return !empty($this->street) && 
               !empty($this->city) && 
               !empty($this->state) && 
               !empty($this->country);
    }

    // Boot method لضبط العنوان الافتراضي
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($address) {
            // إذا كان هذا أول عنوان نشط للمستخدم، اجعله افتراضي
            if ($address->user->addresses()->count() === 0) {
                $address->is_default = true;
            }
        });
    }
} 