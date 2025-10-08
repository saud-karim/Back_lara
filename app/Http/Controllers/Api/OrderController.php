<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Order\CreateOrderRequest;
use App\Http\Requests\Order\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    /**
     * Display a listing of user orders.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $status = $request->get('status');
            
            // Get user's orders only
            $query = \App\Models\Order::where('user_id', auth()->id())
                ->with('orderItems') // To calculate items_count
                ->orderBy('created_at', 'desc');
            
            // Apply status filter if provided
            if ($status) {
                $query->where('status', $status);
            }
            
            // Paginate
            $orders = $query->paginate($perPage);
            
            // Transform data
            $data = $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'user_id' => $order->user_id,
                    
                    'status' => $order->status,
                    'status_ar' => $this->translateStatus($order->status),
                    
                    'payment_method' => $order->payment_method,
                    'payment_method_ar' => $this->translatePaymentMethod($order->payment_method),
                    'payment_status' => $order->payment_status ?? 'pending',
                    'payment_status_ar' => $this->translatePaymentStatus($order->payment_status ?? 'pending'),
                    
                    'subtotal' => (float) ($order->subtotal ?? 0),
                    'shipping_cost' => (float) ($order->shipping_amount ?? 0),
                    'tax_amount' => (float) ($order->tax_amount ?? 0),
                    'discount_amount' => (float) ($order->discount_amount ?? 0),
                    'total_amount' => (float) ($order->total_amount ?? 0),
                    'currency' => $order->currency ?? 'EGP',
                    
                    'items_count' => $order->orderItems->sum('quantity'),
                    
                    'shipping_address' => $order->shipping_address, // Already JSON
                    
                    'estimated_delivery_date' => $order->estimated_delivery ? $order->estimated_delivery->format('Y-m-d') : null,
                    
                    'can_be_cancelled' => $this->canBeCancelled($order),
                    
                    'created_at' => $order->created_at->toIso8601String(),
                    'updated_at' => $order->updated_at->toIso8601String(),
                ];
            });
            
            return response()->json([
                'success' => true,
                'message' => 'Orders retrieved successfully',
                'data' => $data,
                'meta' => [
                    'current_page' => $orders->currentPage(),
                    'last_page' => $orders->lastPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                    'from' => $orders->firstItem() ?? 0,
                    'to' => $orders->lastItem() ?? 0,
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Get user orders error', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve orders',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Store a newly created order.
     */
    public function store(CreateOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder($request->user(), $request->validated());

        return response()->json([
            'message' => 'Order created successfully',
            'order' => new OrderResource($order),
        ], 201);
    }

    /**
     * Display the specified order.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        try {
            // Find order with relationships
            $order = \App\Models\Order::with([
                'orderItems.product'
            ])->find($id);
            
            // Security check: user must own this order
            if (!$order || $order->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }
            
            // Transform order items
            $items = $order->orderItems->map(function($item) {
                $product = $item->product;
                
                // Get product image
                $productImage = null;
                if ($product && $product->images) {
                    $images = is_string($product->images) 
                        ? json_decode($product->images, true) 
                        : $product->images;
                    $productImage = is_array($images) && !empty($images) ? $images[0] : null;
                }
                
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $product ? ($product->name_ar ?? $product->name_en ?? 'Unknown Product') : 'Unknown Product',
                    'product_image' => $productImage,
                    'variant_id' => $item->variant_id,
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'subtotal' => (float) $item->subtotal
                ];
            });
            
            // Prepare response
            $data = [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'user_id' => $order->user_id,
                
                'status' => $order->status,
                'status_ar' => $this->translateStatus($order->status),
                
                'payment_method' => $order->payment_method,
                'payment_method_ar' => $this->translatePaymentMethod($order->payment_method),
                'payment_status' => $order->payment_status ?? 'pending',
                'payment_status_ar' => $this->translatePaymentStatus($order->payment_status ?? 'pending'),
                
                'subtotal' => (float) ($order->subtotal ?? 0),
                'shipping_cost' => (float) ($order->shipping_amount ?? 0),
                'tax_amount' => (float) ($order->tax_amount ?? 0),
                'discount_amount' => (float) ($order->discount_amount ?? 0),
                'total_amount' => (float) ($order->total_amount ?? 0),
                'currency' => $order->currency ?? 'EGP',
                
                'shipping_address' => $order->shipping_address,
                
                'items' => $items,
                
                'notes' => $order->notes,
                'estimated_delivery_date' => $order->estimated_delivery ? $order->estimated_delivery->format('Y-m-d') : null,
                
                'can_be_cancelled' => $this->canBeCancelled($order),
                
                'created_at' => $order->created_at->toIso8601String(),
                'updated_at' => $order->updated_at->toIso8601String()
            ];

        return response()->json([
                'success' => true,
                'message' => 'Order retrieved successfully',
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Get order details error', [
                'error' => $e->getMessage(),
                'order_id' => $id,
                'user_id' => auth()->id(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve order',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Update the specified order.
     */
    public function update(UpdateOrderRequest $request, int $id): JsonResponse
    {
        $order = $this->orderService->updateOrder($id, $request->validated());

        return response()->json([
            'message' => 'Order updated successfully',
            'order' => new OrderResource($order),
        ]);
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $status = $request->validate(['status' => 'required|in:pending,processing,shipped,delivered'])['status'];
        $order = $this->orderService->updateOrderStatus($id, $status);

        return response()->json([
            'message' => 'Order status updated successfully',
            'order' => new OrderResource($order),
        ]);
    }

    /**
     * Generate invoice for order.
     */
    public function generateInvoice(int $id): JsonResponse
    {
        $invoice = $this->orderService->generateInvoice($id);

        return response()->json([
            'message' => 'Invoice generated successfully',
            'invoice' => $invoice,
        ]);
    }

    /**
     * Cancel order.
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        try {
            // Find order
            $order = \App\Models\Order::find($id);
            
            // Security check: user must own this order
            if (!$order || $order->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found'
                ], 404);
            }
            
            // Business rule check: can be cancelled?
            if (!$this->canBeCancelled($order)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot cancel order',
                    'errors' => [
                        'status' => $this->getCancelErrorMessage($order)
                    ]
                ], 400);
            }
            
            // Validate reason (optional)
            $request->validate([
                'reason' => 'nullable|string|max:500'
            ]);
            
            // Update order status
            $order->status = 'cancelled';
            $order->cancelled_at = now();
            $order->cancellation_reason = $request->reason;
            $order->save();
            
            // Restore product stock
            foreach ($order->orderItems as $item) {
                if ($item->variant_id) {
                    $variant = $item->variant;
                    if ($variant) {
                        $variant->stock += $item->quantity;
                        $variant->save();
                    }
                } else {
                    $product = $item->product;
                    if ($product) {
                        $product->stock += $item->quantity;
                        $product->save();
                    }
                }
            }
            
            // Log cancellation
            \Log::info('Order cancelled', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'user_id' => auth()->id(),
                'reason' => $request->reason
            ]);
            
            // Return response
        return response()->json([
                'success' => true,
            'message' => 'Order cancelled successfully',
                'data' => [
                    'id' => $order->id,
                    'order_number' => $order->order_number,
                    'status' => $order->status,
                    'status_ar' => $this->translateStatus($order->status),
                    'updated_at' => $order->updated_at->toIso8601String()
                ]
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Cancel order error', [
                'error' => $e->getMessage(),
                'order_id' => $id,
                'user_id' => auth()->id(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel order',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
    
    /**
     * Check if order can be cancelled.
     */
    private function canBeCancelled($order): bool
    {
        // Already cancelled
        if ($order->status === 'cancelled') {
            return false;
        }
        
        // Cannot cancel shipped/delivered orders
        if (in_array($order->status, ['shipped', 'delivered'])) {
            return false;
        }
        
        // Cannot cancel if already paid
        if (($order->payment_status ?? 'pending') === 'paid') {
            return false;
        }
        
        // Can cancel pending/confirmed/processing orders
        return in_array($order->status, ['pending', 'confirmed', 'processing']);
    }
    
    /**
     * Get cancellation error message.
     */
    private function getCancelErrorMessage($order): string
    {
        if ($order->status === 'cancelled') {
            return 'Order is already cancelled';
        }
        
        if ($order->status === 'shipped') {
            return 'Order is already shipped';
        }
        
        if ($order->status === 'delivered') {
            return 'Order is already delivered';
        }
        
        if (($order->payment_status ?? 'pending') === 'paid') {
            return 'Cannot cancel paid orders. Please contact support for refund.';
        }
        
        return 'This order cannot be cancelled';
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
            'refunded' => 'مسترد',
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
            'credit_card' => 'بطاقة ائتمانية',
            'debit_card' => 'بطاقة خصم',
            'card' => 'بطاقة ائتمانية',
            'paypal' => 'باي بال',
            'bank_transfer' => 'تحويل بنكي',
            'wallet' => 'محفظة إلكترونية',
            'installment' => 'تقسيط',
        ];
        
        return $translations[$method] ?? $method;
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
            'refunded' => 'مسترد',
        ];
        
        return $translations[$status] ?? $status;
    }
} 