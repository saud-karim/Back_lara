<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AdminReviewController extends Controller
{
    /**
     * 1. Get review statistics
     * GET /api/v1/admin/reviews/stats
     */
    public function stats(): JsonResponse
    {
        try {
            $totalReviews = Review::count();
            $approvedReviews = Review::approved()->count();
            $pendingReviews = Review::pending()->count();
            $rejectedReviews = Review::rejected()->count();
            $averageRating = round(Review::avg('rating') ?? 0, 1);
            $verifiedReviews = Review::verified()->count();
            $reviewsWithImages = Review::withImages()->count();
            $helpfulReviews = Review::helpful()->count();

            // Rating breakdown
            $ratingBreakdown = [];
            for ($i = 1; $i <= 5; $i++) {
                $ratingBreakdown[$i] = Review::where('rating', $i)->count();
            }

            // Monthly stats for last 12 months
            $monthlyStats = [];
            for ($i = 11; $i >= 0; $i--) {
                $startOfMonth = now()->subMonths($i)->startOfMonth();
                $endOfMonth = now()->subMonths($i)->endOfMonth();
                
                $monthlyReviews = Review::whereBetween('created_at', [$startOfMonth, $endOfMonth]);
                $monthApproved = clone $monthlyReviews;
                
                $monthlyStats[] = [
                    'month' => $startOfMonth->format('Y-m'),
                    'total' => $monthlyReviews->count(),
                    'approved' => $monthApproved->approved()->count(),
                    'average_rating' => round($monthlyReviews->avg('rating') ?? 0, 1)
                ];
            }

            // Top rated products
            $topRatedProducts = DB::table('reviews')
                ->join('products', 'products.id', '=', 'reviews.product_id')
                ->select(
                    'products.id as product_id',
                    DB::raw('COALESCE(products.name_ar, products.name_en) as product_name'),
                    DB::raw('COUNT(reviews.id) as review_count'),
                    DB::raw('ROUND(AVG(reviews.rating), 1) as average_rating')
                )
                ->where('reviews.status', Review::STATUS_APPROVED)
                ->groupBy('products.id', 'products.name_ar', 'products.name_en')
                ->having('review_count', '>=', 3)
                ->orderBy('average_rating', 'desc')
                ->orderBy('review_count', 'desc')
                ->limit(10)
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_reviews' => $totalReviews,
                    'approved_reviews' => $approvedReviews,
                    'pending_reviews' => $pendingReviews,
                    'rejected_reviews' => $rejectedReviews,
                    'average_rating' => $averageRating,
                    'verified_reviews' => $verifiedReviews,
                    'reviews_with_images' => $reviewsWithImages,
                    'helpful_reviews' => $helpfulReviews,
                    'rating_breakdown' => $ratingBreakdown,
                    'monthly_stats' => $monthlyStats,
                    'top_rated_products' => $topRatedProducts
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve review statistics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * 2. Get reviews list with filters and pagination
     * GET /api/v1/admin/reviews
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'page' => 'nullable|integer|min:1',
                'per_page' => 'nullable|integer|min:1|max:100',
                'search' => 'nullable|string|max:255',
                'status' => 'nullable|in:pending,approved,rejected',
                'rating' => 'nullable|integer|min:1|max:5',
                'verified_only' => 'nullable|boolean',
                'product_id' => 'nullable|integer|exists:products,id',
                'user_id' => 'nullable|integer|exists:users,id',
                'date_from' => 'nullable|date',
                'date_to' => 'nullable|date|after_or_equal:date_from',
                'sort_by' => 'nullable|in:created_at,rating,helpful_count,status',
                'sort_direction' => 'nullable|in:asc,desc'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $query = Review::with(['user', 'product', 'images']);

            // Apply filters
            if ($request->filled('search')) {
                $query->search($request->search);
            }

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('rating')) {
                $query->byRating($request->rating);
            }

            if ($request->boolean('verified_only')) {
                $query->verified();
            }

            if ($request->filled('product_id')) {
                $query->byProduct($request->product_id);
            }

            if ($request->filled('user_id')) {
                $query->byUser($request->user_id);
            }

            if ($request->filled('date_from') || $request->filled('date_to')) {
                $dateFrom = $request->date_from ?? '1900-01-01';
                $dateTo = $request->date_to ?? now()->toDateString();
                $query->dateRange($dateFrom, $dateTo);
            }

            // Sorting
            $sortBy = $request->sort_by ?? 'created_at';
            $sortDirection = $request->sort_direction ?? 'desc';
            $query->orderBy($sortBy, $sortDirection);

            // Pagination
            $perPage = $request->per_page ?? 20;
            $reviews = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $reviews
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve reviews',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * 3. Get review details
     * GET /api/v1/admin/reviews/{id}
     */
    public function show($id): JsonResponse
    {
        try {
            $review = Review::with([
                'user',
                'product',
                'order',
                'images',
                'statusHistory.changedBy',
                'helpfulUsers',
                'adminResponse'
            ])->find($id);

            if (!$review) {
                return response()->json([
                    'success' => false,
                    'message' => 'Review not found'
                ], 404);
            }

            // Format the response according to requirements
            $formattedReview = [
                'id' => $review->id,
                'user' => [
                    'id' => $review->user->id,
                    'name' => $review->user->name,
                    'email' => $review->user->email,
                    'phone' => $review->user->phone ?? null,
                    'avatar' => $review->user->avatar ? asset('storage/' . $review->user->avatar) : null,
                    'total_reviews' => $review->user->reviews()->count(),
                    'average_rating' => round($review->user->reviews()->avg('rating') ?? 0, 1),
                    'verified_buyer' => $review->verified_purchase
                ],
                'product' => [
                    'id' => $review->product->id,
                    'name' => $review->product->name_ar ?? $review->product->name_en,
                    'name_ar' => $review->product->name_ar,
                    'name_en' => $review->product->name_en,
                    'image' => $review->product->images[0] ?? null,
                    'price' => number_format($review->product->price, 2),
                    'category' => $review->product->category->name_ar ?? $review->product->category->name_en ?? 'N/A',
                    'brand' => $review->product->brand->name ?? 'N/A',
                    'sku' => $review->product->sku
                ],
                'order' => $review->order ? [
                    'id' => $review->order->id,
                    'order_number' => $review->order->order_number,
                    'purchase_date' => $review->order->created_at->toISOString(),
                    'total_amount' => number_format($review->order->total_amount, 2)
                ] : null,
                'rating' => $review->rating,
                'review' => $review->review,
                'review_ar' => $review->review_ar,
                'review_en' => $review->review_en,
                'status' => $review->status,
                'verified_purchase' => $review->verified_purchase,
                'helpful_count' => $review->helpful_count,
                'helpful_users' => $review->helpfulUsers->pluck('id')->toArray(),
                'images' => $review->images->map(function ($image) {
                    return [
                        'id' => $image->id,
                        'url' => $image->image_url,
                        'thumbnail' => $image->thumbnail_url,
                        'alt' => $image->alt_text
                    ];
                }),
                'admin_response' => $review->admin_response,
                'admin_response_by' => $review->adminResponse ? [
                    'id' => $review->adminResponse->id,
                    'name' => $review->adminResponse->name
                ] : null,
                'admin_response_at' => $review->admin_response_at?->toISOString(),
                'status_history' => $review->statusHistory->map(function ($history) {
                    return [
                        'status' => $history->new_status,
                        'changed_by' => $history->changedBy ? $history->changedBy->name : 'system',
                        'changed_at' => $history->created_at->toISOString(),
                        'reason' => $history->reason
                    ];
                }),
                'created_at' => $review->created_at->toISOString(),
                'updated_at' => $review->updated_at->toISOString()
            ];

            return response()->json([
                'success' => true,
                'data' => $formattedReview
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve review details',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * 4. Update review status
     * PUT /api/v1/admin/reviews/{id}/status
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,approved,rejected',
                'reason' => 'nullable|string|max:1000',
                'admin_response' => 'nullable|string|max:1000',
                'notify_user' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $review = Review::find($id);
            if (!$review) {
                return response()->json([
                    'success' => false,
                    'message' => 'Review not found'
                ], 404);
            }

            $previousStatus = $review->status;

            // Update review with new status and admin response
            $review->update([
                'status' => $request->status,
                'admin_response' => $request->admin_response,
                'admin_response_by' => Auth::id(),
                'admin_response_at' => now()
            ]);

            // Log status change in history
            $review->statusHistory()->create([
                'old_status' => $previousStatus,
                'new_status' => $request->status,
                'changed_by' => Auth::id(),
                'reason' => $request->reason
            ]);

            // TODO: Send notification to user if notify_user is true
            // if ($request->boolean('notify_user')) {
            //     // Implementation for sending notification
            // }

            return response()->json([
                'success' => true,
                'message' => 'Review status updated successfully',
                'data' => [
                    'id' => $review->id,
                    'status' => $review->status,
                    'previous_status' => $previousStatus,
                    'updated_by' => Auth::user()->name,
                    'updated_at' => $review->updated_at->toISOString(),
                    'reason' => $request->reason
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update review status',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * 5. Bulk actions on reviews
     * POST /api/v1/admin/reviews/bulk
     */
    public function bulk(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'action' => 'required|in:update_status,delete,mark_helpful,export',
                'review_ids' => 'required|array|min:1',
                'review_ids.*' => 'required|integer|exists:reviews,id',
                'data' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $reviewIds = $request->review_ids;
            $action = $request->action;
            $data = $request->data ?? [];
            
            $results = [];
            $successCount = 0;
            $failedCount = 0;

            foreach ($reviewIds as $reviewId) {
                try {
                    $review = Review::find($reviewId);
                    if (!$review) {
                        $results[] = [
                            'review_id' => $reviewId,
                            'status' => 'failed',
                            'message' => 'Review not found'
                        ];
                        $failedCount++;
                        continue;
                    }

                    switch ($action) {
                        case 'update_status':
                            if (!isset($data['status']) || !in_array($data['status'], ['pending', 'approved', 'rejected'])) {
                                throw new \Exception('Invalid status provided');
                            }
                            
                            $previousStatus = $review->status;
                            $review->update([
                                'status' => $data['status'],
                                'admin_response_by' => Auth::id(),
                                'admin_response_at' => now()
                            ]);

                            // Log status change
                            $review->statusHistory()->create([
                                'old_status' => $previousStatus,
                                'new_status' => $data['status'],
                                'changed_by' => Auth::id(),
                                'reason' => $data['reason'] ?? 'Bulk update'
                            ]);

                            $results[] = [
                                'review_id' => $reviewId,
                                'status' => 'success',
                                'message' => 'Status updated'
                            ];
                            break;

                        case 'delete':
                            $review->delete();
                            $results[] = [
                                'review_id' => $reviewId,
                                'status' => 'success',
                                'message' => 'Review deleted'
                            ];
                            break;

                        case 'mark_helpful':
                            $review->increment('helpful_count');
                            $results[] = [
                                'review_id' => $reviewId,
                                'status' => 'success',
                                'message' => 'Marked as helpful'
                            ];
                            break;

                        case 'export':
                            // Export functionality would be implemented here
                            $results[] = [
                                'review_id' => $reviewId,
                                'status' => 'success',
                                'message' => 'Added to export'
                            ];
                            break;
                    }

                    $successCount++;

                } catch (\Exception $e) {
                    $results[] = [
                        'review_id' => $reviewId,
                        'status' => 'failed',
                        'message' => $e->getMessage()
                    ];
                    $failedCount++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Bulk action completed',
                'data' => [
                    'processed_count' => count($reviewIds),
                    'successful_count' => $successCount,
                    'failed_count' => $failedCount,
                    'results' => $results
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk action',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * 6. Get review analytics
     * GET /api/v1/admin/reviews/analytics
     */
    public function analytics(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'period' => 'nullable|in:last_7_days,last_30_days,last_3_months,last_year,custom',
                'product_id' => 'nullable|integer|exists:products,id',
                'category_id' => 'nullable|integer|exists:categories,id',
                'group_by' => 'nullable|in:day,week,month,year'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Determine date range based on period
            $period = $request->period ?? 'last_30_days';
            $dateFrom = match($period) {
                'last_7_days' => now()->subDays(7),
                'last_30_days' => now()->subDays(30),
                'last_3_months' => now()->subMonths(3),
                'last_year' => now()->subYear(),
                default => now()->subDays(30)
            };
            $dateTo = now();

            $query = Review::whereBetween('created_at', [$dateFrom, $dateTo]);

            // Apply filters
            if ($request->filled('product_id')) {
                $query->where('product_id', $request->product_id);
            }

            if ($request->filled('category_id')) {
                $query->whereHas('product', function ($q) use ($request) {
                    $q->where('category_id', $request->category_id);
                });
            }

            // Review trends
            $groupBy = $request->group_by ?? 'day';
            $reviewTrends = $this->getReviewTrends($dateFrom, $dateTo, $groupBy, $query);

            // Rating distribution
            $ratingDistribution = $this->getRatingDistribution($query);

            // Top products
            $topProducts = $this->getTopProducts($query);

            // Response times
            $responseTimes = $this->getResponseTimes();

            return response()->json([
                'success' => true,
                'data' => [
                    'review_trends' => $reviewTrends,
                    'rating_distribution' => $ratingDistribution,
                    'top_products' => $topProducts,
                    'response_times' => $responseTimes
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve analytics',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    // Helper methods for analytics
    private function getReviewTrends($dateFrom, $dateTo, $groupBy, $query)
    {
        $trends = [];
        $format = match($groupBy) {
            'day' => '%Y-%m-%d',
            'week' => '%Y-%u',
            'month' => '%Y-%m',
            'year' => '%Y',
            default => '%Y-%m-%d'
        };

        $results = (clone $query)
            ->selectRaw("
                DATE_FORMAT(created_at, '{$format}') as period,
                COUNT(*) as total_reviews,
                SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_reviews,
                ROUND(AVG(rating), 1) as average_rating,
                ROUND(SUM(CASE WHEN verified_purchase = 1 THEN 1 ELSE 0 END) / COUNT(*) * 100, 1) as verified_percentage
            ")
            ->groupBy('period')
            ->orderBy('period')
            ->get();

        return $results->toArray();
    }

    private function getRatingDistribution($query)
    {
        $total = (clone $query)->count();
        $distribution = [];

        for ($i = 1; $i <= 5; $i++) {
            $count = (clone $query)->where('rating', $i)->count();
            $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
            
            $distribution["{$i}_star"] = [
                'count' => $count,
                'percentage' => $percentage
            ];
        }

        return $distribution;
    }

    private function getTopProducts($query)
    {
        return (clone $query)
            ->join('products', 'products.id', '=', 'reviews.product_id')
            ->select(
                'products.id as product_id',
                DB::raw('COALESCE(products.name_ar, products.name_en) as product_name'),
                DB::raw('COUNT(reviews.id) as review_count'),
                DB::raw('ROUND(AVG(reviews.rating), 1) as average_rating'),
                DB::raw('0 as growth_rate') // TODO: Calculate growth rate
            )
            ->groupBy('products.id', 'products.name_ar', 'products.name_en')
            ->orderBy('review_count', 'desc')
            ->limit(10)
            ->get()
            ->toArray();
    }

    private function getResponseTimes()
    {
        $pendingReviews = Review::pending();
        $pendingCount = $pendingReviews->count();
        $oldestPending = $pendingReviews->orderBy('created_at')->first();

        // Calculate average approval time for approved reviews
        $averageApprovalTime = DB::table('reviews')
            ->where('status', 'approved')
            ->whereNotNull('admin_response_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, admin_response_at)) as avg_hours')
            ->value('avg_hours');

        return [
            'average_approval_time' => $averageApprovalTime ? round($averageApprovalTime, 1) . ' hours' : 'N/A',
            'pending_reviews_count' => $pendingCount,
            'oldest_pending' => $oldestPending?->created_at?->toISOString()
        ];
    }
}
