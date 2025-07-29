<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVariantSupportToOrderItems extends Migration
{
    public function up()
    {
        $this->forge->addColumn('order_items', [
            'variant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'product_id',
            ],
            'variant_sku' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'variant_id',
            ],
            'variant_options' => [
                'type' => 'JSON',
                'null' => true,
                'after' => 'variant_sku',
                'comment' => 'Selected variation options for this order item',
            ],
        ]);

        // Add foreign key for variant_id
        $this->forge->addForeignKey('variant_id', 'product_variants', 'id', 'SET NULL', 'CASCADE', 'order_items');
    }

    public function down()
    {
        $this->forge->dropForeignKey('order_items', 'order_items_variant_id_foreign');
        $this->forge->dropColumn('order_items', ['variant_id', 'variant_sku', 'variant_options']);
    }
}
