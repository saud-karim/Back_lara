<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray($request)
    {
        // Calculate totals
        $subtotal = (float) $this->subtotal;
        $shipping = $subtotal > 500 ? 0.00 : 50.00; // Free shipping over 500 EGP
        $tax = 0.00; // No tax for now
        $discount = 0.00; // Apply coupon discount if exists
        $total = $subtotal + $shipping + $tax - $discount;

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'session_id' => $this->session_id,
            'items_count' => $this->cartItems->sum('quantity'),
            'items' => $this->cartItems->map(function ($item) {
                $product = $item->product;
                
                // Skip items with deleted products
                if (!$product) {
                    return null;
                }
                
                // Get product image
                $productImage = null;
                if ($product->images) {
                    $images = is_string($product->images) 
                        ? json_decode($product->images, true) 
                        : $product->images;
                    $productImage = is_array($images) && !empty($images) ? $images[0] : null;
                }
                
                $unitPrice = (float) ($product->price ?? 0);
                $totalPrice = $item->quantity * $unitPrice;
                
                return [
                    'id' => $item->id,
                    'cart_id' => $this->id,
                    'product_id' => $item->product_id,
                    'product_name' => $product->name_ar ?? $product->name_en ?? 'Unknown',
                    'product_name_en' => $product->name_en ?? '',
                    'product_image' => $productImage,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'stock_available' => $product->stock ?? 0,
                    'notes' => $item->notes,
                    'product' => [
                        'id' => $product->id,
                        'name_ar' => $product->name_ar,
                        'name_en' => $product->name_en,
                        'price' => $unitPrice,
                        'stock' => $product->stock ?? 0,
                        'images' => $productImage ? [$productImage] : [],
                    ]
                ];
            })->filter()->values(), // Remove null items and re-index
            'summary' => [
                'subtotal' => $subtotal,
                'shipping' => $shipping,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'currency' => 'EGP'
            ],
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
