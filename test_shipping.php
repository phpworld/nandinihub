<?php

/**
 * Simple test script to verify shipping method selection
 * Run this from the command line: php test_shipping.php
 */

// Include CodeIgniter bootstrap
require_once 'vendor/autoload.php';

// Set up basic environment
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/test';
$_SERVER['SCRIPT_NAME'] = '/index.php';

// Bootstrap CodeIgniter
$app = \Config\Services::codeigniter();
$app->initialize();

// Test the shipping service
use App\Libraries\ShippingService;
use App\Models\ShippingMethodModel;

echo "Testing Shipping Method Selection\n";
echo "=================================\n\n";

try {
    $shippingService = new ShippingService();
    $shippingModel = new ShippingMethodModel();
    
    // Test different order amounts
    $testAmounts = [100, 250, 500, 1000];
    
    foreach ($testAmounts as $amount) {
        echo "Testing order amount: $" . number_format($amount, 2) . "\n";
        echo "----------------------------------------\n";
        
        // Get available methods
        $availableMethods = $shippingService->getAvailableShippingMethods($amount);
        echo "Available methods: " . count($availableMethods) . "\n";
        
        if (!empty($availableMethods)) {
            foreach ($availableMethods as $method) {
                echo "  - {$method['name']}: $" . number_format($method['cost'], 2) . "\n";
            }
        }
        
        // Get cheapest method
        $cheapestMethod = $shippingService->getBestShippingOption($amount);
        if ($cheapestMethod) {
            echo "Cheapest method: {$cheapestMethod['name']} - $" . number_format($cheapestMethod['cost'], 2) . "\n";
        } else {
            echo "No shipping methods available\n";
        }
        
        // Get formatted methods for checkout
        $formattedMethods = $shippingService->getShippingMethodsForCheckout($amount);
        if (!empty($formattedMethods)) {
            echo "First method (auto-selected): {$formattedMethods[0]['name']} - {$formattedMethods[0]['cost_formatted']}\n";
        }
        
        // Get shipping cost for cart
        $shippingInfo = $shippingService->getCheapestShippingCost($amount);
        echo "Cart shipping info: {$shippingInfo['method_name']} - $" . number_format($shippingInfo['cost'], 2);
        echo $shippingInfo['is_free'] ? " (FREE)" : "";
        echo "\n\n";
    }
    
    echo "Test completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
