<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\WishlistItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    /**
     * جلب قائمة الأمنيات
     */
    public function index(): JsonResponse
    {
        $wishlistItems = Auth::user()->wishlistItems()
                            ->with(['product' => function ($query) {
                                $query->with(['category', 'brand']);
                            }])
                            ->orderBy('created_at', 'desc')
                            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'wishlist' => $wishlistItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product_id' => $item->product_id,
                        'date_added' => $item->created_at,
                        'product' => $item->product
                    ];
                }),
                'total_items' => $wishlistItems->count()
            ]
        ]);
    }

    /**
     * إضافة منتج لقائمة الأمنيات
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $user = Auth::user();
        
        // التحقق من وجود المنتج في قائمة الأمنيات
        $existingItem = WishlistItem::where('user_id', $user->id)
                                  ->where('product_id', $request->product_id)
                                  ->first();

        if ($existingItem) {
            return response()->json([
                'success' => false,
                'message' => 'Product already in wishlist'
            ], 422);
        }

        $wishlistItem = WishlistItem::create([
            'user_id' => $user->id,
            'product_id' => $request->product_id
        ]);

        $wishlistItem->load(['product' => function ($query) {
            $query->with(['category', 'brand']);
        }]);

        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist successfully',
            'data' => [
                'wishlist_item' => [
                    'id' => $wishlistItem->id,
                    'product_id' => $wishlistItem->product_id,
                    'date_added' => $wishlistItem->created_at,
                    'product' => $wishlistItem->product
                ]
            ]
        ], 201);
    }

    /**
     * إزالة منتج من قائمة الأمنيات
     */
    public function remove($productId): JsonResponse
    {
        $user = Auth::user();
        $wishlistItem = WishlistItem::where('user_id', $user->id)
                                  ->where('product_id', $productId)
                                  ->first();

        if (!$wishlistItem) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in wishlist'
            ], 404);
        }

        $wishlistItem->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product removed from wishlist successfully'
        ]);
    }

    /**
     * نقل منتج من قائمة الأمنيات إلى السلة
     */
    public function moveToCart(Request $request, $productId): JsonResponse
    {
        $request->validate([
            'quantity' => 'integer|min:1'
        ]);

        $user = Auth::user();
        $wishlistItem = WishlistItem::where('user_id', $user->id)
                                  ->where('product_id', $productId)
                                  ->first();

        if (!$wishlistItem) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in wishlist'
            ], 404);
        }

        $quantity = $request->get('quantity', 1);
        
        // التحقق من توفر الكمية
        $product = Product::find($productId);
        if ($product->stock < $quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock. Available: ' . $product->stock
            ], 422);
        }

        // نقل إلى السلة
        $wishlistItem->moveToCart($quantity);

        return response()->json([
            'success' => true,
            'message' => 'Product moved to cart successfully'
        ]);
    }

    /**
     * إفراغ قائمة الأمنيات
     */
    public function clear(): JsonResponse
    {
        Auth::user()->wishlistItems()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Wishlist cleared successfully'
        ]);
    }

    /**
     * التحقق من وجود منتج في قائمة الأمنيات
     */
    public function check($productId): JsonResponse
    {
        $user = Auth::user();
        $exists = WishlistItem::where('user_id', $user->id)
                             ->where('product_id', $productId)
                             ->exists();

        return response()->json([
            'success' => true,
            'data' => [
                'in_wishlist' => $exists
            ]
        ]);
    }

    /**
     * إضافة أو إزالة منتج من قائمة الأمنيات (Toggle)
     */
    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        $user = Auth::user();
        $wishlistItem = WishlistItem::where('user_id', $user->id)
                                  ->where('product_id', $request->product_id)
                                  ->first();

        if ($wishlistItem) {
            // إزالة من قائمة الأمنيات
            $wishlistItem->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Product removed from wishlist',
                'data' => [
                    'action' => 'removed',
                    'in_wishlist' => false
                ]
            ]);
        } else {
            // إضافة إلى قائمة الأمنيات
            $wishlistItem = WishlistItem::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Product added to wishlist',
                'data' => [
                    'action' => 'added',
                    'in_wishlist' => true,
                    'wishlist_item_id' => $wishlistItem->id
                ]
            ], 201);
        }
    }
} 