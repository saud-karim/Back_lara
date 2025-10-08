<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Coupon;
use App\Http\Resources\CartResource;
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
        
        // ✅ Get or create cart for user
        $cart = Cart::firstOrCreate([
            'user_id' => $user->id
        ]);

        // ✅ Load cart items with products
        $cart->load(['cartItems.product']);

        // ✅ Clean cart from deleted or inactive products
        $removedCount = 0;
        foreach ($cart->cartItems as $item) {
            if (!$item->product || $item->product->status !== 'active') {
                $item->delete();
                $removedCount++;
            }
        }

        // ✅ Reload cart if items were removed
        if ($removedCount > 0) {
            $cart->load(['cartItems.product']);
        }

        $response = [
            'success' => true,
            'data' => new CartResource($cart)
        ];

        // ✅ Notify about removed products
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
            'product_id' => 'required|integer|exists:products,id',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500'
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
        
        // ✅ Get or create cart for user
        $cart = Cart::firstOrCreate([
            'user_id' => $user->id
        ]);

        // ✅ Check if product already in cart
        $cartItem = CartItem::where('cart_id', $cart->id)
                           ->where('product_id', $validated['product_id'])
                           ->where('variant_id', $validated['variant_id'] ?? null)
                           ->first();

        if ($cartItem) {
            // ✅ Update quantity
            $newQuantity = $cartItem->quantity + $validated['quantity'];
            
            if ($product->stock < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن إضافة المزيد. الحد الأقصى المتوفر: ' . $product->stock
                ], 400);
            }

            $cartItem->update([
                'quantity' => $newQuantity,
                'notes' => $validated['notes'] ?? $cartItem->notes
            ]);
        } else {
            // ✅ Create new cart item
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $validated['product_id'],
                'variant_id' => $validated['variant_id'] ?? null,
                'quantity' => $validated['quantity'],
                'notes' => $validated['notes'] ?? null
            ]);
        }

        // ✅ Reload cart with items
        $cart->load(['cartItems.product']);

        return response()->json([
            'success' => true,
            'message' => 'تم إضافة المنتج للسلة بنجاح',
            'data' => new CartResource($cart)
        ]);
    }

    /**
     * تحديث كمية منتج في السلة
     */
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'variant_id' => 'nullable|integer|exists:product_variants,id',
            'quantity' => 'required|integer|min:0',
            'notes' => 'nullable|string|max:500'
        ]);

        $user = Auth::user();
        
        // ✅ Get user's cart
        $cart = Cart::where('user_id', $user->id)->first();
        
        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'السلة فارغة'
            ], 404);
        }

        // ✅ Find cart item
        $cartItem = CartItem::where('cart_id', $cart->id)
                           ->where('product_id', $validated['product_id'])
                           ->where('variant_id', $validated['variant_id'] ?? null)
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
            // ✅ Check product and stock
            $product = Product::find($validated['product_id']);
            
            if (!$product || $product->status !== 'active') {
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

            $cartItem->update([
                'quantity' => $validated['quantity'],
                'notes' => $validated['notes'] ?? $cartItem->notes
            ]);
            $message = 'تم تحديث السلة بنجاح';
        }

        // ✅ Reload cart with items
        $cart->load(['cartItems.product']);

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => new CartResource($cart)
        ]);
    }

    /**
     * إزالة منتج من السلة
     */
    public function remove($productId): JsonResponse
    {
        $user = Auth::user();
        
        // ✅ Get user's cart
        $cart = Cart::where('user_id', $user->id)->first();
        
        if (!$cart) {
            return response()->json([
                'success' => false,
                'message' => 'السلة فارغة'
            ], 404);
        }

        // ✅ Find cart item
        $cartItem = CartItem::where('cart_id', $cart->id)
                           ->where('product_id', $productId)
                           ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'المنتج غير موجود في السلة'
            ], 404);
        }

        $cartItem->delete();

        // ✅ Reload cart with items
        $cart->load(['cartItems.product']);

        return response()->json([
            'success' => true,
            'message' => 'تم إزالة المنتج من السلة بنجاح',
            'data' => new CartResource($cart)
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
        $user = Auth::user();
        
        // ✅ Get user's cart
        $cart = Cart::where('user_id', $user->id)->first();
        
        if ($cart) {
            // ✅ Delete all cart items
            $cart->cartItems()->delete();
        }

        session()->forget('applied_coupon');

        return response()->json([
            'success' => true,
            'message' => 'تم إفراغ السلة بنجاح',
            'data' => [
                'id' => $cart->id ?? null,
                'user_id' => $user->id,
                'items_count' => 0,
                'items' => [],
                'summary' => [
                    'subtotal' => 0.00,
                    'shipping' => 0.00,
                    'tax' => 0.00,
                    'discount' => 0.00,
                    'total' => 0.00,
                    'currency' => 'EGP'
                ],
                'created_at' => $cart ? $cart->created_at?->toISOString() : now()->toISOString(),
                'updated_at' => $cart ? $cart->updated_at?->toISOString() : now()->toISOString()
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
                'summary' => [
                    'subtotal' => 0.00,
                    'tax' => 0.00,
                    'shipping' => 0.00,
                    'discount' => 0.00,
                    'total' => 0.00,
                    'currency' => 'EGP'
                ],
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

        $taxRate = 0.00; // No tax for now (was 14%)
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
                
                $unitPrice = (float) $item->product->price;
                $totalPrice = $item->quantity * $unitPrice;
                
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name_ar ?: $item->product->name_en,
                    'quantity' => $item->quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'product' => [
                        'id' => $item->product->id,
                        'name_ar' => $item->product->name_ar,
                        'name_en' => $item->product->name_en,
                        'price' => $unitPrice,
                        'images' => is_string($item->product->images) 
                            ? json_decode($item->product->images, true) 
                            : $item->product->images,
                        'category' => $item->product->category,
                        'brand' => $item->product->brand,
                        'stock' => $item->product->stock,
                        'status' => $item->product->status
                    ]
                ];
            })->filter()->values(), // إزالة العناصر null
            'summary' => [
                'subtotal' => (float) round($subtotal, 2),
                'tax' => (float) round($tax, 2),
                'shipping' => (float) $shipping,
                'discount' => (float) round($discount, 2),
                'total' => (float) round($total, 2),
                'currency' => 'EGP'
            ],
            'items_count' => $cartItems->sum('quantity')
        ];
    }
} 