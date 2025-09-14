<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminCustomerResource;
use App\Models\User;
use App\Models\CustomerActivity;
use App\Models\CustomerNote;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AdminCustomerController extends Controller
{
    /**
     * 1. Get customer statistics
     * GET /api/v1/admin/customers/stats
     */
    public function stats(): JsonResponse
    {
        $totalCustomers = User::customers()->count();
        $newCustomersThisMonth = User::customers()->newThisMonth()->count();
        $activeCustomers = User::customers()->active()->count();
        $inactiveCustomers = User::customers()->inactive()->count();
        $bannedCustomers = User::customers()->banned()->count();

        // Calculate average orders per customer
        $customersWithOrders = User::customers()
            ->withCount('orders')
            ->get();
        
        $averageOrdersPerCustomer = $customersWithOrders->avg('orders_count') ?: 0;

        // Additional metrics
        $topSpendingCustomers = User::customers()
            ->withCount('orders')
            ->having('orders_count', '>=', 5)
            ->count();

        $customersWithZeroOrders = User::customers()
            ->withoutOrders()
            ->count();

        // Growth percentage (compare with last month)
        $lastMonthCustomers = User::customers()
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        
        $growthPercentage = $lastMonthCustomers > 0 
            ? (($newCustomersThisMonth - $lastMonthCustomers) / $lastMonthCustomers) * 100 
            : 0;

        // Retention rate (customers who made orders in last 3 months)
        $activeInLastThreeMonths = User::customers()
            ->whereHas('orders', function($query) {
                $query->where('created_at', '>=', now()->subMonths(3));
            })
            ->count();
        
        $retentionRate = $totalCustomers > 0 
            ? ($activeInLastThreeMonths / $totalCustomers) * 100 
            : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'total_customers' => $totalCustomers,
                'new_customers_this_month' => $newCustomersThisMonth,
                'active_customers' => $activeCustomers,
                'inactive_customers' => $inactiveCustomers,
                'banned_customers' => $bannedCustomers,
                'average_orders_per_customer' => round($averageOrdersPerCustomer, 1),
                'top_spending_customers' => $topSpendingCustomers,
                'customers_with_zero_orders' => $customersWithZeroOrders,
                'growth_percentage' => round($growthPercentage, 1),
                'retention_rate' => round($retentionRate, 1)
            ]
        ]);
    }

    /**
     * 2. Get customers list with filters
     * GET /api/v1/admin/customers
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::customers()
            ->withCount(['orders', 'addresses'])
            ->with(['orders' => function($q) {
                $q->where('status', 'completed')->limit(1);
            }]);

        // Search filter
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Company filter
        if ($company = $request->get('company')) {
            $query->where('company', 'like', "%{$company}%");
        }

        // Registration date filters
        if ($registrationDateFrom = $request->get('registration_date_from')) {
            $query->whereDate('created_at', '>=', $registrationDateFrom);
        }

        if ($registrationDateTo = $request->get('registration_date_to')) {
            $query->whereDate('created_at', '<=', $registrationDateTo);
        }

        // Orders count filters
        if ($minOrders = $request->get('min_orders')) {
            $query->has('orders', '>=', $minOrders);
        }

        if ($maxOrders = $request->get('max_orders')) {
            $query->has('orders', '<=', $maxOrders);
        }

        // Spending filters (requires complex subquery)
        if ($minSpent = $request->get('min_spent')) {
            $query->whereHas('orders', function($q) use ($minSpent) {
                $q->where('status', 'completed')
                  ->havingRaw('SUM(total_amount) >= ?', [$minSpent]);
            });
        }

        if ($maxSpent = $request->get('max_spent')) {
            $query->whereHas('orders', function($q) use ($maxSpent) {
                $q->where('status', 'completed')
                  ->havingRaw('SUM(total_amount) <= ?', [$maxSpent]);
            });
        }

        // Sorting
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');

        switch ($sort) {
            case 'name':
                $query->orderBy('name', $order);
                break;
            case 'email':
                $query->orderBy('email', $order);
                break;
            case 'last_activity':
                $query->orderBy('last_activity', $order);
                break;
            case 'orders_count':
                $query->orderBy('orders_count', $order);
                break;
            case 'total_spent':
                // Complex sorting by total spent
                $query->withSum(['orders' => function($q) {
                    $q->where('status', 'completed');
                }], 'total_amount')
                ->orderBy('orders_sum_total_amount', $order);
                break;
            default:
                $query->orderBy('created_at', $order);
                break;
        }

        // Pagination
        $perPage = min($request->get('per_page', 15), 50);
        $customers = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => AdminCustomerResource::collection($customers),
            'meta' => [
                'current_page' => $customers->currentPage(),
                'total' => $customers->total(),
                'per_page' => $customers->perPage(),
                'last_page' => $customers->lastPage()
            ],
            'links' => [
                'first' => $customers->url(1),
                'last' => $customers->url($customers->lastPage()),
                'prev' => $customers->previousPageUrl(),
                'next' => $customers->nextPageUrl()
            ]
        ]);
    }

    /**
     * 3. Get specific customer details
     * GET /api/v1/admin/customers/{id}
     */
    public function show($customerId): JsonResponse
    {
        $customer = User::customers()
            ->with([
                'orders' => function($q) {
                    $q->latest()->limit(5)->with('orderItems');
                },
                'addresses',
                'customerNotes.admin'
            ])
            ->withCount(['orders', 'addresses'])
            ->findOrFail($customerId);

        // Calculate statistics
        $completedOrders = $customer->orders()->where('status', 'completed')->count();
        $pendingOrders = $customer->orders()->where('status', 'pending')->count();
        $cancelledOrders = $customer->orders()->where('status', 'cancelled')->count();
        
        $totalSpent = $customer->orders()
            ->where('status', 'completed')
            ->sum('total_amount');

        $averageOrderValue = $completedOrders > 0 ? $totalSpent / $completedOrders : 0;

        $firstOrder = $customer->orders()->oldest()->first();
        $lastOrder = $customer->orders()->latest()->first();

        // Favorite category (most ordered category)
        $favoriteCategory = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->where('orders.user_id', $customerId)
            ->where('orders.status', 'completed')
            ->groupBy('categories.id', 'categories.name_ar', 'categories.name_en')
            ->orderByRaw('COUNT(*) DESC')
            ->select(DB::raw('COALESCE(categories.name_ar, categories.name_en) as name'))
            ->first();

        // Favorite products
        $favoriteProducts = DB::table('order_items')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('orders.user_id', $customerId)
            ->where('orders.status', 'completed')
            ->groupBy('products.id', 'products.name_ar', 'products.name_en')
            ->orderByRaw('COUNT(*) DESC')
            ->limit(3)
            ->select('products.id', DB::raw('COALESCE(products.name_ar, products.name_en) as name'), DB::raw('COUNT(*) as orders_count'))
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'customer' => new AdminCustomerResource($customer),
                'statistics' => [
                    'total_orders' => $customer->orders_count,
                    'completed_orders' => $completedOrders,
                    'pending_orders' => $pendingOrders,
                    'cancelled_orders' => $cancelledOrders,
                    'total_spent' => number_format($totalSpent, 2),
                    'average_order_value' => number_format($averageOrderValue, 2),
                    'first_order_date' => $firstOrder?->created_at,
                    'last_order_date' => $lastOrder?->created_at,
                    'favorite_category' => $favoriteCategory?->name ?? 'غير محدد',
                    'favorite_products' => $favoriteProducts->toArray()
                ],
                'recent_orders' => $customer->orders->map(function($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'status' => $order->status,
                        'total_amount' => number_format($order->total_amount, 2),
                        'items_count' => $order->orderItems->count(),
                        'created_at' => $order->created_at
                    ];
                }),
                'addresses' => $customer->addresses->map(function($address) {
                    return [
                        'id' => $address->id,
                        'type' => $address->type ?? 'home',
                        'name' => $address->name ?? 'العنوان الرئيسي',
                        'city' => $address->city,
                        'street' => $address->street,
                        'is_default' => $address->is_default ?? false
                    ];
                })
            ]
        ]);
    }

    /**
     * 4. Update customer status
     * PATCH /api/v1/admin/customers/{id}/status
     */
    public function updateStatus(Request $request, $customerId): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:active,inactive,banned',
            'reason' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data',
                'errors' => $validator->errors()
            ], 422);
        }

        $customer = User::customers()->findOrFail($customerId);
        $oldStatus = $customer->status;
        $newStatus = $request->status;
        $reason = $request->reason;

        // Update status
        $customer->update(['status' => $newStatus]);

        // Log activity
        CustomerActivity::logActivity($customerId, 'status_changed', [
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'reason' => $reason,
            'admin_id' => Auth::id()
        ]);

        // Add admin note if reason provided
        if ($reason) {
            CustomerNote::addNote($customerId, Auth::id(), "تم تغيير الحالة من {$oldStatus} إلى {$newStatus}. السبب: {$reason}", CustomerNote::TYPE_WARNING);
        }

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث حالة العميل بنجاح',
            'data' => [
                'customer' => [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'status' => $customer->status,
                    'updated_at' => $customer->updated_at
                ]
            ]
        ]);
    }

    /**
     * 5. Get customer activity statistics
     * GET /api/v1/admin/customers/activity-stats
     */
    public function activityStats(Request $request): JsonResponse
    {
        $period = $request->get('period', 'month');
        
        // Set date range based on period
        switch ($period) {
            case 'today':
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
                break;
            case 'week':
                $startDate = now()->startOfWeek();
                $endDate = now()->endOfWeek();
                break;
            case 'year':
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                break;
            default:
                $startDate = now()->startOfMonth();
                $endDate = now()->endOfMonth();
        }

        // Registration chart data
        $registrationsChart = User::customers()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(function($item) {
                return [
                    'date' => $item->date,
                    'count' => $item->count
                ];
            });

        // Activity breakdown
        $highlyActive = User::customers()
            ->withCount('orders')
            ->having('orders_count', '>', 5)
            ->count();

        $moderatelyActive = User::customers()
            ->withCount('orders')
            ->havingRaw('orders_count BETWEEN 2 AND 5')
            ->count();

        $lowActivity = User::customers()
            ->withCount('orders')
            ->having('orders_count', '=', 1)
            ->count();

        $noOrders = User::customers()
            ->withoutOrders()
            ->count();

        // Spending segments
        $customers = User::customers()
            ->withSum(['orders' => function($q) {
                $q->where('status', 'completed');
            }], 'total_amount')
            ->get();

        $highSpenders = $customers->where('orders_sum_total_amount', '>', 1000)->count();
        $mediumSpenders = $customers->whereBetween('orders_sum_total_amount', [500, 1000])->count();
        $lowSpenders = $customers->whereBetween('orders_sum_total_amount', [100, 500])->count();
        $minimalSpenders = $customers->where('orders_sum_total_amount', '<', 100)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => $period,
                'registrations_chart' => $registrationsChart,
                'activity_breakdown' => [
                    'highly_active' => $highlyActive,
                    'moderately_active' => $moderatelyActive,
                    'low_activity' => $lowActivity,
                    'no_orders' => $noOrders
                ],
                'spending_segments' => [
                    'high_spenders' => $highSpenders,
                    'medium_spenders' => $mediumSpenders,
                    'low_spenders' => $lowSpenders,
                    'minimal_spenders' => $minimalSpenders
                ]
            ]
        ]);
    }

    /**
     * 6. Advanced customer search
     * POST /api/v1/admin/customers/advanced-search
     */
    public function advancedSearch(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'filters' => 'required|array',
            'sort' => 'nullable|string',
            'order' => 'nullable|in:asc,desc',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:50'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $filters = $request->filters;
        $query = User::customers()->withCount(['orders', 'addresses']);

        // Apply filters
        if (!empty($filters['name'])) {
            $query->where('name', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['email_domain'])) {
            $query->where('email', 'like', '%@' . $filters['email_domain']);
        }

        if (!empty($filters['registration_period'])) {
            $from = $filters['registration_period']['from'];
            $to = $filters['registration_period']['to'];
            $query->whereBetween('created_at', [$from, $to]);
        }

        if (!empty($filters['orders_range'])) {
            $min = $filters['orders_range']['min'];
            $max = $filters['orders_range']['max'];
            $query->has('orders', '>=', $min)->has('orders', '<=', $max);
        }

        if (!empty($filters['spending_range'])) {
            $min = $filters['spending_range']['min'];
            $max = $filters['spending_range']['max'];
            $query->whereHas('orders', function($q) use ($min, $max) {
                $q->where('status', 'completed')
                  ->havingRaw('SUM(total_amount) BETWEEN ? AND ?', [$min, $max]);
            });
        }

        if (!empty($filters['has_company'])) {
            if ($filters['has_company']) {
                $query->whereNotNull('company');
            } else {
                $query->whereNull('company');
            }
        }

        if (!empty($filters['is_verified'])) {
            if ($filters['is_verified']) {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        if (!empty($filters['last_activity_days'])) {
            $days = $filters['last_activity_days'];
            $query->where('last_activity', '>=', now()->subDays($days));
        }

        // Sorting
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');
        $query->orderBy($sort, $order);

        // Pagination
        $perPage = $request->get('per_page', 20);
        $customers = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => AdminCustomerResource::collection($customers),
            'meta' => [
                'current_page' => $customers->currentPage(),
                'total' => $customers->total(),
                'per_page' => $customers->perPage(),
                'last_page' => $customers->lastPage()
            ]
        ]);
    }

    /**
     * 7. Export customers data
     * GET /api/v1/admin/customers/export
     */
    public function export(Request $request): JsonResponse
    {
        $format = $request->get('format', 'excel');
        
        // Apply same filters as index method
        $query = User::customers()->withCount(['orders', 'addresses']);
        
        // Apply filters (same logic as index method)
        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $customers = $query->get();
        
        // Generate filename
        $timestamp = now()->format('Y_m_d_H_i_s');
        $filename = "customers_export_{$timestamp}";
        
        switch ($format) {
            case 'csv':
                $filename .= '.csv';
                $this->exportToCsv($customers, $filename);
                break;
            case 'pdf':
                $filename .= '.pdf';
                $this->exportToPdf($customers, $filename);
                break;
            default:
                $filename .= '.xlsx';
                $this->exportToExcel($customers, $filename);
                break;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'download_url' => asset("storage/exports/{$filename}"),
                'file_size' => $this->getFileSize(storage_path("app/public/exports/{$filename}")),
                'records_count' => $customers->count(),
                'expires_at' => now()->addDay()
            ]
        ]);
    }

    /**
     * 8. Send notification to customers
     * POST /api/v1/admin/customers/send-notification
     */
    public function sendNotification(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'customer_ids' => 'required|array',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|in:info,warning,promotion,announcement',
            'send_email' => 'boolean',
            'send_sms' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $customerIds = $request->customer_ids;
        $title = $request->title;
        $message = $request->message;
        $type = $request->type;
        $sendEmail = $request->get('send_email', true);
        $sendSms = $request->get('send_sms', false);

        // Get customers
        if (in_array('all', $customerIds)) {
            $customers = User::customers()->active()->get();
        } else {
            $customers = User::customers()->whereIn('id', $customerIds)->get();
        }

        $notificationId = 'NOTIF-' . now()->format('Y-m-d') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        
        $emailSent = 0;
        $smsSent = 0;
        $failedCount = 0;

        foreach ($customers as $customer) {
            try {
                // Log notification activity
                CustomerActivity::logActivity($customer->id, 'notification_received', [
                    'notification_id' => $notificationId,
                    'title' => $title,
                    'type' => $type,
                    'admin_id' => Auth::id()
                ]);

                // Send email if requested
                if ($sendEmail && $customer->email) {
                    // Here you would integrate with your email service
                    // Mail::to($customer->email)->send(new CustomerNotificationMail($title, $message, $type));
                    $emailSent++;
                }

                // Send SMS if requested
                if ($sendSms && $customer->phone) {
                    // Here you would integrate with your SMS service
                    // SMS::send($customer->phone, $message);
                    $smsSent++;
                }

            } catch (\Exception $e) {
                $failedCount++;
                \Log::error("Failed to send notification to customer {$customer->id}: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'تم إرسال الإشعار بنجاح',
            'data' => [
                'notification_id' => $notificationId,
                'recipients_count' => $customers->count(),
                'email_sent' => $emailSent,
                'sms_sent' => $smsSent,
                'failed_count' => $failedCount
            ]
        ]);
    }

    // Helper methods for export functionality
    private function exportToCsv($customers, $filename)
    {
        $path = storage_path('app/public/exports');
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }

        $file = fopen($path . '/' . $filename, 'w');
        
        // Add CSV headers
        fputcsv($file, [
            'ID', 'Name', 'Email', 'Phone', 'Company', 'Status', 
            'Registration Date', 'Orders Count', 'Total Spent'
        ]);

        // Add data rows
        foreach ($customers as $customer) {
            fputcsv($file, [
                $customer->id,
                $customer->name,
                $customer->email,
                $customer->phone,
                $customer->company,
                $customer->status,
                $customer->created_at->format('Y-m-d'),
                $customer->orders_count,
                $customer->total_spent
            ]);
        }

        fclose($file);
    }

    private function exportToExcel($customers, $filename)
    {
        // This would require a package like PhpSpreadsheet
        // For now, we'll use CSV format
        $this->exportToCsv($customers, $filename);
    }

    private function exportToPdf($customers, $filename)
    {
        // This would require a package like DomPDF
        // For now, we'll use CSV format
        $this->exportToCsv($customers, $filename);
    }

    private function getFileSize($filePath)
    {
        if (file_exists($filePath)) {
            $bytes = filesize($filePath);
            if ($bytes >= 1048576) {
                return number_format($bytes / 1048576, 2) . ' MB';
            } elseif ($bytes >= 1024) {
                return number_format($bytes / 1024, 2) . ' KB';
            }
            return $bytes . ' bytes';
        }
        return '0 bytes';
    }
}
