<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupplierResource;
use App\Models\Supplier;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers.
     */
    public function index(): AnonymousResourceCollection
    {
        $suppliers = Supplier::with(['user', 'products'])->get();
        return SupplierResource::collection($suppliers);
    }

    /**
     * Display the specified supplier.
     */
    public function show(int $id): JsonResponse
    {
        $supplier = Supplier::with(['user', 'products'])->findOrFail($id);

        return response()->json([
            'supplier' => new SupplierResource($supplier),
        ]);
    }

    /**
     * Get supplier profile.
     */
    public function profile(Request $request): JsonResponse
    {
        $supplier = $request->user()->supplier;

        if (!$supplier) {
            return response()->json([
                'message' => 'Supplier profile not found',
            ], 404);
        }

        return response()->json([
            'supplier' => new SupplierResource($supplier->load(['user', 'products'])),
        ]);
    }

    /**
     * Update supplier profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $supplier = $request->user()->supplier;

        if (!$supplier) {
            return response()->json([
                'message' => 'Supplier profile not found',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'rating' => 'sometimes|numeric|min:0|max:5',
            'certifications' => 'sometimes|array',
            'certifications.*' => 'string|url',
        ]);

        $supplier->update($validated);

        return response()->json([
            'message' => 'Supplier profile updated successfully',
            'supplier' => new SupplierResource($supplier->load(['user', 'products'])),
        ]);
    }

    /**
     * Upload supplier certifications.
     */
    public function uploadCertifications(Request $request): JsonResponse
    {
        $supplier = $request->user()->supplier;

        if (!$supplier) {
            return response()->json([
                'message' => 'Supplier profile not found',
            ], 404);
        }

        $validated = $request->validate([
            'certifications' => 'required|array',
            'certifications.*' => 'string|url',
        ]);

        $supplier->update(['certifications' => $validated['certifications']]);

        return response()->json([
            'message' => 'Certifications uploaded successfully',
            'supplier' => new SupplierResource($supplier),
        ]);
    }
} 