<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::all();
        return CategoryResource::collection($categories);
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'message' => 'Category created successfully',
            'category' => new CategoryResource($category),
        ], 201);
    }

    /**
     * Display the specified category.
     */
    public function show($id): JsonResponse
    {
        $category = Category::findOrFail($id);
        
        // جلب المنتجات الخاصة بهذه الفئة (النشطة فقط)
        $products = $category->products()->where('status', 'active')->get();

        return response()->json([
            'category' => new CategoryResource($category),
            'products' => ProductResource::collection($products),
        ]);
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $category->update($validated);

        return response()->json([
            'message' => 'Category updated successfully',
            'category' => new CategoryResource($category),
        ]);
    }

    /**
     * Remove the specified category.
     */
    public function destroy($id): JsonResponse
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
        ]);
    }

    /**
     * Get categories statistics.
     */
    public function statistics(Request $request)
    {
        try {
            $lang = $request->get('lang', 'en');
            
            $categories = Category::with(['products' => function ($query) {
                $query->where('status', 'active');
            }])->get();

            $stats = $categories->map(function ($category) use ($lang) {
                return [
                    'id' => $category->id,
                    'name' => $lang === 'ar' 
                        ? ($category->name_ar ?: $category->name_en) 
                        : ($category->name_en ?: $category->name_ar),
                    'total_products' => $category->products_count,
                    'active_products' => $category->products()->where('status', 'active')->count(),
                    'featured_products' => $category->products()->where('status', 'active')->where('featured', true)->count(),
                    'out_of_stock' => $category->products()->where('status', 'active')->where('stock', '<=', 0)->count(),
                    'low_stock' => $category->products()->where('status', 'active')->where('stock', '>', 0)->where('stock', '<=', 10)->count(),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'categories' => $stats,
                    'summary' => [
                        'total_categories' => $categories->count(),
                        'active_categories' => $categories->where('status', 'active')->count(),
                        'total_products' => $categories->sum(fn($cat) => $cat->products_count),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب الإحصائيات: ' . $e->getMessage()
            ], 500);
        }
    }
} 