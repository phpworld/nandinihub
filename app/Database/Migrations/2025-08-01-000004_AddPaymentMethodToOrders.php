<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentMethodToOrders extends Migration
{
    public function up()
    {
        $fields = [
            'payment_method_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'shipping_method_id',
            ],
            'payment_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'paid', 'failed', 'expired'],
                'default'    => 'pending',
                'after'      => 'payment_method_id',
            ],
        ];

        $this->forge->addColumn('orders', $fields);

        // Add foreign key constraint
        $this->forge->addForeignKey('payment_method_id', 'payment_methods', 'id', 'SET NULL', 'CASCADE', 'orders');
    }

    public function down()
    {
        // Drop foreign key first
        $this->forge->dropForeignKey('orders', 'orders_payment_method_id_foreign');
        
        // Drop columns
        $this->forge->dropColumn('orders', ['payment_method_id', 'payment_status']);
    }
}
