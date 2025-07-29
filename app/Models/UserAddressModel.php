<?php

namespace App\Models;

use CodeIgniter\Model;

class UserAddressModel extends Model
{
    protected $table = 'user_addresses';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id',
        'name',
        'phone',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'pincode',
        'country',
        'is_default',
        'landmark'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation rules
    protected $validationRules = [
        'user_id' => 'required|integer',
        'name' => 'required|min_length[2]|max_length[100]',
        'phone' => 'required|min_length[10]|max_length[20]',
        'address_line1' => 'required|max_length[255]',
        'address_line2' => 'permit_empty|max_length[255]',
        'city' => 'required|max_length[100]',
        'state' => 'required|max_length[100]',
        'pincode' => 'required|min_length[4]|max_length[10]',
        'country' => 'permit_empty|max_length[100]',
        'is_default' => 'permit_empty|in_list[0,1]',
        'landmark' => 'permit_empty|max_length[255]'
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Name is required',
            'min_length' => 'Name must be at least 2 characters long'
        ],
        'phone' => [
            'required' => 'Phone number is required',
            'min_length' => 'Phone number must be at least 10 digits'
        ],
        'address_line1' => [
            'required' => 'Address is required'
        ],
        'city' => [
            'required' => 'City is required'
        ],
        'state' => [
            'required' => 'State is required'
        ],
        'pincode' => [
            'required' => 'Pincode is required',
            'exact_length' => 'Pincode must be exactly 6 digits',
            'numeric' => 'Pincode must contain only numbers'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get user addresses with formatted address
     */
    public function getUserAddresses($userId)
    {
        $addresses = $this->where('user_id', $userId)->orderBy('is_default', 'DESC')->orderBy('created_at', 'DESC')->findAll();

        // Format addresses for display
        foreach ($addresses as &$address) {
            $address['full_address'] = $this->formatAddress($address);
        }

        return $addresses;
    }

    /**
     * Format address for display
     */
    public function formatAddress($address)
    {
        $parts = [];

        if (!empty($address['address_line1'])) {
            $parts[] = $address['address_line1'];
        }

        if (!empty($address['address_line2'])) {
            $parts[] = $address['address_line2'];
        }

        if (!empty($address['landmark'])) {
            $parts[] = 'Near ' . $address['landmark'];
        }

        $cityStatePincode = [];
        if (!empty($address['city'])) {
            $cityStatePincode[] = $address['city'];
        }
        if (!empty($address['state'])) {
            $cityStatePincode[] = $address['state'];
        }
        if (!empty($address['pincode'])) {
            $cityStatePincode[] = $address['pincode'];
        }

        if (!empty($cityStatePincode)) {
            $parts[] = implode(', ', $cityStatePincode);
        }

        if (!empty($address['country']) && $address['country'] !== 'India') {
            $parts[] = $address['country'];
        }

        return implode(', ', $parts);
    }

    /**
     * Set address as default and unset others
     */
    public function setAsDefault($addressId, $userId)
    {
        // First, unset all default addresses for this user
        $this->where('user_id', $userId)->set(['is_default' => 0])->update();

        // Then set the specified address as default
        return $this->update($addressId, ['is_default' => 1]);
    }
}
