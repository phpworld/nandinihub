<?php

namespace App\Controllers\Api;

use App\Models\CartModel;
use App\Models\ProductModel;

class CartApiController extends BaseApiController
{
    protected $cartModel;
    protected $productModel;

    public function __construct()
    {
        $this->cartModel = new CartModel();
        $this->productModel = new ProductModel();
    }

    /**
     * Get user's cart items
     */
    public function index()
    {
        $userId = $this->getAuthenticatedUserId();

        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        $cartItems = $this->cartModel->getUserCartItems($userId);
        $cartSummary = $this->cartModel->getCartSummary($userId);

        // Format cart items with product details
        $formattedItems = [];
        foreach ($cartItems as $item) {
            $product = $this->productModel->find($item['product_id']);

            if ($product && $product['is_active']) {
                $formattedItems[] = [
                    'id' => $item['id'],
                    'product_id' => $item['product_id'],
                    'quantity' => (int) $item['quantity'],
                    'price' => (float) $item['price'],
                    'total' => (float) ($item['price'] * $item['quantity']),
                    'product' => [
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'slug' => $product['slug'],
                        'price' => (float) $product['price'],
                        'sale_price' => $product['sale_price'] ? (float) $product['sale_price'] : null,
                        'image' => $product['image'],
                        'image_url' => $product['image'] ? base_url('uploads/products/' . $product['image']) : null,
                        'stock_quantity' => (int) $product['stock_quantity'],
                        'weight' => $product['weight']
                    ]
                ];
            }
        }

        $response = [
            'items' => $formattedItems,
            'summary' => [
                'total_items' => $cartSummary['total_items'],
                'subtotal' => (float) $cartSummary['subtotal'],
                'total' => (float) $cartSummary['total']
            ]
        ];

        return $this->successResponse($response, 'Cart retrieved successfully');
    }

    /**
     * Add item to cart
     */
    public function add()
    {
        $userId = $this->getAuthenticatedUserId();

        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        $input = $this->request->getJSON(true) ?? $this->request->getPost();

        // Validate required fields
        $requiredFields = ['product_id', 'quantity'];
        $errors = $this->validateRequired($input, $requiredFields);

        if (!empty($errors)) {
            return $this->validationErrorResponse($errors);
        }

        $productId = (int) $input['product_id'];
        $quantity = (int) $input['quantity'];

        // Validate quantity
        if ($quantity <= 0) {
            return $this->errorResponse('Quantity must be greater than 0', 400);
        }

        // Check if product exists and is active
        $product = $this->productModel->find($productId);
        if (!$product || !$product['is_active']) {
            return $this->notFoundResponse('Product not found');
        }

        // Check stock availability
        if ($product['stock_quantity'] < $quantity) {
            return $this->errorResponse('Insufficient stock. Available: ' . $product['stock_quantity'], 400);
        }

        // Add to cart
        $price = $product['sale_price'] ?: $product['price'];
        $cartData = [
            'user_id' => $userId,
            'product_id' => $productId,
            'quantity' => $quantity,
            'price' => $price
        ];
        $added = $this->cartModel->addToCart($cartData);

        if (!$added) {
            return $this->errorResponse('Failed to add item to cart', 500);
        }

        // Get updated cart summary
        $cartSummary = $this->cartModel->getCartSummary($userId);

        return $this->successResponse([
            'cart_summary' => [
                'total_items' => $cartSummary['total_items'],
                'subtotal' => (float) $cartSummary['subtotal'],
                'total' => (float) $cartSummary['total']
            ]
        ], 'Item added to cart successfully');
    }

    /**
     * Update cart item quantity
     */
    public function update($itemId = null)
    {
        $userId = $this->getAuthenticatedUserId();

        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        if (!$itemId) {
            return $this->errorResponse('Cart item ID is required', 400);
        }

        $input = $this->request->getJSON(true) ?? $this->request->getPost();

        if (!isset($input['quantity'])) {
            return $this->errorResponse('Quantity is required', 400);
        }

        $quantity = (int) $input['quantity'];

        // Validate quantity
        if ($quantity <= 0) {
            return $this->errorResponse('Quantity must be greater than 0', 400);
        }

        // Check if cart item belongs to user
        $cartItem = $this->cartModel->where('id', $itemId)
            ->where('user_id', $userId)
            ->first();

        if (!$cartItem) {
            return $this->notFoundResponse('Cart item not found');
        }

        // Check product stock
        $product = $this->productModel->find($cartItem['product_id']);
        if (!$product || !$product['is_active']) {
            return $this->errorResponse('Product is no longer available', 400);
        }

        if ($product['stock_quantity'] < $quantity) {
            return $this->errorResponse('Insufficient stock. Available: ' . $product['stock_quantity'], 400);
        }

        // Update quantity
        $updated = $this->cartModel->update($itemId, ['quantity' => $quantity]);

        if (!$updated) {
            return $this->errorResponse('Failed to update cart item', 500);
        }

        // Get updated cart summary
        $cartSummary = $this->cartModel->getCartSummary($userId);

        return $this->successResponse([
            'cart_summary' => [
                'total_items' => $cartSummary['total_items'],
                'subtotal' => (float) $cartSummary['subtotal'],
                'total' => (float) $cartSummary['total']
            ]
        ], 'Cart item updated successfully');
    }

    /**
     * Remove item from cart
     */
    public function remove($itemId = null)
    {
        $userId = $this->getAuthenticatedUserId();

        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        if (!$itemId) {
            return $this->errorResponse('Cart item ID is required', 400);
        }

        // Check if cart item belongs to user
        $cartItem = $this->cartModel->where('id', $itemId)
            ->where('user_id', $userId)
            ->first();

        if (!$cartItem) {
            return $this->notFoundResponse('Cart item not found');
        }

        // Remove item
        $removed = $this->cartModel->delete($itemId);

        if (!$removed) {
            return $this->errorResponse('Failed to remove cart item', 500);
        }

        // Get updated cart summary
        $cartSummary = $this->cartModel->getCartSummary($userId);

        return $this->successResponse([
            'cart_summary' => [
                'total_items' => $cartSummary['total_items'],
                'subtotal' => (float) $cartSummary['subtotal'],
                'total' => (float) $cartSummary['total']
            ]
        ], 'Cart item removed successfully');
    }

    /**
     * Clear all cart items
     */
    public function clear()
    {
        $userId = $this->getAuthenticatedUserId();

        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        // Clear cart
        $cleared = $this->cartModel->clearUserCart($userId);

        if (!$cleared) {
            return $this->errorResponse('Failed to clear cart', 500);
        }

        return $this->successResponse([
            'cart_summary' => [
                'total_items' => 0,
                'subtotal' => 0.00,
                'total' => 0.00
            ]
        ], 'Cart cleared successfully');
    }

    /**
     * Get cart item count
     */
    public function count()
    {
        $userId = $this->getAuthenticatedUserId();

        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        $count = $this->cartModel->getCartItemCount($userId);

        return $this->successResponse(['count' => $count], 'Cart count retrieved successfully');
    }
}
