<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminOrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'customer' => $this->user ? [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $this->user->phone ?? 'N/A',
                'created_at' => $this->user->created_at?->format('Y-m-d'),
            ] : null,
            'status' => $this->status,
            'status_ar' => $this->status_ar ?? $this->status,
            'payment_status' => $this->payment_status ?? 'pending',
            'payment_status_ar' => $this->payment_status_ar ?? 'قيد الانتظار',
            'payment_method' => $this->payment_method ?? 'cash_on_delivery',
            'amounts' => [
                'subtotal' => (float) ($this->subtotal ?? 0),
                'tax_amount' => (float) ($this->tax_amount ?? 0),
                'shipping_amount' => (float) ($this->shipping_amount ?? 0),
                'discount_amount' => (float) ($this->discount_amount ?? 0),
                'total_amount' => (float) ($this->total_amount ?? 0),
                'currency' => $this->currency ?? 'EGP',
            ],
            'items_count' => $this->items_count ?? 0,
            'shipping_address' => $this->shipping_address ?? [],
            'tracking_number' => $this->tracking_number,
            'estimated_delivery' => $this->estimated_delivery?->format('Y-m-d'),
            'notes' => $this->notes ?? '',
            'can_be_cancelled' => method_exists($this->resource, 'canBeCancelled') ? $this->canBeCancelled() : false,
            
            // Order Items (when loaded)
            'items' => $this->whenLoaded('orderItems', function () {
                if (!$this->orderItems || $this->orderItems->isEmpty()) {
                    return [];
                }
                return $this->orderItems->map(function ($item) {
                    $product = $item->product ?? null;
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'product_name' => $product?->name_ar ?? 'منتج محذوف',
                        'product_name_en' => $product?->name_en ?? 'Deleted Product',
                        'product_image' => $product?->images?->first()?->image_url ?? null,
                        'quantity' => $item->quantity ?? 1,
                        'unit_price' => (float) ($item->unit_price ?? 0),
                        'total_price' => (float) ($item->total_price ?? 0),
                    ];
                });
            }),

            // Status History (when loaded)
            'status_history' => $this->whenLoaded('statusHistories', function () {
                if (!$this->statusHistories || $this->statusHistories->isEmpty()) {
                    return [];
                }
                return $this->statusHistories->map(function ($history) {
                    return [
                        'id' => $history->id,
                        'status' => $history->status ?? 'pending',
                        'status_ar' => $history->status_ar ?? 'قيد الانتظار',
                        'previous_status' => $history->previous_status,
                        'notes' => $history->notes ?? '',
                        'changed_by' => [
                            'id' => $history->user?->id ?? null,
                            'name' => $history->user?->name ?? 'نظام',
                        ],
                        'metadata' => $history->metadata ?? [],
                        'created_at' => $history->created_at?->format('Y-m-d H:i:s') ?? now()->format('Y-m-d H:i:s'),
                    ];
                });
            }),

            // Shipment info (when loaded)
            'shipment' => $this->whenLoaded('shipment', function () {
                return $this->shipment ? [
                    'id' => $this->shipment->id,
                    'tracking_number' => $this->shipment->tracking_number,
                    'carrier' => $this->shipment->carrier,
                    'status' => $this->shipment->status,
                    'shipped_at' => $this->shipment->shipped_at?->format('Y-m-d H:i:s'),
                    'delivered_at' => $this->shipment->delivered_at?->format('Y-m-d H:i:s'),
                ] : null;
            }),

            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
