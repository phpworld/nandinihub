<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductVariationSeeder extends Seeder
{
    public function run()
    {
        // Create variation types
        $variationTypes = [
            [
                'name' => 'Size',
                'slug' => 'size',
                'display_name' => 'Size',
                'type' => 'button',
                'is_required' => 1,
                'sort_order' => 1,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Color',
                'slug' => 'color',
                'display_name' => 'Color',
                'type' => 'color',
                'is_required' => 1,
                'sort_order' => 2,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Material',
                'slug' => 'material',
                'display_name' => 'Material',
                'type' => 'text',
                'is_required' => 0,
                'sort_order' => 3,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        // Check if types already exist, if not insert them
        $existingTypes = $this->db->table('product_variation_types')->get()->getResultArray();

        if (empty($existingTypes)) {
            $this->db->table('product_variation_types')->insert($variationTypes[0]);
            $sizeTypeId = $this->db->insertID();

            $this->db->table('product_variation_types')->insert($variationTypes[1]);
            $colorTypeId = $this->db->insertID();

            $this->db->table('product_variation_types')->insert($variationTypes[2]);
            $materialTypeId = $this->db->insertID();
        } else {
            $sizeTypeId = $existingTypes[0]['id'];
            $colorTypeId = $existingTypes[1]['id'];
            $materialTypeId = $existingTypes[2]['id'];
        }

        // Create variation options
        $variationOptions = [
            // Size options
            [
                'variation_type_id' => $sizeTypeId,
                'name' => 'Small',
                'value' => 'S',
                'sort_order' => 1,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'variation_type_id' => $sizeTypeId,
                'name' => 'Medium',
                'value' => 'M',
                'sort_order' => 2,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'variation_type_id' => $sizeTypeId,
                'name' => 'Large',
                'value' => 'L',
                'sort_order' => 3,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'variation_type_id' => $sizeTypeId,
                'name' => 'Extra Large',
                'value' => 'XL',
                'sort_order' => 4,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],

            // Color options
            [
                'variation_type_id' => $colorTypeId,
                'name' => 'Red',
                'value' => 'red',
                'color_code' => '#FF0000',
                'sort_order' => 1,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'variation_type_id' => $colorTypeId,
                'name' => 'Blue',
                'value' => 'blue',
                'color_code' => '#0000FF',
                'sort_order' => 2,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'variation_type_id' => $colorTypeId,
                'name' => 'Green',
                'value' => 'green',
                'color_code' => '#008000',
                'sort_order' => 3,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'variation_type_id' => $colorTypeId,
                'name' => 'Black',
                'value' => 'black',
                'color_code' => '#000000',
                'sort_order' => 4,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],

            // Material options
            [
                'variation_type_id' => $materialTypeId,
                'name' => 'Cotton',
                'value' => 'cotton',
                'sort_order' => 1,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'variation_type_id' => $materialTypeId,
                'name' => 'Silk',
                'value' => 'silk',
                'sort_order' => 2,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'variation_type_id' => $materialTypeId,
                'name' => 'Brass',
                'value' => 'brass',
                'sort_order' => 3,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('product_variation_options')->insertBatch($variationOptions);

        echo "Product variation types and options seeded successfully!\n";
    }
}
