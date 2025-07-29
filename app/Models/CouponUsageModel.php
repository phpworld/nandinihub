<?php

namespace App\Models;

use CodeIgniter\Model;

class CouponUsageModel extends Model
{
    protected $table            = 'coupon_usage';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'coupon_id',
        'user_id',
        'order_id',
        'discount_amount',
        'order_amount',
        'used_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'coupon_id' => 'required|integer',
        'user_id' => 'required|integer',
        'order_id' => 'required|integer',
        'discount_amount' => 'required|decimal',
        'order_amount' => 'required|decimal'
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setUsedAt'];

    /**
     * Set used_at timestamp
     */
    protected function setUsedAt(array $data)
    {
        if (empty($data['data']['used_at'])) {
            $data['data']['used_at'] = date('Y-m-d H:i:s');
        }
        return $data;
    }

    /**
     * Record coupon usage
     */
    public function recordUsage(int $couponId, int $userId, int $orderId, float $discountAmount, float $orderAmount): bool
    {
        $data = [
            'coupon_id' => $couponId,
            'user_id' => $userId,
            'order_id' => $orderId,
            'discount_amount' => $discountAmount,
            'order_amount' => $orderAmount
        ];

        return $this->insert($data) !== false;
    }

    /**
     * Get usage statistics for a coupon
     */
    public function getCouponStats(int $couponId): array
    {
        $stats = $this->select('
            COUNT(*) as total_uses,
            COUNT(DISTINCT user_id) as unique_users,
            SUM(discount_amount) as total_discount,
            SUM(order_amount) as total_order_value,
            AVG(discount_amount) as avg_discount,
            AVG(order_amount) as avg_order_value
        ')
            ->where('coupon_id', $couponId)
            ->first();

        return $stats ?: [
            'total_uses' => 0,
            'unique_users' => 0,
            'total_discount' => 0,
            'total_order_value' => 0,
            'avg_discount' => 0,
            'avg_order_value' => 0
        ];
    }

    /**
     * Get user's coupon usage history
     */
    public function getUserUsageHistory(int $userId, int $limit = 10): array
    {
        return $this->select('coupon_usage.*, coupons.code, coupons.name, orders.order_number')
            ->join('coupons', 'coupons.id = coupon_usage.coupon_id')
            ->join('orders', 'orders.id = coupon_usage.order_id')
            ->where('coupon_usage.user_id', $userId)
            ->orderBy('coupon_usage.used_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get coupon usage with details
     */
    public function getUsageWithDetails(int $couponId, int $limit = 50): array
    {
        return $this->select('
            coupon_usage.*,
            CONCAT(users.first_name, " ", users.last_name) as user_name,
            users.email as user_email,
            orders.order_number
        ')
            ->join('users', 'users.id = coupon_usage.user_id')
            ->join('orders', 'orders.id = coupon_usage.order_id')
            ->where('coupon_usage.coupon_id', $couponId)
            ->orderBy('coupon_usage.used_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
