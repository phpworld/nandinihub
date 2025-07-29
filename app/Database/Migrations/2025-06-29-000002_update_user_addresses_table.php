<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateUserAddressesTable extends Migration
{
    public function up()
    {
        // Add missing columns to user_addresses table
        $fields = [
            'address_line1' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'phone'
            ],
            'address_line2' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'address_line1'
            ],
            'country' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'default' => 'India',
                'after' => 'pincode'
            ],
            'is_default' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'country'
            ]
        ];

        $this->forge->addColumn('user_addresses', $fields);

        // Copy data from 'address' column to 'address_line1' if it exists
        $db = \Config\Database::connect();
        if ($db->fieldExists('address', 'user_addresses')) {
            $db->query("UPDATE user_addresses SET address_line1 = address WHERE address_line1 IS NULL");
        }
    }

    public function down()
    {
        // Remove the added columns
        $this->forge->dropColumn('user_addresses', ['address_line1', 'address_line2', 'country', 'is_default']);
    }
}
