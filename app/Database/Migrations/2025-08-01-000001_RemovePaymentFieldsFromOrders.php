<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemovePaymentFieldsFromOrders extends Migration
{
    public function up()
    {
        // Remove payment_method and payment_status columns from orders table
        $this->forge->dropColumn('orders', ['payment_method', 'payment_status']);
    }

    public function down()
    {
        // Add back payment_method and payment_status columns
        $fields = [
            'payment_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'shipping_method_id'
            ],
            'payment_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'paid', 'failed', 'refunded'],
                'default'    => 'pending',
                'after'      => 'payment_method'
            ]
        ];

        $this->forge->addColumn('orders', $fields);
    }
}
