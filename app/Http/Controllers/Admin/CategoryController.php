<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    /**
     * إنشاء فئة جديدة
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Validation
            $validated = $request->validate([
                'name_ar' => 'required|string|max:255|unique:categories,name_ar',
                'name_en' => 'required|string|max:255|unique:categories,name_en',
                'description_ar' => 'nullable|string|max:1000',
                'description_en' => 'nullable|string|max:1000',
                'status' => 'required|in:active,inactive',
                'sort_order' => 'required|integer|min:0',
                'image' => 'nullable|string|max:255'
            ], [
                'name_ar.required' => 'اسم الفئة باللغة العربية مطلوب',
                'name_ar.unique' => 'اسم الفئة باللغة العربية موجود مسبقاً',
                'name_en.required' => 'Category name in English is required',
                'name_en.unique' => 'Category name in English already exists',
                'status.required' => 'الحالة مطلوبة',
                'status.in' => 'الحالة يجب أن تكون active أو inactive',
                'sort_order.required' => 'ترتيب الفئة مطلوب',
                'sort_order.integer' => 'ترتيب الفئة يجب أن يكون رقم صحيح',
                'sort_order.min' => 'ترتيب الفئة يجب أن يكون أكبر من أو يساوي 0'
            ]);

            // إنشاء الفئة
            $category = Category::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء الفئة بنجاح',
                'data' => [
                    'category' => new CategoryResource($category)
                ]
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في التحقق من البيانات',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error creating category: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إنشاء الفئة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث فئة موجودة
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);

            // Validation with unique check excluding current category
            $validated = $request->validate([
                'name_ar' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('categories', 'name_ar')->ignore($id)
                ],
                'name_en' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('categories', 'name_en')->ignore($id)
                ],
                'description_ar' => 'nullable|string|max:1000',
                'description_en' => 'nullable|string|max:1000',
                'status' => 'required|in:active,inactive',
                'sort_order' => 'required|integer|min:0',
                'image' => 'nullable|string|max:255'
            ], [
                'name_ar.required' => 'اسم الفئة باللغة العربية مطلوب',
                'name_ar.unique' => 'اسم الفئة باللغة العربية موجود مسبقاً',
                'name_en.required' => 'Category name in English is required',
                'name_en.unique' => 'Category name in English already exists',
                'status.required' => 'الحالة مطلوبة',
                'status.in' => 'الحالة يجب أن تكون active أو inactive',
                'sort_order.required' => 'ترتيب الفئة مطلوب',
                'sort_order.integer' => 'ترتيب الفئة يجب أن يكون رقم صحيح',
                'sort_order.min' => 'ترتيب الفئة يجب أن يكون أكبر من أو يساوي 0'
            ]);

            // تحديث الفئة
            $category->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث الفئة بنجاح',
                'data' => [
                    'category' => new CategoryResource($category->fresh())
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'الفئة غير موجودة',
                'error' => 'Category not found'
            ], 404);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في التحقق من البيانات',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error updating category: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث الفئة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * حذف فئة
     */
    public function destroy($id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);

            // التحقق من وجود منتجات في الفئة
            $productsCount = $category->products()->count();
            if ($productsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "لا يمكن حذف الفئة لأنها تحتوي على {$productsCount} منتج",
                    'error' => 'Category has associated products',
                    'products_count' => $productsCount
                ], 422);
            }

            // حذف الفئة
            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف الفئة بنجاح'
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'الفئة غير موجودة',
                'error' => 'Category not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error deleting category: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف الفئة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تبديل حالة الفئة (نشط/غير نشط)
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);

            // تبديل الحالة
            $newStatus = $category->status === 'active' ? 'inactive' : 'active';
            $category->update(['status' => $newStatus]);

            // رسالة مناسبة حسب الحالة الجديدة
            $message = $newStatus === 'active' 
                ? 'تم تفعيل الفئة بنجاح' 
                : 'تم إلغاء تفعيل الفئة بنجاح';

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'category' => [
                        'id' => $category->id,
                        'status' => $newStatus
                    ]
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'الفئة غير موجودة',
                'error' => 'Category not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error toggling category status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث حالة الفئة',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * إحصائيات الفئات
     */
    public function stats(): JsonResponse
    {
        try {
            $totalCategories = Category::count();
            $activeCategories = Category::where('status', 'active')->count();
            $inactiveCategories = Category::where('status', 'inactive')->count();
            
            // الفئات التي تحتوي على منتجات
            $categoriesWithProducts = Category::has('products')->count();
            
            // الفئات الفارغة (بدون منتجات)
            $emptyCategories = Category::doesntHave('products')->count();
            
            // إجمالي المنتجات
            $totalProducts = Product::count();
            
            // متوسط المنتجات لكل فئة
            $averageProductsPerCategory = $totalCategories > 0 
                ? round($totalProducts / $totalCategories, 2) 
                : 0;

            $stats = [
                'total_categories' => $totalCategories,
                'active_categories' => $activeCategories,
                'inactive_categories' => $inactiveCategories,
                'categories_with_products' => $categoriesWithProducts,
                'empty_categories' => $emptyCategories,
                'total_products' => $totalProducts,
                'average_products_per_category' => $averageProductsPerCategory
            ];

            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting categories stats: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب إحصائيات الفئات',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * قائمة جميع الفئات للإدارة
     */
    public function index()
    {
        $categories = Category::select([
            'id',
            'name_ar',    // ⬅️ مهم!
            'name_en',    // ⬅️ مهم!
            'description_ar',
            'description_en', 
            'status',
            'sort_order',
            'created_at',
            'updated_at'
        ])
        ->withCount('products as products_count')
        ->orderBy('sort_order')
        ->get();

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * تفاصيل فئة واحدة للإدارة
     */
    public function show($id): JsonResponse
    {
        try {
            $category = Category::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'category' => new CategoryResource($category)
                ]
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'الفئة غير موجودة',
                'error' => 'Category not found'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error getting category details: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء جلب تفاصيل الفئة',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 