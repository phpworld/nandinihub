<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table            = 'orders';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'order_number',
        'status',
        'total_amount',
        'subtotal_amount',
        'shipping_amount',
        'tax_amount',
        'discount_amount',
        'coupon_id',
        'coupon_code',
        'shipping_method_id',
        'payment_method_id',
        'payment_status',
        'payment_screenshot',
        'payment_verified',
        'payment_verified_at',
        'payment_verified_by',
        'shipping_address',
        'billing_address',
        'notes'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer',
        'order_number' => 'required|is_unique[orders.order_number,id,{id}]',
        'total_amount' => 'required|decimal',
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateOrderNumber'];

    protected function generateOrderNumber(array $data)
    {
        if (empty($data['data']['order_number'])) {
            $data['data']['order_number'] = 'ORD' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        }
        return $data;
    }

    public function getOrdersWithItems($userId = null, $limit = null)
    {
        $builder = $this->select('orders.*, users.first_name, users.last_name, users.email')
            ->join('users', 'users.id = orders.user_id')
            ->orderBy('orders.created_at', 'DESC');

        if ($userId) {
            $builder->where('orders.user_id', $userId);
        }

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    public function getOrderWithDetails($orderId, $userId = null)
    {
        $builder = $this->select('orders.*, users.first_name, users.last_name, users.email, users.phone, shipping_methods.name as shipping_method_name, shipping_methods.delivery_time as shipping_delivery_time, payment_methods.name as payment_method_name, payment_methods.wallet_address, payment_methods.qr_code, payment_methods.payment_information')
            ->join('users', 'users.id = orders.user_id')
            ->join('shipping_methods', 'shipping_methods.id = orders.shipping_method_id', 'left')
            ->join('payment_methods', 'payment_methods.id = orders.payment_method_id', 'left')
            ->where('orders.id', $orderId);

        if ($userId) {
            $builder->where('orders.user_id', $userId);
        }

        return $builder->first();
    }

    public function getOrderByNumber($orderNumber, $userId = null)
    {
        $builder = $this->select('orders.*, shipping_methods.name as shipping_method_name, shipping_methods.delivery_time as shipping_delivery_time, payment_methods.name as payment_method_name, payment_methods.wallet_address, payment_methods.qr_code, payment_methods.payment_information')
            ->join('shipping_methods', 'shipping_methods.id = orders.shipping_method_id', 'left')
            ->join('payment_methods', 'payment_methods.id = orders.payment_method_id', 'left')
            ->where('order_number', $orderNumber);

        if ($userId) {
            $builder->where('user_id', $userId);
        }

        return $builder->first();
    }

    public function updateOrderStatus($orderId, $status)
    {
        return $this->update($orderId, ['status' => $status]);
    }

    public function updatePaymentStatus($orderId, $paymentStatus)
    {
        return $this->update($orderId, ['payment_status' => $paymentStatus]);
    }

    public function updatePaymentScreenshot($orderId, $screenshotPath)
    {
        return $this->update($orderId, ['payment_screenshot' => $screenshotPath]);
    }

    public function verifyPayment($orderId, $adminId)
    {
        return $this->update($orderId, [
            'payment_verified' => 1,
            'payment_verified_at' => date('Y-m-d H:i:s'),
            'payment_verified_by' => $adminId,
            'payment_status' => 'paid'
        ]);
    }

    public function rejectPayment($orderId, $adminId)
    {
        return $this->update($orderId, [
            'payment_verified' => 0,
            'payment_verified_at' => date('Y-m-d H:i:s'),
            'payment_verified_by' => $adminId,
            'payment_status' => 'failed'
        ]);
    }



    public function canBeCancelled($order)
    {
        // Only pending orders can be cancelled
        if ($order['status'] !== 'pending') {
            return false;
        }

        // Check if order is too old to cancel (e.g., older than 24 hours)
        $orderTime = strtotime($order['created_at']);
        $currentTime = time();
        $hoursSinceOrder = ($currentTime - $orderTime) / 3600;

        // Allow cancellation within 24 hours for pending orders
        if ($hoursSinceOrder > 24) {
            return false;
        }

        return true;
    }

    public function getCancellationReason($order)
    {
        if ($order['status'] !== 'pending') {
            return 'Order cannot be cancelled. Only pending orders can be cancelled.';
        }

        $orderTime = strtotime($order['created_at']);
        $currentTime = time();
        $hoursSinceOrder = ($currentTime - $orderTime) / 3600;

        if ($hoursSinceOrder > 24) {
            return 'Order cannot be cancelled. Cancellation is only allowed within 24 hours of placing the order.';
        }

        return '';
    }

    public function getOrderStats($userId = null)
    {
        $builder = $this->selectSum('total_amount', 'total_spent')
            ->selectCount('id', 'total_orders');

        if ($userId) {
            $builder->where('user_id', $userId);
        }

        return $builder->first();
    }

    public function getUserOrders($userId, $limit = null)
    {
        $builder = $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    public function getUserOrdersWithFilters($userId, $filters = [])
    {
        $builder = $this->where('user_id', $userId);

        // Apply status filter
        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }

        // Apply payment status filter
        if (!empty($filters['payment_status'])) {
            $builder->where('payment_status', $filters['payment_status']);
        }

        // Apply search filter (search in order number or notes)
        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('order_number', $filters['search'])
                ->orLike('notes', $filters['search'])
                ->groupEnd();
        }

        // Apply date range filter
        if (!empty($filters['date_from'])) {
            $builder->where('created_at >=', $filters['date_from'] . ' 00:00:00');
        }
        if (!empty($filters['date_to'])) {
            $builder->where('created_at <=', $filters['date_to'] . ' 23:59:59');
        }

        $builder->orderBy('created_at', 'DESC');

        // Get total count for pagination
        $totalCount = $builder->countAllResults(false);

        // Apply pagination
        $perPage = $filters['per_page'] ?? 10;
        $page = $filters['page'] ?? 1;
        $offset = ($page - 1) * $perPage;

        $orders = $builder->limit($perPage, $offset)->findAll();

        // Create pagination
        $pager = \Config\Services::pager();

        // Build URL with existing query parameters
        $queryParams = [];

        // Preserve existing filters in pagination links
        if (!empty($filters['status'])) {
            $queryParams['status'] = $filters['status'];
        }
        if (!empty($filters['search'])) {
            $queryParams['search'] = $filters['search'];
        }
        if (!empty($filters['date_from'])) {
            $queryParams['date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $queryParams['date_to'] = $filters['date_to'];
        }

        // Set path with query parameters
        $baseUrl = base_url('orders');
        if (!empty($queryParams)) {
            $baseUrl .= '?' . http_build_query($queryParams);
        }
        $pager->setPath($baseUrl);
        $pager->makeLinks($page, $perPage, $totalCount);

        return [
            'data' => $orders,
            'pager' => $pager,
            'total' => $totalCount
        ];
    }

    public function getRecentOrders($limit = 10)
    {
        return $this->select('orders.*, users.first_name, users.last_name')
            ->join('users', 'users.id = orders.user_id')
            ->orderBy('orders.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
