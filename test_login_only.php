<?php
/**
 * Test Login Endpoint Only
 */

$baseUrl = 'http://localhost/nandinihub/api/v1';
$testEmail = 'test@example.com';
$testPassword = 'password123';

function makeRequest($url, $method = 'GET', $data = null, $headers = []) {
    $ch = curl_init();
    
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            $headers[] = 'Content-Type: application/json';
        }
    }
    
    if (!empty($headers)) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'status_code' => $httpCode,
        'response' => json_decode($response, true),
        'raw_response' => $response,
        'error' => $error
    ];
}

echo "🔍 Testing Login Endpoint\n";
echo str_repeat("=", 50) . "\n\n";

// Test 1: Check if endpoint exists
echo "1. Testing endpoint availability...\n";
$result = makeRequest($baseUrl . '/auth/login', 'GET');
echo "Status Code: " . $result['status_code'] . "\n";
echo "Response: " . $result['raw_response'] . "\n";
if ($result['error']) {
    echo "cURL Error: " . $result['error'] . "\n";
}
echo "\n";

// Test 2: Test login with valid credentials
echo "2. Testing login with credentials...\n";
$loginData = [
    'email' => $testEmail,
    'password' => $testPassword
];

$result = makeRequest($baseUrl . '/auth/login', 'POST', $loginData);
echo "Status Code: " . $result['status_code'] . "\n";
echo "Raw Response: " . $result['raw_response'] . "\n";

if ($result['response']) {
    echo "Parsed Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n";
} else {
    echo "Failed to parse JSON response\n";
}

if ($result['error']) {
    echo "cURL Error: " . $result['error'] . "\n";
}

// Test 3: Test with wrong credentials
echo "\n3. Testing login with wrong credentials...\n";
$wrongLoginData = [
    'email' => $testEmail,
    'password' => 'wrongpassword'
];

$result = makeRequest($baseUrl . '/auth/login', 'POST', $wrongLoginData);
echo "Status Code: " . $result['status_code'] . "\n";
echo "Raw Response: " . $result['raw_response'] . "\n";

// Test 4: Test registration endpoint
echo "\n4. Testing registration endpoint...\n";
$registerData = [
    'first_name' => 'Test2',
    'last_name' => 'User2',
    'email' => 'test2@example.com',
    'phone' => '9876543211',
    'password' => $testPassword
];

$result = makeRequest($baseUrl . '/auth/register', 'POST', $registerData);
echo "Status Code: " . $result['status_code'] . "\n";
echo "Raw Response: " . $result['raw_response'] . "\n";

echo "\n🎯 Login Test Complete\n";
?>
