<?php

namespace App\Controllers\Api;

use App\Models\UserDeviceModel;
use App\Models\NotificationModel;

class NotificationApiController extends BaseApiController
{
    protected $userDeviceModel;
    protected $notificationModel;

    public function __construct()
    {
        $this->userDeviceModel = new UserDeviceModel();
        $this->notificationModel = new NotificationModel();
    }

    /**
     * Register device token for push notifications
     */
    public function registerToken()
    {
        $userId = $this->getAuthenticatedUserId();
        
        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        
        if (!isset($input['token'])) {
            return $this->errorResponse('Push token is required', 400);
        }

        $token = $input['token'];
        $platform = $input['platform'] ?? 'unknown';
        $deviceInfo = $input['device_info'] ?? [];

        try {
            // Check if device already exists
            $existingDevice = $this->userDeviceModel->getByUserAndToken($userId, $token);

            if ($existingDevice) {
                // Update existing device
                $updateData = [
                    'platform' => $platform,
                    'device_info' => json_encode($deviceInfo),
                    'is_active' => 1,
                    'last_used_at' => date('Y-m-d H:i:s')
                ];

                $this->userDeviceModel->update($existingDevice['id'], $updateData);
                
                return $this->successResponse([
                    'device_id' => $existingDevice['id'],
                    'message' => 'Device token updated successfully'
                ]);
            } else {
                // Create new device record
                $deviceData = [
                    'user_id' => $userId,
                    'device_token' => $token,
                    'platform' => $platform,
                    'device_info' => json_encode($deviceInfo),
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'last_used_at' => date('Y-m-d H:i:s')
                ];

                $deviceId = $this->userDeviceModel->insert($deviceData);
                
                if ($deviceId) {
                    return $this->successResponse([
                        'device_id' => $deviceId,
                        'message' => 'Device token registered successfully'
                    ]);
                } else {
                    return $this->errorResponse('Failed to register device token', 500);
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'Failed to register device token: ' . $e->getMessage());
            return $this->errorResponse('Failed to register device token', 500);
        }
    }

    /**
     * Update notification preferences
     */
    public function updatePreferences()
    {
        $userId = $this->getAuthenticatedUserId();
        
        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        $input = $this->request->getJSON(true) ?? $this->request->getPost();
        
        $preferences = [
            'order_updates' => $input['order_updates'] ?? true,
            'promotions' => $input['promotions'] ?? true,
            'general' => $input['general'] ?? true,
            'sound' => $input['sound'] ?? true,
            'vibration' => $input['vibration'] ?? true,
        ];

        try {
            // Update user preferences (you might want to create a UserPreferencesModel)
            // For now, we'll store in user_devices table
            $this->userDeviceModel->updatePreferences($userId, $preferences);

            return $this->successResponse([
                'preferences' => $preferences,
                'message' => 'Notification preferences updated successfully'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to update notification preferences: ' . $e->getMessage());
            return $this->errorResponse('Failed to update preferences', 500);
        }
    }

    /**
     * Get notification preferences
     */
    public function getPreferences()
    {
        $userId = $this->getAuthenticatedUserId();
        
        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        try {
            $preferences = $this->userDeviceModel->getPreferences($userId);
            
            return $this->successResponse([
                'preferences' => $preferences ?: [
                    'order_updates' => true,
                    'promotions' => true,
                    'general' => true,
                    'sound' => true,
                    'vibration' => true,
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to get notification preferences: ' . $e->getMessage());
            return $this->errorResponse('Failed to get preferences', 500);
        }
    }

    /**
     * Get user notifications
     */
    public function getNotifications()
    {
        $userId = $this->getAuthenticatedUserId();
        
        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        $page = $this->request->getGet('page') ?? 1;
        $perPage = $this->request->getGet('per_page') ?? 20;
        $type = $this->request->getGet('type'); // order_update, promotion, general

        try {
            $notifications = $this->notificationModel->getUserNotifications($userId, $page, $perPage, $type);
            $total = $this->notificationModel->getUserNotificationCount($userId, $type);

            return $this->successResponse([
                'notifications' => $notifications,
                'pagination' => [
                    'current_page' => (int)$page,
                    'per_page' => (int)$perPage,
                    'total' => $total,
                    'total_pages' => ceil($total / $perPage),
                    'has_next' => ($page * $perPage) < $total,
                    'has_prev' => $page > 1
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to get notifications: ' . $e->getMessage());
            return $this->errorResponse('Failed to get notifications', 500);
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId = null)
    {
        $userId = $this->getAuthenticatedUserId();
        
        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        if (!$notificationId) {
            return $this->errorResponse('Notification ID is required', 400);
        }

        try {
            $notification = $this->notificationModel->find($notificationId);
            
            if (!$notification) {
                return $this->notFoundResponse('Notification not found');
            }

            if ($notification['user_id'] != $userId) {
                return $this->errorResponse('Unauthorized access', 403);
            }

            $this->notificationModel->markAsRead($notificationId);

            return $this->successResponse([
                'message' => 'Notification marked as read'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to mark notification as read: ' . $e->getMessage());
            return $this->errorResponse('Failed to mark notification as read', 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        $userId = $this->getAuthenticatedUserId();
        
        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        try {
            $this->notificationModel->markAllAsRead($userId);

            return $this->successResponse([
                'message' => 'All notifications marked as read'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to mark all notifications as read: ' . $e->getMessage());
            return $this->errorResponse('Failed to mark notifications as read', 500);
        }
    }

    /**
     * Delete notification
     */
    public function deleteNotification($notificationId = null)
    {
        $userId = $this->getAuthenticatedUserId();
        
        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        if (!$notificationId) {
            return $this->errorResponse('Notification ID is required', 400);
        }

        try {
            $notification = $this->notificationModel->find($notificationId);
            
            if (!$notification) {
                return $this->notFoundResponse('Notification not found');
            }

            if ($notification['user_id'] != $userId) {
                return $this->errorResponse('Unauthorized access', 403);
            }

            $this->notificationModel->delete($notificationId);

            return $this->successResponse([
                'message' => 'Notification deleted successfully'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to delete notification: ' . $e->getMessage());
            return $this->errorResponse('Failed to delete notification', 500);
        }
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount()
    {
        $userId = $this->getAuthenticatedUserId();
        
        if (!$userId) {
            return $this->unauthorizedResponse();
        }

        try {
            $count = $this->notificationModel->getUnreadCount($userId);

            return $this->successResponse([
                'unread_count' => $count
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to get unread count: ' . $e->getMessage());
            return $this->errorResponse('Failed to get unread count', 500);
        }
    }
}
