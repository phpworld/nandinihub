<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentMethodModel extends Model
{
    protected $table            = 'payment_methods';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'wallet_address',
        'qr_code',
        'payment_information',
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
        'wallet_address' => 'permit_empty|max_length[500]',
        'qr_code' => 'permit_empty|max_length[500]',
        'payment_information' => 'permit_empty',
        'is_active' => 'in_list[0,1]',
        'sort_order' => 'integer|greater_than_equal_to[0]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Payment method name is required',
            'min_length' => 'Payment method name must be at least 3 characters long',
            'max_length' => 'Payment method name cannot exceed 255 characters'
        ],
        'wallet_address' => [
            'max_length' => 'Wallet address cannot exceed 500 characters'
        ],
        'qr_code' => [
            'max_length' => 'QR code path cannot exceed 500 characters'
        ],
        'is_active' => [
            'in_list' => 'Status must be either active or inactive'
        ],
        'sort_order' => [
            'integer' => 'Sort order must be a number',
            'greater_than_equal_to' => 'Sort order must be 0 or greater'
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
     * Get all active payment methods ordered by sort_order
     */
    public function getActivePaymentMethods(): array
    {
        return $this->where('is_active', 1)
                   ->orderBy('sort_order', 'ASC')
                   ->orderBy('name', 'ASC')
                   ->findAll();
    }

    /**
     * Get all payment methods for admin (including inactive)
     */
    public function getAllPaymentMethods(): array
    {
        return $this->orderBy('sort_order', 'ASC')
                   ->orderBy('name', 'ASC')
                   ->findAll();
    }

    /**
     * Get payment method by ID with validation
     */
    public function getPaymentMethodById(int $methodId): ?array
    {
        return $this->find($methodId);
    }

    /**
     * Get active payment method by ID
     */
    public function getActivePaymentMethodById(int $methodId): ?array
    {
        $method = $this->find($methodId);
        
        if (!$method || !$method['is_active']) {
            return null;
        }

        return $method;
    }

    /**
     * Toggle payment method status
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
     * Get payment methods with pagination for admin
     */
    public function getPaymentMethodsPaginated(int $perPage = 10): array
    {
        return $this->orderBy('sort_order', 'ASC')
                   ->orderBy('name', 'ASC')
                   ->paginate($perPage);
    }

    /**
     * Update sort orders for payment methods
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
     * Get payment methods for checkout (active only)
     */
    public function getPaymentMethodsForCheckout(): array
    {
        return $this->getActivePaymentMethods();
    }

    /**
     * Validate payment method for order
     */
    public function validatePaymentMethodForOrder(int $methodId): array
    {
        $method = $this->getActivePaymentMethodById($methodId);
        
        if (!$method) {
            return [
                'valid' => false,
                'message' => 'Selected payment method is not available'
            ];
        }

        if (empty($method['wallet_address'])) {
            return [
                'valid' => false,
                'message' => 'Payment method is not properly configured'
            ];
        }

        return [
            'valid' => true,
            'method' => $method
        ];
    }

    /**
     * Get next sort order
     */
    public function getNextSortOrder(): int
    {
        $maxSort = $this->selectMax('sort_order')->first();
        return ($maxSort['sort_order'] ?? 0) + 1;
    }
}
