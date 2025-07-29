<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPricingToVariationOptions extends Migration
{
    public function up()
    {
        $this->forge->addColumn('product_variation_options', [
            'price_modifier' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
                'comment'    => 'Price modifier for this option (can be positive or negative)',
                'after'      => 'image'
            ],
            'price_type' => [
                'type'       => 'ENUM',
                'constraint' => ['fixed', 'percentage'],
                'default'    => 'fixed',
                'comment'    => 'Whether price_modifier is a fixed amount or percentage',
                'after'      => 'price_modifier'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('product_variation_options', ['price_modifier', 'price_type']);
    }
}
