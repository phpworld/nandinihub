<?php
/**
 * Debug API Issues
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include CodeIgniter bootstrap
require_once 'vendor/autoload.php';

// Initialize CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

echo "🔍 Debugging API Issues\n";
echo str_repeat("=", 50) . "\n\n";

// Test 1: Database Connection
echo "1. Testing Database Connection...\n";
try {
    $db = \Config\Database::connect();
    $query = $db->query("SELECT 1 as test");
    $result = $query->getRow();
    if ($result && $result->test == 1) {
        echo "✅ Database connection successful\n";
    } else {
        echo "❌ Database connection failed\n";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

// Test 2: Check if users table exists
echo "\n2. Checking users table...\n";
try {
    $db = \Config\Database::connect();
    $query = $db->query("SHOW TABLES LIKE 'users'");
    $result = $query->getRow();
    if ($result) {
        echo "✅ Users table exists\n";
        
        // Check if there are any users
        $userCount = $db->query("SELECT COUNT(*) as count FROM users")->getRow();
        echo "📊 Total users: " . $userCount->count . "\n";
    } else {
        echo "❌ Users table does not exist\n";
    }
} catch (Exception $e) {
    echo "❌ Users table error: " . $e->getMessage() . "\n";
}

// Test 3: Test JWT Library
echo "\n3. Testing JWT Library...\n";
try {
    $jwtHelper = new \App\Libraries\JwtHelper();
    
    // Test token generation
    $testUser = [
        'id' => 1,
        'email' => 'test@example.com',
        'first_name' => 'Test',
        'last_name' => 'User',
        'role' => 'customer'
    ];
    
    $token = $jwtHelper->generateToken($testUser);
    echo "✅ JWT token generated: " . substr($token, 0, 50) . "...\n";
    
    // Test token validation
    $decoded = $jwtHelper->validateToken($token);
    if ($decoded && isset($decoded['user_id'])) {
        echo "✅ JWT token validation successful\n";
        echo "📊 Decoded user ID: " . $decoded['user_id'] . "\n";
    } else {
        echo "❌ JWT token validation failed\n";
    }
} catch (Exception $e) {
    echo "❌ JWT error: " . $e->getMessage() . "\n";
}

// Test 4: Test User Model
echo "\n4. Testing User Model...\n";
try {
    $userModel = new \App\Models\UserModel();
    
    // Test if we can create a test user
    $testUserData = [
        'first_name' => 'Test',
        'last_name' => 'User',
        'email' => 'test@example.com',
        'phone' => '1234567890',
        'password' => password_hash('password123', PASSWORD_DEFAULT),
        'role' => 'customer',
        'is_active' => 1
    ];
    
    // Check if test user already exists
    $existingUser = $userModel->getUserByEmail('test@example.com');
    if ($existingUser) {
        echo "✅ Test user already exists (ID: " . $existingUser['id'] . ")\n";
    } else {
        // Try to create test user
        $userId = $userModel->insert($testUserData);
        if ($userId) {
            echo "✅ Test user created successfully (ID: $userId)\n";
        } else {
            echo "❌ Failed to create test user\n";
            $errors = $userModel->errors();
            if ($errors) {
                echo "Validation errors: " . json_encode($errors) . "\n";
            }
        }
    }
} catch (Exception $e) {
    echo "❌ User Model error: " . $e->getMessage() . "\n";
}

// Test 5: Test Categories
echo "\n5. Testing Categories...\n";
try {
    $categoryModel = new \App\Models\CategoryModel();
    $categories = $categoryModel->findAll();
    echo "✅ Categories loaded: " . count($categories) . " found\n";
} catch (Exception $e) {
    echo "❌ Categories error: " . $e->getMessage() . "\n";
}

// Test 6: Test Products
echo "\n6. Testing Products...\n";
try {
    $productModel = new \App\Models\ProductModel();
    $products = $productModel->limit(5)->findAll();
    echo "✅ Products loaded: " . count($products) . " found\n";
} catch (Exception $e) {
    echo "❌ Products error: " . $e->getMessage() . "\n";
}

// Test 7: Check required tables
echo "\n7. Checking required tables...\n";
$requiredTables = [
    'users', 'categories', 'products', 'orders', 'order_items', 
    'cart_items', 'addresses', 'payment_transactions'
];

try {
    $db = \Config\Database::connect();
    foreach ($requiredTables as $table) {
        $query = $db->query("SHOW TABLES LIKE '$table'");
        $result = $query->getRow();
        if ($result) {
            echo "✅ Table '$table' exists\n";
        } else {
            echo "❌ Table '$table' missing\n";
        }
    }
} catch (Exception $e) {
    echo "❌ Table check error: " . $e->getMessage() . "\n";
}

echo "\n🎯 Debug Summary:\n";
echo "If you see any ❌ errors above, those need to be fixed first.\n";
echo "Common issues:\n";
echo "- Database not connected\n";
echo "- Missing tables (run migrations)\n";
echo "- JWT configuration issues\n";
echo "- Missing test data\n";
?>
