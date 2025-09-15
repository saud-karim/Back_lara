<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct(
        private ProductService $productService
    ) {}

    /**
     * Display a listing of products.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        try {
            // Validate request parameters
            $validator = Validator::make($request->all(), [
                'per_page' => 'nullable|integer|min:1|max:100',
                'page' => 'nullable|integer|min:1',
                'featured' => 'nullable|in:true,false,1,0',
                'lang' => 'nullable|in:ar,en',
                'category_id' => 'nullable|integer|exists:categories,id',
                'supplier_id' => 'nullable|integer|exists:suppliers,id',
                'brand_id' => 'nullable|integer|exists:brands,id',
                'min_price' => 'nullable|numeric|min:0',
                'max_price' => 'nullable|numeric|min:0',
                'sort_by' => 'nullable|in:price,created_at,name_ar,name_en,stock',
                'sort_order' => 'nullable|in:asc,desc',
                'status' => 'nullable|in:active,inactive',
                'in_stock' => 'nullable|boolean'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid parameters',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Clean and sanitize filters
            $filters = $this->sanitizeFilters($request->all());
            
            $products = $this->productService->getProducts($filters);
            
            return ProductResource::collection($products);
            
        } catch (\Exception $e) {
            Log::error('Error in ProductController@index: ' . $e->getMessage(), [
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving products',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        try {
            $product = $this->productService->createProduct($request->validated());

            return response()->json([
                'message' => 'Product created successfully',
                'product' => new ProductResource($product),
            ], 201);
            
        } catch (\Exception $e) {
            Log::error('Error in ProductController@store: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating product',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Display the specified product.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productService->getProduct($id);

            return response()->json([
                'product' => new ProductResource($product),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in ProductController@show: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Product not found',
                'error' => config('app.debug') ? $e->getMessage() : 'Product not found'
            ], 404);
        }
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        try {
            $product = $this->productService->updateProduct($id, $request->validated());

            return response()->json([
                'message' => 'Product updated successfully',
                'product' => new ProductResource($product),
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in ProductController@update: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error updating product',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Remove the specified product.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $this->productService->deleteProduct($id);

            return response()->json([
                'message' => 'Product deleted successfully',
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error in ProductController@destroy: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Filter products by various criteria.
     */
    public function filter(Request $request): AnonymousResourceCollection
    {
        try {
            $filters = $this->sanitizeFilters($request->all());
            $products = $this->productService->filterProducts($filters);
            
            return ProductResource::collection($products);
            
        } catch (\Exception $e) {
            Log::error('Error in ProductController@filter: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error filtering products',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Search products.
     */
    public function search(Request $request): AnonymousResourceCollection
    {
        try {
            $query = $request->get('q');
            
            if (empty($query)) {
                return ProductResource::collection(collect([]));
            }
            
            $products = $this->productService->searchProducts($query);
            
            return ProductResource::collection($products);
            
        } catch (\Exception $e) {
            Log::error('Error in ProductController@search: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Error searching products',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Sanitize filters to prevent errors
     */
    private function sanitizeFilters(array $filters): array
    {
        $sanitized = [];
        
        // Only allow specific keys
        $allowedKeys = [
            'per_page', 'page', 'featured', 'lang', 'category_id', 
            'supplier_id', 'brand_id', 'min_price', 'max_price',
            'sort_by', 'sort_order', 'status', 'in_stock'
        ];
        
        foreach ($allowedKeys as $key) {
            if (isset($filters[$key]) && !empty($filters[$key])) {
                $sanitized[$key] = $filters[$key];
            }
        }
        
        // Ensure per_page has a default and maximum
        $sanitized['per_page'] = min((int)($sanitized['per_page'] ?? 15), 100);
        
        return $sanitized;
    }
} 