<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVariantSupportToCart extends Migration
{
    public function up()
    {
        $this->forge->addColumn('cart', [
            'variant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'product_id',
            ],
            'variant_options' => [
                'type' => 'JSON',
                'null' => true,
                'after' => 'variant_id',
                'comment' => 'Selected variation options for this cart item',
            ],
        ]);

        // Add foreign key for variant_id
        $this->forge->addForeignKey('variant_id', 'product_variants', 'id', 'CASCADE', 'CASCADE', 'cart');
    }

    public function down()
    {
        $this->forge->dropForeignKey('cart', 'cart_variant_id_foreign');
        $this->forge->dropColumn('cart', ['variant_id', 'variant_options']);
    }
}
