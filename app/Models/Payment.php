<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id',
        'user_id',
        'amount',
        'currency',
        'method',
        'status',
        'transaction_id',
        'gateway',
        'gateway_response',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_response' => 'array',
        'processed_at' => 'datetime',
    ];

    // طرق الدفع المتاحة
    const METHOD_CREDIT_CARD = 'credit_card';
    const METHOD_DEBIT_CARD = 'debit_card';
    const METHOD_PAYPAL = 'paypal';
    const METHOD_BANK_TRANSFER = 'bank_transfer';
    const METHOD_CASH_ON_DELIVERY = 'cash_on_delivery';

    // حالات الدفع
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    // العلاقات
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // الـ Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeByMethod($query, $method)
    {
        return $query->where('method', $method);
    }

    // Helper Methods
    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function isRefunded()
    {
        return $this->status === self::STATUS_REFUNDED;
    }

    public function markAsCompleted()
    {
        $this->update([
            'status' => self::STATUS_COMPLETED,
            'processed_at' => now(),
        ]);
    }

    public function markAsFailed()
    {
        $this->update(['status' => self::STATUS_FAILED]);
    }

    public function markAsRefunded()
    {
        $this->update(['status' => self::STATUS_REFUNDED]);
    }

    // الحصول على اسم طريقة الدفع
    public function getMethodNameAttribute()
    {
        $methods = [
            self::METHOD_CREDIT_CARD => __('Credit Card'),
            self::METHOD_DEBIT_CARD => __('Debit Card'),
            self::METHOD_PAYPAL => __('PayPal'),
            self::METHOD_BANK_TRANSFER => __('Bank Transfer'),
            self::METHOD_CASH_ON_DELIVERY => __('Cash on Delivery'),
        ];

        return $methods[$this->method] ?? $this->method;
    }

    // الحصول على اسم الحالة
    public function getStatusNameAttribute()
    {
        $statuses = [
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_PROCESSING => __('Processing'),
            self::STATUS_COMPLETED => __('Completed'),
            self::STATUS_FAILED => __('Failed'),
            self::STATUS_REFUNDED => __('Refunded'),
        ];

        return $statuses[$this->status] ?? $this->status;
    }
} 