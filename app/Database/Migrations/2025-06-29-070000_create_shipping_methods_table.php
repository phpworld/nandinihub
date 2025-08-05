<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateShippingMethodsTable extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'delivery_time' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'cost' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'minimum_order_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'maximum_order_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'is_free_shipping' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
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
        $this->forge->addKey('is_active');
        $this->forge->addKey('sort_order');
        $this->forge->createTable('shipping_methods');

        // Insert default shipping methods
        $this->insertDefaultShippingMethods();
    }

    public function down()
    {
        $this->forge->dropTable('shipping_methods');
    }

    private function insertDefaultShippingMethods()
    {
        $data = [
            [
                'name' => 'Standard Shipping',
                'description' => 'Regular delivery service with tracking',
                'delivery_time' => '3–5 Business Days',
                'cost' => 50.00,
                'minimum_order_amount' => 0.00,
                'maximum_order_amount' => null,
                'is_free_shipping' => 0,
                'is_active' => 1,
                'sort_order' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Express Shipping',
                'description' => 'Fast delivery with priority handling',
                'delivery_time' => '1–2 Business Days',
                'cost' => 150.00,
                'minimum_order_amount' => 0.00,
                'maximum_order_amount' => null,
                'is_free_shipping' => 0,
                'is_active' => 1,
                'sort_order' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Free Shipping',
                'description' => 'Free delivery for orders above $500',
                'delivery_time' => '5–7 Business Days',
                'cost' => 0.00,
                'minimum_order_amount' => 500.00,
                'maximum_order_amount' => null,
                'is_free_shipping' => 1,
                'is_active' => 1,
                'sort_order' => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Local Pickup',
                'description' => 'Pick up from our store location',
                'delivery_time' => 'Same Day',
                'cost' => 0.00,
                'minimum_order_amount' => 0.00,
                'maximum_order_amount' => null,
                'is_free_shipping' => 1,
                'is_active' => 1,
                'sort_order' => 4,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('shipping_methods')->insertBatch($data);
    }
}
