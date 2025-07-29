<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePagesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'content' => [
                'type' => 'LONGTEXT',
            ],
            'meta_title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'meta_description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'meta_keywords' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'show_in_header' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'show_in_footer' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'header_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'footer_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'template' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'default',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug', 'pages_slug_unique');
        $this->forge->addKey('is_active');
        $this->forge->createTable('pages');

        // Insert default pages
        $data = [
            [
                'title' => 'About Us',
                'slug' => 'about-us',
                'content' => '<h2>About Nandini Hub</h2><p>Welcome to Nandini Hub, your trusted shopping destination. We are committed to providing you with the best products and exceptional customer service.</p><p>Our mission is to make online shopping convenient, reliable, and enjoyable for everyone.</p>',
                'meta_title' => 'About Us - Nandini Hub',
                'meta_description' => 'Learn more about Nandini Hub, your trusted online shopping destination.',
                'is_active' => 1,
                'show_in_header' => 1,
                'show_in_footer' => 1,
                'header_order' => 1,
                'footer_order' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Contact Us',
                'slug' => 'contact-us',
                'content' => '<h2>Contact Us</h2><p>We\'d love to hear from you! Get in touch with us for any questions, concerns, or feedback.</p><h3>Contact Information</h3><p><strong>Email:</strong> info@nandinihub.com<br><strong>Phone:</strong> +91 9876543210<br><strong>Address:</strong> 123 Business Street, City, State - 123456</p><h3>Business Hours</h3><p>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM - 4:00 PM<br>Sunday: Closed</p>',
                'meta_title' => 'Contact Us - Nandini Hub',
                'meta_description' => 'Get in touch with Nandini Hub for any questions or support.',
                'is_active' => 1,
                'show_in_header' => 1,
                'show_in_footer' => 1,
                'header_order' => 2,
                'footer_order' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Privacy Policy',
                'slug' => 'privacy-policy',
                'content' => '<h2>Privacy Policy</h2><p>This Privacy Policy describes how Nandini Hub collects, uses, and protects your personal information.</p><h3>Information We Collect</h3><p>We collect information you provide directly to us, such as when you create an account, make a purchase, or contact us.</p><h3>How We Use Your Information</h3><p>We use the information we collect to provide, maintain, and improve our services, process transactions, and communicate with you.</p><h3>Information Security</h3><p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>',
                'meta_title' => 'Privacy Policy - Nandini Hub',
                'meta_description' => 'Read our privacy policy to understand how we protect your personal information.',
                'is_active' => 1,
                'show_in_header' => 0,
                'show_in_footer' => 1,
                'header_order' => 0,
                'footer_order' => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Shipping Policy',
                'slug' => 'shipping-policy',
                'content' => '<h2>Shipping Policy</h2><p>We offer various shipping options to meet your needs.</p><h3>Shipping Methods</h3><ul><li><strong>Standard Shipping:</strong> 3-5 business days</li><li><strong>Express Shipping:</strong> 1-2 business days</li><li><strong>Free Shipping:</strong> 5-7 business days (orders over ₹500)</li><li><strong>Local Pickup:</strong> Same day</li></ul><h3>Shipping Costs</h3><p>Shipping costs are calculated based on the weight and destination of your order. Free shipping is available for orders over ₹500.</p><h3>Processing Time</h3><p>Orders are typically processed within 1-2 business days before shipping.</p>',
                'meta_title' => 'Shipping Policy - Nandini Hub',
                'meta_description' => 'Learn about our shipping options, costs, and delivery times.',
                'is_active' => 1,
                'show_in_header' => 0,
                'show_in_footer' => 1,
                'header_order' => 0,
                'footer_order' => 4,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Terms and Conditions',
                'slug' => 'terms-and-conditions',
                'content' => '<h2>Terms and Conditions</h2><p>By using our website and services, you agree to these terms and conditions.</p><h3>Use of Our Service</h3><p>You may use our service for lawful purposes only. You agree not to use the service in any way that could damage, disable, or impair the service.</p><h3>Account Responsibility</h3><p>You are responsible for maintaining the confidentiality of your account and password and for restricting access to your account.</p><h3>Product Information</h3><p>We strive to provide accurate product information, but we do not warrant that product descriptions or other content is accurate, complete, reliable, or error-free.</p><h3>Limitation of Liability</h3><p>In no event shall Nandini Hub be liable for any indirect, incidental, special, or consequential damages.</p>',
                'meta_title' => 'Terms and Conditions - Nandini Hub',
                'meta_description' => 'Read our terms and conditions for using Nandini Hub services.',
                'is_active' => 1,
                'show_in_header' => 0,
                'show_in_footer' => 1,
                'header_order' => 0,
                'footer_order' => 5,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('pages')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('pages');
    }
}
