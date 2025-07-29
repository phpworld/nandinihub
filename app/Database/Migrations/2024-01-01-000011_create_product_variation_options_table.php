<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductVariationOptionsTable extends Migration
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
            'variation_type_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'value' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'color_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 7,
                'null'       => true,
                'comment'    => 'Hex color code for color variations',
            ],
            'image' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Image for image-based variations',
            ],
            'sort_order' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
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
        $this->forge->addKey('variation_type_id');
        $this->forge->addKey('is_active');
        $this->forge->addKey('sort_order');
        $this->forge->addForeignKey('variation_type_id', 'product_variation_types', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('product_variation_options');
    }

    public function down()
    {
        $this->forge->dropTable('product_variation_options');
    }
}
