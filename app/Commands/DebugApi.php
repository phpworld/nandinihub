<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UserModel;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use App\Libraries\JwtHelper;

class DebugApi extends BaseCommand
{
    protected $group       = 'Debug';
    protected $name        = 'debug:api';
    protected $description = 'Debug API issues and check system status';

    public function run(array $params)
    {
        CLI::write('🔍 Debugging API Issues', 'yellow');
        CLI::write(str_repeat('=', 50), 'yellow');
        CLI::newLine();

        // Test 1: Database Connection
        CLI::write('1. Testing Database Connection...', 'cyan');
        try {
            $db = \Config\Database::connect();
            $query = $db->query("SELECT 1 as test");
            $result = $query->getRow();
            if ($result && $result->test == 1) {
                CLI::write('✅ Database connection successful', 'green');
            } else {
                CLI::write('❌ Database connection failed', 'red');
            }
        } catch (\Exception $e) {
            CLI::write('❌ Database error: ' . $e->getMessage(), 'red');
        }

        // Test 2: Check if users table exists
        CLI::newLine();
        CLI::write('2. Checking users table...', 'cyan');
        try {
            $db = \Config\Database::connect();
            $query = $db->query("SHOW TABLES LIKE 'users'");
            $result = $query->getRow();
            if ($result) {
                CLI::write('✅ Users table exists', 'green');

                // Check if there are any users
                $userCount = $db->query("SELECT COUNT(*) as count FROM users")->getRow();
                CLI::write('📊 Total users: ' . $userCount->count, 'blue');
            } else {
                CLI::write('❌ Users table does not exist', 'red');
                CLI::write('💡 Run: php spark migrate', 'yellow');
            }
        } catch (\Exception $e) {
            CLI::write('❌ Users table error: ' . $e->getMessage(), 'red');
        }

        // Test 3: Test JWT Library
        CLI::newLine();
        CLI::write('3. Testing JWT Library...', 'cyan');
        try {
            $jwtHelper = new JwtHelper();

            // Test token generation
            $testUser = [
                'id' => 1,
                'email' => 'test@example.com',
                'first_name' => 'Test',
                'last_name' => 'User',
                'role' => 'customer'
            ];

            $token = $jwtHelper->generateToken($testUser);
            CLI::write('✅ JWT token generated: ' . substr($token, 0, 50) . '...', 'green');

            // Test token validation
            $decoded = $jwtHelper->validateToken($token);
            if ($decoded && isset($decoded['user_id'])) {
                CLI::write('✅ JWT token validation successful', 'green');
                CLI::write('📊 Decoded user ID: ' . $decoded['user_id'], 'blue');
            } else {
                CLI::write('❌ JWT token validation failed', 'red');
            }
        } catch (\Exception $e) {
            CLI::write('❌ JWT error: ' . $e->getMessage(), 'red');
        }

        // Test 4: Test User Model
        CLI::newLine();
        CLI::write('4. Testing User Model...', 'cyan');
        try {
            $userModel = new UserModel();

            // Check if test user already exists
            $existingUser = $userModel->getUserByEmail('test@example.com');
            if ($existingUser) {
                CLI::write('✅ Test user already exists (ID: ' . $existingUser['id'] . ')', 'green');
            } else {
                // Try to create test user
                $testUserData = [
                    'first_name' => 'Test',
                    'last_name' => 'User',
                    'email' => 'test@example.com',
                    'phone' => '1234567890',
                    'password' => 'password123', // Don't pre-hash, let the model handle it
                    'role' => 'customer',
                    'is_active' => 1
                ];

                $userId = $userModel->insert($testUserData);
                if ($userId) {
                    CLI::write('✅ Test user created successfully (ID: ' . $userId . ')', 'green');
                } else {
                    CLI::write('❌ Failed to create test user', 'red');
                    $errors = $userModel->errors();
                    if ($errors) {
                        CLI::write('Validation errors: ' . json_encode($errors), 'red');
                    }
                }
            }
        } catch (\Exception $e) {
            CLI::write('❌ User Model error: ' . $e->getMessage(), 'red');
        }

        // Test 5: Test Categories
        CLI::newLine();
        CLI::write('5. Testing Categories...', 'cyan');
        try {
            $categoryModel = new CategoryModel();
            $categories = $categoryModel->findAll();
            CLI::write('✅ Categories loaded: ' . count($categories) . ' found', 'green');

            if (count($categories) == 0) {
                CLI::write('💡 Run: php spark db:seed CategorySeeder', 'yellow');
            }
        } catch (\Exception $e) {
            CLI::write('❌ Categories error: ' . $e->getMessage(), 'red');
        }

        // Test 6: Test Products
        CLI::newLine();
        CLI::write('6. Testing Products...', 'cyan');
        try {
            $productModel = new ProductModel();
            $products = $productModel->limit(5)->findAll();
            CLI::write('✅ Products loaded: ' . count($products) . ' found', 'green');

            if (count($products) == 0) {
                CLI::write('💡 Run: php spark db:seed ProductSeeder', 'yellow');
            }
        } catch (\Exception $e) {
            CLI::write('❌ Products error: ' . $e->getMessage(), 'red');
        }

        // Test 7: Check required tables
        CLI::newLine();
        CLI::write('7. Checking required tables...', 'cyan');
        $requiredTables = [
            'users',
            'categories',
            'products',
            'orders',
            'order_items',
            'cart',
            'user_addresses',
            'payment_transactions',
            'notifications',
            'user_devices'
        ];

        $missingTables = [];
        try {
            $db = \Config\Database::connect();
            foreach ($requiredTables as $table) {
                $query = $db->query("SHOW TABLES LIKE '$table'");
                $result = $query->getRow();
                if ($result) {
                    CLI::write("✅ Table '$table' exists", 'green');
                } else {
                    CLI::write("❌ Table '$table' missing", 'red');
                    $missingTables[] = $table;
                }
            }
        } catch (\Exception $e) {
            CLI::write('❌ Table check error: ' . $e->getMessage(), 'red');
        }

        // Summary
        CLI::newLine();
        CLI::write('🎯 Debug Summary:', 'yellow');
        if (!empty($missingTables)) {
            CLI::write('Missing tables: ' . implode(', ', $missingTables), 'red');
            CLI::write('💡 Run: php spark migrate', 'yellow');
        }

        CLI::write('Common fixes:', 'cyan');
        CLI::write('- php spark migrate (create tables)', 'white');
        CLI::write('- php spark db:seed DatabaseSeeder (add sample data)', 'white');
        CLI::write('- Check database connection in .env file', 'white');
        CLI::write('- Ensure JWT secret key is set in App config', 'white');
    }
}
