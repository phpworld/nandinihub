<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UserModel;

class FixTestUser extends BaseCommand
{
    protected $group       = 'Debug';
    protected $name        = 'debug:fix-user';
    protected $description = 'Fix test user with correct password';

    public function run(array $params)
    {
        CLI::write('🔧 Fixing Test User', 'yellow');
        CLI::write(str_repeat('=', 50), 'yellow');
        CLI::newLine();

        try {
            $userModel = new UserModel();
            
            // Delete existing test users
            CLI::write('Deleting existing test users...', 'cyan');
            $userModel->where('email', 'test@example.com')->delete();
            $userModel->where('email', 'test2@example.com')->delete();
            CLI::write('✅ Existing test users deleted', 'green');
            
            // Create new test user with correct password
            CLI::write('Creating new test user...', 'cyan');
            $testUserData = [
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'test@example.com',
                'phone' => '1234567890',
                'password' => 'password123', // Let the model hash this
                'role' => 'customer',
                'is_active' => 1
            ];
            
            $userId = $userModel->insert($testUserData);
            if ($userId) {
                CLI::write('✅ Test user created successfully (ID: ' . $userId . ')', 'green');
                
                // Verify the password works
                $user = $userModel->find($userId);
                $isValid = $userModel->verifyPassword('password123', $user['password']);
                CLI::write('Password verification test: ' . ($isValid ? '✅ Valid' : '❌ Invalid'), $isValid ? 'green' : 'red');
                
                if ($isValid) {
                    CLI::write('🎉 Test user is ready for login testing!', 'green');
                } else {
                    CLI::write('❌ Password verification still failing', 'red');
                }
            } else {
                CLI::write('❌ Failed to create test user', 'red');
                $errors = $userModel->errors();
                if ($errors) {
                    CLI::write('Validation errors: ' . json_encode($errors), 'red');
                }
            }
            
        } catch (\Exception $e) {
            CLI::write('❌ Error: ' . $e->getMessage(), 'red');
        }
    }
}
