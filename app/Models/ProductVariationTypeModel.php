<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductVariationTypeModel extends Model
{
    protected $table            = 'product_variation_types';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'slug',
        'display_name',
        'type',
        'is_required',
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
        'name'         => 'required|min_length[2]|max_length[100]',
        'slug'         => 'required|alpha_dash|is_unique[product_variation_types.slug,id,{id}]',
        'display_name' => 'required|min_length[2]|max_length[100]',
        'type'         => 'required|in_list[text,color,image,button]',
        'is_required'  => 'in_list[0,1]',
        'sort_order'   => 'integer',
        'is_active'    => 'in_list[0,1]'
    ];

    protected $validationMessages = [
        'slug' => [
            'is_unique' => 'This variation type slug already exists.'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['generateSlug'];
    protected $beforeUpdate   = ['generateSlug'];

    protected function generateSlug(array $data)
    {
        if (isset($data['data']['name']) && empty($data['data']['slug'])) {
            $data['data']['slug'] = url_title($data['data']['name'], '-', true);
        }
        return $data;
    }

    /**
     * Get active variation types
     */
    public function getActiveTypes()
    {
        return $this->where('is_active', 1)
                   ->orderBy('sort_order', 'ASC')
                   ->orderBy('name', 'ASC')
                   ->findAll();
    }

    /**
     * Get variation type with its options
     */
    public function getTypeWithOptions($id)
    {
        $type = $this->find($id);
        if (!$type) {
            return null;
        }

        $optionModel = new ProductVariationOptionModel();
        $type['options'] = $optionModel->getOptionsByType($id);

        return $type;
    }

    /**
     * Get all types with their options
     */
    public function getTypesWithOptions()
    {
        $types = $this->getActiveTypes();
        $optionModel = new ProductVariationOptionModel();

        foreach ($types as &$type) {
            $type['options'] = $optionModel->getOptionsByType($type['id']);
        }

        return $types;
    }
}
