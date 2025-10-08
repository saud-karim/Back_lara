<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CheckoutController extends Controller
{
    /**
     * حساب تكلفة الشحن
     */
    public function calculateShipping(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'governorate' => 'required|string',
            'city' => 'required|string',
            'cart_items' => 'nullable|array',
        ]);

        // حساب تكلفة الشحن حسب المحافظة
        $shippingCost = $this->getShippingCost($validated['governorate']);

        return response()->json([
            'success' => true,
            'data' => [
                'shipping_methods' => [
                    [
                        'id' => 1,
                        'name' => 'توصيل عادي',
                        'name_en' => 'Standard Delivery',
                        'cost' => $shippingCost,
                        'estimated_days' => '3-5 أيام عمل',
                        'company' => 'أرامكس'
                    ],
                ],
                'default_method_id' => 1
            ]
        ]);
    }

    /**
     * تطبيق كود خصم
     */
    public function applyCoupon(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'coupon_code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $coupon = Coupon::where('code', $validated['coupon_code'])
                       ->where('is_active', true)
                       ->where('valid_from', '<=', now())
                       ->where('valid_until', '>=', now())
                       ->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'الكوبون غير صالح أو منتهي الصلاحية',
                'error' => [
                    'code' => 'INVALID_COUPON',
                    'reason' => 'expired'
                ]
            ], 400);
        }

        // التحقق من الحد الأدنى للطلب
        if ($coupon->min_order_amount && $validated['subtotal'] < $coupon->min_order_amount) {
            return response()->json([
                'success' => false,
                'message' => 'الحد الأدنى للطلب غير محقق. المطلوب: ' . $coupon->min_order_amount,
                'error' => [
                    'code' => 'MIN_ORDER_NOT_MET',
                    'min_amount' => $coupon->min_order_amount
                ]
            ], 400);
        }

        $discountAmount = $coupon->calculateDiscount($validated['subtotal']);

        return response()->json([
            'success' => true,
            'message' => 'تم تطبيق الكوبون بنجاح',
            'data' => [
                'coupon' => [
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'discount_amount' => $discountAmount,
                    'description' => $coupon->description_ar ?? $coupon->description_en
                ],
                'new_totals' => [
                    'subtotal' => $validated['subtotal'],
                    'discount' => $discountAmount,
                    'after_discount' => $validated['subtotal'] - $discountAmount,
                    'shipping' => 50.00,
                    'total' => $validated['subtotal'] - $discountAmount + 50.00
                ]
            ]
        ]);
    }

    /**
     * التحقق من توفر المنتجات
     */
    public function validateStock(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.variant_id' => 'nullable|integer',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $allAvailable = true;
        $items = [];

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['product_id']);
            
            $availableQuantity = $product->stock;
            $isAvailable = $availableQuantity >= $item['quantity'];

            if (!$isAvailable) {
                $allAvailable = false;
            }

            $items[] = [
                'product_id' => $item['product_id'],
                'variant_id' => $item['variant_id'] ?? null,
                'requested_quantity' => $item['quantity'],
                'available_quantity' => $availableQuantity,
                'is_available' => $isAvailable
            ];
        }

        if (!$allAvailable) {
            return response()->json([
                'success' => false,
                'message' => 'بعض المنتجات غير متوفرة بالكمية المطلوبة',
                'data' => [
                    'all_available' => false,
                    'items' => array_filter($items, fn($i) => !$i['is_available'])
                ]
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'جميع المنتجات متوفرة',
            'data' => [
                'all_available' => true,
                'items' => $items
            ]
        ]);
    }

    /**
     * حساب تكلفة الشحن حسب المحافظة
     */
    private function getShippingCost(string $governorate): float
    {
        // يمكن تخزين هذه القيم في قاعدة البيانات أو config
        $shippingRates = [
            'القاهرة' => 50.00,
            'الجيزة' => 50.00,
            'الإسكندرية' => 60.00,
            'الدقهلية' => 70.00,
            'الشرقية' => 70.00,
            'القليوبية' => 50.00,
            // ... باقي المحافظات
        ];

        return $shippingRates[$governorate] ?? 80.00; // Default shipping cost
    }
}
