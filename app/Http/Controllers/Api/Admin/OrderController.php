<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Http\Resources\AdminOrderResource;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * Display a listing of orders with filters and pagination.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        try {
            $query = Order::with(['user', 'orderItems.product', 'statusHistories.user'])
                          ->select('orders.*');

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }

            if ($request->filled('payment_method')) {
                $query->where('payment_method', $request->payment_method);
            }

            if ($request->filled('customer_id')) {
                $query->where('user_id', $request->customer_id);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                   ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortBy, $sortDirection);

            // Pagination
            $perPage = $request->get('per_page', 15);
            $orders = $query->paginate($perPage);

            return AdminOrderResource::collection($orders)->additional([
                'meta' => [
                    'total' => $orders->total(),
                    'per_page' => $orders->perPage(),
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'stats' => $this->getOrdersStats(),
                    'filters_applied' => $request->only([
                        'status', 'payment_status', 'payment_method', 
                        'customer_id', 'search', 'date_from', 'date_to'
                    ])
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب الطلبات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order): JsonResponse
    {
        try {
            // Log for debugging
            \Log::info("Admin accessing order details", [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'user_id' => auth()->id()
            ]);

            // Load relationships with error handling
            $order->load([
                'user', 
                'orderItems.product.images', 
                'statusHistories.user',
            ]);

            // Load shipment only if relationship exists
            if (method_exists($order, 'shipment')) {
                $order->load('shipment');
            }

            // Verify data integrity
            if (!$order->user) {
                \Log::warning("Order {$order->id} has no user/customer");
            }

            if ($order->orderItems->isEmpty()) {
                \Log::warning("Order {$order->id} has no items");
            }

            return response()->json([
                'success' => true,
                'data' => new AdminOrderResource($order)
            ]);

        } catch (\Exception $e) {
            \Log::error("Error in AdminOrderController@show", [
                'order_id' => $order->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب بيانات الطلب',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Update the order status.
     */
    public function updateStatus(UpdateOrderStatusRequest $request, Order $order): JsonResponse
    {
        try {
            DB::beginTransaction();

            $previousStatus = $order->status;
            $newStatus = $request->status;

            // Update order
            $order->update([
                'status' => $newStatus,
                'tracking_number' => $request->tracking_number ?: $order->tracking_number,
                'estimated_delivery' => $request->estimated_delivery ?: $order->estimated_delivery,
                'notes' => $request->notes ? ($order->notes . "\n" . $request->notes) : $order->notes,
            ]);

            // Create status history record
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'status' => $newStatus,
                'previous_status' => $previousStatus,
                'notes' => $request->notes,
                'metadata' => [
                    'tracking_number' => $request->tracking_number,
                    'estimated_delivery' => $request->estimated_delivery,
                    'notify_customer' => $request->notify_customer ?? false,
                ]
            ]);

            // TODO: Send notification to customer if notify_customer is true
            if ($request->notify_customer) {
                // Implement notification logic here
            }

            DB::commit();

            $order->refresh();
            $order->load(['user', 'orderItems.product', 'statusHistories.user']);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة الطلب بنجاح',
                'data' => new AdminOrderResource($order)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تحديث حالة الطلب',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Add notes to an order.
     */
    public function addNotes(Request $request, Order $order): JsonResponse
    {
        try {
            $request->validate([
                'notes' => 'required|string|max:1000'
            ]);

            // Create status history record with same status but new notes
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'status' => $order->status,
                'previous_status' => $order->status,
                'notes' => $request->notes,
                'metadata' => ['type' => 'admin_note']
            ]);

            // Update order notes
            $existingNotes = $order->notes ? $order->notes . "\n" : '';
            $order->update([
                'notes' => $existingNotes . '[' . now()->format('Y-m-d H:i') . '] ' . $request->notes
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة الملاحظة بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في إضافة الملاحظة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get orders statistics for dashboard.
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->getOrdersStats();
            $revenueStats = $this->getRevenueStats();
            $recentOrders = Order::with(['user', 'orderItems'])
                                ->latest()
                                ->limit(5)
                                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'orders' => $stats,
                    'revenue' => $revenueStats,
                    'recent_orders' => AdminOrderResource::collection($recentOrders),
                    'generated_at' => now()->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب الإحصائيات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk operations on orders.
     */
    public function bulk(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'action' => 'required|string|in:update_status,delete,export,print',
                'order_ids' => 'required|array|min:1',
                'order_ids.*' => 'exists:orders,id',
                'status' => 'required_if:action,update_status|string|in:pending,confirmed,processing,shipped,delivered,cancelled,refunded',
                'notes' => 'nullable|string|max:500',
                'format' => 'required_if:action,export|string|in:excel,csv,pdf',
                'print_type' => 'nullable|string|in:summary,detailed,invoice'
            ]);

            // Handle export action (redirect to exportOrders method)
            if ($request->action === 'export') {
                return $this->exportOrders($request);
            }

            // Handle print action (redirect to printOrders method)
            if ($request->action === 'print') {
                return $this->printOrders($request);
            }

            // Handle other bulk actions
            $orders = Order::whereIn('id', $request->order_ids)->get();
            $results = [];

            DB::beginTransaction();

            foreach ($orders as $order) {
                switch ($request->action) {
                    case 'update_status':
                        $previousStatus = $order->status;
                        $order->update(['status' => $request->status]);
                        
                        // Create history record
                        OrderStatusHistory::create([
                            'order_id' => $order->id,
                            'user_id' => auth()->id(),
                            'status' => $request->status,
                            'previous_status' => $previousStatus,
                            'notes' => $request->notes,
                            'metadata' => ['type' => 'bulk_update']
                        ]);

                        $results[] = [
                            'id' => $order->id,
                            'order_number' => $order->order_number,
                            'status' => 'updated'
                        ];
                        break;

                    case 'delete':
                        $order->delete();
                        $results[] = [
                            'id' => $order->id,
                            'order_number' => $order->order_number,
                            'status' => 'deleted'
                        ];
                        break;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تنفيذ العملية المجمعة بنجاح',
                'data' => [
                    'processed_count' => count($results),
                    'results' => $results
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تنفيذ العملية المجمعة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get orders statistics.
     */
    private function getOrdersStats(): array
    {
        $today = now()->toDateString();
        $thisMonth = now()->startOfMonth();

        return [
            'total' => Order::count(),
            'today' => Order::whereDate('created_at', $today)->count(),
            'this_month' => Order::where('created_at', '>=', $thisMonth)->count(),
            'by_status' => Order::select('status', DB::raw('count(*) as count'))
                               ->groupBy('status')
                               ->pluck('count', 'status')
                               ->toArray(),
            'by_payment_status' => Order::select('payment_status', DB::raw('count(*) as count'))
                                       ->groupBy('payment_status')
                                       ->pluck('count', 'payment_status')
                                       ->toArray(),
        ];
    }

    /**
     * Get revenue statistics.
     */
    private function getRevenueStats(): array
    {
        $today = now()->toDateString();
        $thisMonth = now()->startOfMonth();

        return [
            'total' => Order::where('payment_status', 'paid')->sum('total_amount'),
            'today' => Order::where('payment_status', 'paid')
                           ->whereDate('created_at', $today)
                           ->sum('total_amount'),
            'this_month' => Order::where('payment_status', 'paid')
                                ->where('created_at', '>=', $thisMonth)
                                ->sum('total_amount'),
            'pending' => Order::where('payment_status', 'pending')->sum('total_amount'),
        ];
    }

    /**
     * Update shipping information for an order.
     */
    public function updateShipping(Request $request, Order $order): JsonResponse
    {
        try {
            $request->validate([
                'tracking_number' => 'required|string|max:255',
                'shipping_company' => 'required|string|max:255',
                'estimated_delivery' => 'nullable|date|after:today',
                'notify_customer' => 'nullable|boolean'
            ]);

            DB::beginTransaction();

            $order->update([
                'tracking_number' => $request->tracking_number,
                'shipping_company' => $request->shipping_company,
                'estimated_delivery' => $request->estimated_delivery,
            ]);

            // Create status history record
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'status' => $order->status,
                'previous_status' => $order->status,
                'notes' => 'تم تحديث معلومات الشحن: ' . $request->tracking_number,
                'metadata' => [
                    'type' => 'shipping_update',
                    'tracking_number' => $request->tracking_number,
                    'shipping_company' => $request->shipping_company,
                    'estimated_delivery' => $request->estimated_delivery,
                ]
            ]);

            // TODO: Send notification to customer
            if ($request->notify_customer) {
                // Implement notification logic
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث معلومات الشحن بنجاح',
                'message_ar' => 'تم تحديث معلومات الشحن بنجاح',
                'data' => [
                    'order_id' => $order->id,
                    'tracking_number' => $order->tracking_number,
                    'shipping_company' => $order->shipping_company,
                    'estimated_delivery' => $order->estimated_delivery,
                    'tracking_url' => 'https://' . $order->shipping_company . '.com/track/' . $order->tracking_number
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تحديث معلومات الشحن',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update payment status for an order.
     */
    public function updatePayment(Request $request, Order $order): JsonResponse
    {
        try {
            $request->validate([
                'payment_status' => 'required|string|in:pending,paid,failed,refunded',
                'payment_reference' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:500',
                'notify_customer' => 'nullable|boolean'
            ]);

            DB::beginTransaction();

            $previousPaymentStatus = $order->payment_status;
            
            $order->update([
                'payment_status' => $request->payment_status,
                'payment_reference' => $request->payment_reference ?? $order->payment_reference,
                'paid_at' => $request->payment_status === 'paid' ? now() : $order->paid_at,
            ]);

            // Create status history record
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'status' => $order->status,
                'previous_status' => $order->status,
                'notes' => $request->notes ?? 'تم تحديث حالة الدفع من ' . $previousPaymentStatus . ' إلى ' . $request->payment_status,
                'metadata' => [
                    'type' => 'payment_update',
                    'previous_payment_status' => $previousPaymentStatus,
                    'new_payment_status' => $request->payment_status,
                    'payment_reference' => $request->payment_reference,
                ]
            ]);

            // TODO: Send notification to customer
            if ($request->notify_customer) {
                // Implement notification logic
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث حالة الدفع بنجاح',
                'message_ar' => 'تم تحديث حالة الدفع بنجاح',
                'data' => [
                    'order_id' => $order->id,
                    'payment_status' => $order->payment_status,
                    'payment_status_ar' => $order->payment_status_ar,
                    'payment_reference' => $order->payment_reference,
                    'paid_at' => $order->paid_at?->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'خطأ في تحديث حالة الدفع',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel an order.
     */
    public function cancelOrder(Request $request, Order $order): JsonResponse
    {
        try {
            $request->validate([
                'reason' => 'required|string|max:500',
                'refund' => 'nullable|boolean',
                'notify_customer' => 'nullable|boolean'
            ]);

            if (!$order->canBeCancelled()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا يمكن إلغاء هذا الطلب',
                    'message_ar' => 'لا يمكن إلغاء هذا الطلب (تم التسليم أو الإلغاء مسبقاً)'
                ], 422);
            }

            DB::beginTransaction();

            $previousStatus = $order->status;
            
            $order->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $request->reason,
            ]);

            // Create status history record
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'status' => 'cancelled',
                'previous_status' => $previousStatus,
                'notes' => 'تم إلغاء الطلب: ' . $request->reason,
                'metadata' => [
                    'type' => 'cancellation',
                    'reason' => $request->reason,
                    'refund_initiated' => $request->refund ?? false,
                    'cancelled_by_admin' => true,
                ]
            ]);

            // TODO: Process refund if requested
            $refundInitiated = false;
            if ($request->refund && $order->payment_status === 'paid') {
                // Implement refund logic
                $refundInitiated = true;
                $order->update(['payment_status' => 'refunded']);
            }

            // TODO: Send notification to customer
            if ($request->notify_customer) {
                // Implement notification logic
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إلغاء الطلب بنجاح',
                'message_ar' => 'تم إلغاء الطلب بنجاح',
                'data' => [
                    'order_id' => $order->id,
                    'status' => 'cancelled',
                    'cancelled_at' => $order->cancelled_at->format('Y-m-d H:i:s'),
                    'cancelled_by' => [
                        'id' => auth()->id(),
                        'name' => auth()->user()->name ?? 'Admin'
                    ],
                    'refund_initiated' => $refundInitiated
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'خطأ في إلغاء الطلب',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refund an order.
     */
    public function refundOrder(Request $request, Order $order): JsonResponse
    {
        try {
            $request->validate([
                'amount' => 'nullable|numeric|min:0|max:' . $order->total_amount,
                'reason' => 'required|string|max:500',
                'notify_customer' => 'nullable|boolean'
            ]);

            if ($order->payment_status === 'refunded') {
                return response()->json([
                    'success' => false,
                    'message' => 'تم استرداد هذا الطلب مسبقاً',
                    'message_ar' => 'تم استرداد هذا الطلب مسبقاً'
                ], 422);
            }

            DB::beginTransaction();

            $refundAmount = $request->amount ?? $order->total_amount;
            
            $order->update([
                'payment_status' => 'refunded',
                'refunded_amount' => $refundAmount,
                'refunded_at' => now(),
            ]);

            // Create status history record
            OrderStatusHistory::create([
                'order_id' => $order->id,
                'user_id' => auth()->id(),
                'status' => $order->status,
                'previous_status' => $order->status,
                'notes' => 'تم استرداد مبلغ ' . $refundAmount . ' جنيه: ' . $request->reason,
                'metadata' => [
                    'type' => 'refund',
                    'refund_amount' => $refundAmount,
                    'reason' => $request->reason,
                    'is_full_refund' => $refundAmount == $order->total_amount,
                ]
            ]);

            // TODO: Process actual refund through payment gateway
            // TODO: Send notification to customer
            if ($request->notify_customer) {
                // Implement notification logic
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم استرداد المبلغ بنجاح',
                'message_ar' => 'تم استرداد المبلغ بنجاح',
                'data' => [
                    'refund_id' => 'REF-' . $order->id . '-' . now()->format('YmdHis'),
                    'order_id' => $order->id,
                    'amount' => $refundAmount,
                    'currency' => 'EGP',
                    'status' => 'completed',
                    'processed_at' => now()->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'خطأ في استرداد المبلغ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export orders to Excel/CSV/PDF.
     * Supports both filtering and selected order IDs.
     */
    public function exportOrders(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'format' => 'required|string|in:excel,csv,pdf',
                'order_ids' => 'nullable|array',
                'order_ids.*' => 'exists:orders,id',
                'filters' => 'nullable|array',
                'columns' => 'nullable|array'
            ]);

            // Build query with eager loading
            $query = Order::with(['user', 'orderItems.product']);

            // Priority 1: If specific order IDs are provided (selected orders)
            if ($request->filled('order_ids')) {
                $query->whereIn('id', $request->order_ids);
            }
            // Priority 2: Apply filters
            elseif ($request->has('filters')) {
                $filters = $request->filters;
                
                if (isset($filters['status'])) {
                    $query->where('status', $filters['status']);
                }
                if (isset($filters['payment_status'])) {
                    $query->where('payment_status', $filters['payment_status']);
                }
                if (isset($filters['date_from'])) {
                    $query->whereDate('created_at', '>=', $filters['date_from']);
                }
                if (isset($filters['date_to'])) {
                    $query->whereDate('created_at', '<=', $filters['date_to']);
                }
            }

            $orders = $query->orderBy('created_at', 'desc')->get();
            $totalRecords = $orders->count();

            if ($totalRecords === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا توجد طلبات للتصدير',
                    'message_ar' => 'لا توجد طلبات للتصدير'
                ], 404);
            }

            // Generate filename
            $formatExtension = [
                'excel' => 'xlsx',
                'csv' => 'csv',
                'pdf' => 'pdf'
            ];
            
            $filename = 'orders-export-' . now()->format('Y-m-d-His') . '.' . $formatExtension[$request->format];

            // Prepare export data
            $exportData = $orders->map(function ($order) use ($request) {
                // Get shipping address from JSON column
                $shippingAddress = $order->shipping_address;
                
                $data = [
                    'order_number' => $order->order_number ?? 'N/A',
                    'customer_name' => $order->user?->name ?? 'Guest',
                    'customer_email' => $order->user?->email ?? 'N/A',
                    'customer_phone' => $order->user?->phone ?? 'N/A',
                    'status' => $order->status ?? 'pending',
                    'status_ar' => $this->translateStatus($order->status ?? 'pending'),
                    'payment_status' => $order->payment_status ?? 'pending',
                    'payment_status_ar' => $this->translatePaymentStatus($order->payment_status ?? 'pending'),
                    'payment_method' => $order->payment_method ?? 'cash_on_delivery',
                    'payment_method_ar' => $this->translatePaymentMethod($order->payment_method ?? 'cash_on_delivery'),
                    'total_amount' => $order->total_amount ?? 0,
                    'items_count' => $order->orderItems ? $order->orderItems->count() : 0,
                    'shipping_city' => ($shippingAddress && is_array($shippingAddress)) ? ($shippingAddress['city'] ?? 'N/A') : 'N/A',
                    'shipping_governorate' => ($shippingAddress && is_array($shippingAddress)) ? ($shippingAddress['governorate'] ?? 'N/A') : 'N/A',
                    'created_at' => $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                ];

                return $data;
            })->toArray();

            // Create exports directory if not exists
            $directory = 'exports';
            $storagePath = storage_path('app/public/' . $directory);
            
            if (!file_exists($storagePath)) {
                mkdir($storagePath, 0755, true);
            }

            $filePath = $storagePath . '/' . $filename;

            // Generate CSV file (works for all formats for now)
            $this->generateCsvFile($exportData, $filePath);

            // Generate public URL
            $downloadUrl = url('storage/' . $directory . '/' . $filename);

            return response()->json([
                'success' => true,
                'message' => 'تم التصدير بنجاح - تم إنشاء ' . count($exportData) . ' صفوف',
                'message_ar' => 'تم التصدير بنجاح',
                'data' => [
                    'download_url' => $downloadUrl,
                    'filename' => $filename,
                    'format' => $request->format,
                    'total_records' => $totalRecords,
                    'export_data' => $exportData, // For frontend to generate export
                    'file_exists' => file_exists($filePath),
                    'file_size' => file_exists($filePath) ? filesize($filePath) : 0,
                    'data_count' => count($exportData),
                    'expires_at' => now()->addHours(6)->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في التصدير',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Translate order status to Arabic.
     */
    private function translateStatus(string $status): string
    {
        $translations = [
            'pending' => 'في الانتظار',
            'confirmed' => 'تم التأكيد',
            'processing' => 'قيد المعالجة',
            'shipped' => 'تم الشحن',
            'delivered' => 'تم التسليم',
            'cancelled' => 'ملغي',
            'refunded' => 'مسترد'
        ];

        return $translations[$status] ?? $status;
    }

    /**
     * Translate payment status to Arabic.
     */
    private function translatePaymentStatus(string $status): string
    {
        $translations = [
            'pending' => 'في الانتظار',
            'paid' => 'مدفوع',
            'failed' => 'فشل',
            'refunded' => 'مسترد'
        ];

        return $translations[$status] ?? $status;
    }

    /**
     * Translate payment method to Arabic.
     */
    private function translatePaymentMethod(string $method): string
    {
        $translations = [
            'cash_on_delivery' => 'الدفع عند الاستلام',
            'credit_card' => 'بطاقة ائتمان',
            'debit_card' => 'بطاقة خصم',
            'paypal' => 'باي بال',
            'bank_transfer' => 'تحويل بنكي'
        ];

        return $translations[$method] ?? $method;
    }

    /**
     * Generate CSV file from export data.
     */
    private function generateCsvFile($data, $filePath): void
    {
        $fp = fopen($filePath, 'w');
        
        // UTF-8 BOM for proper Excel encoding
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Headers (Arabic and English)
        $headers = [
            'رقم الطلب',
            'اسم العميل',
            'البريد الإلكتروني',
            'رقم الهاتف',
            'الحالة',
            'حالة الطلب (عربي)',
            'حالة الدفع',
            'حالة الدفع (عربي)',
            'طريقة الدفع',
            'طريقة الدفع (عربي)',
            'المبلغ الإجمالي',
            'عدد المنتجات',
            'المدينة',
            'المحافظة',
            'تاريخ الإنشاء'
        ];
        
        fputcsv($fp, $headers);
        
        // Data rows
        foreach ($data as $row) {
            fputcsv($fp, [
                $row['order_number'] ?? '',
                $row['customer_name'] ?? '',
                $row['customer_email'] ?? '',
                $row['customer_phone'] ?? '',
                $row['status'] ?? '',
                $row['status_ar'] ?? '',
                $row['payment_status'] ?? '',
                $row['payment_status_ar'] ?? '',
                $row['payment_method'] ?? '',
                $row['payment_method_ar'] ?? '',
                $row['total_amount'] ?? '',
                $row['items_count'] ?? '',
                $row['shipping_city'] ?? '',
                $row['shipping_governorate'] ?? '',
                $row['created_at'] ?? ''
            ]);
        }
        
        fclose($fp);
    }

    /**
     * Generate printable view for selected orders.
     */
    public function printOrders(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'order_ids' => 'required|array|min:1',
                'order_ids.*' => 'exists:orders,id',
                'print_type' => 'nullable|string|in:summary,detailed,invoice',
            ]);

            // Get orders with all relationships
            $orders = Order::with([
                'user', 
                'orderItems.product', 
                'statusHistories.user'
            ])
            ->whereIn('id', $request->order_ids)
            ->orderBy('created_at', 'desc')
            ->get();

            if ($orders->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لا توجد طلبات للطباعة',
                    'message_ar' => 'لا توجد طلبات للطباعة'
                ], 404);
            }

            $printType = $request->get('print_type', 'summary');

            // Prepare print data based on type
            $printData = $orders->map(function ($order) use ($printType) {
                // Get status translations safely
                $statusAr = $this->translateStatus($order->status ?? 'pending');
                $paymentStatusAr = $this->translatePaymentStatus($order->payment_status ?? 'pending');
                $paymentMethodAr = $this->translatePaymentMethod($order->payment_method ?? 'cash_on_delivery');

                $data = [
                    'order_number' => $order->order_number ?? 'N/A',
                    'customer' => [
                        'name' => $order->user?->name ?? 'Guest',
                        'email' => $order->user?->email ?? 'N/A',
                        'phone' => $order->user?->phone ?? 'N/A',
                    ],
                    'status' => $order->status ?? 'pending',
                    'status_ar' => $statusAr,
                    'payment_status' => $order->payment_status ?? 'pending',
                    'payment_status_ar' => $paymentStatusAr,
                    'payment_method' => $order->payment_method ?? 'cash_on_delivery',
                    'payment_method_ar' => $paymentMethodAr,
                    'amounts' => [
                        'subtotal' => (float) ($order->subtotal ?? 0),
                        'shipping' => (float) ($order->shipping_amount ?? 0),
                        'tax' => (float) ($order->tax_amount ?? 0),
                        'discount' => (float) ($order->discount_amount ?? 0),
                        'total' => (float) ($order->total_amount ?? 0),
                        'currency' => $order->currency ?? 'EGP'
                    ],
                    'created_at' => $order->created_at ? $order->created_at->format('Y-m-d H:i:s') : now()->format('Y-m-d H:i:s'),
                ];

                // Add detailed info if needed
                if ($printType === 'detailed' || $printType === 'invoice') {
                    $data['items'] = $order->orderItems ? $order->orderItems->map(function ($item) {
                        return [
                            'product_name' => $item->product?->name_ar ?? 'N/A',
                            'product_name_en' => $item->product?->name_en ?? 'N/A',
                            'quantity' => $item->quantity ?? 0,
                            'unit_price' => (float) ($item->price ?? 0),
                            'total_price' => (float) (($item->quantity ?? 0) * ($item->price ?? 0)),
                        ];
                    }) : [];

                    // Get shipping address from JSON column
                    $shippingAddress = $order->shipping_address;
                    if (is_string($shippingAddress)) {
                        $shippingAddress = json_decode($shippingAddress, true);
                    }

                    $data['shipping_address'] = [
                        'name' => $shippingAddress['name'] ?? 'N/A',
                        'phone' => $shippingAddress['phone'] ?? 'N/A',
                        'governorate' => $shippingAddress['governorate'] ?? 'N/A',
                        'city' => $shippingAddress['city'] ?? 'N/A',
                        'district' => $shippingAddress['district'] ?? 'N/A',
                        'street' => $shippingAddress['street'] ?? 'N/A',
                        'postal_code' => $shippingAddress['postal_code'] ?? 'N/A',
                    ];
                }

                return $data;
            });

            return response()->json([
                'success' => true,
                'message' => 'تم تجهيز البيانات للطباعة',
                'data' => [
                    'print_type' => $printType,
                    'total_orders' => $orders->count(),
                    'orders' => $printData,
                    'generated_at' => now()->format('Y-m-d H:i:s'),
                    'company_info' => [
                        'name' => 'BuildTools',
                        'name_ar' => 'بيلد تولز',
                        'phone' => '01234567890',
                        'email' => 'info@buildtools.com',
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            // Enhanced error logging
            \Log::error('Print orders error', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
                'trace' => $e->getTraceAsString(),
                'order_ids' => $request->order_ids ?? [],
                'print_type' => $request->print_type ?? 'N/A'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'خطأ في تجهيز الطباعة',
                'error' => config('app.debug') ? $e->getMessage() : null,
                'line' => config('app.debug') ? $e->getLine() : null
            ], 500);
        }
    }
}
