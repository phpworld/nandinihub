<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserDevicesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'device_token' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
            ],
            'platform' => [
                'type' => 'ENUM',
                'constraint' => ['ios', 'android', 'web'],
                'default' => 'android',
            ],
            'device_info' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'notification_preferences' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_used_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey(['user_id', 'device_token']);
        $this->forge->addKey('is_active');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('user_devices');
    }

    public function down()
    {
        $this->forge->dropTable('user_devices');
    }
}
