<?php

namespace App\Controllers\Api;

use App\Models\OrderModel;
use App\Models\PaymentTransactionModel;
use App\Libraries\HdfcPaymentGateway;

class PaymentApiController extends BaseApiController
{
    protected $orderModel;
    protected $paymentTransactionModel;
    protected $hdfcGateway;

    public function __construct()
    {
        $this->orderModel = new OrderModel();
        $this->paymentTransactionModel = new PaymentTransactionModel();
        $this->hdfcGateway = new HdfcPaymentGateway();
    }

    /**
     * Initiate payment for mobile app
     */
    public function initiate()
    {
        $userId = $this->getAuthenticatedUserId();
        
        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        
        if (!isset($input['order_id'])) {
            return $this->errorResponse('Order ID is required', 400);
        }

        $orderId = $input['order_id'];
        
        // Get order and verify ownership
        $order = $this->orderModel->find($orderId);
        
        if (!$order) {
            return $this->notFoundResponse('Order not found');
        }

        if ($order['user_id'] != $userId) {
            return $this->errorResponse('Unauthorized access to order', 403);
        }

        if ($order['payment_status'] === 'paid') {
            return $this->errorResponse('Order is already paid', 400);
        }

        try {
            // Generate unique transaction ID
            $transactionId = 'TXN' . date('YmdHis') . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

            // Create payment transaction record
            $transactionData = [
                'order_id' => $orderId,
                'transaction_id' => $transactionId,
                'amount' => $order['total_amount'],
                'currency' => 'INR',
                'status' => 'pending',
                'payment_gateway' => 'hdfc'
            ];

            $transactionInserted = $this->paymentTransactionModel->insert($transactionData);
            
            if (!$transactionInserted) {
                return $this->errorResponse('Failed to create payment transaction', 500);
            }

            // Get the inserted transaction
            $transaction = $this->paymentTransactionModel->getByTransactionId($transactionId);

            // Prepare order data for payment gateway
            $orderData = [
                'order_id' => $orderId,
                'transaction_id' => $transactionId,
                'amount' => $order['total_amount'],
                'user_id' => $userId,
                'billing_name' => $order['shipping_name'],
                'billing_email' => $order['user_email'] ?? 'customer@nandinihub.com',
                'billing_tel' => $order['shipping_phone'],
                'billing_address' => $order['shipping_address'],
                'billing_city' => $order['shipping_city'],
                'billing_state' => $order['shipping_state'],
                'billing_zip' => $order['shipping_postal_code'],
                'billing_country' => 'India'
            ];

            // Create order with payment gateway
            $paymentRequest = $this->hdfcGateway->createOrder($orderData);

            if (!$paymentRequest['success']) {
                return $this->errorResponse('Failed to create payment order', 500);
            }

            return $this->successResponse([
                'transaction_id' => $transactionId,
                'payment_url' => $paymentRequest['payment_page_url'],
                'gateway_url' => $paymentRequest['gateway_url'],
                'payment_links' => $paymentRequest['payment_links'] ?? [],
                'order_id' => $orderId,
                'amount' => $order['total_amount'],
                'currency' => 'INR'
            ], 'Payment initiated successfully');

        } catch (\Exception $e) {
            log_message('error', 'Payment initiation failed: ' . $e->getMessage());
            return $this->errorResponse('Failed to initiate payment. Please try again.', 500);
        }
    }

    /**
     * Verify payment status
     */
    public function verify($transactionId = null)
    {
        $userId = $this->getAuthenticatedUserId();
        
        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        if (!$transactionId) {
            return $this->errorResponse('Transaction ID is required', 400);
        }

        $transaction = $this->paymentTransactionModel->getByTransactionId($transactionId);
        
        if (!$transaction) {
            return $this->notFoundResponse('Transaction not found');
        }

        $order = $this->orderModel->find($transaction['order_id']);
        
        if (!$order || $order['user_id'] != $userId) {
            return $this->errorResponse('Unauthorized access', 403);
        }

        // Get payment status from gateway if still pending
        if ($transaction['status'] === 'pending' || $transaction['status'] === 'processing') {
            try {
                $gatewayStatus = $this->hdfcGateway->getTransactionStatus($transactionId);
                
                if ($gatewayStatus && isset($gatewayStatus['status'])) {
                    $newStatus = $this->mapGatewayStatus($gatewayStatus['status']);
                    
                    if ($newStatus !== $transaction['status']) {
                        $this->paymentTransactionModel->updateStatus($transactionId, $newStatus, [
                            'gateway_status' => $gatewayStatus['status'],
                            'gateway_response' => json_encode($gatewayStatus)
                        ]);
                        
                        // Update order status if payment successful
                        if ($newStatus === 'success') {
                            $this->orderModel->update($order['id'], [
                                'payment_status' => 'paid',
                                'status' => 'confirmed'
                            ]);
                        }
                        
                        $transaction['status'] = $newStatus;
                    }
                }
            } catch (\Exception $e) {
                log_message('error', 'Failed to verify payment status: ' . $e->getMessage());
            }
        }

        return $this->successResponse([
            'transaction_id' => $transactionId,
            'status' => $transaction['status'],
            'amount' => $transaction['amount'],
            'currency' => $transaction['currency'],
            'payment_method' => $transaction['payment_method'],
            'processed_at' => $transaction['processed_at'],
            'order' => [
                'id' => $order['id'],
                'order_number' => $order['order_number'],
                'payment_status' => $order['payment_status'],
                'status' => $order['status']
            ]
        ], 'Payment status retrieved successfully');
    }

    /**
     * Get payment methods available
     */
    public function methods()
    {
        $methods = [
            [
                'id' => 'card',
                'name' => 'Credit/Debit Card',
                'description' => 'Pay using your credit or debit card',
                'icon' => 'card',
                'enabled' => true
            ],
            [
                'id' => 'upi',
                'name' => 'UPI',
                'description' => 'Pay using UPI apps like GPay, PhonePe, Paytm',
                'icon' => 'phone',
                'enabled' => true
            ],
            [
                'id' => 'netbanking',
                'name' => 'Net Banking',
                'description' => 'Pay using your bank account',
                'icon' => 'bank',
                'enabled' => true
            ],
            [
                'id' => 'wallet',
                'name' => 'Digital Wallets',
                'description' => 'Pay using digital wallets',
                'icon' => 'wallet',
                'enabled' => true
            ]
        ];

        return $this->successResponse($methods, 'Payment methods retrieved successfully');
    }

    /**
     * Handle payment callback (webhook)
     */
    public function callback()
    {
        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        
        log_message('info', 'Payment callback received: ' . json_encode($input));

        if (!isset($input['transaction_id'])) {
            return $this->errorResponse('Transaction ID is required', 400);
        }

        $transactionId = $input['transaction_id'];
        $transaction = $this->paymentTransactionModel->getByTransactionId($transactionId);
        
        if (!$transaction) {
            return $this->notFoundResponse('Transaction not found');
        }

        try {
            // Process the callback
            $status = $this->mapGatewayStatus($input['status'] ?? 'failed');
            
            $updateData = [
                'gateway_transaction_id' => $input['gateway_transaction_id'] ?? null,
                'gateway_status' => $input['status'] ?? 'failed',
                'gateway_response' => json_encode($input),
                'payment_method' => $input['payment_method'] ?? null,
                'bank_ref_no' => $input['bank_ref_no'] ?? null
            ];

            if ($status === 'failed' && isset($input['failure_reason'])) {
                $updateData['failure_reason'] = $input['failure_reason'];
            }

            $this->paymentTransactionModel->updateStatus($transactionId, $status, $updateData);

            // Update order status
            $order = $this->orderModel->find($transaction['order_id']);
            if ($order) {
                if ($status === 'success') {
                    $this->orderModel->update($order['id'], [
                        'payment_status' => 'paid',
                        'status' => 'confirmed'
                    ]);
                } elseif ($status === 'failed') {
                    $this->orderModel->update($order['id'], [
                        'payment_status' => 'failed'
                    ]);
                }
            }

            return $this->successResponse([
                'transaction_id' => $transactionId,
                'status' => $status
            ], 'Payment callback processed successfully');

        } catch (\Exception $e) {
            log_message('error', 'Payment callback processing failed: ' . $e->getMessage());
            return $this->errorResponse('Failed to process payment callback', 500);
        }
    }

    /**
     * Map gateway status to internal status
     */
    private function mapGatewayStatus($gatewayStatus)
    {
        switch (strtolower($gatewayStatus)) {
            case 'success':
            case 'completed':
            case 'captured':
                return 'success';
            case 'failed':
            case 'error':
            case 'declined':
                return 'failed';
            case 'pending':
            case 'processing':
            case 'initiated':
                return 'processing';
            case 'cancelled':
            case 'aborted':
                return 'cancelled';
            default:
                return 'failed';
        }
    }
}
