<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\BannerModel;
use App\Models\TestimonialModel;

class Home extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $bannerModel;
    protected $testimonialModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->bannerModel = new BannerModel();
        $this->testimonialModel = new TestimonialModel();
    }

    public function index(): string
    {
        $data = [
            'title' => 'Microdose Mushroom - Premium Microdose Products Online',
            'banners' => $this->bannerModel->getSliderBanners(5),
            'featuredProducts' => $this->productModel->getFeaturedProducts(8),
            'categories' => $this->categoryModel->getCategoriesWithProductCount(),
            'latestProducts' => $this->productModel->getActiveProducts(12),
            'testimonials' => $this->testimonialModel->getFeaturedTestimonials(6)
        ];

        return view('home/index', $data);
    }
}
