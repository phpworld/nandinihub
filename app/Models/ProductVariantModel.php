<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductVariantModel extends Model
{
    protected $table            = 'product_variants';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'product_id',
        'sku',
        'price',
        'sale_price',
        'stock_quantity',
        'weight',
        'dimensions',
        'image',
        'is_default',
        'is_active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'product_id'     => 'required|integer',
        'sku'            => 'required|min_length[1]|max_length[100]|is_unique[product_variants.sku,id,{id}]',
        'price'          => 'permit_empty|decimal|greater_than_equal_to[0]',
        'sale_price'     => 'permit_empty|decimal|greater_than_equal_to[0]',
        'stock_quantity' => 'permit_empty|integer|greater_than_equal_to[0]',
        'weight'         => 'permit_empty|decimal|greater_than_equal_to[0]',
        'dimensions'     => 'permit_empty|max_length[255]',
        'is_default'     => 'permit_empty|in_list[0,1]',
        'is_active'      => 'permit_empty|in_list[0,1]'
    ];

    protected $validationMessages = [
        'sku' => [
            'is_unique' => 'This variant SKU already exists.'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get variants by product ID
     */
    public function getVariantsByProduct($productId)
    {
        return $this->where('product_id', $productId)
                   ->where('is_active', 1)
                   ->orderBy('is_default', 'DESC')
                   ->orderBy('id', 'ASC')
                   ->findAll();
    }

    /**
     * Get variant with its options
     */
    public function getVariantWithOptions($id)
    {
        $variant = $this->find($id);
        if (!$variant) {
            return null;
        }

        // Get variant options
        $variantOptionModel = new ProductVariantOptionModel();
        $variant['options'] = $variantOptionModel->getOptionsByVariant($id);

        return $variant;
    }

    /**
     * Get variants with their options by product ID
     */
    public function getVariantsWithOptionsByProduct($productId)
    {
        $variants = $this->getVariantsByProduct($productId);
        $variantOptionModel = new ProductVariantOptionModel();

        foreach ($variants as &$variant) {
            $variant['options'] = $variantOptionModel->getOptionsByVariant($variant['id']);
        }

        return $variants;
    }

    /**
     * Get default variant for a product
     */
    public function getDefaultVariant($productId)
    {
        return $this->where('product_id', $productId)
                   ->where('is_default', 1)
                   ->where('is_active', 1)
                   ->first();
    }

    /**
     * Get variant by SKU
     */
    public function getVariantBySku($sku)
    {
        return $this->where('sku', $sku)
                   ->where('is_active', 1)
                   ->first();
    }

    /**
     * Get variant price (with fallback to product price)
     */
    public function getVariantPrice($variant, $product = null)
    {
        // If variant has sale price, use it
        if (!empty($variant['sale_price'])) {
            return $variant['sale_price'];
        }

        // If variant has regular price, use it
        if (!empty($variant['price'])) {
            return $variant['price'];
        }

        // Fallback to product price
        if ($product) {
            return $product['sale_price'] ?? $product['price'];
        }

        return 0;
    }

    /**
     * Check if variant is in stock
     */
    public function isInStock($variantId, $quantity = 1)
    {
        $variant = $this->find($variantId);
        return $variant && $variant['stock_quantity'] >= $quantity;
    }

    /**
     * Update stock quantity
     */
    public function updateStock($variantId, $quantity, $operation = 'subtract')
    {
        $variant = $this->find($variantId);
        if (!$variant) {
            return false;
        }

        $newQuantity = $operation === 'add' 
            ? $variant['stock_quantity'] + $quantity 
            : $variant['stock_quantity'] - $quantity;

        $newQuantity = max(0, $newQuantity); // Ensure stock doesn't go negative

        return $this->update($variantId, ['stock_quantity' => $newQuantity]);
    }
}
