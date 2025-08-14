<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\ProductModel;
use App\Libraries\ShippingService;

class CartController extends BaseController
{
    protected $cartModel;
    protected $productModel;
    protected $shippingService;

    public function __construct()
    {
        $this->cartModel = new CartModel();
        $this->productModel = new ProductModel();
        $this->shippingService = new ShippingService();
    }

    public function index()
    {
        $userId = session()->get('user_id');
        $sessionId = session()->session_id;

        $cartItems = $this->cartModel->getCartItemsWithDetails($userId, $sessionId);
        $cartTotal = $this->cartModel->getCartTotal($userId, $sessionId);

        // Get cheapest shipping cost for display
        $shippingInfo = $this->shippingService->getCheapestShippingCost($cartTotal);

        $data = [
            'title' => 'Shopping Cart - Microdose Mushroom',
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'shippingInfo' => $shippingInfo
        ];

        return view('cart/index', $data);
    }

    /**
     * AJAX endpoint to get shipping cost for updated cart total
     */
    public function getShippingCost()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $subtotal = (float) $this->request->getPost('subtotal');

        if ($subtotal <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid subtotal'
            ]);
        }

        try {
            $shippingInfo = $this->shippingService->getCheapestShippingCost($subtotal);

            return $this->response->setJSON([
                'success' => true,
                'shipping_cost' => $shippingInfo['cost'],
                'method_name' => $shippingInfo['method_name'],
                'is_free' => $shippingInfo['is_free'],
                'method_id' => $shippingInfo['method_id']
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error getting shipping cost: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error calculating shipping cost'
            ]);
        }
    }

    public function add()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $productId = $this->request->getPost('product_id');
        $variantId = $this->request->getPost('variant_id');
        $variantOptions = $this->request->getPost('variant_options');
        $quantity = $this->request->getPost('quantity') ?? 1;

        // Validate product exists and is active
        $product = $this->productModel->find($productId);
        if (!$product || !$product['is_active']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Product not found or unavailable'
            ]);
        }

        $price = $product['sale_price'] ?? $product['price'];
        $stockQuantity = $product['stock_quantity'];

        // Handle variant if specified
        if ($variantId) {
            $variantModel = new \App\Models\ProductVariantModel();
            $variant = $variantModel->find($variantId);

            if (!$variant || !$variant['is_active'] || $variant['product_id'] != $productId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Product variant not found or unavailable'
                ]);
            }

            // Use variant price and stock
            $price = $variant['sale_price'] ?? $variant['price'] ?? $price;
            $stockQuantity = $variant['stock_quantity'];
        }

        // Check stock
        if ($stockQuantity < $quantity) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Insufficient stock available'
            ]);
        }

        $userId = session()->get('user_id');
        $sessionId = session()->session_id;

        $cartData = [
            'product_id' => $productId,
            'variant_id' => $variantId,
            'variant_options' => $variantOptions ? json_encode($variantOptions) : null,
            'quantity' => $quantity,
            'price' => $price
        ];

        if ($userId) {
            $cartData['user_id'] = $userId;
        } else {
            $cartData['session_id'] = $sessionId;
        }

        if ($this->cartModel->addToCart($cartData)) {
            $cartCount = $this->cartModel->getCartCount($userId, $sessionId);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Product added to cart successfully',
                'cartCount' => $cartCount
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to add product to cart'
            ]);
        }
    }

    public function update()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $cartId = $this->request->getPost('cart_id');
        $quantity = $this->request->getPost('quantity');

        if ($quantity <= 0) {
            return $this->remove();
        }

        if ($this->cartModel->updateCartItem($cartId, $quantity)) {
            $userId = session()->get('user_id');
            $sessionId = session()->session_id;
            $cartTotal = $this->cartModel->getCartTotal($userId, $sessionId);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Cart updated successfully',
                'cartTotal' => $cartTotal
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update cart'
            ]);
        }
    }

    public function remove()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $cartId = $this->request->getPost('cart_id');

        if ($this->cartModel->removeCartItem($cartId)) {
            $userId = session()->get('user_id');
            $sessionId = session()->session_id;
            $cartCount = $this->cartModel->getCartCount($userId, $sessionId);
            $cartTotal = $this->cartModel->getCartTotal($userId, $sessionId);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Item removed from cart',
                'cartCount' => $cartCount,
                'cartTotal' => $cartTotal
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to remove item from cart'
            ]);
        }
    }

    public function clear()
    {
        $userId = session()->get('user_id');
        $sessionId = session()->session_id;

        if ($this->cartModel->clearCart($userId, $sessionId)) {
            session()->setFlashdata('success', 'Cart cleared successfully');
        } else {
            session()->setFlashdata('error', 'Failed to clear cart');
        }

        return redirect()->to('/cart');
    }

    public function getCartCount()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $userId = session()->get('user_id');
        $sessionId = session()->session_id;
        $cartCount = $this->cartModel->getCartCount($userId, $sessionId);

        return $this->response->setJSON([
            'cartCount' => $cartCount
        ]);
    }
}
