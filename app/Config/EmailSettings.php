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
    public string $SMTPHost = 'smtp.hostinger.com';
    public string $SMTPUser = 'info@boxbites.in';
    public string $SMTPPass = 'Anikasingh@2024';
    public int $SMTPPort = 587; // Changed to 587 for TLS
    public string $SMTPCrypto = 'tls'; // Changed to TLS for port 587
    
    // Email Settings
    public string $mailType = 'html';
    public string $charset = 'utf-8';
    public string $newline = "\r\n";
    
    // From Email Settings
    public string $fromEmail = 'info@boxbites.in';
    public string $fromName = 'MICRODOSE MUSHROOM';

    // Admin Email Settings
    public string $adminEmail = 'info@boxbites.in';
    public string $adminName = 'MICRODOSE MUSHROOM - Admin';

    // Site Information
    public string $siteName = 'MICRODOSE MUSHROOM';
    public string $siteUrl = 'https://boxbites.in';
    public string $supportEmail = 'info@boxbites.in';
    
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
