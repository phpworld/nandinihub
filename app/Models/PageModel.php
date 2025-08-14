<?php

namespace App\Models;

use CodeIgniter\Model;

class PageModel extends Model
{
    protected $table            = 'pages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title',
        'slug',
        'content',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'is_active',
        'show_in_header',
        'show_in_footer',
        'header_order',
        'footer_order',
        'template'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'title' => 'required|min_length[3]|max_length[255]',
        'slug' => 'required|min_length[3]|max_length[255]|alpha_dash|is_unique[pages.slug,id,{id}]',
        'content' => 'required|min_length[10]',
        'meta_title' => 'permit_empty|max_length[255]',
        'meta_description' => 'permit_empty|max_length[500]',
        'meta_keywords' => 'permit_empty|max_length[500]',
        'is_active' => 'in_list[0,1]',
        'show_in_header' => 'in_list[0,1]',
        'show_in_footer' => 'in_list[0,1]',
        'header_order' => 'permit_empty|integer|greater_than_equal_to[0]',
        'footer_order' => 'permit_empty|integer|greater_than_equal_to[0]',
        'template' => 'permit_empty|max_length[50]'
    ];

    protected $validationMessages = [
        'title' => [
            'required' => 'Page title is required.',
            'min_length' => 'Page title must be at least 3 characters long.',
            'max_length' => 'Page title cannot exceed 255 characters.'
        ],
        'slug' => [
            'required' => 'Page slug is required.',
            'min_length' => 'Page slug must be at least 3 characters long.',
            'max_length' => 'Page slug cannot exceed 255 characters.',
            'alpha_dash' => 'Page slug can only contain letters, numbers, dashes, and underscores.',
            'is_unique' => 'This slug is already in use. Please choose a different one.'
        ],
        'content' => [
            'required' => 'Page content is required.',
            'min_length' => 'Page content must be at least 10 characters long.'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get active pages for header navigation
     */
    public function getHeaderPages()
    {
        return $this->where('is_active', 1)
            ->where('show_in_header', 1)
            ->orderBy('header_order', 'ASC')
            ->orderBy('title', 'ASC')
            ->findAll();
    }

    /**
     * Get active pages for footer navigation
     */
    public function getFooterPages()
    {
        return $this->where('is_active', 1)
            ->where('show_in_footer', 1)
            ->orderBy('footer_order', 'ASC')
            ->orderBy('title', 'ASC')
            ->findAll();
    }

    /**
     * Get page by slug
     */
    public function getBySlug($slug)
    {
        return $this->where('slug', $slug)
            ->where('is_active', 1)
            ->first();
    }

    /**
     * Generate unique slug from title
     */
    public function generateSlug($title, $id = null)
    {
        $slug = url_title($title, '-', true);
        $originalSlug = $slug;
        $counter = 1;

        while (true) {
            $query = $this->where('slug', $slug);
            if ($id) {
                $query->where('id !=', $id);
            }

            if (!$query->first()) {
                break;
            }

            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Toggle page status
     */
    public function toggleStatus($id)
    {
        $page = $this->find($id);
        if (!$page) {
            return false;
        }

        $newStatus = $page['is_active'] ? 0 : 1;
        return $this->update($id, ['is_active' => $newStatus]);
    }

    /**
     * Update header navigation order
     */
    public function updateHeaderOrder($pageIds)
    {
        foreach ($pageIds as $order => $pageId) {
            $this->update($pageId, ['header_order' => $order + 1]);
        }
        return true;
    }

    /**
     * Update footer navigation order
     */
    public function updateFooterOrder($pageIds)
    {
        foreach ($pageIds as $order => $pageId) {
            $this->update($pageId, ['footer_order' => $order + 1]);
        }
        return true;
    }

    /**
     * Get pages with pagination and search
     */
    public function getPagesWithPagination($perPage = 10, $search = '')
    {
        if (!empty($search)) {
            $this->groupStart()
                ->like('title', $search)
                ->orLike('slug', $search)
                ->orLike('content', $search)
                ->groupEnd();
        }

        return $this->orderBy('created_at', 'DESC')
            ->paginate($perPage);
    }

    /**
     * Get total pages count for search
     */
    public function getTotalPages($search = '')
    {
        if (!empty($search)) {
            $this->groupStart()
                ->like('title', $search)
                ->orLike('slug', $search)
                ->orLike('content', $search)
                ->groupEnd();
        }

        return $this->countAllResults();
    }
}
