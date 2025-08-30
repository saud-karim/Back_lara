<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CostCalculation;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CostCalculatorController extends Controller
{
    /**
     * Calculate project cost.
     */
    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'area' => 'nullable|numeric|min:0',
            'materials' => 'required|array|min:1',
            'materials.*.product_id' => 'required|exists:products,id',
            'materials.*.quantity' => 'required|integer|min:1',
        ]);

        $totalCost = 0;
        $materials = [];

        foreach ($validated['materials'] as $material) {
            $product = Product::findOrFail($material['product_id']);
            
            // Ensure product is not null before accessing properties
            if ($product) {
                $itemCost = $product->price * $material['quantity'];
                $totalCost += $itemCost;

                $materials[] = [
                    'product_id' => $material['product_id'],
                    'quantity' => $material['quantity'],
                    'unit_price' => $product->price,
                    'item_cost' => $itemCost,
                ];
            }
        }

        // Save calculation if user is authenticated
        if (auth()->check()) {
            CostCalculation::create([
                'user_id' => auth()->id(),
                'area' => $validated['area'] ?? null,
                'materials' => $materials, // Save the processed materials with costs
                'total_cost' => $totalCost,
            ]);
        }

        return response()->json([
            'area' => $validated['area'] ?? null,
            'materials' => $materials,
            'total_cost' => $totalCost,
            'cost_per_sqm' => isset($validated['area']) && $validated['area'] > 0 ? $totalCost / $validated['area'] : null,
        ]);
    }
} 