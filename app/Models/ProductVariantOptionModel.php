<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductVariantOptionModel extends Model
{
    protected $table            = 'product_variant_options';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'variant_id',
        'variation_option_id'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'variant_id'          => 'required|integer',
        'variation_option_id' => 'required|integer'
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get options for a variant
     */
    public function getOptionsByVariant($variantId)
    {
        try {
            return $this->select('product_variant_options.*,
                                 product_variation_options.name as option_name,
                                 product_variation_options.value as option_value,
                                 product_variation_options.color_code,
                                 product_variation_options.image as option_image,
                                 product_variation_options.price_modifier,
                                 product_variation_options.price_type,
                                 product_variation_options.variation_type_id,
                                 product_variation_types.name as type_name,
                                 product_variation_types.display_name as type_display_name,
                                 product_variation_types.type as type_type')
                       ->join('product_variation_options', 'product_variation_options.id = product_variant_options.variation_option_id')
                       ->join('product_variation_types', 'product_variation_types.id = product_variation_options.variation_type_id')
                       ->where('product_variant_options.variant_id', $variantId)
                       ->orderBy('product_variation_types.sort_order', 'ASC')
                       ->findAll();
        } catch (\Exception $e) {
            log_message('error', 'Failed to get options for variant ' . $variantId . ': ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get variants by option combination
     */
    public function getVariantByOptions($productId, $optionIds)
    {
        if (empty($optionIds)) {
            return null;
        }

        // Get all variants for the product
        $variantModel = new ProductVariantModel();
        $variants = $variantModel->getVariantsByProduct($productId);

        foreach ($variants as $variant) {
            $variantOptions = $this->where('variant_id', $variant['id'])->findAll();
            $variantOptionIds = array_column($variantOptions, 'variation_option_id');

            // Check if option IDs match exactly
            if (count($optionIds) === count($variantOptionIds) && 
                empty(array_diff($optionIds, $variantOptionIds))) {
                return $variant;
            }
        }

        return null;
    }

    /**
     * Create variant options
     */
    public function createVariantOptions($variantId, $optionIds)
    {
        $data = [];
        foreach ($optionIds as $optionId) {
            $data[] = [
                'variant_id' => $variantId,
                'variation_option_id' => $optionId
            ];
        }

        return $this->insertBatch($data);
    }

    /**
     * Update variant options
     */
    public function updateVariantOptions($variantId, $optionIds)
    {
        // Delete existing options
        $this->where('variant_id', $variantId)->delete();

        // Insert new options
        return $this->createVariantOptions($variantId, $optionIds);
    }

    /**
     * Delete variant options
     */
    public function deleteVariantOptions($variantId)
    {
        return $this->where('variant_id', $variantId)->delete();
    }

    /**
     * Get all possible option combinations for a product
     */
    public function getProductOptionCombinations($productId)
    {
        $variantModel = new ProductVariantModel();
        $variants = $variantModel->getVariantsByProduct($productId);

        $combinations = [];
        foreach ($variants as $variant) {
            $options = $this->getOptionsByVariant($variant['id']);
            $combinations[] = [
                'variant' => $variant,
                'options' => $options
            ];
        }

        return $combinations;
    }
}
