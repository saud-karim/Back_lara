<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BrandController extends Controller
{
    /**
     * جلب قائمة العلامات التجارية
     */
    public function index(Request $request): JsonResponse
    {
        $query = Brand::query();

        // فلترة حسب الحالة
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // فلترة العلامات المميزة
        if ($request->boolean('featured')) {
            $query->where('featured', true);
        }

        // البحث
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%");
            });
        }

        $brands = $query->active()
                       ->orderBy('sort_order')
                       ->orderBy('name_en')
                       ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => [
                'brands' => $brands->items(),
                'pagination' => [
                    'current_page' => $brands->currentPage(),
                    'total_pages' => $brands->lastPage(),
                    'total_brands' => $brands->total(),
                    'per_page' => $brands->perPage(),
                ]
            ]
        ]);
    }

    /**
     * عرض تفاصيل علامة تجارية محددة
     */
    public function show($id): JsonResponse
    {
        $brand = Brand::with(['products' => function ($query) {
            $query->limit(10);
        }])->find($id);

        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'Brand not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'brand' => $brand,
                'products_count' => $brand->products()->count(),
            ]
        ]);
    }

    /**
     * جلب المنتجات لعلامة تجارية محددة
     */
    public function products($id, Request $request): JsonResponse
    {
        $brand = Brand::find($id);

        if (!$brand) {
            return response()->json([
                'success' => false,
                'message' => 'Brand not found'
            ], 404);
        }

        $products = $brand->products()
                         ->with(['category'])
                         ->paginate($request->get('per_page', 12));

        return response()->json([
            'success' => true,
            'data' => [
                'brand' => $brand,
                'products' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'total_pages' => $products->lastPage(),
                    'total_products' => $products->total(),
                    'per_page' => $products->perPage(),
                ]
            ]
        ]);
    }
} 