<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'address',
        'phone',
        'company',
        'status',
        'last_activity',
        'avatar',
        'registration_source',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_activity' => 'datetime',
    ];

    /**
     * Get the orders for the user.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get the supplier profile for the user.
     */
    public function supplier()
    {
        return $this->hasOne(Supplier::class);
    }

    /**
     * Get the notifications for the user.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the cost calculations for the user.
     */
    public function costCalculations()
    {
        return $this->hasMany(CostCalculation::class);
    }

    /**
     * Get the addresses for the user (excluding soft deleted).
     */
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    /**
     * Get all addresses including soft deleted.
     */
    public function allAddresses()
    {
        return $this->hasMany(Address::class)->withTrashed();
    }

    /**
     * Get the cart items for the user.
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Get the wishlist items for the user.
     */
    public function wishlistItems()
    {
        return $this->hasMany(WishlistItem::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Get the payments for the user.
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the customer activities for the user.
     */
    public function activities()
    {
        return $this->hasMany(CustomerActivity::class, 'customer_id');
    }

    /**
     * Get the customer notes for the user.
     */
    public function customerNotes()
    {
        return $this->hasMany(CustomerNote::class, 'customer_id');
    }

    /**
     * Get the admin notes created by the user.
     */
    public function adminNotes()
    {
        return $this->hasMany(CustomerNote::class, 'admin_id');
    }

    /**
     * Check if user is admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is customer.
     */
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Check if user is supplier.
     */
    public function isSupplier(): bool
    {
        return $this->role === 'supplier';
    }

    // Customer Management Scopes
    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeBanned($query)
    {
        return $query->where('status', 'banned');
    }

    public function scopeWithOrders($query)
    {
        return $query->has('orders');
    }

    public function scopeWithoutOrders($query)
    {
        return $query->doesntHave('orders');
    }

    public function scopeRecentActivity($query, $days = 30)
    {
        return $query->where('last_activity', '>=', now()->subDays($days));
    }

    public function scopeNewThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    // Customer Management Helper Methods
    public function updateLastActivity()
    {
        $this->update(['last_activity' => now()]);
    }

    public function getTotalSpentAttribute()
    {
        return $this->orders()
                   ->where('status', 'completed')
                   ->sum('total_amount');
    }

    public function getOrdersCountAttribute()
    {
        return $this->orders()->count();
    }

    public function getFavoritePaymentMethodAttribute()
    {
        // استخدم الـ method المحسنة من Payment model
        return Payment::getFavoritePaymentMethod($this->id) ?? 'cash_on_delivery';
    }

    public function getAddressesCountAttribute()
    {
        return $this->addresses()->count();
    }

    public function hasRecentActivity($days = 30)
    {
        return $this->last_activity && $this->last_activity >= now()->subDays($days);
    }

    public function isVerified()
    {
        return !is_null($this->email_verified_at);
    }

    // Status management methods
    public function activate($reason = null)
    {
        $this->update(['status' => 'active']);
        CustomerActivity::logActivity($this->id, 'status_changed', [
            'new_status' => 'active',
            'reason' => $reason
        ]);
    }

    public function deactivate($reason = null)
    {
        $this->update(['status' => 'inactive']);
        CustomerActivity::logActivity($this->id, 'status_changed', [
            'new_status' => 'inactive',
            'reason' => $reason
        ]);
    }

    public function ban($reason = null)
    {
        $this->update(['status' => 'banned']);
        CustomerActivity::logActivity($this->id, 'status_changed', [
            'new_status' => 'banned',
            'reason' => $reason
        ]);
    }
}
