<?php
/**
 * Test Specific User Login
 */

$baseUrl = 'http://localhost/nandinihub/api/v1';
$userEmail = 'vinaysingh43@gmail.com';
$userPassword = '12345678';

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

echo "🔍 Testing Login for: $userEmail\n";
echo str_repeat("=", 60) . "\n\n";

// Test 1: Check if user exists in database
echo "1. Checking if user exists in database...\n";
try {
    // Include CodeIgniter bootstrap
    require_once 'vendor/autoload.php';
    $app = \Config\Services::codeigniter();
    $app->initialize();
    
    $userModel = new \App\Models\UserModel();
    $user = $userModel->getUserByEmail($userEmail);
    
    if ($user) {
        echo "✅ User found in database:\n";
        echo "   ID: " . $user['id'] . "\n";
        echo "   Name: " . $user['first_name'] . " " . $user['last_name'] . "\n";
        echo "   Email: " . $user['email'] . "\n";
        echo "   Active: " . ($user['is_active'] ? 'Yes' : 'No') . "\n";
        echo "   Role: " . $user['role'] . "\n";
        echo "   Created: " . $user['created_at'] . "\n";
        
        // Test password verification
        $isValidPassword = $userModel->verifyPassword($userPassword, $user['password']);
        echo "   Password verification: " . ($isValidPassword ? '✅ Valid' : '❌ Invalid') . "\n";
        
        if (!$isValidPassword) {
            echo "   🔍 Password hash: " . substr($user['password'], 0, 50) . "...\n";
            echo "   🔍 Testing with different password formats...\n";
            
            // Test if password is stored as plain text (shouldn't be)
            if ($user['password'] === $userPassword) {
                echo "   ⚠️  Password is stored as plain text!\n";
            }
            
            // Test with bcrypt verification
            if (password_verify($userPassword, $user['password'])) {
                echo "   ✅ Password verified with password_verify()\n";
            } else {
                echo "   ❌ Password verification failed with password_verify()\n";
            }
        }
        
    } else {
        echo "❌ User not found in database\n";
        echo "💡 Let's create this user...\n";
        
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
            echo "✅ User created successfully (ID: $userId)\n";
        } else {
            echo "❌ Failed to create user\n";
            $errors = $userModel->errors();
            if ($errors) {
                echo "Validation errors: " . json_encode($errors) . "\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("-", 40) . "\n";

// Test 2: Test API login
echo "2. Testing API login...\n";
$loginData = [
    'email' => $userEmail,
    'password' => $userPassword
];

$result = makeRequest($baseUrl . '/auth/login', 'POST', $loginData);
echo "Status Code: " . $result['status_code'] . "\n";
echo "Raw Response: " . $result['raw_response'] . "\n";

if ($result['response']) {
    echo "Parsed Response: " . json_encode($result['response'], JSON_PRETTY_PRINT) . "\n";
    
    if ($result['response']['success'] && isset($result['response']['data']['token'])) {
        echo "✅ Login successful! Token received.\n";
        $token = $result['response']['data']['token'];
        
        // Test 3: Test authenticated request
        echo "\n3. Testing authenticated request...\n";
        $authHeaders = ['Authorization: Bearer ' . $token];
        $profileResult = makeRequest($baseUrl . '/user/profile', 'GET', null, $authHeaders);
        echo "Profile Status: " . $profileResult['status_code'] . "\n";
        echo "Profile Response: " . $profileResult['raw_response'] . "\n";
    }
} else {
    echo "Failed to parse JSON response\n";
}

if ($result['error']) {
    echo "cURL Error: " . $result['error'] . "\n";
}

echo "\n🎯 Login Test Complete\n";
?>
