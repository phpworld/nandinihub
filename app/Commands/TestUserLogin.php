<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UserModel;

class TestUserLogin extends BaseCommand
{
    protected $group       = 'Debug';
    protected $name        = 'debug:test-login';
    protected $description = 'Test specific user login';

    public function run(array $params)
    {
        $userEmail = 'vinaysingh43@gmail.com';
        $userPassword = '12345678';
        
        CLI::write('🔍 Testing Login for: ' . $userEmail, 'yellow');
        CLI::write(str_repeat('=', 60), 'yellow');
        CLI::newLine();

        try {
            $userModel = new UserModel();
            
            // Check if user exists
            CLI::write('1. Checking if user exists in database...', 'cyan');
            $user = $userModel->getUserByEmail($userEmail);
            
            if ($user) {
                CLI::write('✅ User found in database:', 'green');
                CLI::write('   ID: ' . $user['id'], 'white');
                CLI::write('   Name: ' . $user['first_name'] . ' ' . $user['last_name'], 'white');
                CLI::write('   Email: ' . $user['email'], 'white');
                CLI::write('   Active: ' . ($user['is_active'] ? 'Yes' : 'No'), 'white');
                CLI::write('   Role: ' . $user['role'], 'white');
                CLI::write('   Created: ' . $user['created_at'], 'white');
                
                // Test password verification
                $isValidPassword = $userModel->verifyPassword($userPassword, $user['password']);
                CLI::write('   Password verification: ' . ($isValidPassword ? '✅ Valid' : '❌ Invalid'), $isValidPassword ? 'green' : 'red');
                
                if (!$isValidPassword) {
                    CLI::write('   🔍 Password hash: ' . substr($user['password'], 0, 50) . '...', 'yellow');
                    CLI::write('   🔍 Testing with different password formats...', 'yellow');
                    
                    // Test if password is stored as plain text (shouldn't be)
                    if ($user['password'] === $userPassword) {
                        CLI::write('   ⚠️  Password is stored as plain text!', 'red');
                    }
                    
                    // Test with bcrypt verification
                    if (password_verify($userPassword, $user['password'])) {
                        CLI::write('   ✅ Password verified with password_verify()', 'green');
                    } else {
                        CLI::write('   ❌ Password verification failed with password_verify()', 'red');
                        
                        // Let's try to update the password
                        CLI::write('   🔧 Updating password with correct hash...', 'yellow');
                        $hashedPassword = password_hash($userPassword, PASSWORD_DEFAULT);
                        $updated = $userModel->update($user['id'], ['password' => $hashedPassword]);
                        if ($updated) {
                            CLI::write('   ✅ Password updated successfully', 'green');
                            
                            // Test again
                            $user = $userModel->find($user['id']);
                            $isValidPassword = $userModel->verifyPassword($userPassword, $user['password']);
                            CLI::write('   Password verification after update: ' . ($isValidPassword ? '✅ Valid' : '❌ Invalid'), $isValidPassword ? 'green' : 'red');
                        } else {
                            CLI::write('   ❌ Failed to update password', 'red');
                        }
                    }
                }
                
            } else {
                CLI::write('❌ User not found in database', 'red');
                CLI::write('💡 Creating user...', 'yellow');
                
                // Create the user
                $userData = [
                    'first_name' => 'Vinay',
                    'last_name' => 'Singh',
                    'email' => $userEmail,
                    'phone' => '9876543210',
                    'password' => $userPassword, // Let the model hash it
                    'role' => 'customer',
                    'is_active' => 1
                ];
                
                $userId = $userModel->insert($userData);
                if ($userId) {
                    CLI::write('✅ User created successfully (ID: ' . $userId . ')', 'green');
                    
                    // Verify the created user
                    $newUser = $userModel->find($userId);
                    $isValidPassword = $userModel->verifyPassword($userPassword, $newUser['password']);
                    CLI::write('Password verification for new user: ' . ($isValidPassword ? '✅ Valid' : '❌ Invalid'), $isValidPassword ? 'green' : 'red');
                } else {
                    CLI::write('❌ Failed to create user', 'red');
                    $errors = $userModel->errors();
                    if ($errors) {
                        CLI::write('Validation errors: ' . json_encode($errors), 'red');
                    }
                }
            }
            
            CLI::newLine();
            CLI::write(str_repeat('-', 40), 'yellow');
            
            // Test API login
            CLI::write('2. Testing API login...', 'cyan');
            $this->testApiLogin($userEmail, $userPassword);
            
        } catch (\Exception $e) {
            CLI::write('❌ Error: ' . $e->getMessage(), 'red');
        }
    }
    
    private function testApiLogin($email, $password)
    {
        $baseUrl = 'http://localhost/nandinihub/api/v1';
        
        $loginData = [
            'email' => $email,
            'password' => $password
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/auth/login');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        CLI::write('API Status Code: ' . $httpCode, $httpCode == 200 ? 'green' : 'red');
        
        if ($error) {
            CLI::write('cURL Error: ' . $error, 'red');
            return;
        }
        
        $responseData = json_decode($response, true);
        if ($responseData) {
            if ($responseData['success']) {
                CLI::write('✅ API Login successful!', 'green');
                CLI::write('User: ' . $responseData['data']['user']['first_name'] . ' ' . $responseData['data']['user']['last_name'], 'white');
                CLI::write('Token: ' . substr($responseData['data']['token'], 0, 50) . '...', 'white');
            } else {
                CLI::write('❌ API Login failed: ' . $responseData['message'], 'red');
                if (isset($responseData['errors'])) {
                    CLI::write('Errors: ' . json_encode($responseData['errors']), 'red');
                }
            }
        } else {
            CLI::write('❌ Failed to parse API response', 'red');
            CLI::write('Raw response: ' . $response, 'yellow');
        }
    }
}
