<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository
{
    /**
     * Create a new product.
     */
    public function create(array $data): Product
    {
        return Product::create($data);
    }

    /**
     * Find product by ID.
     */
    public function find(int $id): Product
    {
        return Product::with(['category', 'supplier'])->findOrFail($id);
    }

    /**
     * Update product.
     */
    public function update(int $id, array $data): Product
    {
        $product = $this->find($id);
        $product->update($data);
        return $product;
    }

    /**
     * Delete product.
     */
    public function delete(int $id): bool
    {
        $product = $this->find($id);
        return $product->delete();
    }

    /**
     * Get all products with pagination.
     */
    public function getProducts(array $filters = []): LengthAwarePaginator
    {
        $query = Product::with(['category', 'supplier']);

        if (isset($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['supplier_id'])) {
            $query->where('supplier_id', $filters['supplier_id']);
        }

        if (isset($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }

        if (isset($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        if (isset($filters['in_stock'])) {
            $query->where('stock', '>', 0);
        }

        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate($filters['per_page'] ?? 15);
    }

    /**
     * Filter products by criteria.
     */
    public function filter(array $filters): LengthAwarePaginator
    {
        return $this->getProducts($filters);
    }

    /**
     * Search products.
     */
    public function search(string $query): LengthAwarePaginator
    {
        return Product::with(['category', 'supplier'])
            ->where('name', 'like', '%' . $query . '%')
            ->orWhere('description', 'like', '%' . $query . '%')
            ->paginate(15);
    }

    /**
     * Get products by category.
     */
    public function getByCategory(int $categoryId): Collection
    {
        return Product::with(['category', 'supplier'])
            ->where('category_id', $categoryId)
            ->get();
    }

    /**
     * Get products by supplier.
     */
    public function getBySupplier(int $supplierId): Collection
    {
        return Product::with(['category', 'supplier'])
            ->where('supplier_id', $supplierId)
            ->get();
    }

    /**
     * Get low stock products.
     */
    public function getLowStock(int $threshold = 10): Collection
    {
        return Product::with(['category', 'supplier'])
            ->where('stock', '<=', $threshold)
            ->get();
    }

    /**
     * Get products with images.
     */
    public function getWithImages(): Collection
    {
        return Product::with(['category', 'supplier'])
            ->whereNotNull('images')
            ->get();
    }
} 