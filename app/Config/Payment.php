<?php

namespace App\Config;

use CodeIgniter\Config\BaseConfig;

class Payment extends BaseConfig
{
    /**
     * HDFC SmartGateway Configuration (Powered by Juspay)
     */
    public array $hdfc = [
        // Test Environment Settings
        'test_mode' => true,
        'test_merchant_id' => 'test_merchant_hdfc',
        'test_api_key' => 'test_api_key_here',
        'test_gateway_url' => 'https://smartgatewayuat.hdfcbank.com',
        'test_api_endpoint' => 'https://smartgatewayuat.hdfcbank.com',

        // Production Environment Settings (Use when going live)
        'live_merchant_id' => 'LIVE_MERCHANT_ID',
        'live_api_key' => 'LIVE_API_KEY',
        'live_gateway_url' => 'https://smartgateway.hdfcbank.com',
        'live_api_endpoint' => 'https://smartgateway.hdfcbank.com',

        // Common Settings
        'currency' => 'USD',
        'language' => 'en',
        'country' => 'US',

        // Callback URLs (will be dynamically set based on base_url)
        'return_url' => '',
        'notify_url' => '',
        'cancel_url' => '',

        // Payment Methods
        'payment_options' => [
            'card' => 'Credit/Debit Card',
            'nb' => 'Net Banking',
            'upi' => 'UPI',
            'wallet' => 'Wallet',
            'emi' => 'EMI'
        ],

        // Transaction Settings
        'timeout' => 300, // 5 minutes
        'max_amount' => 100000, // Rs. 1,00,000
        'min_amount' => 1, // Rs. 1

        // API Settings
        'api_version' => 'v1',
        'request_timeout' => 30, // seconds
    ];

    /**
     * Get current environment configuration
     */
    public function getHdfcConfig(): array
    {
        $config = $this->hdfc;
        $baseUrl = base_url();

        if ($config['test_mode']) {
            $config['merchant_id'] = $config['test_merchant_id'];
            $config['api_key'] = $config['test_api_key'];
            $config['gateway_url'] = $config['test_gateway_url'];
            $config['api_endpoint'] = $config['test_api_endpoint'];
        } else {
            $config['merchant_id'] = $config['live_merchant_id'];
            $config['api_key'] = $config['live_api_key'];
            $config['gateway_url'] = $config['live_gateway_url'];
            $config['api_endpoint'] = $config['live_api_endpoint'];
        }

        // Set callback URLs
        $config['return_url'] = $baseUrl . 'payment/callback';
        $config['notify_url'] = $baseUrl . 'payment/webhook';
        $config['cancel_url'] = $baseUrl . 'payment/failure/cancelled';

        return $config;
    }

    /**
     * Payment status mappings for HDFC SmartGateway
     */
    public array $statusMapping = [
        'CHARGED' => 'success',
        'PENDING' => 'pending',
        'PENDING_VBV' => 'pending',
        'AUTHORIZATION_FAILED' => 'failed',
        'AUTHENTICATION_FAILED' => 'failed',
        'JUSPAY_DECLINED' => 'failed',
        'AUTHORIZING' => 'processing',
        'COD_INITIATED' => 'pending',
        'STARTED' => 'processing',
        'AUTO_REFUNDED' => 'refunded',
        'CAPTURE_INITIATED' => 'processing',
        'CAPTURE_FAILED' => 'failed',
        'VOID_INITIATED' => 'processing',
        'VOIDED' => 'cancelled',
        'NOT_FOUND' => 'failed'
    ];
}
