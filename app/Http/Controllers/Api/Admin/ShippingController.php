<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SendToShippingRequest;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ShippingController extends Controller
{
    /**
     * Preview shipping data before sending
     * POST /api/v1/admin/shipping/preview
     */
    public function preview(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'order_ids' => 'required|array|min:1',
                'order_ids.*' => 'required|integer|exists:orders,id',
            ]);

            $orders = Order::with(['user', 'orderItems.product'])
                ->whereIn('id', $request->order_ids)
                ->get();

            $ordersData = [];
            $validOrders = 0;
            $invalidOrders = 0;
            $totalAmount = 0;

            foreach ($orders as $order) {
                $validation = $this->validateOrderForShipping($order);
                
                if ($validation['is_valid']) {
                    $validOrders++;
                } else {
                    $invalidOrders++;
                }

                $totalAmount += $order->total_amount;

                $ordersData[] = [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'customer' => [
                        'name' => $order->user->name ?? 'N/A',
                        'phone' => $order->shipping_address['phone'] ?? $order->user->phone ?? null,
                        'email' => $order->user->email ?? null,
                    ],
                    'shipping_address' => $order->shipping_address,
                    'items' => $order->orderItems->map(function ($item) {
                        return [
                            'product_id' => $item->product_id,
                            'product_name' => $item->product->name_ar ?? $item->product->name_en ?? 'Unknown',
                            'sku' => $item->product->sku ?? null,
                            'quantity' => $item->quantity,
                            'unit_price' => (float) $item->unit_price,
                            'subtotal' => (float) $item->subtotal,
                            'weight' => $item->product->weight ?? 0,
                        ];
                    }),
                    'total_amount' => (float) $order->total_amount,
                    'subtotal' => (float) $order->subtotal,
                    'shipping_cost' => (float) $order->shipping_amount,
                    'tax_amount' => (float) ($order->tax_amount ?? 0),
                    'discount_amount' => (float) ($order->discount_amount ?? 0),
                    'payment_method' => $order->payment_method,
                    'payment_status' => $order->payment_status ?? 'pending',
                    'notes' => $order->notes,
                    'created_at' => $order->created_at->toIso8601String(),
                    'validation' => $validation,
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'orders' => $ordersData,
                    'summary' => [
                        'total_orders' => count($orders),
                        'valid_orders' => $validOrders,
                        'invalid_orders' => $invalidOrders,
                        'total_amount' => (float) $totalAmount,
                    ],
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Shipping preview error', [
                'error' => $e->getMessage(),
                'order_ids' => $request->order_ids ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to preview shipping data',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Send orders to shipping company
     * POST /api/v1/admin/shipping/send
     */
    public function send(SendToShippingRequest $request): JsonResponse
    {
        try {
            $orders = Order::with(['user', 'orderItems.product'])
                ->whereIn('id', $request->order_ids)
                ->get();

            $results = [];
            $successCount = 0;
            $failedCount = 0;

            foreach ($orders as $order) {
                // Validate order before sending
                $validation = $this->validateOrderForShipping($order);
                if (!$validation['is_valid']) {
                    $results[] = [
                        'order_id' => $order->id,
                        'status' => 'failed',
                        'error' => implode(', ', $validation['errors']),
                        'shipping_company' => $request->shipping_company,
                    ];
                    $failedCount++;
                    continue;
                }

                // Extract data based on field mapping
                $shippingData = $this->extractShippingData(
                    $order,
                    $request->field_mapping
                );

                // Send to shipping company
                $result = $this->sendToShippingCompany(
                    $order,
                    $shippingData,
                    $request->shipping_company,
                    $request->custom_api_url,
                    $request->custom_api_key
                );

                if ($result['status'] === 'success') {
                    // Update order with tracking info
                    $order->update([
                        'tracking_number' => $result['tracking_number'],
                        'shipping_company' => $request->shipping_company,
                        'shipping_status' => 'sent',
                        'shipped_at' => now(),
                    ]);
                    $successCount++;
                } else {
                    $failedCount++;
                }

                $results[] = $result;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'results' => $results,
                    'summary' => [
                        'total' => count($results),
                        'success' => $successCount,
                        'failed' => $failedCount,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Shipping send error', [
                'error' => $e->getMessage(),
                'order_ids' => $request->order_ids ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process shipping request',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Retry failed shipment
     * POST /api/v1/admin/shipping/retry
     */
    public function retry(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'order_id' => 'required|integer|exists:orders,id',
                'shipping_company' => 'required|string|max:50',
            ]);

            $order = Order::with(['user', 'orderItems.product'])->find($request->order_id);

            // Validate order
            $validation = $this->validateOrderForShipping($order);
            if (!$validation['is_valid']) {
                return response()->json([
                    'success' => false,
                    'data' => [
                        'order_id' => $order->id,
                        'status' => 'failed',
                        'error' => implode(', ', $validation['errors']),
                    ],
                ]);
            }

            // Extract shipping data
            $shippingData = $this->extractShippingData($order, null);

            // Send to shipping company
            $result = $this->sendToShippingCompany(
                $order,
                $shippingData,
                $request->shipping_company,
                null,
                null
            );

            if ($result['status'] === 'success') {
                // Update order
                $order->update([
                    'tracking_number' => $result['tracking_number'],
                    'shipping_company' => $request->shipping_company,
                    'shipping_status' => 'sent',
                    'shipped_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'order_id' => $order->id,
                        'status' => 'success',
                        'tracking_number' => $result['tracking_number'],
                        'message' => 'Shipment retry successful',
                    ],
                ]);
            }

            return response()->json([
                'success' => false,
                'data' => [
                    'order_id' => $order->id,
                    'status' => 'failed',
                    'error' => $result['error'] ?? 'Unknown error',
                ],
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Shipping retry error', [
                'error' => $e->getMessage(),
                'order_id' => $request->order_id ?? null,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to retry shipment',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get shipping status
     * GET /api/v1/admin/shipping/status/{order_id}
     */
    public function status(int $orderId): JsonResponse
    {
        try {
            $order = Order::find($orderId);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found',
                ], 404);
            }

            if (!$order->tracking_number) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tracking information available',
                    'data' => [
                        'order_id' => $order->id,
                        'shipping_status' => $order->shipping_status ?? 'not_sent',
                    ],
                ]);
            }

            // Get status from shipping company
            $statusData = $this->getShippingStatus(
                $order->tracking_number,
                $order->shipping_company
            );

            return response()->json([
                'success' => true,
                'data' => $statusData,
            ]);

        } catch (\Exception $e) {
            Log::error('Get shipping status error', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get shipping status',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Validate order for shipping
     */
    private function validateOrderForShipping(Order $order): array
    {
        $errors = [];
        $warnings = [];

        // Check customer phone
        $phone = $order->shipping_address['phone'] ?? $order->user->phone ?? null;
        if (!$phone) {
            $errors[] = 'Missing customer phone number';
        } elseif (!$this->isValidPhone($phone)) {
            $warnings[] = 'Phone number may be invalid';
        }

        // Check shipping address
        $address = $order->shipping_address;
        if (!$address || !is_array($address)) {
            $errors[] = 'Missing shipping address';
        } else {
            if (empty($address['street'])) {
                $errors[] = 'Missing street address';
            }
            if (empty($address['city'])) {
                $errors[] = 'Missing city';
            }
            if (empty($address['governorate'])) {
                $errors[] = 'Missing governorate';
            }
        }

        // Check order items
        if ($order->orderItems->isEmpty()) {
            $errors[] = 'Order has no items';
        }

        // Check total amount
        if ($order->total_amount <= 0) {
            $errors[] = 'Invalid order total';
        }

        return [
            'is_valid' => empty($errors),
            'warnings' => $warnings,
            'errors' => $errors,
        ];
    }

    /**
     * Extract shipping data based on field mapping
     */
    private function extractShippingData(Order $order, ?array $fieldMapping): array
    {
        // If no field mapping provided, use defaults
        if (!$fieldMapping) {
            $fieldMapping = $this->getDefaultFieldMapping();
        }

        $data = [];

        foreach ($fieldMapping as $field) {
            if (!isset($field['enabled']) || !$field['enabled']) {
                continue;
            }

            $value = $this->getFieldValue($order, $field['field_path']);
            $data[$field['id']] = $value;
        }

        return $data;
    }

    /**
     * Get default field mapping
     */
    private function getDefaultFieldMapping(): array
    {
        return [
            ['id' => 'order_number', 'field_path' => 'order.order_number', 'enabled' => true],
            ['id' => 'customer_name', 'field_path' => 'customer.name', 'enabled' => true],
            ['id' => 'customer_phone', 'field_path' => 'customer.phone', 'enabled' => true],
            ['id' => 'items_product_name', 'field_path' => 'items[].product.name', 'enabled' => true],
            ['id' => 'items_quantity', 'field_path' => 'items[].quantity', 'enabled' => true],
            ['id' => 'items_unit_price', 'field_path' => 'items[].unit_price', 'enabled' => true],
            ['id' => 'address_street', 'field_path' => 'shipping_address.street', 'enabled' => true],
            ['id' => 'address_city', 'field_path' => 'shipping_address.city', 'enabled' => true],
            ['id' => 'address_governorate', 'field_path' => 'shipping_address.governorate', 'enabled' => true],
            ['id' => 'order_total', 'field_path' => 'order.total_amount', 'enabled' => true],
        ];
    }

    /**
     * Get field value from order using dot notation path
     */
    private function getFieldValue(Order $order, string $path)
    {
        $parts = explode('.', $path);
        $isArray = strpos($path, '[]') !== false;

        if ($isArray) {
            // Handle array fields like items[].product.name
            if (strpos($path, 'items[]') === 0) {
                $remaining = str_replace('items[].', '', $path);
                return $order->orderItems->map(function ($item) use ($remaining) {
                    return data_get($item, $remaining);
                })->toArray();
            }
            return null;
        }

        // Handle regular fields
        if (strpos($path, 'order.') === 0) {
            $field = str_replace('order.', '', $path);
            return data_get($order, $field);
        }

        if (strpos($path, 'customer.') === 0) {
            $field = str_replace('customer.', '', $path);
            if ($field === 'name') {
                return $order->user->name ?? $order->shipping_address['name'] ?? null;
            }
            if ($field === 'phone') {
                return $order->shipping_address['phone'] ?? $order->user->phone ?? null;
            }
            return data_get($order->user, $field);
        }

        if (strpos($path, 'shipping_address.') === 0) {
            $field = str_replace('shipping_address.', '', $path);
            return $order->shipping_address[$field] ?? null;
        }

        return null;
    }

    /**
     * Send order to shipping company - FLEXIBLE for any API
     */
    private function sendToShippingCompany(
        Order $order,
        array $data,
        string $company,
        ?string $customUrl = null,
        ?string $customKey = null
    ): array {
        try {
            // If no custom API URL provided, cannot send
            if (!$customUrl) {
                return [
                    'order_id' => $order->id,
                    'status' => 'failed',
                    'error' => 'No API URL provided. Please specify custom_api_url.',
                    'shipping_company' => $company,
                ];
            }

            Log::info('Sending to shipping company', [
                'order_id' => $order->id,
                'company' => $company,
                'api_url' => $customUrl,
                'has_api_key' => !empty($customKey),
            ]);

            // Build payload from extracted data
            $payload = $this->buildPayload($order, $data);

            // Prepare headers
            $headers = [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ];

            // Add API key if provided
            if ($customKey) {
                $headers['Authorization'] = 'Bearer ' . $customKey;
            }

            // Make real HTTP request to shipping company API
            $response = Http::timeout(30)
                ->withHeaders($headers)
                ->post($customUrl, $payload);

            // Log response for debugging
            Log::info('Shipping API response', [
                'order_id' => $order->id,
                'status_code' => $response->status(),
                'response_body' => $response->body(),
            ]);

            // Check if request was successful
            if ($response->successful()) {
                $responseData = $response->json();

                // Try to extract tracking number from various possible field names
                $trackingNumber = $responseData['tracking_number']
                    ?? $responseData['trackingNumber']
                    ?? $responseData['tracking_id']
                    ?? $responseData['trackingId']
                    ?? $responseData['awb']
                    ?? $responseData['shipment_id']
                    ?? $responseData['shipmentId']
                    ?? $responseData['reference']
                    ?? $responseData['id']
                    ?? 'TRACK-' . time();

                Log::info('Shipment created successfully', [
                    'order_id' => $order->id,
                    'tracking_number' => $trackingNumber,
                ]);

                return [
                    'order_id' => $order->id,
                    'status' => 'success',
                    'tracking_number' => $trackingNumber,
                    'shipping_company' => $company,
                    'message' => 'Shipment created successfully',
                    'raw_response' => $responseData,
                ];
            }

            // API returned error status
            $responseData = $response->json();
            $errorMessage = $responseData['message']
                ?? $responseData['error']
                ?? $responseData['error_message']
                ?? $responseData['msg']
                ?? 'API request failed with status ' . $response->status();

            Log::error('Shipping API error response', [
                'order_id' => $order->id,
                'status_code' => $response->status(),
                'error' => $errorMessage,
                'response' => $response->body(),
            ]);

            return [
                'order_id' => $order->id,
                'status' => 'failed',
                'error' => $errorMessage,
                'shipping_company' => $company,
            ];

        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            Log::error('Shipping API connection error', [
                'order_id' => $order->id,
                'company' => $company,
                'error' => $e->getMessage(),
            ]);

            return [
                'order_id' => $order->id,
                'status' => 'failed',
                'error' => 'Cannot connect to shipping API: ' . $e->getMessage(),
                'shipping_company' => $company,
            ];

        } catch (\Illuminate\Http\Client\RequestException $e) {
            Log::error('Shipping API request error', [
                'order_id' => $order->id,
                'company' => $company,
                'error' => $e->getMessage(),
            ]);

            return [
                'order_id' => $order->id,
                'status' => 'failed',
                'error' => 'API request error: ' . $e->getMessage(),
                'shipping_company' => $company,
            ];

        } catch (\Exception $e) {
            Log::error('Shipping API unexpected error', [
                'order_id' => $order->id,
                'company' => $company,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'order_id' => $order->id,
                'status' => 'failed',
                'error' => 'Unexpected error: ' . $e->getMessage(),
                'shipping_company' => $company,
            ];
        }
    }

    /**
     * Build payload for shipping API from extracted data
     */
    private function buildPayload(Order $order, array $data): array
    {
        // Start with basic structure
        $payload = [
            'order_reference' => $data['order_number'] ?? $order->order_number,
            'order_id' => $order->id,
        ];

        // Add all extracted data dynamically
        foreach ($data as $key => $value) {
            // Skip if already added
            if ($key === 'order_number') {
                continue;
            }

            // Handle array fields (like items)
            if (is_array($value) && !empty($value)) {
                $payload[$key] = $value;
            } else {
                $payload[$key] = $value;
            }
        }

        // Always include essential order info
        if (!isset($payload['total_amount'])) {
            $payload['total_amount'] = (float) $order->total_amount;
        }

        if (!isset($payload['currency'])) {
            $payload['currency'] = $order->currency ?? 'EGP';
        }

        if (!isset($payload['payment_method'])) {
            $payload['payment_method'] = $order->payment_method;
        }

        // Add customer info if not already included
        if (!isset($payload['customer_name']) && $order->user) {
            $payload['customer_name'] = $order->user->name;
        }

        if (!isset($payload['customer_phone'])) {
            $payload['customer_phone'] = $order->shipping_address['phone'] ?? $order->user->phone ?? null;
        }

        if (!isset($payload['customer_email']) && $order->user) {
            $payload['customer_email'] = $order->user->email;
        }

        // Add shipping address if not already included
        if (!isset($payload['shipping_address']) && $order->shipping_address) {
            $payload['shipping_address'] = $order->shipping_address;
        }

        // Add items if not already included
        if (!isset($payload['items']) && $order->orderItems) {
            $payload['items'] = $order->orderItems->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name_ar ?? $item->product->name_en ?? 'Unknown',
                    'quantity' => $item->quantity,
                    'unit_price' => (float) $item->unit_price,
                    'subtotal' => (float) $item->subtotal,
                ];
            })->toArray();
        }

        Log::info('Built payload for shipping API', [
            'order_id' => $order->id,
            'payload_keys' => array_keys($payload),
        ]);

        return $payload;
    }

    /**
     * Get shipping status from company
     */
    private function getShippingStatus(string $trackingNumber, ?string $company): array
    {
        // For now, simulate status response
        // In production, this would query the shipping company API

        return [
            'order_id' => null, // Will be filled from caller
            'tracking_number' => $trackingNumber,
            'shipping_company' => $company ?? 'unknown',
            'status' => 'in_transit',
            'status_ar' => 'قيد التوصيل',
            'current_location' => 'مركز التوزيع - القاهرة',
            'estimated_delivery' => now()->addDays(2)->format('Y-m-d'),
            'history' => [
                [
                    'status' => 'created',
                    'status_ar' => 'تم الإنشاء',
                    'timestamp' => now()->subDays(1)->toIso8601String(),
                    'location' => 'مركز الفرز - القاهرة',
                ],
                [
                    'status' => 'picked_up',
                    'status_ar' => 'تم الاستلام',
                    'timestamp' => now()->subHours(12)->toIso8601String(),
                    'location' => 'مركز الفرز - القاهرة',
                ],
                [
                    'status' => 'in_transit',
                    'status_ar' => 'قيد التوصيل',
                    'timestamp' => now()->subHours(2)->toIso8601String(),
                    'location' => 'مركز التوزيع - القاهرة',
                ],
            ],
        ];
    }

    /**
     * Validate phone number format
     */
    private function isValidPhone(string $phone): bool
    {
        // Basic validation for Egyptian phone numbers
        return preg_match('/^\+?20[0-9]{10}$|^0[0-9]{10}$/', $phone) === 1;
    }
}
