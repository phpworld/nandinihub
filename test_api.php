<?php
/**
 * Simple test script to verify Mobile API endpoints
 * Run this from the command line: php test_api.php
 */

echo "=== Nandini Hub Mobile API Test ===\n\n";

$baseUrl = 'http://localhost/nandinihub/api/v1';

// Test 1: Check if API endpoints are accessible
echo "1. Testing API Endpoints Accessibility...\n";

$endpoints = [
    'GET /products' => $baseUrl . '/products',
    'GET /categories' => $baseUrl . '/categories',
    'GET /products/featured' => $baseUrl . '/products/featured',
    'GET /categories/tree' => $baseUrl . '/categories/tree'
];

foreach ($endpoints as $name => $url) {
    $response = @file_get_contents($url);
    if ($response !== false) {
        $data = json_decode($response, true);
        if (isset($data['success'])) {
            echo "   ✓ $name - Response: " . $data['message'] . "\n";
        } else {
            echo "   ✗ $name - Invalid JSON response\n";
        }
    } else {
        echo "   ✗ $name - Failed to connect\n";
    }
}

echo "\n2. Testing Authentication Endpoints...\n";

// Test registration endpoint
$registerUrl = $baseUrl . '/auth/register';
$registerData = [
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => 'test' . time() . '@example.com',
    'password' => 'password123',
    'phone' => '9876543210'
];

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => 'Content-Type: application/json',
        'content' => json_encode($registerData)
    ]
]);

$response = @file_get_contents($registerUrl, false, $context);
if ($response !== false) {
    $data = json_decode($response, true);
    if (isset($data['success']) && $data['success']) {
        echo "   ✓ POST /auth/register - User registered successfully\n";
        $token = $data['data']['token'] ?? null;
        
        if ($token) {
            echo "   ✓ JWT Token generated successfully\n";
            
            // Test protected endpoint
            $profileUrl = $baseUrl . '/auth/profile';
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => 'Authorization: Bearer ' . $token
                ]
            ]);
            
            $profileResponse = @file_get_contents($profileUrl, false, $context);
            if ($profileResponse !== false) {
                $profileData = json_decode($profileResponse, true);
                if (isset($profileData['success']) && $profileData['success']) {
                    echo "   ✓ GET /auth/profile - Protected endpoint accessible with JWT\n";
                } else {
                    echo "   ✗ GET /auth/profile - " . ($profileData['message'] ?? 'Unknown error') . "\n";
                }
            } else {
                echo "   ✗ GET /auth/profile - Failed to access protected endpoint\n";
            }
        }
    } else {
        echo "   ✗ POST /auth/register - " . ($data['message'] ?? 'Registration failed') . "\n";
    }
} else {
    echo "   ✗ POST /auth/register - Failed to connect\n";
}

echo "\n3. Testing Required Files...\n";

$requiredFiles = [
    'app/Libraries/JwtHelper.php',
    'app/Filters/JwtAuth.php',
    'app/Controllers/Api/BaseApiController.php',
    'app/Controllers/Api/AuthApiController.php',
    'app/Controllers/Api/ProductApiController.php',
    'app/Controllers/Api/CategoryApiController.php',
    'app/Controllers/Api/CartApiController.php',
    'app/Controllers/Api/OrderApiController.php',
    'app/Controllers/Api/AddressApiController.php',
    'API_DOCUMENTATION.md'
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "   ✓ $file\n";
    } else {
        echo "   ✗ $file (missing)\n";
    }
}

echo "\n4. Testing JWT Library...\n";

try {
    require_once 'vendor/autoload.php';
    
    // Test JWT Helper
    $jwtHelper = new \App\Libraries\JwtHelper();
    
    $testUser = [
        'id' => 1,
        'email' => 'test@example.com',
        'first_name' => 'Test',
        'last_name' => 'User',
        'role' => 'customer'
    ];
    
    $token = $jwtHelper->generateToken($testUser);
    echo "   ✓ JWT Token generation works\n";
    
    $decoded = $jwtHelper->validateToken($token);
    if ($decoded && isset($decoded['user_id'])) {
        echo "   ✓ JWT Token validation works\n";
    } else {
        echo "   ✗ JWT Token validation failed\n";
    }
    
} catch (Exception $e) {
    echo "   ✗ JWT Library error: " . $e->getMessage() . "\n";
}

echo "\n=== Test Complete ===\n";
echo "API Base URL: $baseUrl\n";
echo "Documentation: API_DOCUMENTATION.md\n";
echo "\nNext Steps:\n";
echo "1. Test endpoints using Postman or curl\n";
echo "2. Create mobile app folder structure\n";
echo "3. Implement mobile app with API integration\n";
?>
