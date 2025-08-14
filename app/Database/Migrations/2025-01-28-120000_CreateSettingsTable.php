<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'setting_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'setting_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'setting_type' => [
                'type'       => 'ENUM',
                'constraint' => ['string', 'text', 'number', 'boolean', 'json'],
                'default'    => 'string',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addUniqueKey('setting_key');
        $this->forge->createTable('settings');

        // Insert default settings
        $data = [
            [
                'setting_key'   => 'site_name',
                'setting_value' => 'Microdose Mushroom',
                'setting_type'  => 'string',
                'description'   => 'Website name displayed in header and title',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'site_tagline',
                'setting_value' => 'Your Trusted Microdose Destination',
                'setting_type'  => 'string',
                'description'   => 'Website tagline or slogan',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'site_description',
                'setting_value' => 'Microdose Mushroom is your one-stop destination for quality microdose products at affordable prices.',
                'setting_type'  => 'text',
                'description'   => 'Website description for SEO',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'contact_email',
                'setting_value' => 'info@microdosemushroom.com',
                'setting_type'  => 'string',
                'description'   => 'Primary contact email address',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'contact_phone',
                'setting_value' => '+91 9876543210',
                'setting_type'  => 'string',
                'description'   => 'Primary contact phone number',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'google_analytics_id',
                'setting_value' => '',
                'setting_type'  => 'string',
                'description'   => 'Google Analytics Measurement ID (e.g., G-XXXXXXXXXX)',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'google_analytics_enabled',
                'setting_value' => '0',
                'setting_type'  => 'boolean',
                'description'   => 'Enable or disable Google Analytics tracking',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'currency',
                'setting_value' => 'USD',
                'setting_type'  => 'string',
                'description'   => 'Default currency code (USD, INR, EUR, etc.)',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'currency_symbol',
                'setting_value' => '$',
                'setting_type'  => 'string',
                'description'   => 'Currency symbol to display',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'currency_position',
                'setting_value' => 'before',
                'setting_type'  => 'string',
                'description'   => 'Currency symbol position (before/after)',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'footer_text',
                'setting_value' => 'Your trusted source for premium quality microdose mushrooms and psychedelic products. Bringing natural wellness to your doorstep.',
                'setting_type'  => 'text',
                'description'   => 'Footer description text',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'footer_copyright',
                'setting_value' => '© 2024 Microdose Mushroom. All rights reserved.',
                'setting_type'  => 'string',
                'description'   => 'Footer copyright text',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'business_address',
                'setting_value' => '123 Business Street, City, State - 123456',
                'setting_type'  => 'text',
                'description'   => 'Business address for invoices and contact',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'timezone',
                'setting_value' => 'Asia/Kolkata',
                'setting_type'  => 'string',
                'description'   => 'Default timezone for the website',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'date_format',
                'setting_value' => 'd/m/Y',
                'setting_type'  => 'string',
                'description'   => 'Date format for display',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'site_logo',
                'setting_value' => '',
                'setting_type'  => 'string',
                'description'   => 'Site logo file path',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'site_favicon',
                'setting_value' => '',
                'setting_type'  => 'string',
                'description'   => 'Site favicon file path',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'custom_js_enabled',
                'setting_value' => '0',
                'setting_type'  => 'boolean',
                'description'   => 'Enable custom JavaScript injection',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'setting_key'   => 'custom_js_footer',
                'setting_value' => '',
                'setting_type'  => 'text',
                'description'   => 'Custom JavaScript code for footer',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('settings')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('settings');
    }
}
