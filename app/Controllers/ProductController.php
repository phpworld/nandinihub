<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;

class ProductController extends BaseController
{
    protected $productModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }

    public function index()
    {
        // Get filter parameters
        $filters = [
            'category_id' => $this->request->getGet('category'),
            'min_price' => $this->request->getGet('min_price'),
            'max_price' => $this->request->getGet('max_price'),
            'search' => $this->request->getGet('q'),
            'sort' => $this->request->getGet('sort')
        ];

        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return $value !== null && $value !== '';
        });

        $data = [
            'title' => 'All Products - Nandini Hub',
            'products' => $this->productModel->getProductsWithFilters($filters),
            'categories' => $this->categoryModel->getActiveCategories(),
            'priceRange' => $this->productModel->getPriceRange(),
            'currentFilters' => $filters
        ];

        return view('products/index', $data);
    }

    public function show($slug)
    {
        $product = $this->productModel->getProductBySlugWithVariants($slug);

        if (!$product) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Product not found');
        }

        // Get variation types for this product
        $variationTypes = $this->productModel->getProductVariationTypes($product['id']);

        // Get related products from same category
        $relatedProducts = $this->productModel->getProductsByCategory($product['category_id'], 4);

        // Remove current product from related products
        $relatedProducts = array_filter($relatedProducts, function($p) use ($product) {
            return $p['id'] != $product['id'];
        });

        $data = [
            'title' => $product['name'] . ' - Nandini Hub',
            'product' => $product,
            'variationTypes' => $variationTypes,
            'relatedProducts' => array_slice($relatedProducts, 0, 3)
        ];

        return view('products/show', $data);
    }

    public function category($slug)
    {
        $category = $this->categoryModel->getCategoryBySlug($slug);

        if (!$category) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Category not found');
        }

        // Get filter parameters
        $filters = [
            'category_id' => $category['id'],
            'min_price' => $this->request->getGet('min_price'),
            'max_price' => $this->request->getGet('max_price'),
            'search' => $this->request->getGet('q'),
            'sort' => $this->request->getGet('sort')
        ];

        // Remove empty filters (except category_id)
        $filters = array_filter($filters, function($value, $key) {
            return $key === 'category_id' || ($value !== null && $value !== '');
        }, ARRAY_FILTER_USE_BOTH);

        $data = [
            'title' => $category['name'] . ' - Nandini Hub',
            'category' => $category,
            'products' => $this->productModel->getProductsWithFilters($filters),
            'categories' => $this->categoryModel->getActiveCategories(),
            'priceRange' => $this->productModel->getPriceRange(),
            'currentFilters' => $filters
        ];

        return view('products/category', $data);
    }

    public function search()
    {
        $keyword = $this->request->getGet('q');

        if (empty($keyword)) {
            return redirect()->to('/products');
        }

        // Get filter parameters
        $filters = [
            'category_id' => $this->request->getGet('category'),
            'min_price' => $this->request->getGet('min_price'),
            'max_price' => $this->request->getGet('max_price'),
            'search' => $keyword,
            'sort' => $this->request->getGet('sort')
        ];

        // Remove empty filters (except search)
        $filters = array_filter($filters, function($value, $key) {
            return $key === 'search' || ($value !== null && $value !== '');
        }, ARRAY_FILTER_USE_BOTH);

        $data = [
            'title' => 'Search Results for "' . $keyword . '" - Nandini Hub',
            'keyword' => $keyword,
            'products' => $this->productModel->getProductsWithFilters($filters),
            'categories' => $this->categoryModel->getActiveCategories(),
            'priceRange' => $this->productModel->getPriceRange(),
            'currentFilters' => $filters
        ];

        return view('products/search', $data);
    }
}
