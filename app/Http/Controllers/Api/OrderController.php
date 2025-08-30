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
     * Display a listing of orders.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $orders = $this->orderService->getOrders($request->user(), $request->all());
        
        return OrderResource::collection($orders);
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
    public function show(int $id): JsonResponse
    {
        $order = $this->orderService->getOrder($id);

        return response()->json([
            'order' => new OrderResource($order),
        ]);
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
    public function cancel(int $id): JsonResponse
    {
        $this->orderService->cancelOrder($id);

        return response()->json([
            'message' => 'Order cancelled successfully',
        ]);
    }
} 