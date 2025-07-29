<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UserModel;

class CheckUser extends BaseCommand
{
    protected $group       = 'Debug';
    protected $name        = 'debug:user';
    protected $description = 'Check user data and password verification';

    public function run(array $params)
    {
        CLI::write('🔍 Checking User Data', 'yellow');
        CLI::write(str_repeat('=', 50), 'yellow');
        CLI::newLine();

        try {
            $userModel = new UserModel();
            
            // Get test user
            $user = $userModel->getUserByEmail('test@example.com');
            
            if ($user) {
                CLI::write('✅ Test user found:', 'green');
                CLI::write('ID: ' . $user['id'], 'white');
                CLI::write('Email: ' . $user['email'], 'white');
                CLI::write('Name: ' . $user['first_name'] . ' ' . $user['last_name'], 'white');
                CLI::write('Active: ' . ($user['is_active'] ? 'Yes' : 'No'), 'white');
                CLI::write('Password Hash: ' . substr($user['password'], 0, 50) . '...', 'white');
                
                // Test password verification
                $testPassword = 'password123';
                $isValid = $userModel->verifyPassword($testPassword, $user['password']);
                CLI::write('Password verification for \'password123\': ' . ($isValid ? '✅ Valid' : '❌ Invalid'), $isValid ? 'green' : 'red');
                
                // Test with different password
                $isValid2 = $userModel->verifyPassword('wrongpassword', $user['password']);
                CLI::write('Password verification for \'wrongpassword\': ' . ($isValid2 ? '✅ Valid' : '❌ Invalid'), $isValid2 ? 'green' : 'red');
                
                // Check if password looks like a hash
                if (strlen($user['password']) >= 60 && strpos($user['password'], '$') !== false) {
                    CLI::write('✅ Password appears to be properly hashed', 'green');
                } else {
                    CLI::write('❌ Password does not appear to be hashed correctly', 'red');
                    CLI::write('Password length: ' . strlen($user['password']), 'yellow');
                    CLI::write('Password content: ' . $user['password'], 'yellow');
                }
                
            } else {
                CLI::write('❌ Test user not found', 'red');
                
                // List all users
                $allUsers = $userModel->findAll();
                CLI::write('Available users:', 'cyan');
                foreach ($allUsers as $u) {
                    CLI::write("- ID: {$u['id']}, Email: {$u['email']}, Name: {$u['first_name']} {$u['last_name']}", 'white');
                }
            }
            
            // Test with the newly registered user
            CLI::newLine();
            CLI::write(str_repeat('-', 30), 'yellow');
            $newUser = $userModel->getUserByEmail('test2@example.com');
            if ($newUser) {
                CLI::write('✅ New test user found:', 'green');
                CLI::write('ID: ' . $newUser['id'], 'white');
                CLI::write('Email: ' . $newUser['email'], 'white');
                CLI::write('Password Hash: ' . substr($newUser['password'], 0, 50) . '...', 'white');
                
                // Test password verification
                $isValid = $userModel->verifyPassword('password123', $newUser['password']);
                CLI::write('Password verification for \'password123\': ' . ($isValid ? '✅ Valid' : '❌ Invalid'), $isValid ? 'green' : 'red');
                
                // Now test login with this user
                CLI::newLine();
                CLI::write('Testing login with new user...', 'cyan');
                
                // Simulate login process
                $loginEmail = 'test2@example.com';
                $loginPassword = 'password123';
                
                $loginUser = $userModel->getUserByEmail($loginEmail);
                if ($loginUser && $userModel->verifyPassword($loginPassword, $loginUser['password'])) {
                    CLI::write('✅ Login simulation successful', 'green');
                } else {
                    CLI::write('❌ Login simulation failed', 'red');
                }
            }
            
        } catch (\Exception $e) {
            CLI::write('❌ Error: ' . $e->getMessage(), 'red');
        }
    }
}
