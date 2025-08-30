<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NewsletterSubscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'email',
        'preferences',
        'status',
    ];

    protected $casts = [
        'preferences' => 'array',
    ];

    // حالات الاشتراك
    const STATUS_ACTIVE = 'active';
    const STATUS_UNSUBSCRIBED = 'unsubscribed';

    // أنواع التفضيلات
    const PREFERENCE_NEW_PRODUCTS = 'new_products';
    const PREFERENCE_OFFERS = 'offers';
    const PREFERENCE_INDUSTRY_NEWS = 'industry_news';
    const PREFERENCE_TIPS = 'tips';

    // الـ Scopes
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeUnsubscribed($query)
    {
        return $query->where('status', self::STATUS_UNSUBSCRIBED);
    }

    // Helper Methods
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isUnsubscribed()
    {
        return $this->status === self::STATUS_UNSUBSCRIBED;
    }

    public function subscribe()
    {
        $this->update(['status' => self::STATUS_ACTIVE]);
    }

    public function unsubscribe()
    {
        $this->update(['status' => self::STATUS_UNSUBSCRIBED]);
    }

    public function hasPreference($preference)
    {
        return in_array($preference, $this->preferences ?? []);
    }

    public function addPreference($preference)
    {
        $preferences = $this->preferences ?? [];
        
        if (!in_array($preference, $preferences)) {
            $preferences[] = $preference;
            $this->update(['preferences' => $preferences]);
        }
    }

    public function removePreference($preference)
    {
        $preferences = $this->preferences ?? [];
        
        if (($key = array_search($preference, $preferences)) !== false) {
            unset($preferences[$key]);
            $this->update(['preferences' => array_values($preferences)]);
        }
    }

    public function updatePreferences($preferences)
    {
        $this->update(['preferences' => $preferences]);
    }

    public static function getAvailablePreferences()
    {
        return [
            self::PREFERENCE_NEW_PRODUCTS => __('New Products'),
            self::PREFERENCE_OFFERS => __('Special Offers'),
            self::PREFERENCE_INDUSTRY_NEWS => __('Industry News'),
            self::PREFERENCE_TIPS => __('Tips & Tutorials'),
        ];
    }
} 