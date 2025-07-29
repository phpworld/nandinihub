<?php

namespace App\Controllers\Api;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\ReviewModel;

class ProductApiController extends BaseApiController
{
    protected $productModel;
    protected $categoryModel;
    protected $reviewModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->reviewModel = new ReviewModel();
    }

    /**
     * Get all products with pagination and filters
     */
    public function index()
    {
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 20);
        $categoryId = $this->request->getGet('category_id');
        $search = $this->request->getGet('search');
        $featured = $this->request->getGet('featured');
        $sortBy = $this->request->getGet('sort_by') ?? 'created_at';
        $sortOrder = $this->request->getGet('sort_order') ?? 'desc';

        // Validate pagination
        if ($page < 1) $page = 1;
        if ($perPage < 1 || $perPage > 100) $perPage = 20;

        $offset = ($page - 1) * $perPage;

        // Build query
        $builder = $this->productModel->builder();
        $builder->where('is_active', 1);

        // Apply filters
        if ($categoryId) {
            $builder->where('category_id', $categoryId);
        }

        if ($search) {
            $builder->groupStart()
                    ->like('name', $search)
                    ->orLike('description', $search)
                    ->orLike('short_description', $search)
                    ->groupEnd();
        }

        if ($featured === '1' || $featured === 'true') {
            $builder->where('is_featured', 1);
        }

        // Get total count
        $total = $builder->countAllResults(false);

        // Apply sorting and pagination
        $validSortFields = ['name', 'price', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $validSortFields)) {
            $sortBy = 'created_at';
        }
        
        $sortOrder = strtolower($sortOrder) === 'asc' ? 'ASC' : 'DESC';
        
        $builder->orderBy($sortBy, $sortOrder)
                ->limit($perPage, $offset);

        $products = $builder->get()->getResultArray();

        // Add category information and format data
        foreach ($products as &$product) {
            $category = $this->categoryModel->find($product['category_id']);
            $product['category'] = $category ? [
                'id' => $category['id'],
                'name' => $category['name'],
                'slug' => $category['slug']
            ] : null;

            // Format price
            $product['price'] = (float) $product['price'];
            $product['sale_price'] = $product['sale_price'] ? (float) $product['sale_price'] : null;
            
            // Parse gallery images
            $product['gallery'] = $product['gallery'] ? json_decode($product['gallery'], true) : [];
            
            // Add image URLs
            $product['image_url'] = $product['image'] ? base_url('uploads/products/' . $product['image']) : null;
            $product['gallery_urls'] = [];
            foreach ($product['gallery'] as $image) {
                $product['gallery_urls'][] = base_url('uploads/products/' . $image);
            }
        }

        return $this->paginatedResponse($products, $page, $perPage, $total, 'Products retrieved successfully');
    }

    /**
     * Get single product by ID or slug
     */
    public function show($id = null)
    {
        if (!$id) {
            return $this->errorResponse('Product ID is required', 400);
        }

        // Try to find by ID first, then by slug
        $product = $this->productModel->find($id);
        if (!$product) {
            $product = $this->productModel->where('slug', $id)->first();
        }

        if (!$product || !$product['is_active']) {
            return $this->notFoundResponse('Product not found');
        }

        // Add category information
        $category = $this->categoryModel->find($product['category_id']);
        $product['category'] = $category ? [
            'id' => $category['id'],
            'name' => $category['name'],
            'slug' => $category['slug']
        ] : null;

        // Add variation information
        $variantModel = new \App\Models\ProductVariantModel();
        $product['variants'] = $variantModel->getVariantsWithOptionsByProduct($product['id']);
        $product['has_variants'] = !empty($product['variants']);

        // Add variation types
        $productModel = new \App\Models\ProductModel();
        $product['variation_types'] = $productModel->getProductVariationTypes($product['id']);

        // Format price
        $product['price'] = (float) $product['price'];
        $product['sale_price'] = $product['sale_price'] ? (float) $product['sale_price'] : null;
        
        // Parse gallery images
        $product['gallery'] = $product['gallery'] ? json_decode($product['gallery'], true) : [];
        
        // Add image URLs
        $product['image_url'] = $product['image'] ? base_url('uploads/products/' . $product['image']) : null;
        $product['gallery_urls'] = [];
        foreach ($product['gallery'] as $image) {
            $product['gallery_urls'][] = base_url('uploads/products/' . $image);
        }

        // Get product reviews
        $reviews = $this->reviewModel->getProductReviews($product['id']);
        $product['reviews'] = $reviews;
        $product['average_rating'] = $this->reviewModel->getAverageRating($product['id']);
        $product['total_reviews'] = count($reviews);

        return $this->successResponse($product, 'Product retrieved successfully');
    }

    /**
     * Get product variant by selected options
     */
    public function getVariantByOptions($productId = null)
    {
        if (!$productId) {
            return $this->errorResponse('Product ID is required', 400);
        }

        $optionIds = $this->request->getGet('options');
        if (empty($optionIds)) {
            return $this->errorResponse('Variation options are required', 400);
        }

        // Convert comma-separated string to array
        if (is_string($optionIds)) {
            $optionIds = explode(',', $optionIds);
        }
        $optionIds = array_map('intval', $optionIds);

        $variantOptionModel = new \App\Models\ProductVariantOptionModel();
        $variant = $variantOptionModel->getVariantByOptions($productId, $optionIds);

        if (!$variant) {
            return $this->errorResponse('No variant found for selected options', 404);
        }

        // Get variant with options
        $variantModel = new \App\Models\ProductVariantModel();
        $variantWithOptions = $variantModel->getVariantWithOptions($variant['id']);

        return $this->successResponse($variantWithOptions);
    }

    /**
     * Get featured products
     */
    public function featured()
    {
        $limit = (int) ($this->request->getGet('limit') ?? 10);
        if ($limit < 1 || $limit > 50) $limit = 10;

        $products = $this->productModel->getFeaturedProducts($limit);

        // Format products
        foreach ($products as &$product) {
            $category = $this->categoryModel->find($product['category_id']);
            $product['category'] = $category ? [
                'id' => $category['id'],
                'name' => $category['name'],
                'slug' => $category['slug']
            ] : null;

            $product['price'] = (float) $product['price'];
            $product['sale_price'] = $product['sale_price'] ? (float) $product['sale_price'] : null;
            $product['gallery'] = $product['gallery'] ? json_decode($product['gallery'], true) : [];
            $product['image_url'] = $product['image'] ? base_url('uploads/products/' . $product['image']) : null;
        }

        return $this->successResponse($products, 'Featured products retrieved successfully');
    }

    /**
     * Search products
     */
    public function search()
    {
        $query = $this->request->getGet('q');
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 20);

        if (!$query) {
            return $this->errorResponse('Search query is required', 400);
        }

        if ($page < 1) $page = 1;
        if ($perPage < 1 || $perPage > 100) $perPage = 20;

        $offset = ($page - 1) * $perPage;

        // Search products
        $builder = $this->productModel->builder();
        $builder->where('is_active', 1)
                ->groupStart()
                ->like('name', $query)
                ->orLike('description', $query)
                ->orLike('short_description', $query)
                ->groupEnd();

        $total = $builder->countAllResults(false);

        $products = $builder->orderBy('name', 'ASC')
                           ->limit($perPage, $offset)
                           ->get()
                           ->getResultArray();

        // Format products
        foreach ($products as &$product) {
            $category = $this->categoryModel->find($product['category_id']);
            $product['category'] = $category ? [
                'id' => $category['id'],
                'name' => $category['name'],
                'slug' => $category['slug']
            ] : null;

            $product['price'] = (float) $product['price'];
            $product['sale_price'] = $product['sale_price'] ? (float) $product['sale_price'] : null;
            $product['gallery'] = $product['gallery'] ? json_decode($product['gallery'], true) : [];
            $product['image_url'] = $product['image'] ? base_url('uploads/products/' . $product['image']) : null;
        }

        return $this->paginatedResponse($products, $page, $perPage, $total, 'Search results retrieved successfully');
    }

    /**
     * Get products by category
     */
    public function byCategory($categoryId = null)
    {
        if (!$categoryId) {
            return $this->errorResponse('Category ID is required', 400);
        }

        // Check if category exists
        $category = $this->categoryModel->find($categoryId);
        if (!$category || !$category['is_active']) {
            return $this->notFoundResponse('Category not found');
        }

        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 20);

        if ($page < 1) $page = 1;
        if ($perPage < 1 || $perPage > 100) $perPage = 20;

        $offset = ($page - 1) * $perPage;

        // Get products in category
        $builder = $this->productModel->builder();
        $builder->where('category_id', $categoryId)
                ->where('is_active', 1);

        $total = $builder->countAllResults(false);

        $products = $builder->orderBy('name', 'ASC')
                           ->limit($perPage, $offset)
                           ->get()
                           ->getResultArray();

        // Format products
        foreach ($products as &$product) {
            $product['category'] = [
                'id' => $category['id'],
                'name' => $category['name'],
                'slug' => $category['slug']
            ];

            $product['price'] = (float) $product['price'];
            $product['sale_price'] = $product['sale_price'] ? (float) $product['sale_price'] : null;
            $product['gallery'] = $product['gallery'] ? json_decode($product['gallery'], true) : [];
            $product['image_url'] = $product['image'] ? base_url('uploads/products/' . $product['image']) : null;
        }

        return $this->paginatedResponse($products, $page, $perPage, $total, 'Category products retrieved successfully');
    }
}
