<?php

namespace App\Services;

use App\Events\OrderPlaced;
use App\Events\OrderStatusUpdated;
use App\Models\Order;
use App\Models\User;
use App\Repositories\OrderRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private OrderRepository $orderRepository,
        private ProductService $productService
    ) {}

    /**
     * Get orders for user.
     */
    public function getOrders(User $user, array $filters = []): LengthAwarePaginator
    {
        return $this->orderRepository->getOrdersForUser($user->id, $filters);
    }

    /**
     * Create a new order.
     */
    public function createOrder(User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data) {
            // Calculate total price
            $totalPrice = $this->calculateTotalPrice($data['items']);

            // Create order
            $order = $this->orderRepository->create([
                'user_id' => $user->id,
                'status' => 'pending',
                'total_price' => $totalPrice,
                'shipping_address' => $data['shipping_address'],
                'payment_method' => $data['payment_method'],
            ]);

            // Create order items and update stock
            foreach ($data['items'] as $item) {
                $product = $this->productService->getProduct($item['product_id']);
                
                $orderItemData = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                ];
                
                $this->orderRepository->createOrderItem($order->id, $orderItemData);
                $this->productService->updateStock($item['product_id'], $item['quantity']);
            }

            // Fire order placed event
            event(new OrderPlaced($order));

            return $order;
        });
    }

    /**
     * Get order by ID.
     */
    public function getOrder(int $id): Order
    {
        return $this->orderRepository->find($id);
    }

    /**
     * Update order.
     */
    public function updateOrder(int $id, array $data): Order
    {
        return $this->orderRepository->update($id, $data);
    }

    /**
     * Update order status.
     */
    public function updateOrderStatus(int $id, string $status): Order
    {
        $order = $this->orderRepository->update($id, ['status' => $status]);

        // Fire order status updated event
        event(new OrderStatusUpdated($order, $status));

        return $order;
    }

    /**
     * Cancel order.
     */
    public function cancelOrder(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $order = $this->getOrder($id);

            if ($order->status === 'pending') {
                // Restore stock
                foreach ($order->orderItems as $item) {
                    $item->product->increaseStock($item->quantity);
                }

                $this->orderRepository->update($id, ['status' => 'cancelled']);
                return true;
            }

            return false;
        });
    }

    /**
     * Generate invoice for order.
     */
    public function generateInvoice(int $id): array
    {
        $order = $this->orderRepository->find($id);
        
        // Ensure relationships are loaded
        $order->load(['user', 'orderItems.product']);
        
        return [
            'order_id' => $order->id,
            'customer_name' => $order->user?->name ?? 'Unknown Customer',
            'customer_email' => $order->user?->email ?? 'No Email',
            'shipping_address' => $order->shipping_address,
            'items' => $order->orderItems->map(function ($item) {
                return [
                    'product_name' => $item->product?->name ?? 'Unknown Product',
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total' => $item->total_price,
                ];
            }),
            'total_price' => $order->total_price,
            'payment_method' => $order->payment_method,
            'status' => $order->status,
            'created_at' => $order->created_at,
        ];
    }

    /**
     * Calculate total price from items.
     */
    private function calculateTotalPrice(array $items): float
    {
        $total = 0;

        foreach ($items as $item) {
            $product = $this->productService->getProduct($item['product_id']);
            $total += $product->price * $item['quantity'];
        }

        return $total;
    }
} 