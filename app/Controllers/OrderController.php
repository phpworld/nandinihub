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
        $paymentStatus = $this->request->getGet('payment_status');
        $search = $this->request->getGet('search');
        $dateFrom = $this->request->getGet('date_from');
        $dateTo = $this->request->getGet('date_to');
        $page = $this->request->getGet('page') ?? 1;
        $perPage = 10;

        // Get user's orders with filters
        $orders = $this->orderModel->getUserOrdersWithFilters($userId, [
            'status' => $status,
            'payment_status' => $paymentStatus,
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
                'payment_status' => $paymentStatus,
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

    public function showPaymentDetails($orderNumber)
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return redirect()->to('/login');
        }

        $order = $this->orderModel->getOrderByNumber($orderNumber, $userId);
        if (!$order) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Order not found');
        }

        // Only show payment details for pending payment orders
        if ($order['payment_status'] !== 'pending') {
            return redirect()->to('/orders/' . $orderNumber);
        }

        $data = [
            'title' => 'Payment Required - Order #' . $order['order_number'],
            'order' => $order
        ];

        return view('orders/payment_details', $data);
    }

    public function uploadPaymentScreenshot()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'User not authenticated'
            ]);
        }

        $orderNumber = $this->request->getPost('order_number');
        if (!$orderNumber) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Order number is required'
            ]);
        }

        // Get order and verify ownership
        $order = $this->orderModel->getOrderByNumber($orderNumber, $userId);
        if (!$order) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Order not found'
            ]);
        }

        // Check if screenshot already uploaded
        if (!empty($order['payment_screenshot'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Payment screenshot already uploaded'
            ]);
        }

        // Handle file upload
        $file = $this->request->getFile('payment_screenshot');
        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid file upload'
            ]);
        }

        $screenshotPath = $this->handleScreenshotUpload($file, $orderNumber);
        if (!$screenshotPath) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to upload screenshot'
            ]);
        }

        // Update order with screenshot path
        if ($this->orderModel->updatePaymentScreenshot($order['id'], $screenshotPath)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Payment screenshot uploaded successfully'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to save screenshot information'
            ]);
        }
    }

    private function handleScreenshotUpload($file, $orderNumber): ?string
    {
        $validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

        if (!in_array($file->getMimeType(), $validTypes)) {
            return null;
        }

        if ($file->getSize() > 5 * 1024 * 1024) { // 5MB limit
            return null;
        }

        $uploadPath = 'uploads/payment_screenshots/';
        if (!is_dir(FCPATH . $uploadPath)) {
            mkdir(FCPATH . $uploadPath, 0755, true);
        }

        $fileName = 'payment_' . $orderNumber . '_' . time() . '.' . $file->getExtension();
        if ($file->move(FCPATH . $uploadPath, $fileName)) {
            return $uploadPath . $fileName;
        }

        return null;
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

        // Get payment methods
        $paymentMethodModel = new \App\Models\PaymentMethodModel();
        $paymentMethods = $paymentMethodModel->getActivePaymentMethods();

        // Load user addresses
        $addressModel = new \App\Models\UserAddressModel();
        $userAddresses = $addressModel->getUserAddresses($userId);

        $data = [
            'title' => 'Checkout - Microdose Mushroom',
            'cartItems' => $cartItems,
            'cartTotal' => $cartTotal,
            'user' => $user,
            'shippingMethods' => $shippingMethods,
            'paymentMethods' => $paymentMethods,
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
            'shipping_method_id' => 'required|integer',
            'payment_method_id' => 'required|integer'
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

        // Validate payment method
        $paymentMethodId = (int) $this->request->getPost('payment_method_id');
        $paymentMethodModel = new \App\Models\PaymentMethodModel();
        $paymentValidation = $paymentMethodModel->validatePaymentMethodForOrder($paymentMethodId);

        if (!$paymentValidation['valid']) {
            session()->setFlashdata('error', $paymentValidation['message']);
            return redirect()->back()->withInput();
        }

        $paymentMethod = $paymentValidation['method'];

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
            'payment_method_id' => $paymentMethodId,
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

            // Send confirmation emails to user and admin
            $this->sendOrderConfirmationEmails($order, $cartItems);
            session()->setFlashdata('success', 'Order placed successfully! Please complete the payment to confirm your order.');
            return redirect()->to('/orders/' . $order['order_number'] . '/payment');
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

    private function sendOrderConfirmationEmails($order, $orderItems)
    {
        try {
            // Get user information
            $userModel = new \App\Models\UserModel();
            $user = $userModel->find($order['user_id']);

            if (!$user) {
                log_message('error', 'User not found for order: ' . $order['order_number']);
                return false;
            }

            // Initialize email service
            $emailService = new \App\Libraries\EmailService();

            // Send confirmation email to user
            $userEmailSent = $emailService->sendOrderConfirmation($order, $orderItems, $user);

            // Send notification email to admin
            $adminEmailSent = $emailService->sendAdminOrderNotification($order, $orderItems, $user);

            if ($userEmailSent) {
                log_message('info', 'Order confirmation email sent to user: ' . $user['email'] . ' for order: ' . $order['order_number']);
            } else {
                log_message('error', 'Failed to send order confirmation email to user for order: ' . $order['order_number']);
            }

            if ($adminEmailSent) {
                log_message('info', 'Order notification email sent to admin for order: ' . $order['order_number']);
            } else {
                log_message('error', 'Failed to send order notification email to admin for order: ' . $order['order_number']);
            }

            return $userEmailSent || $adminEmailSent; // Return true if at least one email was sent

        } catch (\Exception $e) {
            log_message('error', 'Error sending order confirmation emails: ' . $e->getMessage());
            return false;
        }
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
