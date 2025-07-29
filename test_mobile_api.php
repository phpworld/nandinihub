<?php

/**
 * Mobile API Testing Script
 * Tests all mobile app API endpoints
 */

// Configuration
$baseUrl = 'http://localhost/nandinihub/api/v1';
$testEmail = 'test@example.com';
$testPassword = 'password123';

// Test results
$results = [];
$authToken = null;

function makeRequest($url, $method = 'GET', $data = null, $headers = [])
{
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $headers[] = 'Content-Type: application/json';
        }
    } elseif ($method === 'PUT') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $headers[] = 'Content-Type: application/json';
        }
    } elseif ($method === 'DELETE') {
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    }

    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return [
        'status_code' => $httpCode,
        'response' => json_decode($response, true),
        'raw_response' => $response
    ];
}

function testEndpoint($name, $url, $method = 'GET', $data = null, $expectedStatus = 200)
{
    global $baseUrl, $authToken, $results;

    $headers = [];
    if ($authToken) {
        $headers[] = "Authorization: Bearer $authToken";
    }

    echo "Testing: $name\n";
    echo "URL: $baseUrl$url\n";
    echo "Method: $method\n";

    $result = makeRequest($baseUrl . $url, $method, $data, $headers);

    $success = $result['status_code'] === $expectedStatus;
    $results[] = [
        'name' => $name,
        'success' => $success,
        'status_code' => $result['status_code'],
        'expected_status' => $expectedStatus,
        'response' => $result['response']
    ];

    echo "Status: " . ($success ? "✅ PASS" : "❌ FAIL") . "\n";
    echo "HTTP Code: {$result['status_code']} (Expected: $expectedStatus)\n";

    if ($result['response']) {
        echo "Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n";
    }

    echo str_repeat("-", 80) . "\n\n";

    return $result;
}

echo "🚀 Starting Mobile API Tests\n";
echo str_repeat("=", 80) . "\n\n";

// Test 1: User Registration
echo "📝 Testing Authentication Endpoints\n\n";

$registerData = [
    'first_name' => 'Test',
    'last_name' => 'User',
    'email' => 'test_' . time() . '@example.com', // Use unique email
    'phone' => '9876543210',
    'password' => $testPassword
];

$registerResult = testEndpoint('User Registration', '/auth/register', 'POST', $registerData, 201);

// Test 2: User Login
$loginData = [
    'email' => $testEmail,
    'password' => $testPassword
];

$loginResult = testEndpoint('User Login', '/auth/login', 'POST', $loginData, 200);

if ($loginResult['response']['success'] ?? false) {
    $authToken = $loginResult['response']['data']['token'] ?? null;
    echo "🔑 Auth token obtained: " . substr($authToken, 0, 20) . "...\n\n";
}

// Test 3: Get Profile
testEndpoint('Get User Profile', '/auth/profile', 'GET', null, 200);

// Test 4: Products Endpoints
echo "🛍️ Testing Product Endpoints\n\n";

testEndpoint('Get Products', '/products', 'GET', null, 200);
testEndpoint('Get Featured Products', '/products/featured', 'GET', null, 200);
testEndpoint('Search Products', '/products/search?q=puja', 'GET', null, 200);

// Test 5: Categories Endpoints
echo "📂 Testing Category Endpoints\n\n";

testEndpoint('Get Categories', '/categories', 'GET', null, 200);
testEndpoint('Get Category Tree', '/categories/tree', 'GET', null, 200);
testEndpoint('Get Popular Categories', '/categories/popular', 'GET', null, 200);

// Test 6: Cart Endpoints
echo "🛒 Testing Cart Endpoints\n\n";

testEndpoint('Get Cart', '/cart', 'GET', null, 200);

// Add item to cart (assuming product ID 1 exists)
$cartData = [
    'product_id' => 1,
    'quantity' => 2
];
testEndpoint('Add to Cart', '/cart/add', 'POST', $cartData, 200);

testEndpoint('Get Cart After Adding', '/cart', 'GET', null, 200);

// Test 7: Orders Endpoints
echo "📦 Testing Order Endpoints\n\n";

testEndpoint('Get Orders', '/orders', 'GET', null, 200);

// Test 8: Addresses Endpoints
echo "📍 Testing Address Endpoints\n\n";

testEndpoint('Get Addresses', '/addresses', 'GET', null, 200);

$addressData = [
    'name' => 'Test Address',
    'phone' => '9876543210',
    'address_line1' => '123 Test Street',
    'city' => 'Test City',
    'state' => 'Test State',
    'pincode' => '123456',
    'country' => 'India'
];
testEndpoint('Create Address', '/addresses', 'POST', $addressData, 201);

// Test 9: Payment Endpoints
echo "💳 Testing Payment Endpoints\n\n";

testEndpoint('Get Payment Methods', '/payment/methods', 'GET', null, 200);

// Test 10: Notification Endpoints
echo "🔔 Testing Notification Endpoints\n\n";

$tokenData = [
    'token' => 'ExponentPushToken[test-token-123]',
    'platform' => 'android',
    'device_info' => [
        'brand' => 'Test',
        'model' => 'Test Device'
    ]
];
testEndpoint('Register Push Token', '/notifications/register-token', 'POST', $tokenData, 200);

testEndpoint('Get Notifications', '/notifications', 'GET', null, 200);
testEndpoint('Get Unread Count', '/notifications/unread-count', 'GET', null, 200);
testEndpoint('Get Notification Preferences', '/notifications/preferences', 'GET', null, 200);

// Test Summary
echo "📊 Test Summary\n";
echo str_repeat("=", 80) . "\n";

$totalTests = count($results);
$passedTests = array_filter($results, function ($result) {
    return $result['success'];
});
$passedCount = count($passedTests);
$failedCount = $totalTests - $passedCount;

echo "Total Tests: $totalTests\n";
echo "Passed: $passedCount ✅\n";
echo "Failed: $failedCount ❌\n";
echo "Success Rate: " . round(($passedCount / $totalTests) * 100, 2) . "%\n\n";

if ($failedCount > 0) {
    echo "❌ Failed Tests:\n";
    foreach ($results as $result) {
        if (!$result['success']) {
            echo "- {$result['name']} (HTTP {$result['status_code']})\n";
        }
    }
    echo "\n";
}

echo "🎉 Mobile API testing completed!\n";

// Save results to file
file_put_contents('mobile_api_test_results.json', json_encode($results, JSON_PRETTY_PRINT));
echo "📄 Results saved to mobile_api_test_results.json\n";
