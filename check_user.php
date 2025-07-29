<?php
/**
 * Check User Data
 */

// Include CodeIgniter bootstrap
require_once 'vendor/autoload.php';

// Initialize CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

echo "🔍 Checking User Data\n";
echo str_repeat("=", 50) . "\n\n";

try {
    $userModel = new \App\Models\UserModel();
    
    // Get test user
    $user = $userModel->getUserByEmail('test@example.com');
    
    if ($user) {
        echo "✅ Test user found:\n";
        echo "ID: " . $user['id'] . "\n";
        echo "Email: " . $user['email'] . "\n";
        echo "Name: " . $user['first_name'] . " " . $user['last_name'] . "\n";
        echo "Active: " . ($user['is_active'] ? 'Yes' : 'No') . "\n";
        echo "Password Hash: " . substr($user['password'], 0, 50) . "...\n";
        
        // Test password verification
        $testPassword = 'password123';
        $isValid = $userModel->verifyPassword($testPassword, $user['password']);
        echo "Password verification for 'password123': " . ($isValid ? '✅ Valid' : '❌ Invalid') . "\n";
        
        // Test with different password
        $isValid2 = $userModel->verifyPassword('wrongpassword', $user['password']);
        echo "Password verification for 'wrongpassword': " . ($isValid2 ? '✅ Valid' : '❌ Invalid') . "\n";
        
        // Check if password looks like a hash
        if (strlen($user['password']) >= 60 && strpos($user['password'], '$') !== false) {
            echo "✅ Password appears to be properly hashed\n";
        } else {
            echo "❌ Password does not appear to be hashed correctly\n";
        }
        
    } else {
        echo "❌ Test user not found\n";
        
        // List all users
        $allUsers = $userModel->findAll();
        echo "Available users:\n";
        foreach ($allUsers as $u) {
            echo "- ID: {$u['id']}, Email: {$u['email']}, Name: {$u['first_name']} {$u['last_name']}\n";
        }
    }
    
    // Test with the newly registered user
    echo "\n" . str_repeat("-", 30) . "\n";
    $newUser = $userModel->getUserByEmail('test2@example.com');
    if ($newUser) {
        echo "✅ New test user found:\n";
        echo "ID: " . $newUser['id'] . "\n";
        echo "Email: " . $newUser['email'] . "\n";
        echo "Password Hash: " . substr($newUser['password'], 0, 50) . "...\n";
        
        // Test password verification
        $isValid = $userModel->verifyPassword('password123', $newUser['password']);
        echo "Password verification for 'password123': " . ($isValid ? '✅ Valid' : '❌ Invalid') . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
