<?php

namespace App\Controllers\Api;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\CartModel;
use App\Models\ProductModel;
use App\Models\UserAddressModel;

class OrderApiController extends BaseApiController
{
    protected $orderModel;
    protected $orderItemModel;
    protected $cartModel;
    protected $productModel;
    protected $userAddressModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->cartModel = new CartModel();
        $this->productModel = new ProductModel();
        $this->userAddressModel = new UserAddressModel();
    }

    /**
     * Get user's orders with pagination
     */
    public function index()
    {
        $userId = $this->getAuthenticatedUserId();
        
        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = (int) ($this->request->getGet('per_page') ?? 20);
        $status = $this->request->getGet('status');

        if ($page < 1) $page = 1;
        if ($perPage < 1 || $perPage > 100) $perPage = 20;

        $offset = ($page - 1) * $perPage;

        // Build query
        $builder = $this->orderModel->builder();
        $builder->where('user_id', $userId);

        if ($status) {
            $builder->where('status', $status);
        }

        $total = $builder->countAllResults(false);

        $orders = $builder->orderBy('created_at', 'DESC')
                         ->limit($perPage, $offset)
                         ->get()
                         ->getResultArray();

        // Format orders
        foreach ($orders as &$order) {
            $order['total'] = (float) $order['total'];
            $order['shipping_cost'] = (float) $order['shipping_cost'];
            $order['tax_amount'] = (float) $order['tax_amount'];
            $order['discount_amount'] = (float) $order['discount_amount'];
            
            // Get order items count
            $order['items_count'] = $this->orderItemModel->where('order_id', $order['id'])->countAllResults();
            
            // Parse shipping address
            $order['shipping_address'] = json_decode($order['shipping_address'], true);
            
            // Format dates
            $order['created_at'] = date('Y-m-d H:i:s', strtotime($order['created_at']));
            $order['updated_at'] = date('Y-m-d H:i:s', strtotime($order['updated_at']));
        }

        return $this->paginatedResponse($orders, $page, $perPage, $total, 'Orders retrieved successfully');
    }

    /**
     * Get single order details
     */
    public function show($orderId = null)
    {
        $userId = $this->getAuthenticatedUserId();
        
        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        if (!$orderId) {
            return $this->errorResponse('Order ID is required', 400);
        }

        // Get order
        $order = $this->orderModel->where('id', $orderId)
                                 ->where('user_id', $userId)
                                 ->first();

        if (!$order) {
            return $this->notFoundResponse('Order not found');
        }

        // Format order
        $order['total'] = (float) $order['total'];
        $order['shipping_cost'] = (float) $order['shipping_cost'];
        $order['tax_amount'] = (float) $order['tax_amount'];
        $order['discount_amount'] = (float) $order['discount_amount'];
        $order['shipping_address'] = json_decode($order['shipping_address'], true);

        // Get order items
        $orderItems = $this->orderItemModel->where('order_id', $orderId)->findAll();
        
        $formattedItems = [];
        foreach ($orderItems as $item) {
            $product = $this->productModel->find($item['product_id']);
            
            $formattedItems[] = [
                'id' => $item['id'],
                'product_id' => $item['product_id'],
                'quantity' => (int) $item['quantity'],
                'price' => (float) $item['price'],
                'total' => (float) $item['total'],
                'product' => $product ? [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'slug' => $product['slug'],
                    'image' => $product['image'],
                    'image_url' => $product['image'] ? base_url('uploads/products/' . $product['image']) : null
                ] : null
            ];
        }

        $order['items'] = $formattedItems;

        return $this->successResponse($order, 'Order retrieved successfully');
    }

    /**
     * Create new order from cart
     */
    public function create()
    {
        $userId = $this->getAuthenticatedUserId();
        
        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        
        // Validate required fields
        $requiredFields = ['shipping_address_id'];
        $errors = $this->validateRequired($input, $requiredFields);
        
        if (!empty($errors)) {
            return $this->validationErrorResponse($errors);
        }

        // Get shipping address
        $shippingAddress = $this->userAddressModel->where('id', $input['shipping_address_id'])
                                                  ->where('user_id', $userId)
                                                  ->first();

        if (!$shippingAddress) {
            return $this->errorResponse('Invalid shipping address', 400);
        }

        // Get cart items
        $cartItems = $this->cartModel->getUserCartItems($userId);
        
        if (empty($cartItems)) {
            return $this->errorResponse('Cart is empty', 400);
        }

        // Validate cart items and calculate totals
        $subtotal = 0;
        $validItems = [];
        
        foreach ($cartItems as $item) {
            $product = $this->productModel->find($item['product_id']);
            
            if (!$product || !$product['is_active']) {
                return $this->errorResponse("Product '{$product['name']}' is no longer available", 400);
            }
            
            if ($product['stock_quantity'] < $item['quantity']) {
                return $this->errorResponse("Insufficient stock for '{$product['name']}'. Available: {$product['stock_quantity']}", 400);
            }
            
            $validItems[] = $item;
            $subtotal += $item['price'] * $item['quantity'];
        }

        // Calculate shipping and tax (you can customize this logic)
        $shippingCost = $this->calculateShippingCost($subtotal);
        $taxAmount = $this->calculateTax($subtotal);
        $discountAmount = 0; // You can implement coupon logic here
        $total = $subtotal + $shippingCost + $taxAmount - $discountAmount;

        // Create order
        $orderData = [
            'user_id' => $userId,
            'order_number' => $this->generateOrderNumber(),
            'status' => 'pending',
            'payment_status' => 'pending',
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total' => $total,
            'shipping_address' => json_encode([
                'name' => $shippingAddress['name'],
                'phone' => $shippingAddress['phone'],
                'address_line_1' => $shippingAddress['address_line_1'],
                'address_line_2' => $shippingAddress['address_line_2'],
                'city' => $shippingAddress['city'],
                'state' => $shippingAddress['state'],
                'postal_code' => $shippingAddress['postal_code'],
                'country' => $shippingAddress['country']
            ]),
            'notes' => $input['notes'] ?? null
        ];

        $orderId = $this->orderModel->insert($orderData);
        
        if (!$orderId) {
            return $this->errorResponse('Failed to create order', 500);
        }

        // Create order items
        foreach ($validItems as $item) {
            $this->orderItemModel->insert([
                'order_id' => $orderId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['price'] * $item['quantity']
            ]);

            // Update product stock
            $product = $this->productModel->find($item['product_id']);
            $this->productModel->update($item['product_id'], [
                'stock_quantity' => $product['stock_quantity'] - $item['quantity']
            ]);
        }

        // Clear cart
        $this->cartModel->clearUserCart($userId);

        // Get created order
        $order = $this->orderModel->find($orderId);
        $order['total'] = (float) $order['total'];
        $order['shipping_cost'] = (float) $order['shipping_cost'];
        $order['tax_amount'] = (float) $order['tax_amount'];
        $order['discount_amount'] = (float) $order['discount_amount'];
        $order['shipping_address'] = json_decode($order['shipping_address'], true);

        return $this->successResponse($order, 'Order created successfully', 201);
    }

    /**
     * Cancel order
     */
    public function cancel($orderId = null)
    {
        $userId = $this->getAuthenticatedUserId();
        
        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        if (!$orderId) {
            return $this->errorResponse('Order ID is required', 400);
        }

        // Get order
        $order = $this->orderModel->where('id', $orderId)
                                 ->where('user_id', $userId)
                                 ->first();

        if (!$order) {
            return $this->notFoundResponse('Order not found');
        }

        // Check if order can be cancelled
        if (!in_array($order['status'], ['pending', 'confirmed'])) {
            return $this->errorResponse('Order cannot be cancelled', 400);
        }

        // Update order status
        $updated = $this->orderModel->update($orderId, [
            'status' => 'cancelled',
            'cancelled_at' => date('Y-m-d H:i:s')
        ]);

        if (!$updated) {
            return $this->errorResponse('Failed to cancel order', 500);
        }

        // Restore product stock
        $orderItems = $this->orderItemModel->where('order_id', $orderId)->findAll();
        foreach ($orderItems as $item) {
            $product = $this->productModel->find($item['product_id']);
            if ($product) {
                $this->productModel->update($item['product_id'], [
                    'stock_quantity' => $product['stock_quantity'] + $item['quantity']
                ]);
            }
        }

        return $this->successResponse(null, 'Order cancelled successfully');
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber(): string
    {
        return 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }

    /**
     * Calculate shipping cost
     */
    private function calculateShippingCost(float $subtotal): float
    {
        // Free shipping for orders above 500
        if ($subtotal >= 500) {
            return 0.00;
        }
        
        return 50.00; // Flat shipping rate
    }

    /**
     * Calculate tax amount
     */
    private function calculateTax(float $subtotal): float
    {
        return $subtotal * 0.18; // 18% GST
    }
}
