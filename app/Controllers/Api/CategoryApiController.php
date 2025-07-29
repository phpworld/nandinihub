<?php

namespace App\Controllers\Api;

use App\Models\CategoryModel;
use App\Models\ProductModel;

class CategoryApiController extends BaseApiController
{
    protected $categoryModel;
    protected $productModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
        $this->productModel = new ProductModel();
    }

    /**
     * Get all active categories
     */
    public function index()
    {
        $categories = $this->categoryModel->getActiveCategories();

        // Add product count and image URLs for each category
        foreach ($categories as &$category) {
            // Get product count
            $category['product_count'] = $this->productModel->where('category_id', $category['id'])
                ->where('is_active', 1)
                ->countAllResults();

            // Add image URL
            $category['image_url'] = $category['image'] ? base_url('uploads/categories/' . $category['image']) : null;

            // Format dates
            $category['created_at'] = date('Y-m-d H:i:s', strtotime($category['created_at']));
            $category['updated_at'] = date('Y-m-d H:i:s', strtotime($category['updated_at']));
        }

        return $this->successResponse($categories, 'Categories retrieved successfully');
    }

    /**
     * Get single category by ID or slug
     */
    public function show($id = null)
    {
        if (!$id) {
            return $this->errorResponse('Category ID is required', 400);
        }

        // Try to find by ID first, then by slug
        $category = $this->categoryModel->find($id);
        if (!$category) {
            $category = $this->categoryModel->where('slug', $id)->first();
        }

        if (!$category || !$category['is_active']) {
            return $this->notFoundResponse('Category not found');
        }

        // Add product count
        $category['product_count'] = $this->productModel->where('category_id', $category['id'])
            ->where('is_active', 1)
            ->countAllResults();

        // Add image URL
        $category['image_url'] = $category['image'] ? base_url('uploads/categories/' . $category['image']) : null;

        // Format dates
        $category['created_at'] = date('Y-m-d H:i:s', strtotime($category['created_at']));
        $category['updated_at'] = date('Y-m-d H:i:s', strtotime($category['updated_at']));

        return $this->successResponse($category, 'Category retrieved successfully');
    }

    /**
     * Get category tree (flat structure since no parent_id column exists)
     */
    public function tree()
    {
        $categories = $this->categoryModel->getActiveCategories();

        // Add product count and image URLs for each category
        foreach ($categories as &$category) {
            // Get product count
            $category['product_count'] = $this->productModel->where('category_id', $category['id'])
                ->where('is_active', 1)
                ->countAllResults();

            // Add image URL
            $category['image_url'] = $category['image'] ? base_url('uploads/categories/' . $category['image']) : null;

            // Since no parent_id column exists, return as flat structure
            $category['children'] = [];
        }

        return $this->successResponse($categories, 'Category tree retrieved successfully');
    }

    /**
     * Get popular categories (based on product count)
     */
    public function popular()
    {
        $limit = (int) ($this->request->getGet('limit') ?? 10);
        if ($limit < 1 || $limit > 50) $limit = 10;

        $categories = $this->categoryModel->getActiveCategories();

        // Add product count for each category
        foreach ($categories as &$category) {
            $category['product_count'] = $this->productModel->where('category_id', $category['id'])
                ->where('is_active', 1)
                ->countAllResults();

            $category['image_url'] = $category['image'] ? base_url('uploads/categories/' . $category['image']) : null;
        }

        // Sort by product count (descending) and limit
        usort($categories, function ($a, $b) {
            return $b['product_count'] - $a['product_count'];
        });

        $categories = array_slice($categories, 0, $limit);

        return $this->successResponse($categories, 'Popular categories retrieved successfully');
    }

    /**
     * Search categories
     */
    public function search()
    {
        $query = $this->request->getGet('q');

        if (!$query) {
            return $this->errorResponse('Search query is required', 400);
        }

        $builder = $this->categoryModel->builder();
        $categories = $builder->where('is_active', 1)
            ->groupStart()
            ->like('name', $query)
            ->orLike('description', $query)
            ->groupEnd()
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        // Add product count and image URLs
        foreach ($categories as &$category) {
            $category['product_count'] = $this->productModel->where('category_id', $category['id'])
                ->where('is_active', 1)
                ->countAllResults();

            $category['image_url'] = $category['image'] ? base_url('uploads/categories/' . $category['image']) : null;
        }

        return $this->successResponse($categories, 'Search results retrieved successfully');
    }
}
