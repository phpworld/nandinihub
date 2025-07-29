<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductVariationOptionModel extends Model
{
    protected $table            = 'product_variation_options';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'variation_type_id',
        'name',
        'value',
        'color_code',
        'image',
        'price_modifier',
        'price_type',
        'sort_order',
        'is_active'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'variation_type_id' => 'required|integer',
        'name'              => 'required|min_length[1]|max_length[100]',
        'value'             => 'required|min_length[1]|max_length[255]',
        'color_code'        => 'permit_empty|regex_match[/^#[0-9A-Fa-f]{6}$/]',
        'price_modifier'    => 'permit_empty|decimal',
        'price_type'        => 'in_list[fixed,percentage]',
        'sort_order'        => 'integer',
        'is_active'         => 'in_list[0,1]'
    ];

    protected $validationMessages = [
        'color_code' => [
            'regex_match' => 'Color code must be a valid hex color (e.g., #FF0000).'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Get options by variation type
     */
    public function getOptionsByType($typeId)
    {
        return $this->where('variation_type_id', $typeId)
                   ->where('is_active', 1)
                   ->orderBy('sort_order', 'ASC')
                   ->orderBy('name', 'ASC')
                   ->findAll();
    }

    /**
     * Get option with variation type info
     */
    public function getOptionWithType($id)
    {
        return $this->select('product_variation_options.*, product_variation_types.name as type_name, product_variation_types.type as type_display')
                   ->join('product_variation_types', 'product_variation_types.id = product_variation_options.variation_type_id')
                   ->where('product_variation_options.id', $id)
                   ->first();
    }

    /**
     * Get options by multiple IDs
     */
    public function getOptionsByIds($ids)
    {
        if (empty($ids)) {
            return [];
        }

        return $this->select('product_variation_options.*, product_variation_types.name as type_name, product_variation_types.type as type_display')
                   ->join('product_variation_types', 'product_variation_types.id = product_variation_options.variation_type_id')
                   ->whereIn('product_variation_options.id', $ids)
                   ->where('product_variation_options.is_active', 1)
                   ->findAll();
    }

    /**
     * Get all options with type information
     */
    public function getOptionsWithTypes()
    {
        return $this->select('product_variation_options.*, product_variation_types.name as type_name, product_variation_types.type as type_display')
                   ->join('product_variation_types', 'product_variation_types.id = product_variation_options.variation_type_id')
                   ->where('product_variation_options.is_active', 1)
                   ->where('product_variation_types.is_active', 1)
                   ->orderBy('product_variation_types.sort_order', 'ASC')
                   ->orderBy('product_variation_options.sort_order', 'ASC')
                   ->findAll();
    }

    /**
     * Calculate total price modifier for selected options
     */
    public function calculatePriceModifier($optionIds, $basePrice)
    {
        if (empty($optionIds) || !$basePrice) {
            return 0;
        }

        $options = $this->getOptionsByIds($optionIds);
        $totalModifier = 0;

        foreach ($options as $option) {
            if ($option['price_modifier'] != 0) {
                if ($option['price_type'] === 'percentage') {
                    $totalModifier += ($basePrice * $option['price_modifier'] / 100);
                } else {
                    $totalModifier += $option['price_modifier'];
                }
            }
        }

        return $totalModifier;
    }

    /**
     * Get final price with option modifiers
     */
    public function getFinalPrice($optionIds, $basePrice)
    {
        $modifier = $this->calculatePriceModifier($optionIds, $basePrice);
        return max(0, $basePrice + $modifier); // Ensure price doesn't go negative
    }
}
