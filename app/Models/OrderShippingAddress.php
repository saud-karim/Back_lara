<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderShippingAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'governorate',
        'city',
        'area',
        'street',
        'building_number',
        'floor',
        'apartment',
        'landmark',
        'postal_code',
        'latitude',
        'longitude',
    ];

    /**
     * Get the order that owns the shipping address.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get full address as string
     */
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->building_number,
            $this->street,
            $this->floor ? "الطابق {$this->floor}" : null,
            $this->apartment ? "شقة {$this->apartment}" : null,
            $this->area,
            $this->city,
            $this->governorate,
        ]);

        return implode('، ', $parts);
    }
}
