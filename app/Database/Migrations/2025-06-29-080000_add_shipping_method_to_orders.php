<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddShippingMethodToOrders extends Migration
{
    public function up()
    {
        // Add shipping_method_id column to orders table
        $this->forge->addColumn('orders', [
            'shipping_method_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'shipping_amount'
            ]
        ]);

        // Add foreign key constraint
        $this->forge->addForeignKey('shipping_method_id', 'shipping_methods', 'id', 'SET NULL', 'CASCADE', 'fk_orders_shipping_method');
    }

    public function down()
    {
        // Drop foreign key first
        $this->forge->dropForeignKey('orders', 'fk_orders_shipping_method');
        
        // Drop the column
        $this->forge->dropColumn('orders', 'shipping_method_id');
    }
}
