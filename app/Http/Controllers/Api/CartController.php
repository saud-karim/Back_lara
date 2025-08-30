<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

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
                             $query->with(['category', 'brand']);
                         }])
                         ->get();

        $cart = $this->calculateCartTotals($cartItems);

        return response()->json([
            'success' => true,
            'data' => [
                'cart' => $cart
            ]
        ]);
    }

    /**
     * إضافة منتج للسلة
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::find($request->product_id);

        // التحقق من توفر الكمية
        if ($product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Available: ' . $product->stock
            ], 422);
        }

        $user = Auth::user();
        $cartItem = CartItem::where('user_id', $user->id)
                           ->where('product_id', $request->product_id)
                           ->first();

        if ($cartItem) {
            $newQuantity = $cartItem->quantity + $request->quantity;
            
            if ($product->stock < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot add more items. Max available: ' . $product->stock
                ], 422);
            }

            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            $cartItem = CartItem::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity
            ]);
        }

        // جلب السلة المحدثة
        $cartItems = $user->cartItems()
                         ->with(['product' => function ($query) {
                             $query->with(['category', 'brand']);
                         }])
                         ->get();

        $cart = $this->calculateCartTotals($cartItems);

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully',
            'data' => [
                'cart' => $cart
            ]
        ]);
    }

    /**
     * تحديث كمية منتج في السلة
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:0'
        ]);

        $user = Auth::user();
        $cartItem = CartItem::where('user_id', $user->id)
                           ->where('product_id', $request->product_id)
                           ->first();

        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in cart'
            ], 404);
        }

        if ($request->quantity == 0) {
            $cartItem->delete();
        } else {
            // التحقق من توفر الكمية
            $product = Product::find($request->product_id);
            if ($product->stock < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock. Available: ' . $product->stock
                ], 422);
            }

            $cartItem->update(['quantity' => $request->quantity]);
        }

        // جلب السلة المحدثة
        $cartItems = $user->cartItems()
                         ->with(['product' => function ($query) {
                             $query->with(['category', 'brand']);
                         }])
                         ->get();

        $cart = $this->calculateCartTotals($cartItems);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully',
            'data' => [
                'cart' => $cart
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
                'message' => 'Product not found in cart'
            ], 404);
        }

        $cartItem->delete();

        // جلب السلة المحدثة
        $cartItems = $user->cartItems()
                         ->with(['product' => function ($query) {
                             $query->with(['category', 'brand']);
                         }])
                         ->get();

        $cart = $this->calculateCartTotals($cartItems);

        return response()->json([
            'success' => true,
            'message' => 'Product removed from cart successfully',
            'data' => [
                'cart' => $cart
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
                'message' => 'Invalid or expired coupon'
            ], 422);
        }

        $user = Auth::user();
        $cartItems = $user->cartItems()
                         ->with(['product'])
                         ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 422);
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        if (!$coupon->canBeUsedForAmount($subtotal)) {
            return response()->json([
                'success' => false,
                'message' => 'Minimum order amount not met. Required: ' . $coupon->min_order_amount
            ], 422);
        }

        $discount = $coupon->calculateDiscount($subtotal);
        
        // حفظ الكوبون في الجلسة أو قاعدة البيانات
        session(['applied_coupon' => [
            'code' => $coupon->code,
            'discount' => $discount,
            'coupon_id' => $coupon->id
        ]]);

        $cart = $this->calculateCartTotals($cartItems, $coupon);

        return response()->json([
            'success' => true,
            'message' => 'Coupon applied successfully',
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

        $user = Auth::user();
        $cartItems = $user->cartItems()
                         ->with(['product'])
                         ->get();

        $cart = $this->calculateCartTotals($cartItems);

        return response()->json([
            'success' => true,
            'message' => 'Coupon removed successfully',
            'data' => [
                'cart' => $cart
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
            'message' => 'Cart cleared successfully',
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
     * حساب إجماليات السلة
     */
    private function calculateCartTotals($cartItems, $coupon = null)
    {
        $subtotal = $cartItems->sum(function ($item) {
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
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->product->price,
                    'total_price' => $item->quantity * $item->product->price,
                    'product' => $item->product
                ];
            }),
            'subtotal' => round($subtotal, 2),
            'tax' => round($tax, 2),
            'shipping' => $shipping,
            'discount' => round($discount, 2),
            'total' => round($total, 2),
            'currency' => 'EGP',
            'items_count' => $cartItems->sum('quantity')
        ];
    }
} 