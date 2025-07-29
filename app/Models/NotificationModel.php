<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'title',
        'message',
        'type',
        'data',
        'is_read',
        'read_at',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'user_id' => 'required|integer',
        'title' => 'required|max_length[255]',
        'message' => 'required',
        'type' => 'required|in_list[order_update,promotion,general,system]'
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required',
            'integer' => 'User ID must be an integer'
        ],
        'title' => [
            'required' => 'Title is required',
            'max_length' => 'Title is too long'
        ],
        'message' => [
            'required' => 'Message is required'
        ],
        'type' => [
            'required' => 'Type is required',
            'in_list' => 'Type must be order_update, promotion, general, or system'
        ]
    ];

    /**
     * Get user notifications with pagination
     */
    public function getUserNotifications($userId, $page = 1, $perPage = 20, $type = null)
    {
        $builder = $this->where('user_id', $userId);

        if ($type) {
            $builder->where('type', $type);
        }

        $offset = ($page - 1) * $perPage;

        return $builder->orderBy('created_at', 'DESC')
                      ->limit($perPage, $offset)
                      ->findAll();
    }

    /**
     * Get user notification count
     */
    public function getUserNotificationCount($userId, $type = null)
    {
        $builder = $this->where('user_id', $userId);

        if ($type) {
            $builder->where('type', $type);
        }

        return $builder->countAllResults();
    }

    /**
     * Get unread notification count for user
     */
    public function getUnreadCount($userId)
    {
        return $this->where('user_id', $userId)
                   ->where('is_read', 0)
                   ->countAllResults();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId)
    {
        return $this->update($notificationId, [
            'is_read' => 1,
            'read_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead($userId)
    {
        return $this->where('user_id', $userId)
                   ->where('is_read', 0)
                   ->set([
                       'is_read' => 1,
                       'read_at' => date('Y-m-d H:i:s')
                   ])
                   ->update();
    }

    /**
     * Create notification for user
     */
    public function createNotification($userId, $title, $message, $type = 'general', $data = null)
    {
        $notificationData = [
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'data' => $data ? json_encode($data) : null,
            'is_read' => 0
        ];

        return $this->insert($notificationData);
    }

    /**
     * Create order update notification
     */
    public function createOrderNotification($userId, $orderId, $status, $message = null)
    {
        $title = 'Order Update';
        $defaultMessage = "Your order #{$orderId} status has been updated to {$status}";
        
        $data = [
            'order_id' => $orderId,
            'status' => $status
        ];

        return $this->createNotification(
            $userId,
            $title,
            $message ?: $defaultMessage,
            'order_update',
            $data
        );
    }

    /**
     * Create promotion notification
     */
    public function createPromotionNotification($userId, $title, $message, $productId = null, $categoryId = null)
    {
        $data = [];
        
        if ($productId) {
            $data['product_id'] = $productId;
        }
        
        if ($categoryId) {
            $data['category_id'] = $categoryId;
        }

        return $this->createNotification(
            $userId,
            $title,
            $message,
            'promotion',
            $data
        );
    }

    /**
     * Create system notification
     */
    public function createSystemNotification($userId, $title, $message, $data = null)
    {
        return $this->createNotification(
            $userId,
            $title,
            $message,
            'system',
            $data
        );
    }

    /**
     * Bulk create notifications for multiple users
     */
    public function createBulkNotifications($userIds, $title, $message, $type = 'general', $data = null)
    {
        $notifications = [];
        $timestamp = date('Y-m-d H:i:s');

        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'data' => $data ? json_encode($data) : null,
                'is_read' => 0,
                'created_at' => $timestamp,
                'updated_at' => $timestamp
            ];
        }

        return $this->insertBatch($notifications);
    }

    /**
     * Delete old read notifications
     */
    public function cleanupOldNotifications($days = 30)
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->where('is_read', 1)
                   ->where('read_at <', $cutoffDate)
                   ->delete();
    }

    /**
     * Get recent notifications for dashboard
     */
    public function getRecentNotifications($userId, $limit = 5)
    {
        return $this->where('user_id', $userId)
                   ->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }
}
