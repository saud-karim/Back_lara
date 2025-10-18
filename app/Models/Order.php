<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_number',
        'user_id',
        'status',
        'subtotal',
        'tax_amount',
        'shipping_amount',
        'discount_amount',
        'total_amount',
        'currency',
        'payment_method',
        'payment_status',
        'shipping_address',
        'notes',
        'tracking_number',
        'shipping_company',
        'shipping_status',
        'shipped_at',
        'estimated_delivery',
        'cancelled_at',
        'cancellation_reason',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'shipping_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'shipping_address' => 'array',
        'estimated_delivery' => 'date',
        'cancelled_at' => 'datetime',
        'shipped_at' => 'datetime',
    ];

    /**
     * Boot method - Auto-generate order_number
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    /**
     * Generate unique order number
     */
    public static function generateOrderNumber(): string
    {
        $year = date('Y');
        
        // Get last order number for this year
        $lastOrder = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $lastNumber = 0;
        if ($lastOrder && $lastOrder->order_number) {
            // Extract number from last order: ORD-2025-00123 → 123
            preg_match('/ORD-' . $year . '-(\d+)/', $lastOrder->order_number, $matches);
            $lastNumber = isset($matches[1]) ? intval($matches[1]) : 0;
        }
        
        $newNumber = $lastNumber + 1;
        
        // Format: ORD-2025-00001
        return 'ORD-' . $year . '-' . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get the user that owns the order.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the order items for the order.
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the shipment for the order.
     */
    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }

    /**
     * Calculate total amount from order items.
     */
    public function calculateTotalAmount(): float
    {
        return $this->orderItems->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });
    }

    /**
     * Get the order status histories for the order.
     */
    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('created_at', 'desc');
    }

    /**
     * Check if order is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if order can be cancelled.
     */
    public function canBeCancelled(): bool
    {
        return in_array($this->status, ['pending', 'confirmed']);
    }

    /**
     * Get status in Arabic.
     */
    public function getStatusArAttribute(): string
    {
        return match($this->status) {
            'pending' => 'في الانتظار',
            'confirmed' => 'تم التأكيد', 
            'processing' => 'قيد التحضير',
            'shipped' => 'تم الشحن',
            'delivered' => 'تم التسليم',
            'cancelled' => 'ملغي',
            'refunded' => 'مسترد',
            default => $this->status
        };
    }

    /**
     * Get payment status in Arabic.
     */
    public function getPaymentStatusArAttribute(): string
    {
        return match($this->payment_status) {
            'pending' => 'في الانتظار',
            'paid' => 'تم الدفع',
            'failed' => 'فشل الدفع',
            'refunded' => 'مسترد',
            default => $this->payment_status
        };
    }

         /**
      * Get total items count.
      */
     public function getItemsCountAttribute(): int
     {
         return $this->orderItems->sum('quantity');
     }

    /**
     * Check if order is processing.
     */
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    /**
     * Check if order is shipped.
     */
    public function isShipped(): bool
    {
        return $this->status === 'shipped';
    }

    /**
     * Check if order is delivered.
     */
    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    /**
     * Update order status.
     */
    public function updateStatus(string $status): void
    {
        $this->update(['status' => $status]);
    }
} 