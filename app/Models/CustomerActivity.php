<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'activity_type',
        'activity_data',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'activity_data' => 'array',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // Scopes
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByActivityType($query, $activityType)
    {
        return $query->where('activity_type', $activityType);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Activity type constants
    const TYPE_LOGIN = 'login';
    const TYPE_ORDER = 'order';
    const TYPE_VIEW_PRODUCT = 'view_product';
    const TYPE_ADD_TO_CART = 'add_to_cart';
    const TYPE_WISHLIST = 'wishlist';
    const TYPE_SEARCH = 'search';
    const TYPE_REGISTER = 'register';
    const TYPE_LOGOUT = 'logout';

    public static function getActivityTypes()
    {
        return [
            self::TYPE_LOGIN => 'تسجيل الدخول',
            self::TYPE_ORDER => 'طلب جديد',
            self::TYPE_VIEW_PRODUCT => 'عرض منتج',
            self::TYPE_ADD_TO_CART => 'إضافة للسلة',
            self::TYPE_WISHLIST => 'قائمة الأمنيات',
            self::TYPE_SEARCH => 'البحث',
            self::TYPE_REGISTER => 'التسجيل',
            self::TYPE_LOGOUT => 'تسجيل الخروج',
        ];
    }

    // Helper methods
    public static function logActivity($customerId, $activityType, $data = [], $ipAddress = null, $userAgent = null)
    {
        return self::create([
            'customer_id' => $customerId,
            'activity_type' => $activityType,
            'activity_data' => $data,
            'ip_address' => $ipAddress ?: request()->ip(),
            'user_agent' => $userAgent ?: request()->userAgent(),
        ]);
    }
}
