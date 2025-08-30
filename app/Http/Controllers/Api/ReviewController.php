<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReviewController extends Controller
{
    /**
     * جلب تقييمات منتج محدد
     */
    public function productReviews($productId, Request $request): JsonResponse
    {
        $product = Product::find($productId);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        $query = $product->reviews()->with('user:id,name');

        // فلترة حسب الحالة (للاختبار فقط - يمكن إزالتها لاحقاً)
        if ($request->boolean('include_pending')) {
            // عرض جميع المراجعات بما في ذلك pending
        } else {
            $query->approved(); // عرض المراجعات المعتمدة فقط
        }

        // فلترة حسب التقييم
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        // فلترة المراجعات المحققة
        if ($request->boolean('verified_only')) {
            $query->where('verified_purchase', true);
        }

        // ترتيب حسب الأحدث أو الأكثر إفادة
        $sortBy = $request->get('sort', 'newest');
        switch ($sortBy) {
            case 'helpful':
                $query->orderBy('helpful_count', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $reviews = $query->paginate($request->get('per_page', 10));

        // إحصائيات التقييمات
        $ratingSummary = $product->reviews()
            ->approved()
            ->selectRaw('rating, COUNT(*) as count')
            ->groupBy('rating')
            ->pluck('count', 'rating')
            ->toArray();

        $totalReviews = array_sum($ratingSummary);
        $averageRating = $totalReviews > 0 ? 
            array_sum(array_map(fn($rating, $count) => $rating * $count, array_keys($ratingSummary), $ratingSummary)) / $totalReviews : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'reviews' => $reviews->items(),
                'pagination' => [
                    'current_page' => $reviews->currentPage(),
                    'total_pages' => $reviews->lastPage(),
                    'total_reviews' => $reviews->total(),
                    'per_page' => $reviews->perPage(),
                ],
                'summary' => [
                    'average_rating' => round($averageRating, 1),
                    'total_reviews' => $totalReviews,
                    'rating_breakdown' => [
                        '5' => $ratingSummary[5] ?? 0,
                        '4' => $ratingSummary[4] ?? 0,
                        '3' => $ratingSummary[3] ?? 0,
                        '2' => $ratingSummary[2] ?? 0,
                        '1' => $ratingSummary[1] ?? 0,
                    ]
                ]
            ]
        ]);
    }

    /**
     * إضافة تقييم لمنتج
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'string'
        ]);

        $user = Auth::user();
        
        // التحقق من وجود تقييم سابق
        $existingReview = ProductReview::where('user_id', $user->id)
                                     ->where('product_id', $request->product_id)
                                     ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this product'
            ], 422);
        }

        // التحقق من شراء المنتج
        $hasPurchased = $user->orders()
                           ->whereHas('orderItems', function ($query) use ($request) {
                               $query->where('product_id', $request->product_id);
                           })
                           ->where('status', 'delivered')
                           ->exists();

        $review = ProductReview::create([
            'user_id' => $user->id,
            'product_id' => $request->product_id,
            'rating' => $request->rating,
            'review' => $request->review,
            'verified_purchase' => $hasPurchased,
            'images' => $request->images ?? [],
            'status' => 'pending' // يحتاج موافقة إدارية
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully. It will be published after approval.',
            'data' => [
                'review' => $review
            ]
        ], 201);
    }

    /**
     * تحديث تقييم
     */
    public function update(Request $request, $id): JsonResponse
    {
        $review = ProductReview::where('id', $id)
                             ->where('user_id', Auth::id())
                             ->first();

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
            'images' => 'nullable|array|max:5',
            'images.*' => 'string'
        ]);

        $review->update([
            'rating' => $request->rating,
            'review' => $request->review,
            'images' => $request->images ?? [],
            // للاختبار: نبقي على نفس الـ status أو نجعله approved مباشرة
            'status' => 'approved' // أو يمكن إزالة هذا السطر للاحتفاظ بالـ status الحالي
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review updated successfully',
            'data' => [
                'review' => $review
            ]
        ]);
    }

    /**
     * حذف تقييم
     */
    public function destroy($id): JsonResponse
    {
        $review = ProductReview::where('id', $id)
                             ->where('user_id', Auth::id())
                             ->first();

        if (!$review) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }

        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Review deleted successfully'
        ]);
    }

    /**
     * إضافة إعجاب لتقييم
     */
    public function markHelpful($id): JsonResponse
    {
        $review = ProductReview::find($id);

        if (!$review || !$review->isApproved()) {
            return response()->json([
                'success' => false,
                'message' => 'Review not found'
            ], 404);
        }

        $review->incrementHelpful();

        return response()->json([
            'success' => true,
            'message' => 'Thank you for your feedback',
            'data' => [
                'helpful_count' => $review->helpful_count
            ]
        ]);
    }
} 