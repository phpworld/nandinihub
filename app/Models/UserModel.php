<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'address',
        'city',
        'state',
        'pincode',
        'is_active',
        'role'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'first_name' => 'required|min_length[2]|max_length[100]',
        'last_name'  => 'required|min_length[2]|max_length[100]',
        'email'      => 'required|valid_email|is_unique[users.email,id,{id}]',
        'password'   => 'required|min_length[6]',
        'phone'      => 'permit_empty|min_length[10]|max_length[20]',
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'This email is already registered.'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['hashPassword'];
    protected $beforeUpdate   = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public function getUserByEmail($email)
    {
        return $this->where('email', $email)->where('is_active', 1)->first();
    }

    public function getFullName($user)
    {
        return $user['first_name'] . ' ' . $user['last_name'];
    }
}
