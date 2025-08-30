<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\ProductFeature;
use App\Models\ProductSpecification;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductController extends Controller
{
    /**
     * قائمة المنتجات للإدارة مع فلاتر متقدمة
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::with(['category', 'supplier', 'brand']);

        // البحث في الاسم والوصف
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name_ar', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('description_ar', 'like', "%{$search}%")
                  ->orWhere('description_en', 'like', "%{$search}%");
            });
        }

        // فلتر الفئة
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // فلتر الحالة
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // فلتر المورد
        if ($request->filled('supplier')) {
            $query->where('supplier_id', $request->supplier);
        }

        // فلتر المنتجات المميزة
        if ($request->filled('featured')) {
            $query->where('featured', $request->boolean('featured'));
        }

        // فلتر المخزون المنخفض
        if ($request->filled('low_stock')) {
            if ($request->boolean('low_stock')) {
                $query->where('stock', '<=', 10)->where('stock', '>', 0);
            }
        }

        // فلتر نفاد المخزون
        if ($request->filled('out_of_stock')) {
            if ($request->boolean('out_of_stock')) {
                $query->where('stock', '=', 0);
            }
        }

        // الترتيب
        $sortField = $request->get('sort', 'created_at');
        $sortOrder = $request->get('order', 'desc');
        
        $allowedSortFields = ['created_at', 'name_ar', 'name_en', 'price', 'stock', 'rating'];
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortOrder);
        }

        // Pagination
        $perPage = min($request->get('per_page', 15), 50);
        $products = $query->paginate($perPage);

        // تحويل البيانات لـ Resources مع اللغة
        $lang = $request->get('lang', 'ar');
        $productsResource = $products->getCollection()->map(function ($product) use ($lang) {
            return [
                'id' => $product->id,
                'name' => $lang === 'ar' ? $product->name_ar : $product->name_en,
                'description' => $lang === 'ar' ? $product->description_ar : $product->description_en,
                'price' => $product->price,
                'original_price' => $product->original_price,
                'rating' => $product->rating,
                'reviews_count' => $product->reviews_count,
                'stock' => $product->stock,
                'status' => $product->status,
                'featured' => $product->featured,
                'images' => $product->images ?? [],
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $lang === 'ar' ? $product->category->name_ar : $product->category->name_en
                ] : null,
                'supplier' => $product->supplier ? [
                    'id' => $product->supplier->id,
                    'name' => $lang === 'ar' ? $product->supplier->name_ar : $product->supplier->name_en
                ] : null,
                'brand' => $product->brand ? [
                    'id' => $product->brand->id,
                    'name' => $lang === 'ar' ? $product->brand->name_ar : $product->brand->name_en
                ] : null,
                'is_in_stock' => $product->stock > 0,
                'has_low_stock' => $product->stock <= 10 && $product->stock > 0,
                'created_at' => $product->created_at,
                'updated_at' => $product->updated_at
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $productsResource,
            'meta' => [
                'current_page' => $products->currentPage(),
                'total' => $products->total(),
                'per_page' => $products->perPage(),
                'last_page' => $products->lastPage(),
                'from' => $products->firstItem(),
                'to' => $products->lastItem()
            ],
            'links' => [
                'first' => $products->url(1),
                'last' => $products->url($products->lastPage()),
                'prev' => $products->previousPageUrl(),
                'next' => $products->nextPageUrl(),
            ]
        ]);
    }

    /**
     * إحصائيات المنتجات
     */
    public function stats(): JsonResponse
    {
        $stats = [
            'total' => Product::count(),
            'active' => Product::where('status', 'active')->count(),
            'inactive' => Product::where('status', 'inactive')->count(),
            'featured' => Product::where('featured', true)->count(),
            'low_stock' => Product::where('stock', '<=', 10)->where('stock', '>', 0)->count(),
            'out_of_stock' => Product::where('stock', '=', 0)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * تفاصيل منتج واحد
     */
    public function show(Request $request, $id): JsonResponse
    {
        $product = Product::with(['category', 'supplier', 'brand'])
                          ->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'المنتج غير موجود'
            ], 404);
        }

        $lang = $request->get('lang', 'ar');

        $productData = [
            'id' => $product->id,
            'name_ar' => $product->name_ar,
            'name_en' => $product->name_en,
            'name' => $lang === 'ar' ? $product->name_ar : $product->name_en,
            'description_ar' => $product->description_ar,
            'description_en' => $product->description_en,
            'description' => $lang === 'ar' ? $product->description_ar : $product->description_en,
            'price' => $product->price,
            'original_price' => $product->original_price,
            'stock' => $product->stock,
            'sku' => $product->sku,
            'rating' => $product->rating,
            'reviews_count' => $product->reviews_count,
            'status' => $product->status,
            'featured' => $product->featured,
            'images' => $product->images ?? [],
            'category_id' => $product->category_id,
            'supplier_id' => $product->supplier_id,
            'brand_id' => $product->brand_id,
            'category' => $product->category ? [
                'id' => $product->category->id,
                'name' => $lang === 'ar' ? $product->category->name_ar : $product->category->name_en
            ] : null,
            'supplier' => $product->supplier ? [
                'id' => $product->supplier->id,
                'name' => $lang === 'ar' ? $product->supplier->name_ar : $product->supplier->name_en
            ] : null,
            'brand' => $product->brand ? [
                'id' => $product->brand->id,
                'name' => $lang === 'ar' ? $product->brand->name_ar : $product->brand->name_en
            ] : null,
            'features' => [], // TODO: Add when ProductFeature model is created
            'specifications' => [], // TODO: Add when ProductSpecification model is created
            'is_in_stock' => $product->stock > 0,
            'has_low_stock' => $product->stock <= 10 && $product->stock > 0,
            'created_at' => $product->created_at,
            'updated_at' => $product->updated_at
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'product' => $productData
            ]
        ]);
    }

    /**
     * إنشاء منتج جديد
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'brand_id' => 'nullable|exists:brands,id',
            'status' => 'required|in:active,inactive',
            'featured' => 'boolean',
            
            // الصور الجديدة
            'new_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            
            // الصور الموجودة والبيانات الإضافية
            'existing_images' => 'nullable|string', // JSON string
            'features' => 'nullable|string', // JSON string
            'specifications' => 'nullable|string' // JSON string
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // إنشاء المنتج
            $product = Product::create([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'description_ar' => $request->description_ar,
                'description_en' => $request->description_en,
                'price' => $request->price,
                'original_price' => $request->original_price,
                'stock' => $request->stock,
                'category_id' => $request->category_id,
                'supplier_id' => $request->supplier_id,
                'brand_id' => $request->brand_id,
                'status' => $request->status,
                'featured' => $request->boolean('featured'),
                'sku' => $this->generateSKU(),
                'rating' => 0,
                'reviews_count' => 0,
                'images' => []
            ]);

            // معالجة الصور
            $allImages = [];
            
            // الصور الموجودة
            if ($request->existing_images) {
                $existingImages = json_decode($request->existing_images, true);
                if (is_array($existingImages)) {
                    $allImages = array_merge($allImages, $existingImages);
                }
            }

            // رفع الصور الجديدة
            if ($request->hasFile('new_images')) {
                foreach ($request->file('new_images') as $file) {
                    if ($file && $file->isValid()) {
                        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('images/products'), $filename);
                        $allImages[] = '/images/products/' . $filename;
                    }
                }
            }

            // حفظ الصور في قاعدة البيانات
            $product->update(['images' => json_encode($allImages)]);

            // معالجة الفيتشرز
            if ($request->features) {
                $features = json_decode($request->features, true);
                if (is_array($features)) {
                    foreach ($features as $index => $feature) {
                        ProductFeature::create([
                            'product_id' => $product->id,
                            'feature_ar' => $feature,
                            'feature_en' => $feature,
                            'sort_order' => $index + 1
                        ]);
                    }
                }
            }

            // معالجة المواصفات
            if ($request->specifications) {
                $specifications = json_decode($request->specifications, true);
                if (is_array($specifications)) {
                    foreach ($specifications as $spec) {
                        if (isset($spec['key']) && isset($spec['value'])) {
                            ProductSpecification::create([
                                'product_id' => $product->id,
                                'spec_key' => $spec['key'],
                                'spec_value_ar' => $spec['value'],
                                'spec_value_en' => $spec['value']
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم إنشاء المنتج بنجاح',
                'data' => [
                    'product' => $product->fresh()->load(['category', 'supplier', 'brand', 'features', 'specifications'])
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في إنشاء المنتج',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تحديث منتج
     */
    public function update(Request $request, $id): JsonResponse
    {
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'المنتج غير موجود'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'description_ar' => 'required|string',
            'description_en' => 'required|string',
            'price' => 'required|numeric|min:0',
            'original_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'brand_id' => 'nullable|exists:brands,id',
            'status' => 'required|in:active,inactive',
            'featured' => 'boolean',
            
            // الصور الجديدة
            'new_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            
            // الصور الموجودة والبيانات الإضافية
            'existing_images' => 'nullable|string', // JSON string
            'features' => 'nullable|string', // JSON string
            'specifications' => 'nullable|string' // JSON string
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // تحديث بيانات المنتج الأساسية
            $product->update([
                'name_ar' => $request->name_ar,
                'name_en' => $request->name_en,
                'description_ar' => $request->description_ar,
                'description_en' => $request->description_en,
                'price' => $request->price,
                'original_price' => $request->original_price,
                'stock' => $request->stock,
                'category_id' => $request->category_id,
                'supplier_id' => $request->supplier_id,
                'brand_id' => $request->brand_id,
                'status' => $request->status,
                'featured' => $request->boolean('featured'),
            ]);

            // معالجة الصور
            $allImages = [];
            
            // الصور الموجودة
            if ($request->existing_images) {
                $existingImages = json_decode($request->existing_images, true);
                if (is_array($existingImages)) {
                    $allImages = array_merge($allImages, $existingImages);
                }
            }

            // رفع الصور الجديدة
            if ($request->hasFile('new_images')) {
                foreach ($request->file('new_images') as $file) {
                    if ($file && $file->isValid()) {
                        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                        $file->move(public_path('images/products'), $filename);
                        $allImages[] = '/images/products/' . $filename;
                    }
                }
            }

            // حفظ الصور في قاعدة البيانات
            $product->update(['images' => json_encode($allImages)]);

            // تحديث الفيتشرز (حذف القديمة وإضافة الجديدة)
            $product->features()->delete();
            if ($request->features) {
                $features = json_decode($request->features, true);
                if (is_array($features)) {
                    foreach ($features as $index => $feature) {
                        ProductFeature::create([
                            'product_id' => $product->id,
                            'feature_ar' => $feature,
                            'feature_en' => $feature,
                            'sort_order' => $index + 1
                        ]);
                    }
                }
            }

            // تحديث المواصفات (حذف القديمة وإضافة الجديدة)
            $product->specifications()->delete();
            if ($request->specifications) {
                $specifications = json_decode($request->specifications, true);
                if (is_array($specifications)) {
                    foreach ($specifications as $spec) {
                        if (isset($spec['key']) && isset($spec['value'])) {
                            ProductSpecification::create([
                                'product_id' => $product->id,
                                'spec_key' => $spec['key'],
                                'spec_value_ar' => $spec['value'],
                                'spec_value_en' => $spec['value']
                            ]);
                        }
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث المنتج بنجاح',
                'data' => [
                    'product' => $product->fresh()->load(['category', 'supplier', 'brand', 'features', 'specifications'])
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحديث المنتج',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * تبديل حالة المنتج (نشط/غير نشط)
     */
    public function toggleStatus($id): JsonResponse
    {
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'المنتج غير موجود'
            ], 404);
        }

        try {
            $newStatus = $product->status === 'active' ? 'inactive' : 'active';
            $product->update(['status' => $newStatus]);

            $statusMessage = $newStatus === 'active' ? 'تم تفعيل المنتج بنجاح' : 'تم إلغاء تفعيل المنتج بنجاح';

            return response()->json([
                'success' => true,
                'message' => $statusMessage,
                'data' => [
                    'product' => [
                        'id' => $product->id,
                        'status' => $product->status
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث حالة المنتج'
            ], 500);
        }
    }

    /**
     * تبديل حالة المنتج المميز
     */
    public function toggleFeatured($id): JsonResponse
    {
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'المنتج غير موجود'
            ], 404);
        }

        try {
            $newFeatured = !$product->featured;
            $product->update(['featured' => $newFeatured]);

            $featuredMessage = $newFeatured ? 'تم إضافة المنتج للمميزة بنجاح' : 'تم إزالة المنتج من المميزة بنجاح';

            return response()->json([
                'success' => true,
                'message' => $featuredMessage,
                'data' => [
                    'product' => [
                        'id' => $product->id,
                        'featured' => $product->featured
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث حالة المنتج المميز'
            ], 500);
        }
    }

    /**
     * حذف منتج
     */
    public function destroy($id): JsonResponse
    {
        $product = Product::find($id);
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'المنتج غير موجود'
            ], 404);
        }

        try {
            // حذف المنتج مع العلاقات (Soft Delete)
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف المنتج بنجاح'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف المنتج'
            ], 500);
        }
    }

    /**
     * توليد رقم SKU فريد
     */
    private function generateSKU(): string
    {
        $prefix = 'PRD';
        $timestamp = time();
        $random = mt_rand(100, 999);
        
        do {
            $sku = $prefix . '-' . $timestamp . '-' . $random;
            $exists = Product::where('sku', $sku)->exists();
            if ($exists) {
                $random = mt_rand(100, 999);
            }
        } while ($exists);
        
        return $sku;
    }
} 