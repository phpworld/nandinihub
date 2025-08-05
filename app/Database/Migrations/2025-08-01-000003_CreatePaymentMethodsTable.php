<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentMethodsTable extends Migration
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
            'wallet_address' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'qr_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 500,
                'null'       => true,
                'comment'    => 'Path to QR code image or QR code data',
            ],
            'payment_information' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Additional payment instructions or information',
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
        $this->forge->createTable('payment_methods');

        // Insert default cryptocurrency payment methods
        $data = [
            [
                'name' => 'Bitcoin (BTC)',
                'wallet_address' => '',
                'qr_code' => '',
                'payment_information' => 'Send Bitcoin to the wallet address shown. Transaction will be confirmed after 1 confirmation.',
                'is_active' => 0,
                'sort_order' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'USDT (Tether)',
                'wallet_address' => '',
                'qr_code' => '',
                'payment_information' => 'Send USDT to the wallet address shown. Please ensure you are using the correct network (TRC20/ERC20).',
                'is_active' => 0,
                'sort_order' => 2,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'Ethereum (ETH)',
                'wallet_address' => '',
                'qr_code' => '',
                'payment_information' => 'Send Ethereum to the wallet address shown. Transaction will be confirmed after 12 confirmations.',
                'is_active' => 0,
                'sort_order' => 3,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('payment_methods')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('payment_methods');
    }
}
