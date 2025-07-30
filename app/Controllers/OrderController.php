<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\CartModel;
use App\Models\ProductModel;
use App\Models\UserModel;

class OrderController extends BaseController
{
    protected $orderModel;
    protected $orderItemModel;
    protected $cartModel;
    protected $productModel;
    protected $userModel;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->cartModel = new CartModel();
        $this->productModel = new ProductModel();
        $this->userModel = new UserModel();
    }

    public function index()
    {
        if (!session()->get('user_id')) {
            session()->set('redirect_to', current_url());
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');

        // Get filter parameters
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 10;

        // Get user's orders with filters
        $orders = $this->orderModel->getUserOrdersWithFilters($userId, [
            'status' => $status,
            'search' => $search,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'page' => $page,
            'per_page' => $perPage
        ]);

        // Get order items for each order (for preview)
        foreach ($orders['data'] as &$order) {
            $order['items'] = $this->orderItemModel->getOrderItems($order['id']);
        }

        // Get order statistics
        $orderStats = $this->orderModel->getOrderStats($userId);

        $data = [
            'title' => 'My Orders - Microdose Mushroom',
            'orders' => $orders['data'],
            'pager' => $orders['pager'],
            'orderStats' => $orderStats,
            'filters' => [
                'status' => $status,
                'search' => $search,
                'date_from' => $dateFrom,
                'date_to' => $dateTo
            ]
        ];

        return view('orders/index', $data);
    }

    public function show($orderNumber)
    {
        if (!session()->get('user_id')) {
            session()->set('redirect_to', current_url());
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');

        // Get order by order number and user ID
        $order = $this->orderModel->getOrderByNumber($orderNumber, $userId);

        if (!$order) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Order not found');
        }

        // Get order items with product details
        $orderItems = $this->orderItemModel->getOrderItemsWithProducts($order['id']);

        // Check if order can be cancelled
        $canCancel = $this->orderModel->canBeCancelled($order);
        $cancellationReason = $this->orderModel->getCancellationReason($order);

        // Get order timeline/status history (if implemented)
        $orderTimeline = $this->getOrderTimeline($order);

        $data = [
            'title' => 'Order #' . $orderNumber . ' - Microdose Mushroom',
            'order' => $order,
            'orderItems' => $orderItems,
            'canCancel' => $canCancel,
            'cancellationReason' => $cancellationReason,
            'orderTimeline' => $orderTimeline
        ];

        return view('orders/show', $data);
    }

    private function getOrderTimeline($order)
    {
        // Create a basic timeline based on order status and dates
        $timeline = [];

        $timeline[] = [
            'status' => 'Order Placed',
            'date' => $order['created_at'],
            'description' => 'Your order has been placed successfully.',
            'icon' => 'fas fa-shopping-cart',
            'color' => 'success',
            'completed' => true
        ];

        if ($order['status'] !== 'pending') {
            $timeline[] = [
                'status' => 'Order Confirmed',
                'date' => $order['updated_at'],
                'description' => 'Your order has been confirmed and is being prepared.',
                'icon' => 'fas fa-check-circle',
                'color' => 'info',
                'completed' => in_array($order['status'], ['confirmed', 'processing', 'shipped', 'delivered'])
            ];
        }

        if (in_array($order['status'], ['processing', 'shipped', 'delivered'])) {
            $timeline[] = [
                'status' => 'Processing',
                'date' => $order['updated_at'],
                'description' => 'Your order is being processed and prepared for shipment.',
                'icon' => 'fas fa-cogs',
                'color' => 'warning',
                'completed' => in_array($order['status'], ['processing', 'shipped', 'delivered'])
            ];
        }

        if (in_array($order['status'], ['shipped', 'delivered'])) {
            $timeline[] = [
                'status' => 'Shipped',
                'date' => $order['updated_at'],
                'description' => 'Your order has been shipped and is on its way.',
                'icon' => 'fas fa-truck',
                'color' => 'primary',
                'completed' => in_array($order['status'], ['shipped', 'delivered'])
            ];
        }

        if ($order['status'] === 'delivered') {
            $timeline[] = [
                'status' => 'Delivered',
                'date' => $order['updated_at'],
                'description' => 'Your order has been delivered successfully.',
                'icon' => 'fas fa-check-double',
                'color' => 'success',
                'completed' => true
            ];
        }

        if ($order['status'] === 'cancelled') {
            $timeline[] = [
                'status' => 'Cancelled',
                'date' => $order['updated_at'],
                'description' => 'Your order has been cancelled.',
                'icon' => 'fas fa-times-circle',
                'color' => 'danger',
                'completed' => true
            ];
        }

        return $timeline;
    }

    public function checkout()
    {
        if (!session()->get('user_id')) {
            session()->set('redirect_to', '/checkout');
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');
        $sessionId = session()->session_id;
        $cartItems = $this->cartModel->getCartItemsWithDetails($userId, $sessionId);

        if (empty($cartItems)) {
            session()->setFlashdata('error', 'Your cart is empty');
            return redirect()->to('/cart');
        }

        $cartTotal = $this->cartModel->getCartTotal($userId, $sessionId);
        $user = $this->userModel->find($userId);

        // Load shipping service and get available shipping methods
        $shippingService = new \App\Libraries\ShippingService();
        $shippingMethods = $shippingService->getShippingMethodsForCheckout($cartTotal);

        // Load user addresses
        $addressModel = new \App\Models\UserAddressModel();
        $userAddresses = $addressModel->getUserAddresses($userId);

        $data = [
            'title' => 'Checkout - Microdose Mushroom',
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'user' => $user,
            'shippingMethods' => $shippingMethods,
            'userAddresses' => $userAddresses
        ];

        return view('orders/checkout', $data);
    }

    public function processCheckout()
    {
        if (!session()->get('user_id')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');
        $sessionId = session()->session_id;

        // Debug: Log the incoming data
        log_message('info', 'Order processing attempt for user ID: ' . $userId);
        log_message('info', 'POST data: ' . json_encode($this->request->getPost()));

        $cartItems = $this->cartModel->getCartItemsWithDetails($userId, $sessionId);

        if (empty($cartItems)) {
            session()->setFlashdata('error', 'Your cart is empty');
            return redirect()->to('/cart');
        }

        // Validate form data
        $rules = [
            'delivery_address_id' => 'required|integer',
            'payment_method' => 'required|in_list[cod,online]',
            'shipping_method_id' => 'required|integer'
        ];

        if (!$this->validate($rules)) {
            log_message('error', 'Order validation failed: ' . json_encode($this->validator->getErrors()));
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get selected delivery address
        $deliveryAddressId = (int) $this->request->getPost('delivery_address_id');
        $addressModel = new \App\Models\UserAddressModel();
        $deliveryAddress = $addressModel->where(['id' => $deliveryAddressId, 'user_id' => $userId])->first();

        if (!$deliveryAddress) {
            session()->setFlashdata('error', 'Invalid delivery address selected.');
            return redirect()->back()->withInput();
        }

        // Format the delivery address
        $shippingAddress = $addressModel->formatAddress($deliveryAddress);

        // Get billing address (either from form or same as shipping)
        $billingAddress = $this->request->getPost('billing_address');
        if (empty($billingAddress)) {
            $billingAddress = $shippingAddress;
        }

        // Calculate totals
        $subtotal = $this->cartModel->getCartTotal($userId, $sessionId);

        // Validate cart items and prices
        $this->validateCartPrices($cartItems);

        // Handle coupon discount
        $appliedCoupon = session()->get('applied_coupon');
        $discountAmount = 0;
        $couponId = null;
        $couponCode = null;

        if ($appliedCoupon) {
            $couponService = new \App\Libraries\CouponService();
            $cartData = ['total' => $subtotal];
            $couponResult = $couponService->applyCoupon($appliedCoupon['code'], $cartData, $userId);

            if ($couponResult['success']) {
                $discountAmount = $couponResult['discount_amount'];
                $couponId = $appliedCoupon['coupon_id'];
                $couponCode = $appliedCoupon['code'];
            }
        }

        // Calculate shipping using shipping service
        $shippingService = new \App\Libraries\ShippingService();
        $shippingMethodId = (int) $this->request->getPost('shipping_method_id');

        // Validate shipping method
        $shippingValidation = $shippingService->validateShippingMethod($shippingMethodId, $subtotal);
        if (!$shippingValidation['valid']) {
            session()->setFlashdata('error', $shippingValidation['message']);
            return redirect()->back()->withInput();
        }

        // Calculate shipping cost
        $shippingResult = $shippingService->calculateShippingCost($shippingMethodId, $subtotal, $couponCode);
        if (!$shippingResult['success']) {
            session()->setFlashdata('error', $shippingResult['message']);
            return redirect()->back()->withInput();
        }

        $shipping = $shippingResult['cost'];
        $tax = ($subtotal - $discountAmount) * 0.18;
        $total = $subtotal - $discountAmount + $shipping + $tax;

        // Create order data
        $orderData = [
            'user_id' => $userId,
            'total_amount' => $total,
            'subtotal_amount' => $subtotal,
            'shipping_amount' => $shipping,
            'tax_amount' => $tax,
            'discount_amount' => $discountAmount,
            'coupon_id' => $couponId,
            'coupon_code' => $couponCode,
            'shipping_method_id' => $shippingMethodId,
            'payment_method' => $this->request->getPost('payment_method'),
            'payment_status' => 'pending',
            'shipping_address' => $shippingAddress,
            'billing_address' => trim($billingAddress),
            'notes' => trim($this->request->getPost('notes')),
            'status' => 'pending'
        ];

        log_message('info', 'Order data prepared: ' . json_encode($orderData));

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Temporarily disable model validation since we're validating in controller
            $this->orderModel->skipValidation(true);

            // Insert order
            $orderId = $this->orderModel->insert($orderData);

            if (!$orderId) {
                $errors = $this->orderModel->errors();
                log_message('error', 'Order creation failed: ' . json_encode($errors));
                throw new \Exception('Failed to create order: ' . json_encode($errors));
            }

            log_message('info', 'Order created with ID: ' . $orderId);

            // Create order items
            if (!$this->orderItemModel->createOrderItems($orderId, $cartItems)) {
                $errors = $this->orderItemModel->errors();
                log_message('error', 'Order items creation failed: ' . json_encode($errors));
                throw new \Exception('Failed to create order items: ' . json_encode($errors));
            }

            log_message('info', 'Order items created successfully');

            // Update product stock
            foreach ($cartItems as $item) {
                $product = $this->productModel->find($item['product_id']);
                if (!$product) {
                    throw new \Exception('Product not found: ' . $item['product_id']);
                }

                if ($product['stock_quantity'] < $item['quantity']) {
                    throw new \Exception('Insufficient stock for product: ' . $product['name']);
                }

                $newStock = $product['stock_quantity'] - $item['quantity'];
                $this->productModel->update($item['product_id'], ['stock_quantity' => $newStock]);
            }

            log_message('info', 'Product stock updated successfully');

            // Clear cart
            $this->cartModel->clearCart($userId);

            log_message('info', 'Cart cleared successfully');

            $db->transComplete();

            if ($db->transStatus() === false) {
                log_message('error', 'Database transaction failed');
                throw new \Exception('Database transaction failed');
            }

            // Get order details for confirmation
            $order = $this->orderModel->find($orderId);

            if (!$order) {
                log_message('error', 'Order not found after creation: ' . $orderId);
                throw new \Exception('Order not found after creation');
            }

            log_message('info', 'Order completed successfully: ' . $order['order_number']);

            // Process coupon usage if coupon was applied
            if ($couponId && $discountAmount > 0) {
                $couponService = new \App\Libraries\CouponService();
                $couponUsageSuccess = $couponService->processCouponUsage($couponId, $userId, $orderId, $discountAmount, $subtotal);

                if ($couponUsageSuccess) {
                    log_message('info', 'Coupon usage recorded successfully for order: ' . $order['order_number']);
                    // Remove applied coupon from session
                    session()->remove('applied_coupon');
                } else {
                    log_message('error', 'Failed to record coupon usage for order: ' . $order['order_number']);
                }
            }

            // Handle different payment methods
            if ($order['payment_method'] === 'online') {
                // For online payment, redirect to payment initiation
                session()->setFlashdata('info', 'Order created successfully! Please complete the payment to confirm your order.');
                return redirect()->to('/orders/' . $order['order_number'] . '?payment=pending');
            } else {
                // For COD, send confirmation email and redirect to order details
                $this->sendOrderConfirmationEmail($order, $cartItems);
                session()->setFlashdata('success', 'Order placed successfully! Order number: ' . $order['order_number']);
                return redirect()->to('/orders/' . $order['order_number']);
            }
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Order processing exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'Failed to place order: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }



    private function validateCartPrices($cartItems)
    {
        foreach ($cartItems as $item) {
            // Validate that final_price is calculated correctly
            if (isset($item['variant_id']) && $item['variant_id']) {
                // For variants, check that final_price includes option modifiers
                $basePrice = $item['variant_sale_price'] ?? $item['variant_price'] ?? $item['sale_price'] ?? $item['price'];
                $expectedFinalPrice = $basePrice + ($item['price_modifier'] ?? 0);

                if (abs($item['final_price'] - $expectedFinalPrice) > 0.01) {
                    log_message('error', "Price mismatch for variant {$item['variant_id']}: expected {$expectedFinalPrice}, got {$item['final_price']}");
                    throw new \Exception('Price calculation error. Please refresh your cart and try again.');
                }
            } else {
                // For regular products, final_price should match sale_price or price
                $expectedPrice = $item['sale_price'] ?? $item['price'];
                if (abs($item['final_price'] - $expectedPrice) > 0.01) {
                    log_message('error', "Price mismatch for product {$item['product_id']}: expected {$expectedPrice}, got {$item['final_price']}");
                    throw new \Exception('Price calculation error. Please refresh your cart and try again.');
                }
            }

            // Validate stock availability
            if (isset($item['variant_id']) && $item['variant_id']) {
                $availableStock = $item['variant_stock'] ?? 0;
            } else {
                $availableStock = $item['stock_quantity'] ?? 0;
            }

            if ($item['quantity'] > $availableStock) {
                throw new \Exception("Insufficient stock for {$item['name']}. Available: {$availableStock}, Requested: {$item['quantity']}");
            }
        }
    }

    private function sendOrderConfirmationEmail($order, $orderItems)
    {
        // Email functionality will be implemented in the email notifications section
        // For now, we'll just log the order
        log_message('info', 'Order confirmation email should be sent for order: ' . $order['order_number']);
    }

    public function cancelOrder($orderNumber)
    {
        // Check if user is logged in
        if (!session()->get('user_id')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Please login to cancel orders'
                ]);
            }
            return redirect()->to('/login');
        }

        $userId = session()->get('user_id');
        $order = $this->orderModel->getOrderByNumber($orderNumber, $userId);

        // Check if order exists
        if (!$order) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Order not found'
                ]);
            }
            session()->setFlashdata('error', 'Order not found');
            return redirect()->to('/orders');
        }

        // Check if order can be cancelled
        if (!$this->orderModel->canBeCancelled($order)) {
            $message = $this->orderModel->getCancellationReason($order);
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $message
                ]);
            }
            session()->setFlashdata('error', $message);
            return redirect()->to('/orders/' . $orderNumber);
        }

        // Attempt to cancel the order
        try {
            if ($this->orderModel->updateOrderStatus($order['id'], 'cancelled')) {
                // Restore product stock
                $orderItems = $this->orderItemModel->getOrderItems($order['id']);
                foreach ($orderItems as $item) {
                    $product = $this->productModel->find($item['product_id']);
                    if ($product) {
                        $newStock = $product['stock_quantity'] + $item['quantity'];
                        $this->productModel->update($item['product_id'], ['stock_quantity' => $newStock]);
                    }
                }

                $message = 'Order cancelled successfully';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => true,
                        'message' => $message
                    ]);
                }
                session()->setFlashdata('success', $message);
            } else {
                $message = 'Failed to cancel order. Please try again.';
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => $message
                    ]);
                }
                session()->setFlashdata('error', $message);
            }
        } catch (\Exception $e) {
            log_message('error', 'Order cancellation failed: ' . $e->getMessage());
            $message = 'An error occurred while cancelling the order. Please try again.';
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $message
                ]);
            }
            session()->setFlashdata('error', $message);
        }

        return redirect()->to('/orders/' . $orderNumber);
    }
}
