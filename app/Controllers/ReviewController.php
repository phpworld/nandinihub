<?php

namespace App\Controllers;

use App\Models\ReviewModel;
use App\Models\ProductModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;

class ReviewController extends BaseController
{
    protected $reviewModel;
    protected $productModel;
    protected $orderModel;
    protected $orderItemModel;

    public function __construct()
    {
        $this->reviewModel = new ReviewModel();
        $this->productModel = new ProductModel();
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
    }

    public function create($productSlug)
    {
        if (!session()->get('user_id')) {
            session()->set('redirect_to', current_url());
            return redirect()->to('/login');
        }

        $product = $this->productModel->getProductBySlug($productSlug);
        if (!$product) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Product not found');
        }

        $userId = session()->get('user_id');

        // Check if user can review this product
        if (!$this->reviewModel->canUserReview($product['id'], $userId)) {
            session()->setFlashdata('error', 'You can only review products you have purchased, or you have already reviewed this product.');
            return redirect()->to('/product/' . $productSlug);
        }

        $data = [
            'title' => 'Write Review for ' . $product['name'] . ' - Microdose Mushroom',
            'product' => $product
        ];

        return view('reviews/create', $data);
    }

    public function store()
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        $rules = [
            'product_id' => 'required|integer',
            'rating' => 'required|integer|greater_than[0]|less_than[6]',
            'title' => 'permit_empty|max_length[255]',
            'review' => 'permit_empty|max_length[1000]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userId = session()->get('user_id');
        $productId = $this->request->getPost('product_id');

        // Check if user can review this product
        if (!$this->reviewModel->canUserReview($productId, $userId)) {
            session()->setFlashdata('error', 'You can only review products you have purchased, or you have already reviewed this product.');
            return redirect()->back();
        }

        // Find the order for verification
        $orderItem = $this->orderItemModel->select('order_items.order_id')
            ->join('orders', 'orders.id = order_items.order_id')
            ->where('order_items.product_id', $productId)
            ->where('orders.user_id', $userId)
            ->whereIn('orders.status', ['delivered', 'processing', 'shipped'])
            ->first();

        $reviewData = [
            'product_id' => $productId,
            'user_id' => $userId,
            'order_id' => $orderItem['order_id'] ?? null,
            'rating' => $this->request->getPost('rating'),
            'title' => $this->request->getPost('title'),
            'review' => $this->request->getPost('review'),
            'is_verified' => $orderItem ? 1 : 0,
            'is_approved' => 1 // Auto-approve for now
        ];

        if ($this->reviewModel->insert($reviewData)) {
            session()->setFlashdata('success', 'Thank you for your review! It has been submitted successfully.');

            $product = $this->productModel->find($productId);
            return redirect()->to('/product/' . $product['slug']);
        } else {
            session()->setFlashdata('error', 'Failed to submit review. Please try again.');
            return redirect()->back()->withInput();
        }
    }

    public function helpful($reviewId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        if ($this->reviewModel->incrementHelpfulCount($reviewId)) {
            $review = $this->reviewModel->find($reviewId);
            return $this->response->setJSON([
                'success' => true,
                'helpful_count' => $review['helpful_count']
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update helpful count'
            ]);
        }
    }

    public function getProductReviews($productId)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $reviews = $this->reviewModel->getProductReviews($productId, 10);
        $stats = $this->reviewModel->getProductRatingStats($productId);

        return $this->response->setJSON([
            'reviews' => $reviews,
            'stats' => $stats
        ]);
    }
}
