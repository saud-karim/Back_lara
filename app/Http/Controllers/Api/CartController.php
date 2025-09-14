<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CartController extends Controller
{
    /**
     * جلب محتويات السلة
     */
    public function index(): JsonResponse
    {
        $user = Auth::user();
        $cartItems = $user->cartItems()
                         ->with(['product' => function ($query) {
                             $query->where('status', 'active')
                                   ->with(['category', 'brand']);
                         }])
                         ->get();

        // تنظيف السلة من المنتجات المحذوفة أو غير النشطة
        $validItems = collect();
        $removedCount = 0;

        foreach ($cartItems as $item) {
            if ($item->product && $item->product->status === 'active') {
                $validItems->push($item);
            } else {
                // منتج محذوف أو غير نشط - إزالة من السلة
                $item->delete();
                $removedCount++;
            }
        }

        $cart = $this->calculateCartTotals($validItems);

        $response = [
            'success' => true,
            'data' => [
                'cart' => $cart
            ]
        ];

        // إشعار عن المنتجات المحذوفة
        if ($removedCount > 0) {
            $response['message'] = "تم إزالة {$removedCount} منتج محذوف من سلتك.";
        }

        return response()->json($response);
    }

    /**
     * إضافة منتج للسلة
     */
    public function add(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::find($validated['product_id']);

        // ✅ فحص وجود المنتج
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'المنتج المطلوب غير موجود أو تم حذفه'
            ], 404);
        }

        // ✅ فحص حالة المنتج
        if ($product->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'هذا المنتج غير متوفر حالياً'
            ], 400);
        }

        // ✅ فحص المخزون
        if ($product->stock < $validated['quantity']) {
            return response()->json([
                'success' => false,
                'message' => 'الكمية المطلوبة غير متوفرة في المخزون. متوفر: ' . $product->stock
            ], 400);
        }

        $user = Auth::user();
        $cartItem = CartItem::where('user_id', $user->id)
                           ->where('product_id', $validated['product_id'])
                           ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $validated['quantity'];
            
            if ($product->stock < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن إضافة المزيد. الحد الأقصى المتوفر: ' . $product->stock
                ], 400);
            }

            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            CartItem::create([
                'user_id' => $user->id,
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity']
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المنتج للسلة بنجاح',
            'data' => [
                'cart' => $this->getCartData()
            ]
        ]);
    }

    /**
     * تحديث كمية منتج في السلة
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'quantity' => 'required|integer|min:0'
        ]);

        $user = Auth::user();
        $cartItem = CartItem::where('user_id', $user->id)
                           ->where('product_id', $validated['product_id'])
                           ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'المنتج غير موجود في السلة'
            ], 404);
        }

        if ($validated['quantity'] == 0) {
            $cartItem->delete();
            $message = 'تم إزالة المنتج من السلة';
        } else {
            // التحقق من المنتج ومخزونه
            $product = Product::find($validated['product_id']);
            
            if (!$product || $product->status !== 'active') {
                // منتج محذوف أو غير نشط - إزالة من السلة
                $cartItem->delete();
                return response()->json([
                    'success' => false,
                    'message' => 'المنتج غير متوفر حالياً وتم إزالته من السلة'
                ], 400);
            }

            if ($product->stock < $validated['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'الكمية المطلوبة غير متوفرة. متوفر: ' . $product->stock
                ], 400);
            }

            $cartItem->update(['quantity' => $validated['quantity']]);
            $message = 'تم تحديث السلة بنجاح';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'cart' => $this->getCartData()
            ]
        ]);
    }

    /**
     * إزالة منتج من السلة
     */
    public function remove($productId): JsonResponse
    {
        $user = Auth::user();
        $cartItem = CartItem::where('user_id', $user->id)
                           ->where('product_id', $productId)
                           ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'المنتج غير موجود في السلة'
            ], 404);
        }

        $cartItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'تم إزالة المنتج من السلة بنجاح',
            'data' => [
                'cart' => $this->getCartData()
            ]
        ]);
    }

    /**
     * تطبيق كوبون خصم
     */
    public function applyCoupon(Request $request): JsonResponse
    {
        $request->validate([
            'coupon_code' => 'required|string'
        ]);

        $coupon = Coupon::where('code', $request->coupon_code)
                       ->available()
                       ->first();

        if (!$coupon) {
            return response()->json([
                'success' => false,
                'message' => 'كوبون الخصم غير صالح أو منتهي الصلاحية'
            ], 422);
        }

        $user = Auth::user();
        $cartItems = $user->cartItems()
                         ->with(['product' => function ($query) {
                             $query->where('status', 'active');
                         }])
                         ->get();

        // تنظيف المنتجات المحذوفة
        $validItems = $this->cleanCartItems($cartItems);

        if ($validItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'السلة فارغة'
            ], 422);
        }

        $subtotal = $validItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        if (!$coupon->canBeUsedForAmount($subtotal)) {
            return response()->json([
                'success' => false,
                'message' => 'الحد الأدنى للطلب غير محقق. المطلوب: ' . $coupon->min_order_amount
            ], 422);
        }

        $discount = $coupon->calculateDiscount($subtotal);
        
        // حفظ الكوبون في الجلسة
        session(['applied_coupon' => [
            'code' => $coupon->code,
            'discount' => $discount,
            'coupon_id' => $coupon->id
        ]]);

        $cart = $this->calculateCartTotals($validItems, $coupon);

        return response()->json([
            'success' => true,
            'message' => 'تم تطبيق كوبون الخصم بنجاح',
            'data' => [
                'cart' => $cart,
                'discount' => [
                    'code' => $coupon->code,
                    'amount' => $discount,
                    'type' => $coupon->type,
                    'value' => $coupon->value
                ]
            ]
        ]);
    }

    /**
     * إزالة كوبون الخصم
     */
    public function removeCoupon(): JsonResponse
    {
        session()->forget('applied_coupon');

        return response()->json([
            'success' => true,
            'message' => 'تم إزالة كوبون الخصم بنجاح',
            'data' => [
                'cart' => $this->getCartData()
            ]
        ]);
    }

    /**
     * إفراغ السلة
     */
    public function clear(): JsonResponse
    {
        Auth::user()->cartItems()->delete();
        session()->forget('applied_coupon');

        return response()->json([
            'success' => true,
            'message' => 'تم إفراغ السلة بنجاح',
            'data' => [
                'cart' => [
                    'items' => [],
                    'subtotal' => 0,
                    'tax' => 0,
                    'shipping' => 0,
                    'discount' => 0,
                    'total' => 0,
                    'currency' => 'EGP',
                    'items_count' => 0
                ]
            ]
        ]);
    }

    /**
     * جلب بيانات السلة النظيفة
     */
    private function getCartData()
    {
        $user = Auth::user();
        $cartItems = $user->cartItems()
                         ->with(['product' => function ($query) {
                             $query->where('status', 'active')
                                   ->with(['category', 'brand']);
                         }])
                         ->get();

        $validItems = $this->cleanCartItems($cartItems);
        return $this->calculateCartTotals($validItems);
    }

    /**
     * تنظيف السلة من المنتجات المحذوفة
     */
    private function cleanCartItems($cartItems)
    {
        $validItems = collect();

        foreach ($cartItems as $item) {
            if ($item->product && $item->product->status === 'active') {
                $validItems->push($item);
            } else {
                // منتج محذوف أو غير نشط - إزالة من السلة
                $item->delete();
            }
        }

        return $validItems;
    }

    /**
     * حساب إجماليات السلة
     */
    private function calculateCartTotals($cartItems, $coupon = null)
    {
        if ($cartItems->isEmpty()) {
            return [
                'id' => 'cart_' . Auth::id(),
                'user_id' => Auth::id(),
                'items' => [],
                'subtotal' => 0,
                'tax' => 0,
                'shipping' => 0,
                'discount' => 0,
                'total' => 0,
                'currency' => 'EGP',
                'items_count' => 0
            ];
        }

        $subtotal = $cartItems->sum(function ($item) {
            // ✅ فحص آمن للمنتج قبل الوصول للسعر
            if (!$item->product) {
                return 0;
            }
            return $item->quantity * $item->product->price;
        });

        $discount = 0;
        if ($coupon) {
            $discount = $coupon->calculateDiscount($subtotal);
        } else {
            $appliedCoupon = session('applied_coupon');
            if ($appliedCoupon) {
                $discount = $appliedCoupon['discount'];
            }
        }

        $taxRate = 0.14; // 14% ضريبة
        $tax = ($subtotal - $discount) * $taxRate;
        
        $shipping = $subtotal > 500 ? 0 : 50; // شحن مجاني فوق 500 جنيه
        $total = $subtotal - $discount + $tax + $shipping;

        return [
            'id' => 'cart_' . Auth::id(),
            'user_id' => Auth::id(),
            'items' => $cartItems->map(function ($item) {
                // ✅ فحص آمن للمنتج
                if (!$item->product) {
                    return null;
                }
                
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => (string) $item->product->price,
                    'total_price' => (string) ($item->quantity * $item->product->price),
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name_ar ?: $item->product->name_en,
                        'price' => (string) $item->product->price,
                        'images' => is_string($item->product->images) 
                            ? json_decode($item->product->images, true) 
                            : $item->product->images,
                        'category' => $item->product->category,
                        'brand' => $item->product->brand,
                        'stock' => $item->product->stock,
                        'status' => $item->product->status
                    ]
                ];
            })->filter(), // إزالة العناصر null
            'subtotal' => (string) round($subtotal, 2),
            'tax' => (string) round($tax, 2),
            'shipping' => (string) $shipping,
            'discount' => (string) round($discount, 2),
            'total' => (string) round($total, 2),
            'currency' => 'EGP',
            'items_count' => $cartItems->sum('quantity')
        ];
    }
} 