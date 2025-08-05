<?php

namespace App\Config;

use CodeIgniter\Config\BaseConfig;

class EmailSettings extends BaseConfig
{
    /**
     * Email configuration for order notifications
     */
    
    // SMTP Configuration
    public string $protocol = 'smtp';
    public string $SMTPHost = 'smtp.gmail.com';
    public string $SMTPUser = 'your-email@gmail.com'; // Change this to your email
    public string $SMTPPass = 'your-app-password'; // Change this to your app password
    public int $SMTPPort = 587;
    public string $SMTPCrypto = 'tls';
    
    // Email Settings
    public string $mailType = 'html';
    public string $charset = 'utf-8';
    public string $newline = "\r\n";
    
    // From Email Settings
    public string $fromEmail = 'noreply@nandinihub.com';
    public string $fromName = 'Nandini Hub';
    
    // Admin Email Settings
    public string $adminEmail = 'admin@nandinihub.com'; // Change this to your admin email
    public string $adminName = 'Nandini Hub Admin';
    
    // Site Information
    public string $siteName = 'Nandini Hub';
    public string $siteUrl = 'https://nandinihub.com';
    public string $supportEmail = 'support@nandinihub.com';
    
    /**
     * Email Templates Configuration
     */
    public array $templates = [
        'order_confirmation' => [
            'subject' => 'Order Confirmation - Order #{order_number}',
            'enabled' => true
        ],
        'admin_notification' => [
            'subject' => 'New Order Received - Order #{order_number}',
            'enabled' => true
        ],
        'status_update' => [
            'subject' => 'Order Status Update - Order #{order_number}',
            'enabled' => true
        ],
        'welcome' => [
            'subject' => 'Welcome to Nandini Hub - Your Wellness Journey Begins',
            'enabled' => true
        ]
    ];
    
    /**
     * Order Status Messages for Email Templates
     */
    public array $statusMessages = [
        'pending' => 'Your order is being processed and will be confirmed soon.',
        'confirmed' => 'Your order has been confirmed and is being prepared.',
        'processing' => 'Your order is being prepared for shipment.',
        'shipped' => 'Your order has been shipped and is on its way to you!',
        'delivered' => 'Your order has been delivered. Thank you for shopping with us!',
        'cancelled' => 'Your order has been cancelled as requested.'
    ];
}
