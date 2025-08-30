<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Process payment.
     */
    public function process(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|in:card,cod,installment',
            'amount' => 'required|numeric|min:0',
        ]);

        // Here you would integrate with payment gateways like Stripe
        // For now, we'll just return a success response

        return response()->json([
            'message' => 'Payment processed successfully',
            'transaction_id' => 'TXN_' . uniqid(),
            'status' => 'completed',
        ]);
    }

    /**
     * Handle payment callback.
     */
    public function callback(Request $request): JsonResponse
    {
        // Handle payment gateway callbacks
        // This would typically verify the payment and update order status

        return response()->json([
            'message' => 'Callback received successfully',
        ]);
    }
} 