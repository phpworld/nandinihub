<?php

namespace App\Models;

use CodeIgniter\Model;

class TestimonialModel extends Model
{
    protected $table            = 'testimonials';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'email',
        'position',
        'company',
        'testimonial',
        'rating',
        'image',
        'location',
        'is_featured',
        'is_active',
        'sort_order'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'name' => 'required|min_length[2]|max_length[255]',
        'testimonial' => 'required|min_length[10]',
        'rating' => 'required|integer|greater_than[0]|less_than[6]',
        'email' => 'permit_empty|valid_email',
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Name is required.',
            'min_length' => 'Name must be at least 2 characters long.',
        ],
        'testimonial' => [
            'required' => 'Testimonial text is required.',
            'min_length' => 'Testimonial must be at least 10 characters long.',
        ],
        'rating' => [
            'required' => 'Rating is required.',
            'integer' => 'Rating must be a number.',
            'greater_than' => 'Rating must be between 1 and 5.',
            'less_than' => 'Rating must be between 1 and 5.',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;

    /**
     * Get active testimonials
     */
    public function getActiveTestimonials($limit = null)
    {
        $builder = $this->where('is_active', 1)
                       ->orderBy('sort_order', 'ASC')
                       ->orderBy('created_at', 'DESC');

        if ($limit) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Get featured testimonials
     */
    public function getFeaturedTestimonials($limit = 6)
    {
        return $this->where('is_active', 1)
                   ->where('is_featured', 1)
                   ->orderBy('sort_order', 'ASC')
                   ->orderBy('created_at', 'DESC')
                   ->limit($limit)
                   ->findAll();
    }

    /**
     * Get testimonials with pagination
     */
    public function getTestimonialsWithPagination($perPage = 10, $page = 1)
    {
        return $this->orderBy('created_at', 'DESC')
                   ->paginate($perPage, 'default', $page);
    }

    /**
     * Get testimonial statistics
     */
    public function getTestimonialStats()
    {
        $total = $this->countAll();
        $active = $this->where('is_active', 1)->countAllResults();
        $featured = $this->where('is_featured', 1)->where('is_active', 1)->countAllResults();
        
        // Calculate average rating
        $avgRating = $this->selectAvg('rating')->where('is_active', 1)->first();
        
        return [
            'total' => $total,
            'active' => $active,
            'featured' => $featured,
            'average_rating' => round($avgRating['rating'] ?? 0, 1)
        ];
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured($id)
    {
        $testimonial = $this->find($id);
        if ($testimonial) {
            return $this->update($id, ['is_featured' => $testimonial['is_featured'] ? 0 : 1]);
        }
        return false;
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        $testimonial = $this->find($id);
        if ($testimonial) {
            return $this->update($id, ['is_active' => $testimonial['is_active'] ? 0 : 1]);
        }
        return false;
    }
}
