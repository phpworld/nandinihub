<?php

namespace App\Controllers;

use App\Models\WishlistModel;
use App\Models\ProductModel;

class WishlistController extends BaseController
{
    protected $wishlistModel;
    protected $productModel;

    public function __construct()
    {
        $this->wishlistModel = new WishlistModel();
        $this->productModel = new ProductModel();
    }

    /**
     * Display user's wishlist
     */
    public function index()
    {
        if (!session()->get('user_id')) {
            session()->set('redirect_to', current_url());
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');
        $wishlistItems = $this->wishlistModel->getWishlistItems($userId);

        $data = [
            'title' => 'My Wishlist - Nandini Hub',
            'wishlistItems' => $wishlistItems,
            'wishlistCount' => count($wishlistItems)
        ];

        return view('user/wishlist/index', $data);
    }

    /**
     * Add product to wishlist (AJAX)
     */
    public function add()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        if (!session()->get('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to add items to wishlist',
                'redirect' => base_url('login')
            ]);
        }

        $userId = session()->get('user_id');
        $productId = $this->request->getPost('product_id');

        // Validate product exists and is active
        $product = $this->productModel->find($productId);
        if (!$product || !$product['is_active']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Product not found or unavailable'
            ]);
        }

        if ($this->wishlistModel->addToWishlist($userId, $productId)) {
            $wishlistCount = $this->wishlistModel->getWishlistCount($userId);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Product added to wishlist',
                'wishlistCount' => $wishlistCount,
                'in_wishlist' => true
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Product is already in your wishlist'
            ]);
        }
    }

    /**
     * Remove product from wishlist (AJAX)
     */
    public function remove()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        if (!session()->get('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login first'
            ]);
        }

        $userId = session()->get('user_id');
        $productId = $this->request->getPost('product_id');

        if ($this->wishlistModel->removeFromWishlist($userId, $productId)) {
            $wishlistCount = $this->wishlistModel->getWishlistCount($userId);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Product removed from wishlist',
                'wishlistCount' => $wishlistCount,
                'in_wishlist' => false
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to remove product from wishlist'
            ]);
        }
    }

    /**
     * Toggle wishlist status (AJAX)
     */
    public function toggle()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        if (!session()->get('user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please login to manage wishlist',
                'redirect' => base_url('login')
            ]);
        }

        $userId = session()->get('user_id');
        $productId = $this->request->getPost('product_id');

        // Validate product exists and is active
        $product = $this->productModel->find($productId);
        if (!$product || !$product['is_active']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Product not found or unavailable'
            ]);
        }

        $result = $this->wishlistModel->toggleWishlist($userId, $productId);
        $wishlistCount = $this->wishlistModel->getWishlistCount($userId);

        $message = $result['action'] === 'added' ? 'Product added to wishlist' : 'Product removed from wishlist';

        return $this->response->setJSON([
            'success' => true,
            'message' => $message,
            'action' => $result['action'],
            'in_wishlist' => $result['in_wishlist'],
            'wishlistCount' => $wishlistCount
        ]);
    }

    /**
     * Clear entire wishlist
     */
    public function clear()
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');

        if ($this->wishlistModel->clearWishlist($userId)) {
            session()->setFlashdata('success', 'Wishlist cleared successfully.');
        } else {
            session()->setFlashdata('error', 'Failed to clear wishlist.');
        }

        return redirect()->to('/wishlist');
    }

    /**
     * Get wishlist count (AJAX)
     */
    public function getCount()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $userId = session()->get('user_id');
        $count = $userId ? $this->wishlistModel->getWishlistCount($userId) : 0;

        return $this->response->setJSON([
            'success' => true,
            'count' => $count
        ]);
    }

    /**
     * Check if product is in wishlist (AJAX)
     */
    public function check()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $userId = session()->get('user_id');
        $productId = $this->request->getPost('product_id');

        $inWishlist = $this->wishlistModel->isInWishlist($userId, $productId);

        return $this->response->setJSON([
            'success' => true,
            'in_wishlist' => $inWishlist
        ]);
    }
}
