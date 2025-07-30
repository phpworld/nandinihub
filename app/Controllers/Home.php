<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\BannerModel;

class Home extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $bannerModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->bannerModel = new BannerModel();
    }

    public function index(): string
    {
        $data = [
            'title' => 'Microdose Mushroom - Premium Microdose Products Online',
            'banners' => $this->bannerModel->getSliderBanners(5),
            'featuredProducts' => $this->productModel->getFeaturedProducts(8),
            'categories' => $this->categoryModel->getActiveCategories(),
            'latestProducts' => $this->productModel->getActiveProducts(12)
        ];

        return view('home/index', $data);
    }
}
