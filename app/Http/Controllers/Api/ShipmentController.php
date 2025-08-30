<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShipmentResource;
use App\Models\Shipment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShipmentController extends Controller
{
    /**
     * Display the specified shipment.
     */
    public function show(int $id): JsonResponse
    {
        $shipment = Shipment::with('order')->findOrFail($id);

        return response()->json([
            'shipment' => new ShipmentResource($shipment),
        ]);
    }

    /**
     * Update shipment tracking.
     */
    public function updateTracking(Request $request, int $id): JsonResponse
    {
        $shipment = Shipment::findOrFail($id);

        $validated = $request->validate([
            'status' => 'sometimes|in:pending,in_transit,delivered',
            'tracking_number' => 'sometimes|string',
            'estimated_delivery' => 'sometimes|date',
        ]);

        $shipment->update($validated);

        return response()->json([
            'message' => 'Shipment tracking updated successfully',
            'shipment' => new ShipmentResource($shipment),
        ]);
    }
} 