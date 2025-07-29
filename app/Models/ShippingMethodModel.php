<?php

namespace App\Models;

use CodeIgniter\Model;

class ShippingMethodModel extends Model
{
    protected $table            = 'shipping_methods';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'description',
        'delivery_time',
        'cost',
        'minimum_order_amount',
        'maximum_order_amount',
        'is_free_shipping',
        'is_active',
        'sort_order'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|min_length[3]|max_length[255]',
        'delivery_time' => 'required|min_length[3]|max_length[100]',
        'cost' => 'required|decimal|greater_than_equal_to[0]',
        'minimum_order_amount' => 'decimal|greater_than_equal_to[0]',
        'maximum_order_amount' => 'permit_empty|decimal|greater_than[0]',
        'is_free_shipping' => 'in_list[0,1]',
        'is_active' => 'in_list[0,1]',
        'sort_order' => 'integer|greater_than_equal_to[0]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Shipping method name is required',
            'min_length' => 'Shipping method name must be at least 3 characters',
            'max_length' => 'Shipping method name cannot exceed 255 characters'
        ],
        'delivery_time' => [
            'required' => 'Delivery time is required',
            'min_length' => 'Delivery time must be at least 3 characters',
            'max_length' => 'Delivery time cannot exceed 100 characters'
        ],
        'cost' => [
            'required' => 'Cost is required',
            'decimal' => 'Cost must be a valid decimal number',
            'greater_than_equal_to' => 'Cost cannot be negative'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get all active shipping methods ordered by sort_order
     */
    public function getActiveShippingMethods(): array
    {
        return $this->where('is_active', 1)
                   ->orderBy('sort_order', 'ASC')
                   ->findAll();
    }

    /**
     * Get available shipping methods for a given order amount
     */
    public function getAvailableShippingMethods(float $orderAmount): array
    {
        $methods = $this->getActiveShippingMethods();
        $availableMethods = [];

        foreach ($methods as $method) {
            // Check minimum order amount
            if ($orderAmount >= $method['minimum_order_amount']) {
                // Check maximum order amount (if set)
                if ($method['maximum_order_amount'] === null || $orderAmount <= $method['maximum_order_amount']) {
                    $availableMethods[] = $method;
                }
            }
        }

        return $availableMethods;
    }

    /**
     * Calculate shipping cost for a given method and order amount
     */
    public function calculateShippingCost(int $methodId, float $orderAmount): float
    {
        $method = $this->find($methodId);
        
        if (!$method || !$method['is_active']) {
            return 0.00;
        }

        // Check if order qualifies for this shipping method
        if ($orderAmount < $method['minimum_order_amount']) {
            return 0.00;
        }

        if ($method['maximum_order_amount'] !== null && $orderAmount > $method['maximum_order_amount']) {
            return 0.00;
        }

        // Return the shipping cost
        return (float) $method['cost'];
    }

    /**
     * Get shipping method by ID with validation
     */
    public function getShippingMethodById(int $methodId): ?array
    {
        $method = $this->find($methodId);
        
        if (!$method || !$method['is_active']) {
            return null;
        }

        return $method;
    }

    /**
     * Toggle shipping method status
     */
    public function toggleStatus(int $id): bool
    {
        $method = $this->find($id);
        if (!$method) {
            return false;
        }

        $newStatus = $method['is_active'] ? 0 : 1;
        return $this->update($id, ['is_active' => $newStatus]);
    }

    /**
     * Get shipping methods with pagination for admin
     */
    public function getShippingMethodsPaginated(int $perPage = 10): array
    {
        return $this->orderBy('sort_order', 'ASC')
                   ->paginate($perPage);
    }

    /**
     * Update sort orders for shipping methods
     */
    public function updateSortOrders(array $sortData): bool
    {
        $db = \Config\Database::connect();
        $db->transStart();

        foreach ($sortData as $id => $sortOrder) {
            $this->update($id, ['sort_order' => $sortOrder]);
        }

        $db->transComplete();
        return $db->transStatus();
    }

    /**
     * Get the default shipping method (lowest cost active method)
     */
    public function getDefaultShippingMethod(): ?array
    {
        return $this->where('is_active', 1)
                   ->orderBy('cost', 'ASC')
                   ->orderBy('sort_order', 'ASC')
                   ->first();
    }

    /**
     * Check if free shipping is available for order amount
     */
    public function hasFreeShipping(float $orderAmount): bool
    {
        $freeShippingMethods = $this->where('is_active', 1)
                                   ->where('is_free_shipping', 1)
                                   ->where('minimum_order_amount <=', $orderAmount)
                                   ->findAll();

        foreach ($freeShippingMethods as $method) {
            if ($method['maximum_order_amount'] === null || $orderAmount <= $method['maximum_order_amount']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get cheapest shipping method for order amount
     */
    public function getCheapestShippingMethod(float $orderAmount): ?array
    {
        $availableMethods = $this->getAvailableShippingMethods($orderAmount);
        
        if (empty($availableMethods)) {
            return null;
        }

        // Sort by cost ascending
        usort($availableMethods, function($a, $b) {
            return $a['cost'] <=> $b['cost'];
        });

        return $availableMethods[0];
    }
}
