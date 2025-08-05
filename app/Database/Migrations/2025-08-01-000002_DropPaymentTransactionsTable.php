<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropPaymentTransactionsTable extends Migration
{
    public function up()
    {
        // Drop the payment_transactions table
        $this->forge->dropTable('payment_transactions', true);
    }

    public function down()
    {
        // Recreate the payment_transactions table
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'order_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
            ],
            'transaction_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'gateway_transaction_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'payment_gateway' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'hdfc',
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'currency' => [
                'type'       => 'VARCHAR',
                'constraint' => 3,
                'default'    => 'INR',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'processing', 'success', 'failed', 'cancelled', 'refunded'],
                'default'    => 'pending',
            ],
            'gateway_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'gateway_response' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'payment_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'bank_ref_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'failure_reason' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'processed_at' => [
                'type' => 'DATETIME',
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
        $this->forge->addUniqueKey('transaction_id');
        $this->forge->addKey('order_id');
        $this->forge->addKey('gateway_transaction_id');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('payment_transactions');
    }
}
