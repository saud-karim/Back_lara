<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    public function __construct(
        private ProductRepository $productRepository
    ) {}

    /**
     * Get all products with pagination.
     */
    public function getProducts(array $filters = []): LengthAwarePaginator
    {
        return $this->productRepository->getProducts($filters);
    }

    /**
     * Create a new product.
     */
    public function createProduct(array $data): Product
    {
        return $this->productRepository->create($data);
    }

    /**
     * Get product by ID.
     */
    public function getProduct(int $id): Product
    {
        return $this->productRepository->find($id);
    }

    /**
     * Update product.
     */
    public function updateProduct(int $id, array $data): Product
    {
        return $this->productRepository->update($id, $data);
    }

    /**
     * Delete product.
     */
    public function deleteProduct(int $id): bool
    {
        return $this->productRepository->delete($id);
    }

    /**
     * Filter products by criteria.
     */
    public function filterProducts(array $filters): LengthAwarePaginator
    {
        return $this->productRepository->filter($filters);
    }

    /**
     * Search products.
     */
    public function searchProducts(string $query): LengthAwarePaginator
    {
        return $this->productRepository->search($query);
    }

    /**
     * Update product stock.
     */
    public function updateStock(int $productId, int $quantity): bool
    {
        $product = $this->getProduct($productId);
        return $product->decreaseStock($quantity);
    }

    /**
     * Get products by category.
     */
    public function getProductsByCategory(int $categoryId): Collection
    {
        return $this->productRepository->getByCategory($categoryId);
    }

    /**
     * Get products by supplier.
     */
    public function getProductsBySupplier(int $supplierId): Collection
    {
        return $this->productRepository->getBySupplier($supplierId);
    }

    /**
     * Get low stock products.
     */
    public function getLowStockProducts(int $threshold = 10): Collection
    {
        return $this->productRepository->getLowStock($threshold);
    }
} 