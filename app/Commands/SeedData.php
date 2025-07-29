<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\CategoryModel;
use App\Models\ProductModel;

class SeedData extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'db:seed-sample';
    protected $description = 'Seed sample data for testing';

    public function run(array $params)
    {
        CLI::write('🌱 Seeding Sample Data', 'yellow');
        CLI::write(str_repeat('=', 50), 'yellow');
        CLI::newLine();

        $this->seedCategories();
        $this->seedProducts();
        
        CLI::write('🎉 Sample data seeding completed!', 'green');
    }

    private function seedCategories()
    {
        CLI::write('Seeding categories...', 'cyan');
        
        $categoryModel = new CategoryModel();
        
        // Check if categories already exist
        $existingCount = $categoryModel->countAll();
        if ($existingCount > 0) {
            CLI::write("✅ Categories already exist ($existingCount found)", 'green');
            return;
        }

        $categories = [
            [
                'name' => 'Puja Items',
                'description' => 'Essential items for puja and worship',
                'is_active' => 1,
                'sort_order' => 1,
                'parent_id' => null
            ],
            [
                'name' => 'Incense & Dhoop',
                'description' => 'Agarbatti, dhoop sticks and cones',
                'is_active' => 1,
                'sort_order' => 2,
                'parent_id' => null
            ],
            [
                'name' => 'Oils & Ghee',
                'description' => 'Puja oils, ghee and lamps',
                'is_active' => 1,
                'sort_order' => 3,
                'parent_id' => null
            ],
            [
                'name' => 'Flowers & Garlands',
                'description' => 'Fresh and artificial flowers for decoration',
                'is_active' => 1,
                'sort_order' => 4,
                'parent_id' => null
            ],
            [
                'name' => 'Idols & Statues',
                'description' => 'Religious idols and statues',
                'is_active' => 1,
                'sort_order' => 5,
                'parent_id' => null
            ],
            [
                'name' => 'Books & Scriptures',
                'description' => 'Religious books and scriptures',
                'is_active' => 1,
                'sort_order' => 6,
                'parent_id' => null
            ]
        ];

        foreach ($categories as $category) {
            $categoryModel->insert($category);
        }

        CLI::write('✅ Categories seeded successfully', 'green');
    }

    private function seedProducts()
    {
        CLI::write('Seeding products...', 'cyan');
        
        $productModel = new ProductModel();
        
        // Check if products already exist
        $existingCount = $productModel->countAll();
        if ($existingCount > 0) {
            CLI::write("✅ Products already exist ($existingCount found)", 'green');
            return;
        }

        $products = [
            [
                'name' => 'Sandalwood Incense Sticks',
                'short_description' => 'Premium sandalwood agarbatti for daily puja',
                'description' => 'High quality sandalwood incense sticks made from pure sandalwood powder. Perfect for daily worship and meditation.',
                'price' => 150.00,
                'sale_price' => 120.00,
                'sku' => 'INC001',
                'stock_quantity' => 100,
                'category_id' => 2,
                'is_active' => 1,
                'is_featured' => 1,
                'weight' => 100,
                'dimensions' => '20x5x2 cm'
            ],
            [
                'name' => 'Brass Diya Set',
                'short_description' => 'Traditional brass oil lamps set of 5',
                'description' => 'Beautiful handcrafted brass diyas perfect for festivals and daily puja. Set includes 5 different sized lamps.',
                'price' => 500.00,
                'sale_price' => null,
                'sku' => 'DIYA001',
                'stock_quantity' => 50,
                'category_id' => 1,
                'is_active' => 1,
                'is_featured' => 1,
                'weight' => 500,
                'dimensions' => '15x15x5 cm'
            ],
            [
                'name' => 'Ganga Jal (Holy Water)',
                'short_description' => 'Pure Ganga water for puja rituals',
                'description' => 'Sacred water from the holy river Ganga, collected and preserved for religious ceremonies.',
                'price' => 100.00,
                'sale_price' => 80.00,
                'sku' => 'WATER001',
                'stock_quantity' => 200,
                'category_id' => 1,
                'is_active' => 1,
                'is_featured' => 0,
                'weight' => 250,
                'dimensions' => '10x10x15 cm'
            ],
            [
                'name' => 'Marigold Garland',
                'short_description' => 'Fresh marigold flower garland',
                'description' => 'Freshly made marigold garland perfect for deity decoration and festival celebrations.',
                'price' => 50.00,
                'sale_price' => null,
                'sku' => 'FLOWER001',
                'stock_quantity' => 30,
                'category_id' => 4,
                'is_active' => 1,
                'is_featured' => 0,
                'weight' => 50,
                'dimensions' => '100x5x5 cm'
            ],
            [
                'name' => 'Ganesha Idol (Marble)',
                'short_description' => 'Beautiful marble Ganesha statue',
                'description' => 'Handcrafted marble Ganesha idol with intricate details. Perfect for home temple and office.',
                'price' => 2500.00,
                'sale_price' => 2000.00,
                'sku' => 'IDOL001',
                'stock_quantity' => 10,
                'category_id' => 5,
                'is_active' => 1,
                'is_featured' => 1,
                'weight' => 1000,
                'dimensions' => '20x15x25 cm'
            ]
        ];

        foreach ($products as $product) {
            $productModel->insert($product);
        }

        CLI::write('✅ Products seeded successfully', 'green');
    }
}
