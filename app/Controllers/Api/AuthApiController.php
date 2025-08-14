<?php

namespace App\Controllers\Api;

use App\Models\UserModel;
use App\Libraries\JwtHelper;

class AuthApiController extends BaseApiController
{
    protected $userModel;
    protected $jwtHelper;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->jwtHelper = new JwtHelper();
    }

    /**
     * User registration
     */
    public function register()
    {
        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        
        // Validate required fields
        $requiredFields = ['first_name', 'last_name', 'email', 'password', 'phone'];
        $errors = $this->validateRequired($input, $requiredFields);
        
        if (!empty($errors)) {
            return $this->validationErrorResponse($errors);
        }

        // Additional validation
        if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please provide a valid email address.';
        }

        if (strlen($input['password']) < 6) {
            $errors['password'] = 'Password must be at least 6 characters long.';
        }

        // Check if email already exists
        if ($this->userModel->getUserByEmail($input['email'])) {
            $errors['email'] = 'Email address is already registered.';
        }

        if (!empty($errors)) {
            return $this->validationErrorResponse($errors);
        }

        // Sanitize input
        $userData = $this->sanitizeInput($input);
        
        // Hash password
        $userData['password'] = password_hash($userData['password'], PASSWORD_DEFAULT);
        $userData['role'] = 'customer';
        $userData['is_active'] = 1;

        // Create user
        $userId = $this->userModel->insert($userData);
        
        if (!$userId) {
            return $this->errorResponse('Failed to create user account', 500);
        }

        // Get created user
        $user = $this->userModel->find($userId);
        unset($user['password']); // Remove password from response

        // Send welcome email
        try {
            $emailService = new \App\Libraries\EmailService();
            $emailResult = $emailService->sendWelcomeEmail($user);
            log_message('info', 'Welcome email result for new API user ' . $user['email'] . ': ' . ($emailResult ? 'SUCCESS' : 'FAILED'));
        } catch (\Exception $e) {
            log_message('error', 'Failed to send welcome email to new API user ' . $user['email'] . ': ' . $e->getMessage());
        }

        // Generate JWT token
        $token = $this->jwtHelper->generateToken($user);
        $refreshToken = $this->jwtHelper->generateRefreshToken($userId);

        return $this->successResponse([
            'user' => $user,
            'token' => $token,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer'
        ], 'User registered successfully', 201);
    }

    /**
     * User login
     */
    public function login()
    {
        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        
        // Validate required fields
        $requiredFields = ['email', 'password'];
        $errors = $this->validateRequired($input, $requiredFields);
        
        if (!empty($errors)) {
            return $this->validationErrorResponse($errors);
        }

        // Get user by email
        $user = $this->userModel->getUserByEmail($input['email']);
        
        if (!$user || !$this->userModel->verifyPassword($input['password'], $user['password'])) {
            return $this->errorResponse('Invalid email or password', 401);
        }

        // Check if user is active
        if (!$user['is_active']) {
            return $this->errorResponse('Your account has been deactivated', 403);
        }

        // Remove password from response
        unset($user['password']);

        // Generate JWT token
        $token = $this->jwtHelper->generateToken($user);
        $refreshToken = $this->jwtHelper->generateRefreshToken($user['id']);

        return $this->successResponse([
            'user' => $user,
            'token' => $token,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer'
        ], 'Login successful');
    }

    /**
     * Refresh token
     */
    public function refresh()
    {
        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        
        if (!isset($input['refresh_token'])) {
            return $this->errorResponse('Refresh token is required', 400);
        }

        // Validate refresh token
        $decoded = $this->jwtHelper->validateRefreshToken($input['refresh_token']);
        
        if (!$decoded) {
            return $this->errorResponse('Invalid or expired refresh token', 401);
        }

        // Get user
        $user = $this->userModel->find($decoded['user_id']);
        
        if (!$user || !$user['is_active']) {
            return $this->errorResponse('User not found or inactive', 404);
        }

        // Remove password from response
        unset($user['password']);

        // Generate new tokens
        $token = $this->jwtHelper->generateToken($user);
        $refreshToken = $this->jwtHelper->generateRefreshToken($user['id']);

        return $this->successResponse([
            'token' => $token,
            'refresh_token' => $refreshToken,
            'token_type' => 'Bearer'
        ], 'Token refreshed successfully');
    }

    /**
     * Get current user profile
     */
    public function profile()
    {
        $userId = $this->getAuthenticatedUserId();
        
        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        $user = $this->userModel->find($userId);
        
        if (!$user) {
            return $this->notFoundResponse('User not found');
        }

        // Remove password from response
        unset($user['password']);

        return $this->successResponse($user, 'Profile retrieved successfully');
    }

    /**
     * Update user profile
     */
    public function updateProfile()
    {
        $userId = $this->getAuthenticatedUserId();
        
        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        
        // Allowed fields for update
        $allowedFields = ['first_name', 'last_name', 'phone', 'date_of_birth', 'gender'];
        $updateData = [];
        
        foreach ($allowedFields as $field) {
            if (isset($input[$field])) {
                $updateData[$field] = $input[$field];
            }
        }

        if (empty($updateData)) {
            return $this->errorResponse('No valid fields provided for update', 400);
        }

        // Sanitize input
        $updateData = $this->sanitizeInput($updateData);

        // Update user
        $updated = $this->userModel->update($userId, $updateData);
        
        if (!$updated) {
            return $this->errorResponse('Failed to update profile', 500);
        }

        // Get updated user
        $user = $this->userModel->find($userId);
        unset($user['password']);

        return $this->successResponse($user, 'Profile updated successfully');
    }

    /**
     * Change password
     */
    public function changePassword()
    {
        $userId = $this->getAuthenticatedUserId();
        
        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        
        // Validate required fields
        $requiredFields = ['current_password', 'new_password'];
        $errors = $this->validateRequired($input, $requiredFields);
        
        if (!empty($errors)) {
            return $this->validationErrorResponse($errors);
        }

        // Get user
        $user = $this->userModel->find($userId);
        
        // Verify current password
        if (!$this->userModel->verifyPassword($input['current_password'], $user['password'])) {
            return $this->errorResponse('Current password is incorrect', 400);
        }

        // Validate new password
        if (strlen($input['new_password']) < 6) {
            return $this->errorResponse('New password must be at least 6 characters long', 400);
        }

        // Update password
        $updated = $this->userModel->update($userId, [
            'password' => password_hash($input['new_password'], PASSWORD_DEFAULT)
        ]);
        
        if (!$updated) {
            return $this->errorResponse('Failed to update password', 500);
        }

        return $this->successResponse(null, 'Password changed successfully');
    }

    /**
     * Logout (token blacklisting would be implemented here in production)
     */
    public function logout()
    {
        // In a production environment, you would blacklist the token here
        // For now, we'll just return a success response
        return $this->successResponse(null, 'Logged out successfully');
    }
}
