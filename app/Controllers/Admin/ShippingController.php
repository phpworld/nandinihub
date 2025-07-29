<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ShippingMethodModel;

class ShippingController extends BaseController
{
    protected $shippingModel;

    public function __construct()
    {
        $this->shippingModel = new ShippingMethodModel();
    }

    /**
     * Check admin access
     */
    private function checkAdminAccess()
    {
        // Check if user is logged in
        if (!session()->get('user_id')) {
            // Store the intended URL for redirect after login
            session()->set('redirect_to', current_url());
            session()->setFlashdata('error', 'Please login to access the admin panel.');
            return redirect()->to('/login');
        }

        // Load user model to verify admin role
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find(session()->get('user_id'));

        if (!$user || $user['role'] !== 'admin') {
            session()->setFlashdata('error', 'Access denied - Admin privileges required.');
            return redirect()->to('/');
        }

        // Check if user account is active
        if (!$user['is_active']) {
            session()->destroy();
            session()->setFlashdata('error', 'Your account has been deactivated. Please contact administrator.');
            return redirect()->to('/login');
        }

        return true;
    }

    /**
     * Get admin data for views
     */
    private function getAdminData(string $activeSection = 'shipping'): array
    {
        return [
            'sidebarItems' => $this->getSidebarItems(),
            'activeSection' => $activeSection,
            'user' => [
                'name' => session()->get('first_name') . ' ' . session()->get('last_name'),
                'email' => session()->get('email'),
                'role' => session()->get('role')
            ]
        ];
    }

    /**
     * Get sidebar items
     */
    private function getSidebarItems(): array
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
                'title' => 'Settings',
                'url' => base_url('admin/settings'),
                'icon' => 'fas fa-cog',
                'key' => 'settings'
            ]
        ];
    }

    /**
     * Display shipping methods list
     */
    public function index()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $perPage = 10;
        $shippingMethods = $this->shippingModel->orderBy('sort_order', 'ASC')->paginate($perPage);
        $pager = $this->shippingModel->pager;

        $data = array_merge($this->getAdminData('shipping'), [
            'title' => 'Shipping Methods - Admin',
            'shippingMethods' => $shippingMethods,
            'pager' => $pager
        ]);

        return view('admin/shipping/index', $data);
    }

    /**
     * Show create shipping method form
     */
    public function create()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $data = array_merge($this->getAdminData('shipping'), [
            'title' => 'Add Shipping Method - Admin',
            'method' => null
        ]);

        return view('admin/shipping/form', $data);
    }

    /**
     * Store new shipping method
     */
    public function store()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'delivery_time' => 'required|min_length[3]|max_length[100]',
            'cost' => 'required|decimal|greater_than_equal_to[0]',
            'minimum_order_amount' => 'decimal|greater_than_equal_to[0]',
            'maximum_order_amount' => 'permit_empty|decimal|greater_than[0]',
            'sort_order' => 'integer|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => trim($this->request->getPost('name')),
            'description' => trim($this->request->getPost('description')),
            'delivery_time' => trim($this->request->getPost('delivery_time')),
            'cost' => (float) $this->request->getPost('cost'),
            'minimum_order_amount' => (float) $this->request->getPost('minimum_order_amount'),
            'maximum_order_amount' => $this->request->getPost('maximum_order_amount') ? (float) $this->request->getPost('maximum_order_amount') : null,
            'is_free_shipping' => $this->request->getPost('is_free_shipping') ? 1 : 0,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'sort_order' => (int) $this->request->getPost('sort_order')
        ];

        if ($this->shippingModel->insert($data)) {
            session()->setFlashdata('success', 'Shipping method created successfully!');
            return redirect()->to('/admin/shipping');
        } else {
            session()->setFlashdata('error', 'Failed to create shipping method. Please try again.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show edit shipping method form
     */
    public function edit($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $method = $this->shippingModel->find($id);
        if (!$method) {
            session()->setFlashdata('error', 'Shipping method not found.');
            return redirect()->to('/admin/shipping');
        }

        $data = array_merge($this->getAdminData('shipping'), [
            'title' => 'Edit Shipping Method - Admin',
            'method' => $method
        ]);

        return view('admin/shipping/form', $data);
    }

    /**
     * Update shipping method
     */
    public function update($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $method = $this->shippingModel->find($id);
        if (!$method) {
            session()->setFlashdata('error', 'Shipping method not found.');
            return redirect()->to('/admin/shipping');
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'delivery_time' => 'required|min_length[3]|max_length[100]',
            'cost' => 'required|decimal|greater_than_equal_to[0]',
            'minimum_order_amount' => 'decimal|greater_than_equal_to[0]',
            'maximum_order_amount' => 'permit_empty|decimal|greater_than[0]',
            'sort_order' => 'integer|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => trim($this->request->getPost('name')),
            'description' => trim($this->request->getPost('description')),
            'delivery_time' => trim($this->request->getPost('delivery_time')),
            'cost' => (float) $this->request->getPost('cost'),
            'minimum_order_amount' => (float) $this->request->getPost('minimum_order_amount'),
            'maximum_order_amount' => $this->request->getPost('maximum_order_amount') ? (float) $this->request->getPost('maximum_order_amount') : null,
            'is_free_shipping' => $this->request->getPost('is_free_shipping') ? 1 : 0,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'sort_order' => (int) $this->request->getPost('sort_order')
        ];

        if ($this->shippingModel->update($id, $data)) {
            session()->setFlashdata('success', 'Shipping method updated successfully!');
            return redirect()->to('/admin/shipping');
        } else {
            session()->setFlashdata('error', 'Failed to update shipping method. Please try again.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Delete shipping method
     */
    public function delete($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $method = $this->shippingModel->find($id);
        if (!$method) {
            session()->setFlashdata('error', 'Shipping method not found.');
            return redirect()->to('/admin/shipping');
        }

        if ($this->shippingModel->delete($id)) {
            session()->setFlashdata('success', 'Shipping method deleted successfully!');
        } else {
            session()->setFlashdata('error', 'Failed to delete shipping method. Please try again.');
        }

        return redirect()->to('/admin/shipping');
    }

    /**
     * Toggle shipping method status
     */
    public function toggle($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $method = $this->shippingModel->find($id);
        if (!$method) {
            return $this->response->setJSON(['success' => false, 'message' => 'Shipping method not found']);
        }

        $newStatus = $method['is_active'] ? 0 : 1;
        if ($this->shippingModel->update($id, ['is_active' => $newStatus])) {
            $statusText = $newStatus ? 'activated' : 'deactivated';
            return $this->response->setJSON([
                'success' => true,
                'message' => "Shipping method {$statusText} successfully!",
                'new_status' => $newStatus
            ]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status']);
        }
    }

    /**
     * Update sort orders via AJAX
     */
    public function updateSortOrder()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $sortData = $this->request->getJSON(true);

        if (empty($sortData)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No sort data provided']);
        }

        if ($this->shippingModel->updateSortOrders($sortData)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Sort order updated successfully!']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update sort order']);
        }
    }
}
