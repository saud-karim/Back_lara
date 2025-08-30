<?php

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository
{
    /**
     * Create a new order.
     */
    public function create(array $data): Order
    {
        return Order::create($data);
    }

    /**
     * Find order by ID.
     */
    public function find(int $id): Order
    {
        return Order::with(['user', 'orderItems.product', 'shipment'])->findOrFail($id);
    }

    /**
     * Update order.
     */
    public function update(int $id, array $data): Order
    {
        $order = $this->find($id);
        $order->update($data);
        return $order;
    }

    /**
     * Delete order.
     */
    public function delete(int $id): bool
    {
        $order = $this->find($id);
        return $order->delete();
    }

    /**
     * Get orders for user with pagination.
     */
    public function getOrdersForUser(int $userId, array $filters = []): LengthAwarePaginator
    {
        $query = Order::with(['orderItems.product', 'shipment'])
            ->where('user_id', $userId);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Get all orders with pagination.
     */
    public function getAll(array $filters = []): LengthAwarePaginator
    {
        $query = Order::with(['user', 'orderItems.product', 'shipment']);

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['payment_method'])) {
            $query->where('payment_method', $filters['payment_method']);
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Create order item.
     */
    public function createOrderItem(int $orderId, array $itemData): OrderItem
    {
        return OrderItem::create([
            'order_id' => $orderId,
            'product_id' => $itemData['product_id'],
            'quantity' => $itemData['quantity'],
            'unit_price' => $itemData['unit_price'] ?? 0,
        ]);
    }

    /**
     * Get orders by status.
     */
    public function getByStatus(string $status): Collection
    {
        return Order::with(['user', 'orderItems.product'])
            ->where('status', $status)
            ->get();
    }

    /**
     * Get pending orders.
     */
    public function getPendingOrders(): Collection
    {
        return $this->getByStatus('pending');
    }

    /**
     * Get processing orders.
     */
    public function getProcessingOrders(): Collection
    {
        return $this->getByStatus('processing');
    }

    /**
     * Get shipped orders.
     */
    public function getShippedOrders(): Collection
    {
        return $this->getByStatus('shipped');
    }

    /**
     * Get delivered orders.
     */
    public function getDeliveredOrders(): Collection
    {
        return $this->getByStatus('delivered');
    }
} 