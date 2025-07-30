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
        ];

        $this->db->table('settings')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('settings');
    }
}
