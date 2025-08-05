<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaymentScreenshotToOrders extends Migration
{
    public function up()
    {
        $fields = [
            'payment_screenshot' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'after'      => 'payment_status',
                'comment'    => 'Path to payment proof screenshot uploaded by customer'
            ],
            'payment_verified' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'payment_screenshot',
                'comment'    => 'Whether admin has verified the payment screenshot'
            ],
            'payment_verified_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'payment_verified',
                'comment' => 'When the payment was verified by admin'
            ],
            'payment_verified_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'payment_verified_at',
                'comment'    => 'Admin user ID who verified the payment'
            ]
        ];

        $this->forge->addColumn('orders', $fields);

        // Add foreign key for payment_verified_by
        $this->forge->addForeignKey('payment_verified_by', 'users', 'id', 'SET NULL', 'CASCADE', 'orders');
    }

    public function down()
    {
        // Drop foreign key first
        $this->forge->dropForeignKey('orders', 'orders_payment_verified_by_foreign');
        
        // Drop columns
        $this->forge->dropColumn('orders', [
            'payment_screenshot', 
            'payment_verified', 
            'payment_verified_at', 
            'payment_verified_by'
        ]);
    }
}
