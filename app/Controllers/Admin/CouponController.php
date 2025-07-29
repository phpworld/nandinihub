<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CouponModel;
use App\Models\CouponUsageModel;
use App\Libraries\CouponService;

class CouponController extends BaseController
{
    private CouponModel $couponModel;
    private CouponUsageModel $usageModel;
    private CouponService $couponService;

    public function __construct()
    {
        $this->couponModel = new CouponModel();
        $this->usageModel = new CouponUsageModel();
        $this->couponService = new CouponService();
    }

    /**
     * Check admin access and redirect if not authorized
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

        // Verify user exists and has admin role
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
     * Get admin data with sidebar items
     */
    private function getAdminData($activeSection = 'coupons')
    {
        $userId = session()->get('user_id');
        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);
        $sidebarItems = $this->getSidebarItems();

        return [
            'activeSection' => $activeSection,
            'user' => $user,
            'sidebarItems' => $sidebarItems
        ];
    }

    /**
     * Get sidebar items (copied from AdminController)
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
                'title' => 'Settings',
                'url' => base_url('admin/settings'),
                'icon' => 'fas fa-cog',
                'key' => 'settings'
            ]
        ];
    }

    /**
     * List all coupons
     */
    public function index()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }
        $data = array_merge($this->getAdminData('coupons'), [
            'title' => 'Manage Coupons',
            'coupons' => $this->couponModel->orderBy('created_at', 'DESC')->findAll()
        ]);

        return view('admin/coupons/index', $data);
    }

    /**
     * Create new coupon form
     */
    public function create()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }
        $data = array_merge($this->getAdminData('coupons'), [
            'title' => 'Create New Coupon',
            'coupon' => null,
            'validation' => null
        ]);

        return view('admin/coupons/form', $data);
    }

    /**
     * Store new coupon
     */
    public function store()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }
        $rules = [
            'code' => 'required|min_length[3]|max_length[50]|is_unique[coupons.code]',
            'name' => 'required|min_length[3]|max_length[255]',
            'type' => 'required|in_list[percentage,fixed_amount,free_shipping]',
            'value' => 'required|decimal|greater_than[0]',
            'minimum_order_amount' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'maximum_discount_amount' => 'permit_empty|decimal|greater_than[0]',
            'usage_limit' => 'permit_empty|integer|greater_than[0]',
            'usage_limit_per_customer' => 'required|integer|greater_than[0]',
            'valid_from' => 'permit_empty|valid_date',
            'valid_until' => 'permit_empty|valid_date'
        ];

        if (!$this->validate($rules)) {
            $data = array_merge($this->getAdminData('coupons'), [
                'title' => 'Create New Coupon',
                'coupon' => null,
                'validation' => $this->validator
            ]);
            return view('admin/coupons/form', $data);
        }

        $data = [
            'code' => strtoupper($this->request->getPost('code')),
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'type' => $this->request->getPost('type'),
            'value' => $this->request->getPost('value'),
            'minimum_order_amount' => $this->request->getPost('minimum_order_amount') ?: 0,
            'maximum_discount_amount' => $this->request->getPost('maximum_discount_amount') ?: null,
            'usage_limit' => $this->request->getPost('usage_limit') ?: null,
            'usage_limit_per_customer' => $this->request->getPost('usage_limit_per_customer'),
            'valid_from' => $this->request->getPost('valid_from') ?: null,
            'valid_until' => $this->request->getPost('valid_until') ?: null,
            'is_active' => $this->request->getPost('is_active') ? true : false
        ];

        if ($this->couponModel->insert($data)) {
            session()->setFlashdata('success', 'Coupon created successfully!');
            return redirect()->to('/admin/coupons');
        } else {
            session()->setFlashdata('error', 'Failed to create coupon. Please try again.');
            return redirect()->back()->withInput();
        }
    }

    /**
     * Edit coupon form
     */
    public function edit($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        try {
            $coupon = $this->couponModel->find($id);

            if (!$coupon) {
                session()->setFlashdata('error', 'Coupon not found.');
                return redirect()->to('/admin/coupons');
            }

            $data = array_merge($this->getAdminData('coupons'), [
                'title' => 'Edit Coupon',
                'coupon' => $coupon,
                'validation' => null
            ]);

            return view('admin/coupons/form', $data);
        } catch (\Exception $e) {
            log_message('error', 'Coupon edit error: ' . $e->getMessage());
            session()->setFlashdata('error', 'An error occurred while loading the coupon: ' . $e->getMessage());
            return redirect()->to('/admin/coupons');
        }
    }

    /**
     * Update coupon
     */
    public function update($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }
        $coupon = $this->couponModel->find($id);

        if (!$coupon) {
            session()->setFlashdata('error', 'Coupon not found.');
            return redirect()->to('/admin/coupons');
        }

        $rules = [
            'code' => "required|min_length[3]|max_length[50]|is_unique[coupons.code,id,{$id}]",
            'name' => 'required|min_length[3]|max_length[255]',
            'type' => 'required|in_list[percentage,fixed_amount,free_shipping]',
            'value' => 'required|decimal|greater_than[0]',
            'minimum_order_amount' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'maximum_discount_amount' => 'permit_empty|decimal|greater_than[0]',
            'usage_limit' => 'permit_empty|integer|greater_than[0]',
            'usage_limit_per_customer' => 'required|integer|greater_than[0]',
            'valid_from' => 'permit_empty|valid_date',
            'valid_until' => 'permit_empty|valid_date'
        ];

        if (!$this->validate($rules)) {
            $data = array_merge($this->getAdminData('coupons'), [
                'title' => 'Edit Coupon',
                'coupon' => $coupon,
                'validation' => $this->validator
            ]);
            return view('admin/coupons/form', $data);
        }

        $data = [
            'code' => strtoupper($this->request->getPost('code')),
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'type' => $this->request->getPost('type'),
            'value' => $this->request->getPost('value'),
            'minimum_order_amount' => $this->request->getPost('minimum_order_amount') ?: 0,
            'maximum_discount_amount' => $this->request->getPost('maximum_discount_amount') ?: null,
            'usage_limit' => $this->request->getPost('usage_limit') ?: null,
            'usage_limit_per_customer' => $this->request->getPost('usage_limit_per_customer'),
            'valid_from' => $this->request->getPost('valid_from') ?: null,
            'valid_until' => $this->request->getPost('valid_until') ?: null,
            'is_active' => $this->request->getPost('is_active') ? true : false
        ];

        try {
            // Temporarily disable model validation to use controller validation
            $this->couponModel->skipValidation(true);

            if ($this->couponModel->update($id, $data)) {
                session()->setFlashdata('success', 'Coupon updated successfully!');
                return redirect()->to('/admin/coupons');
            } else {
                session()->setFlashdata('error', 'Failed to update coupon. Please try again.');
                return redirect()->back()->withInput();
            }
        } catch (\Exception $e) {
            log_message('error', 'Coupon update error: ' . $e->getMessage());
            session()->setFlashdata('error', 'An error occurred while updating the coupon: ' . $e->getMessage());
            return redirect()->back()->withInput();
        } finally {
            // Re-enable model validation for other operations
            $this->couponModel->skipValidation(false);
        }
    }

    /**
     * Delete coupon
     */
    public function delete($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }
        $coupon = $this->couponModel->find($id);

        if (!$coupon) {
            session()->setFlashdata('error', 'Coupon not found.');
            return redirect()->to('/admin/coupons');
        }

        if ($this->couponModel->delete($id)) {
            session()->setFlashdata('success', 'Coupon deleted successfully!');
        } else {
            session()->setFlashdata('error', 'Failed to delete coupon. Please try again.');
        }

        return redirect()->to('/admin/coupons');
    }

    /**
     * Toggle coupon status
     */
    public function toggle($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }
        $coupon = $this->couponModel->find($id);

        if (!$coupon) {
            return $this->response->setJSON(['success' => false, 'message' => 'Coupon not found']);
        }

        $newStatus = !$coupon['is_active'];

        if ($this->couponModel->update($id, ['is_active' => $newStatus])) {
            $message = $newStatus ? 'Coupon activated successfully!' : 'Coupon deactivated successfully!';
            return $this->response->setJSON(['success' => true, 'message' => $message, 'status' => $newStatus]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update coupon status']);
        }
    }

    /**
     * View coupon statistics
     */
    public function stats($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }
        $stats = $this->couponService->getCouponStatistics($id);

        if (empty($stats)) {
            session()->setFlashdata('error', 'Coupon not found.');
            return redirect()->to('/admin/coupons');
        }

        $data = array_merge($this->getAdminData('coupons'), [
            'title' => 'Coupon Statistics',
            'stats' => $stats,
            'usage_history' => $this->usageModel->getUsageWithDetails($id, 20)
        ]);

        return view('admin/coupons/stats', $data);
    }
}
