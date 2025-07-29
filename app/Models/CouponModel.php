<?php

namespace App\Models;

use CodeIgniter\Model;

class CouponModel extends Model
{
    protected $table            = 'coupons';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'minimum_order_amount',
        'maximum_discount_amount',
        'usage_limit',
        'usage_limit_per_customer',
        'used_count',
        'valid_from',
        'valid_until',
        'is_active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'code' => 'required|min_length[3]|max_length[50]',
        'name' => 'required|min_length[3]|max_length[255]',
        'type' => 'required|in_list[percentage,fixed_amount,free_shipping]',
        'value' => 'required|decimal|greater_than[0]',
        'minimum_order_amount' => 'decimal|greater_than_equal_to[0]',
        'maximum_discount_amount' => 'permit_empty|decimal|greater_than[0]',
        'usage_limit' => 'permit_empty|integer|greater_than[0]',
        'usage_limit_per_customer' => 'integer|greater_than[0]',
        'valid_from' => 'permit_empty|valid_date',
        'valid_until' => 'permit_empty|valid_date'
    ];

    // Validation rules for updates (with unique check excluding current record)
    protected $validationRulesUpdate = [
        'code' => 'required|min_length[3]|max_length[50]|is_unique[coupons.code,id,{id}]',
        'name' => 'required|min_length[3]|max_length[255]',
        'type' => 'required|in_list[percentage,fixed_amount,free_shipping]',
        'value' => 'required|decimal|greater_than[0]',
        'minimum_order_amount' => 'decimal|greater_than_equal_to[0]',
        'maximum_discount_amount' => 'permit_empty|decimal|greater_than[0]',
        'usage_limit' => 'permit_empty|integer|greater_than[0]',
        'usage_limit_per_customer' => 'integer|greater_than[0]',
        'valid_from' => 'permit_empty|valid_date',
        'valid_until' => 'permit_empty|valid_date'
    ];

    protected $validationMessages = [
        'code' => [
            'required' => 'Coupon code is required',
            'min_length' => 'Coupon code must be at least 3 characters',
            'max_length' => 'Coupon code cannot exceed 50 characters',
            'is_unique' => 'This coupon code already exists'
        ],
        'name' => [
            'required' => 'Coupon name is required',
            'min_length' => 'Coupon name must be at least 3 characters'
        ],
        'type' => [
            'required' => 'Coupon type is required',
            'in_list' => 'Invalid coupon type'
        ],
        'value' => [
            'required' => 'Coupon value is required',
            'decimal' => 'Coupon value must be a valid number',
            'greater_than' => 'Coupon value must be greater than 0'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateCode'];
    protected $beforeUpdate   = ['generateCode'];

    /**
     * Generate coupon code if not provided
     */
    protected function generateCode(array $data)
    {
        if (empty($data['data']['code'])) {
            $data['data']['code'] = $this->generateUniqueCode();
        } else {
            $data['data']['code'] = strtoupper($data['data']['code']);
        }
        return $data;
    }

    /**
     * Generate unique coupon code
     */
    private function generateUniqueCode(): string
    {
        do {
            $code = 'COUP' . strtoupper(substr(md5(uniqid()), 0, 6));
        } while ($this->where('code', $code)->first());

        return $code;
    }

    /**
     * Get coupon by code
     */
    public function getByCode(string $code): ?array
    {
        return $this->where('code', strtoupper($code))->first();
    }

    /**
     * Get active coupons
     */
    public function getActiveCoupons(): array
    {
        return $this->where('is_active', true)
            ->where('(valid_from IS NULL OR valid_from <= NOW())')
            ->where('(valid_until IS NULL OR valid_until >= NOW())')
            ->findAll();
    }

    /**
     * Validate coupon for use
     */
    public function validateCoupon(string $code, float $orderAmount, ?int $userId = null): array
    {
        $coupon = $this->getByCode($code);

        if (!$coupon) {
            return ['valid' => false, 'message' => 'Invalid coupon code'];
        }

        // Check if coupon is active
        if (!$coupon['is_active']) {
            return ['valid' => false, 'message' => 'This coupon is no longer active'];
        }

        // Check validity dates
        $now = date('Y-m-d H:i:s');
        if ($coupon['valid_from'] && $coupon['valid_from'] > $now) {
            return ['valid' => false, 'message' => 'This coupon is not yet valid'];
        }
        if ($coupon['valid_until'] && $coupon['valid_until'] < $now) {
            return ['valid' => false, 'message' => 'This coupon has expired'];
        }

        // Check minimum order amount
        if ($coupon['minimum_order_amount'] > 0 && $orderAmount < $coupon['minimum_order_amount']) {
            return [
                'valid' => false,
                'message' => 'Minimum order amount of ₹' . number_format($coupon['minimum_order_amount'], 2) . ' required'
            ];
        }

        // Check usage limit
        if ($coupon['usage_limit'] && $coupon['used_count'] >= $coupon['usage_limit']) {
            return ['valid' => false, 'message' => 'This coupon has reached its usage limit'];
        }

        // Check per-customer usage limit
        if ($userId && $coupon['usage_limit_per_customer'] > 0) {
            $usageModel = new \App\Models\CouponUsageModel();
            $userUsageCount = $usageModel->where('coupon_id', $coupon['id'])
                ->where('user_id', $userId)
                ->countAllResults();

            if ($userUsageCount >= $coupon['usage_limit_per_customer']) {
                return ['valid' => false, 'message' => 'You have already used this coupon the maximum number of times'];
            }
        }

        return ['valid' => true, 'coupon' => $coupon];
    }

    /**
     * Calculate discount amount
     */
    public function calculateDiscount(array $coupon, float $orderAmount): float
    {
        $discount = 0;

        switch ($coupon['type']) {
            case 'percentage':
                $discount = ($orderAmount * $coupon['value']) / 100;
                break;
            case 'fixed_amount':
                $discount = $coupon['value'];
                break;
            case 'free_shipping':
                // This would be handled separately in shipping calculation
                $discount = 0;
                break;
        }

        // Apply maximum discount limit if set
        if ($coupon['maximum_discount_amount'] && $discount > $coupon['maximum_discount_amount']) {
            $discount = $coupon['maximum_discount_amount'];
        }

        // Ensure discount doesn't exceed order amount
        if ($discount > $orderAmount) {
            $discount = $orderAmount;
        }

        return round($discount, 2);
    }

    /**
     * Increment usage count
     */
    public function incrementUsage(int $couponId): bool
    {
        return $this->set('used_count', 'used_count + 1', false)
            ->where('id', $couponId)
            ->update();
    }
}
