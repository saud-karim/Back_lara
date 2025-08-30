<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\ProductReview;
use App\Models\ContactMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get admin dashboard statistics
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_customers' => User::where('role', 'customer')->count(),
            'total_revenue' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'low_stock_products' => Product::where('stock', '<=', 10)->count(),
            'new_customers_this_month' => User::where('role', 'customer')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->count(),
            'monthly_growth_percentage' => $this->calculateMonthlyGrowth()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Get recent activity for admin dashboard
     */
    public function recentActivity(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 5);
        $activities = [];

        // Recent orders
        $recentOrders = Order::with('user')
            ->latest()
            ->take($limit)
            ->get();

        foreach ($recentOrders as $order) {
            $activities[] = [
                'id' => $order->id,
                'type' => 'order',
                'message' => "طلب جديد #{$order->id}",
                'timestamp' => $order->created_at->toISOString(),
                'user_name' => $order->user->name ?? 'غير محدد'
            ];
        }

        // New customers
        $newCustomers = User::where('role', 'customer')
            ->latest()
            ->take($limit)
            ->get();

        foreach ($newCustomers as $customer) {
            $activities[] = [
                'id' => $customer->id,
                'type' => 'customer',
                'message' => 'عميل جديد انضم',
                'timestamp' => $customer->created_at->toISOString(),
                'user_name' => $customer->name
            ];
        }

        // Low stock products
        $lowStockProducts = Product::where('stock', '<=', 10)
            ->latest()
            ->take($limit)
            ->get();

        foreach ($lowStockProducts as $product) {
            $activities[] = [
                'id' => $product->id,
                'type' => 'product',
                'message' => "مخزون منخفض: {$product->name_ar}",
                'timestamp' => $product->updated_at->toISOString(),
                'product_id' => $product->id
            ];
        }

        // Recent reviews
        $recentReviews = ProductReview::with('product', 'user')
            ->where('status', 'approved')
            ->latest()
            ->take($limit)
            ->get();

        foreach ($recentReviews as $review) {
            $activities[] = [
                'id' => $review->id,
                'type' => 'review',
                'message' => "تقييم جديد {$review->rating}⭐",
                'timestamp' => $review->created_at->toISOString(),
                'product_name' => $review->product->name_ar ?? 'منتج محذوف',
                'rating' => $review->rating
            ];
        }

        // Sort all activities by timestamp (newest first)
        usort($activities, function ($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        // Limit the final result
        $activities = array_slice($activities, 0, $limit);

        return response()->json([
            'success' => true,
            'data' => $activities
        ]);
    }

    /**
     * Calculate monthly growth percentage
     */
    private function calculateMonthlyGrowth(): float
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $previousMonth = Carbon::now()->subMonth()->startOfMonth();

        $currentMonthRevenue = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$currentMonth, Carbon::now()])
            ->sum('total_amount');

        $previousMonthRevenue = Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$previousMonth, $currentMonth])
            ->sum('total_amount');

        if ($previousMonthRevenue == 0) {
            return $currentMonthRevenue > 0 ? 100.0 : 0.0;
        }

        $growth = (($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100;
        
        return round($growth, 1);
    }

    /**
     * Get admin dashboard overview with charts data
     */
    public function overview(): JsonResponse
    {
        $stats = $this->stats()->getData()->data;

        // Sales chart data (last 7 days)
        $salesChart = $this->getSalesChartData();
        
        // Orders by status
        $ordersByStatus = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // Top selling products
        $topProducts = Product::select('products.*', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->groupBy('products.id')
            ->orderBy('total_sold', 'desc')
            ->take(5)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => $stats,
                'sales_chart' => $salesChart,
                'orders_by_status' => $ordersByStatus,
                'top_products' => $topProducts
            ]
        ]);
    }

    /**
     * Get sales chart data for the last 7 days
     */
    private function getSalesChartData(): array
    {
        $data = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $revenue = Order::where('payment_status', 'paid')
                ->whereDate('created_at', $date)
                ->sum('total_amount');
                
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'revenue' => (float) $revenue
            ];
        }
        
        return $data;
    }
} 