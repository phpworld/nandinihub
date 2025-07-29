<?php

namespace App\Controllers\Api;

use App\Models\UserAddressModel;

class AddressApiController extends BaseApiController
{
    protected $userAddressModel;

    public function __construct()
    {
        $this->userAddressModel = new UserAddressModel();
    }

    /**
     * Get user's addresses
     */
    public function index()
    {
        $userId = $this->getAuthenticatedUserId();

        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        $addresses = $this->userAddressModel->where('user_id', $userId)
            ->orderBy('is_default', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Format addresses
        foreach ($addresses as &$address) {
            $address['is_default'] = (bool) $address['is_default'];
            $address['created_at'] = date('Y-m-d H:i:s', strtotime($address['created_at']));
            $address['updated_at'] = date('Y-m-d H:i:s', strtotime($address['updated_at']));
        }

        return $this->successResponse($addresses, 'Addresses retrieved successfully');
    }

    /**
     * Get single address
     */
    public function show($addressId = null)
    {
        $userId = $this->getAuthenticatedUserId();

        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        if (!$addressId) {
            return $this->errorResponse('Address ID is required', 400);
        }

        $address = $this->userAddressModel->where('id', $addressId)
            ->where('user_id', $userId)
            ->first();

        if (!$address) {
            return $this->notFoundResponse('Address not found');
        }

        // Format address
        $address['is_default'] = (bool) $address['is_default'];
        $address['created_at'] = date('Y-m-d H:i:s', strtotime($address['created_at']));
        $address['updated_at'] = date('Y-m-d H:i:s', strtotime($address['updated_at']));

        return $this->successResponse($address, 'Address retrieved successfully');
    }

    /**
     * Create new address
     */
    public function create()
    {
        $userId = $this->getAuthenticatedUserId();

        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        $input = $this->request->getJSON(true) ?? $this->request->getPost();

        // Validate required fields
        $requiredFields = ['name', 'phone', 'address_line1', 'city', 'state', 'pincode', 'country'];
        $errors = $this->validateRequired($input, $requiredFields);

        if (!empty($errors)) {
            return $this->validationErrorResponse($errors);
        }

        // Additional validation
        if (!preg_match('/^[0-9]{10}$/', $input['phone'])) {
            $errors['phone'] = 'Phone number must be 10 digits.';
        }

        if (!preg_match('/^[0-9]{6}$/', $input['pincode'])) {
            $errors['pincode'] = 'Pincode must be 6 digits.';
        }

        if (!empty($errors)) {
            return $this->validationErrorResponse($errors);
        }

        // Sanitize input
        $addressData = $this->sanitizeInput($input);
        $addressData['user_id'] = $userId;
        $addressData['is_default'] = isset($input['is_default']) ? (bool) $input['is_default'] : false;

        // If this is set as default, unset other default addresses
        if ($addressData['is_default']) {
            $this->userAddressModel->where('user_id', $userId)
                ->set(['is_default' => 0])
                ->update();
        }

        // Create address
        $addressId = $this->userAddressModel->insert($addressData);

        if (!$addressId) {
            return $this->errorResponse('Failed to create address', 500);
        }

        // Get created address
        $address = $this->userAddressModel->find($addressId);
        $address['is_default'] = (bool) $address['is_default'];

        return $this->successResponse($address, 'Address created successfully', 201);
    }

    /**
     * Update address
     */
    public function update($addressId = null)
    {
        $userId = $this->getAuthenticatedUserId();

        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        if (!$addressId) {
            return $this->errorResponse('Address ID is required', 400);
        }

        // Check if address belongs to user
        $existingAddress = $this->userAddressModel->where('id', $addressId)
            ->where('user_id', $userId)
            ->first();

        if (!$existingAddress) {
            return $this->notFoundResponse('Address not found');
        }

        $input = $this->request->getJSON(true) ?? $this->request->getPost();

        // Allowed fields for update
        $allowedFields = ['name', 'phone', 'address_line1', 'address_line2', 'city', 'state', 'pincode', 'country', 'is_default'];
        $updateData = [];

        foreach ($allowedFields as $field) {
            if (isset($input[$field])) {
                $updateData[$field] = $input[$field];
            }
        }

        if (empty($updateData)) {
            return $this->errorResponse('No valid fields provided for update', 400);
        }

        // Validate phone and postal code if provided
        $errors = [];
        if (isset($updateData['phone']) && !preg_match('/^[0-9]{10}$/', $updateData['phone'])) {
            $errors['phone'] = 'Phone number must be 10 digits.';
        }

        if (isset($updateData['pincode']) && !preg_match('/^[0-9]{6}$/', $updateData['pincode'])) {
            $errors['pincode'] = 'Pincode must be 6 digits.';
        }

        if (!empty($errors)) {
            return $this->validationErrorResponse($errors);
        }

        // Sanitize input
        $updateData = $this->sanitizeInput($updateData);

        // Handle default address logic
        if (isset($updateData['is_default']) && $updateData['is_default']) {
            // Unset other default addresses
            $this->userAddressModel->where('user_id', $userId)
                ->where('id !=', $addressId)
                ->set(['is_default' => 0])
                ->update();
        }

        // Update address
        $updated = $this->userAddressModel->update($addressId, $updateData);

        if (!$updated) {
            return $this->errorResponse('Failed to update address', 500);
        }

        // Get updated address
        $address = $this->userAddressModel->find($addressId);
        $address['is_default'] = (bool) $address['is_default'];

        return $this->successResponse($address, 'Address updated successfully');
    }

    /**
     * Delete address
     */
    public function delete($addressId = null)
    {
        $userId = $this->getAuthenticatedUserId();

        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        if (!$addressId) {
            return $this->errorResponse('Address ID is required', 400);
        }

        // Check if address belongs to user
        $address = $this->userAddressModel->where('id', $addressId)
            ->where('user_id', $userId)
            ->first();

        if (!$address) {
            return $this->notFoundResponse('Address not found');
        }

        // Delete address
        $deleted = $this->userAddressModel->delete($addressId);

        if (!$deleted) {
            return $this->errorResponse('Failed to delete address', 500);
        }

        // If deleted address was default, set another address as default
        if ($address['is_default']) {
            $nextAddress = $this->userAddressModel->where('user_id', $userId)
                ->first();
            if ($nextAddress) {
                $this->userAddressModel->update($nextAddress['id'], ['is_default' => 1]);
            }
        }

        return $this->successResponse(null, 'Address deleted successfully');
    }

    /**
     * Set address as default
     */
    public function setDefault($addressId = null)
    {
        $userId = $this->getAuthenticatedUserId();

        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        if (!$addressId) {
            return $this->errorResponse('Address ID is required', 400);
        }

        // Check if address belongs to user
        $address = $this->userAddressModel->where('id', $addressId)
            ->where('user_id', $userId)
            ->first();

        if (!$address) {
            return $this->notFoundResponse('Address not found');
        }

        // Unset other default addresses
        $this->userAddressModel->where('user_id', $userId)
            ->set(['is_default' => 0])
            ->update();

        // Set this address as default
        $updated = $this->userAddressModel->update($addressId, ['is_default' => 1]);

        if (!$updated) {
            return $this->errorResponse('Failed to set default address', 500);
        }

        return $this->successResponse(null, 'Default address updated successfully');
    }
}
