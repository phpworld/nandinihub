<?php

namespace App\Models;

use CodeIgniter\Model;

class UserDeviceModel extends Model
{
    protected $table = 'user_devices';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'device_token',
        'platform',
        'device_info',
        'notification_preferences',
        'is_active',
        'created_at',
        'updated_at',
        'last_used_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'user_id' => 'required|integer',
        'device_token' => 'required|max_length[500]',
        'platform' => 'required|in_list[ios,android,web]'
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required',
            'integer' => 'User ID must be an integer'
        ],
        'device_token' => [
            'required' => 'Device token is required',
            'max_length' => 'Device token is too long'
        ],
        'platform' => [
            'required' => 'Platform is required',
            'in_list' => 'Platform must be ios, android, or web'
        ]
    ];

    /**
     * Get device by user ID and token
     */
    public function getByUserAndToken($userId, $token)
    {
        return $this->where('user_id', $userId)
                   ->where('device_token', $token)
                   ->first();
    }

    /**
     * Get all active devices for a user
     */
    public function getUserDevices($userId)
    {
        return $this->where('user_id', $userId)
                   ->where('is_active', 1)
                   ->findAll();
    }

    /**
     * Deactivate device
     */
    public function deactivateDevice($deviceId)
    {
        return $this->update($deviceId, ['is_active' => 0]);
    }

    /**
     * Update device preferences
     */
    public function updatePreferences($userId, $preferences)
    {
        return $this->where('user_id', $userId)
                   ->set('notification_preferences', json_encode($preferences))
                   ->update();
    }

    /**
     * Get user notification preferences
     */
    public function getPreferences($userId)
    {
        $device = $this->where('user_id', $userId)
                      ->where('is_active', 1)
                      ->first();

        if ($device && $device['notification_preferences']) {
            return json_decode($device['notification_preferences'], true);
        }

        return null;
    }

    /**
     * Get all active device tokens for push notifications
     */
    public function getActiveTokens($userIds = null)
    {
        $builder = $this->where('is_active', 1)
                       ->where('device_token IS NOT NULL');

        if ($userIds) {
            if (is_array($userIds)) {
                $builder->whereIn('user_id', $userIds);
            } else {
                $builder->where('user_id', $userIds);
            }
        }

        return $builder->findAll();
    }

    /**
     * Clean up old inactive devices
     */
    public function cleanupOldDevices($days = 90)
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));
        
        return $this->where('is_active', 0)
                   ->where('updated_at <', $cutoffDate)
                   ->delete();
    }

    /**
     * Update last used timestamp
     */
    public function updateLastUsed($deviceId)
    {
        return $this->update($deviceId, ['last_used_at' => date('Y-m-d H:i:s')]);
    }
}
