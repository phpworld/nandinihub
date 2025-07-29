<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PageModel;

class PagesController extends BaseController
{
    protected $pageModel;

    public function __construct()
    {
        $this->pageModel = new PageModel();
    }

    /**
     * Check admin access
     */
    private function checkAdminAccess()
    {
        if (!session()->get('user_id')) {
            // Store the intended URL for redirect after login
            session()->set('redirect_to', current_url());
            session()->setFlashdata('error', 'Please login to access the admin panel.');
            return redirect()->to('/login');
        }

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find(session()->get('user_id'));

        if (!$user || $user['role'] !== 'admin') {
            session()->setFlashdata('error', 'Access denied - Admin privileges required.');
            return redirect()->to('/');
        }

        return true;
    }

    /**
     * Display pages list
     */
    public function index()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        try {
            $search = $this->request->getGet('search');
            $perPage = 10;

            $pages = $this->pageModel->getPagesWithPagination($perPage, $search);
            $pager = $this->pageModel->pager;

            $data = [
                'title' => 'Page Management',
                'pages' => $pages,
                'pager' => $pager,
                'search' => $search,
                'total' => $this->pageModel->getTotalPages($search),
                'sidebarItems' => $this->getSidebarItems(),
                'activeSection' => 'pages'
            ];

            return view('admin/pages/index', $data);
        } catch (\Exception $e) {
            log_message('error', 'Pages index error: ' . $e->getMessage());
            session()->setFlashdata('error', 'An error occurred while loading pages: ' . $e->getMessage());
            return redirect()->to('/admin');
        }
    }

    /**
     * Show create page form
     */
    public function create()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        try {
            $data = [
                'title' => 'Create New Page',
                'page' => null,
                'validation' => null,
                'sidebarItems' => $this->getSidebarItems(),
                'activeSection' => 'pages'
            ];

            return view('admin/pages/form', $data);
        } catch (\Exception $e) {
            log_message('error', 'Pages create error: ' . $e->getMessage());
            session()->setFlashdata('error', 'An error occurred while loading the create page form: ' . $e->getMessage());
            return redirect()->to('/admin');
        }
    }

    /**
     * Store new page
     */
    public function store()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'content' => 'required|min_length[10]',
            'meta_title' => 'permit_empty|max_length[255]',
            'meta_description' => 'permit_empty|max_length[500]',
            'meta_keywords' => 'permit_empty|max_length[500]',
            'header_order' => 'integer|greater_than_equal_to[0]',
            'footer_order' => 'integer|greater_than_equal_to[0]',
            'template' => 'permit_empty|max_length[50]'
        ];

        if (!$this->validate($rules)) {
            return view('admin/pages/form', [
                'title' => 'Create New Page',
                'page' => null,
                'validation' => $this->validator,
                'sidebarItems' => $this->getSidebarItems(),
                'activeSection' => 'pages'
            ]);
        }

        // Generate slug from title
        $title = $this->request->getPost('title');
        $slug = $this->request->getPost('slug');

        if (empty($slug)) {
            $slug = $this->pageModel->generateSlug($title);
        } else {
            $slug = url_title($slug, '-', true);
            $slug = $this->pageModel->generateSlug($slug);
        }

        $data = [
            'title' => $title,
            'slug' => $slug,
            'content' => $this->request->getPost('content'),
            'meta_title' => $this->request->getPost('meta_title'),
            'meta_description' => $this->request->getPost('meta_description'),
            'meta_keywords' => $this->request->getPost('meta_keywords'),
            'is_active' => (int)$this->request->getPost('is_active'),
            'show_in_header' => (int)$this->request->getPost('show_in_header'),
            'show_in_footer' => (int)$this->request->getPost('show_in_footer'),
            'header_order' => (int)$this->request->getPost('header_order'),
            'footer_order' => (int)$this->request->getPost('footer_order'),
            'template' => $this->request->getPost('template') ?: 'default'
        ];

        if ($this->pageModel->save($data)) {
            return redirect()->to('/admin/pages')->with('success', 'Page created successfully.');
        } else {
            return view('admin/pages/form', [
                'title' => 'Create New Page',
                'page' => null,
                'validation' => $this->pageModel->validation,
                'sidebarItems' => $this->getSidebarItems(),
                'activeSection' => 'pages'
            ]);
        }
    }

    /**
     * Show edit page form
     */
    public function edit($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $page = $this->pageModel->find($id);
        if (!$page) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Page not found');
        }

        $data = [
            'title' => 'Edit Page',
            'page' => $page,
            'validation' => null,
            'sidebarItems' => $this->getSidebarItems(),
            'activeSection' => 'pages'
        ];

        return view('admin/pages/form', $data);
    }

    /**
     * Update page
     */
    public function update($id)
    {
        log_message('info', '=== UPDATE METHOD CALLED ===');
        log_message('info', 'Request method: ' . $this->request->getMethod());
        log_message('info', 'Request URI: ' . $this->request->getUri());

        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $page = $this->pageModel->find($id);
        if (!$page) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Page not found');
        }

        // Debug logging
        log_message('info', 'Page update attempt for ID: ' . $id);
        log_message('info', 'POST data: ' . json_encode($this->request->getPost()));
        log_message('info', 'All request data: ' . json_encode($this->request->getVar()));

        // Check if this is actually a POST request
        if (!$this->request->getMethod() === 'post') {
            session()->setFlashdata('error', 'Invalid request method: ' . $this->request->getMethod());
            return redirect()->to('/admin/pages/' . $id . '/edit');
        }

        $rules = [
            'title' => 'required|min_length[3]|max_length[255]',
            'slug' => "permit_empty|min_length[3]|max_length[255]|alpha_dash|is_unique[pages.slug,id,{$id}]",
            'content' => 'required|min_length[10]',
            'meta_title' => 'permit_empty|max_length[255]',
            'meta_description' => 'permit_empty|max_length[500]',
            'meta_keywords' => 'permit_empty|max_length[500]',
            'header_order' => 'permit_empty',
            'footer_order' => 'permit_empty',
            'template' => 'permit_empty|max_length[50]'
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            log_message('error', 'Page update validation failed: ' . json_encode($errors));

            // Create detailed error message
            $errorMessages = [];
            foreach ($errors as $field => $error) {
                $errorMessages[] = "$field: $error";
            }
            $errorMessage = 'Validation failed: ' . implode(' | ', $errorMessages);

            session()->setFlashdata('error', $errorMessage);
            session()->setFlashdata('debug_post', json_encode($this->request->getPost()));

            return view('admin/pages/form', [
                'title' => 'Edit Page',
                'page' => array_merge($page, $this->request->getPost()),
                'validation' => $this->validator,
                'sidebarItems' => $this->getSidebarItems(),
                'activeSection' => 'pages'
            ]);
        }

        // Handle slug
        $title = $this->request->getPost('title');
        $slug = $this->request->getPost('slug');

        // If no slug provided, generate from title
        if (empty($slug)) {
            $slug = $this->pageModel->generateSlug($title, $id);
        } else {
            // Clean the provided slug
            $slug = url_title($slug, '-', true);
            // Only regenerate if it's different from current slug
            if ($slug !== $page['slug']) {
                // Check if the new slug is unique
                $existingPage = $this->pageModel->where('slug', $slug)->where('id !=', $id)->first();
                if ($existingPage) {
                    $slug = $this->pageModel->generateSlug($slug, $id);
                }
            }
        }

        log_message('info', 'Generated slug: ' . $slug);

        $data = [
            'title' => $title,
            'slug' => $slug,
            'content' => $this->request->getPost('content'),
            'meta_title' => $this->request->getPost('meta_title'),
            'meta_description' => $this->request->getPost('meta_description'),
            'meta_keywords' => $this->request->getPost('meta_keywords'),
            'is_active' => (int)$this->request->getPost('is_active'),
            'show_in_header' => (int)$this->request->getPost('show_in_header'),
            'show_in_footer' => (int)$this->request->getPost('show_in_footer'),
            'header_order' => (int)($this->request->getPost('header_order') ?: 0),
            'footer_order' => (int)($this->request->getPost('footer_order') ?: 0),
            'template' => $this->request->getPost('template') ?: 'default'
        ];

        // Debug: Log the data being updated
        log_message('info', 'Update data: ' . json_encode($data));

        try {
            // Disable model validation to use controller validation
            $this->pageModel->skipValidation(true);

            // Debug: Show what data we're trying to update
            log_message('info', 'Attempting to update with data: ' . json_encode($data));
            session()->setFlashdata('debug_update_data', 'Update data: ' . json_encode($data));

            // Test database connection
            $db = \Config\Database::connect();
            $testQuery = $db->query("SELECT COUNT(*) as count FROM pages WHERE id = ?", [$id]);
            $testResult = $testQuery->getRow();
            log_message('info', 'Database test - page exists: ' . json_encode($testResult));

            $updateResult = $this->pageModel->update($id, $data);
            log_message('info', 'Update result: ' . ($updateResult ? 'success' : 'failed'));

            // Check if update actually worked by fetching the record
            $updatedPage = $this->pageModel->find($id);
            log_message('info', 'Page after update: ' . json_encode($updatedPage));

            if ($updateResult) {
                log_message('info', 'Page updated successfully');
                session()->setFlashdata('success', 'Page "' . $data['title'] . '" updated successfully at ' . date('Y-m-d H:i:s'));
                return redirect()->to('/admin/pages');
            } else {
                $errors = $this->pageModel->errors();
                log_message('error', 'Page update errors: ' . json_encode($errors));
                session()->setFlashdata('error', 'Failed to update page. Database error: ' . json_encode($errors));

                return redirect()->to('/admin/pages/' . $id . '/edit');
            }
        } catch (\Exception $e) {
            log_message('error', 'Page update exception: ' . $e->getMessage());
            session()->setFlashdata('error', 'An error occurred while updating the page: ' . $e->getMessage());

            return redirect()->to('/admin/pages/' . $id . '/edit');
        }
    }

    /**
     * Delete page
     */
    public function delete($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $page = $this->pageModel->find($id);
        if (!$page) {
            return $this->response->setJSON(['success' => false, 'message' => 'Page not found']);
        }

        if ($this->pageModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Page deleted successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete page']);
        }
    }

    /**
     * Toggle page status
     */
    public function toggleStatus($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        if ($this->pageModel->toggleStatus($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Status updated successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status']);
        }
    }

    /**
     * Update header navigation order
     */
    public function updateHeaderOrder()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $pageIds = $this->request->getJSON(true)['page_ids'] ?? [];

        if ($this->pageModel->updateHeaderOrder($pageIds)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Header order updated successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update header order']);
        }
    }

    /**
     * Update footer navigation order
     */
    public function updateFooterOrder()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $pageIds = $this->request->getJSON(true)['page_ids'] ?? [];

        if ($this->pageModel->updateFooterOrder($pageIds)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Footer order updated successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update footer order']);
        }
    }

    /**
     * Get sidebar items for admin layout
     */
    private function getSidebarItems()
    {
        return [
            [
                'title' => 'Dashboard',
                'url' => base_url('admin/dashboard'),
                'icon' => 'fas fa-tachometer-alt',
                'key' => 'dashboard'
            ],
            [
                'title' => 'Products',
                'url' => base_url('admin/products'),
                'icon' => 'fas fa-box',
                'key' => 'products',
                'submenu' => [
                    ['title' => 'All Products', 'url' => base_url('admin/products')],
                    ['title' => 'Add Product', 'url' => base_url('admin/products/create')],
                    ['title' => 'Categories', 'url' => base_url('admin/categories')]
                ]
            ],
            [
                'title' => 'Orders',
                'url' => base_url('admin/orders'),
                'icon' => 'fas fa-shopping-bag',
                'key' => 'orders'
            ],
            [
                'title' => 'Users',
                'url' => base_url('admin/users'),
                'icon' => 'fas fa-users',
                'key' => 'users'
            ],
            [
                'title' => 'Reviews',
                'url' => base_url('admin/reviews'),
                'icon' => 'fas fa-star',
                'key' => 'reviews'
            ],
            [
                'title' => 'Banners',
                'url' => base_url('admin/banners'),
                'icon' => 'fas fa-image',
                'key' => 'banners'
            ],
            [
                'title' => 'Coupons',
                'url' => base_url('admin/coupons'),
                'icon' => 'fas fa-tags',
                'key' => 'coupons',
                'submenu' => [
                    ['title' => 'All Coupons', 'url' => base_url('admin/coupons')],
                    ['title' => 'Add Coupon', 'url' => base_url('admin/coupons/create')]
                ]
            ],
            [
                'title' => 'Shipping',
                'url' => base_url('admin/shipping'),
                'icon' => 'fas fa-shipping-fast',
                'key' => 'shipping',
                'submenu' => [
                    ['title' => 'Shipping Methods', 'url' => base_url('admin/shipping')],
                    ['title' => 'Add Method', 'url' => base_url('admin/shipping/create')]
                ]
            ],
            [
                'title' => 'Pages',
                'url' => base_url('admin/pages'),
                'icon' => 'fas fa-file-alt',
                'key' => 'pages',
                'submenu' => [
                    ['title' => 'All Pages', 'url' => base_url('admin/pages')],
                    ['title' => 'Add Page', 'url' => base_url('admin/pages/create')]
                ]
            ],
            [
                'title' => 'Settings',
                'url' => base_url('admin/settings'),
                'icon' => 'fas fa-cog',
                'key' => 'settings'
            ]
        ];
    }
}
