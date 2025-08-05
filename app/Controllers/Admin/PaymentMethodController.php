<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PaymentMethodModel;

class PaymentMethodController extends BaseController
{
    protected $paymentMethodModel;

    public function __construct()
    {
        $this->paymentMethodModel = new PaymentMethodModel();
    }

    /**
     * Check admin access
     */
    private function checkAdminAccess()
    {
        if (!session()->get('user_id') || session()->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Access denied. Admin login required.');
        }
        return true;
    }

    /**
     * Get common admin data
     */
    private function getAdminData($activeSection = 'payment_methods')
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
                    ['title' => 'Categories', 'url' => base_url('admin/categories')],
                    ['title' => 'Variations', 'url' => base_url('admin/product-variations')]
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
                'title' => 'Payment Methods',
                'url' => base_url('admin/payment-methods'),
                'icon' => 'fas fa-credit-card',
                'key' => 'payment_methods',
                'submenu' => [
                    ['title' => 'All Payment Methods', 'url' => base_url('admin/payment-methods')],
                    ['title' => 'Add Payment Method', 'url' => base_url('admin/payment-methods/create')]
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
                'title' => 'Testimonials',
                'url' => base_url('admin/testimonials'),
                'icon' => 'fas fa-comments',
                'key' => 'testimonials',
                'submenu' => [
                    ['title' => 'All Testimonials', 'url' => base_url('admin/testimonials')],
                    ['title' => 'Add Testimonial', 'url' => base_url('admin/testimonials/create')]
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
     * Display payment methods list
     */
    public function index()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $perPage = 10;
        $paymentMethods = $this->paymentMethodModel->orderBy('sort_order', 'ASC')->paginate($perPage);
        $pager = $this->paymentMethodModel->pager;

        $data = array_merge($this->getAdminData('payment_methods'), [
            'title' => 'Payment Methods - Admin',
            'paymentMethods' => $paymentMethods,
            'pager' => $pager
        ]);

        return view('admin/payment_methods/index', $data);
    }

    /**
     * Show create payment method form
     */
    public function create()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $data = array_merge($this->getAdminData('payment_methods'), [
            'title' => 'Add Payment Method - Admin',
            'method' => null,
            'nextSortOrder' => $this->paymentMethodModel->getNextSortOrder()
        ]);

        return view('admin/payment_methods/form', $data);
    }

    /**
     * Store new payment method
     */
    public function store()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'wallet_address' => 'permit_empty|max_length[500]',
            'payment_information' => 'permit_empty',
            'sort_order' => 'integer|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => trim($this->request->getPost('name')),
            'wallet_address' => trim($this->request->getPost('wallet_address')),
            'payment_information' => trim($this->request->getPost('payment_information')),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'sort_order' => (int) $this->request->getPost('sort_order')
        ];

        // Handle QR code upload
        $qrFile = $this->request->getFile('qr_code');
        if ($qrFile && $qrFile->isValid() && !$qrFile->hasMoved()) {
            $qrPath = $this->handleQRCodeUpload($qrFile);
            if ($qrPath) {
                $data['qr_code'] = $qrPath;
            }
        }

        if ($this->paymentMethodModel->insert($data)) {
            session()->setFlashdata('success', 'Payment method created successfully.');
        } else {
            session()->setFlashdata('error', 'Failed to create payment method.');
        }

        return redirect()->to('/admin/payment-methods');
    }

    /**
     * Show edit payment method form
     */
    public function edit($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $method = $this->paymentMethodModel->find($id);
        if (!$method) {
            session()->setFlashdata('error', 'Payment method not found.');
            return redirect()->to('/admin/payment-methods');
        }

        $data = array_merge($this->getAdminData('payment_methods'), [
            'title' => 'Edit Payment Method - Admin',
            'method' => $method
        ]);

        return view('admin/payment_methods/form', $data);
    }

    /**
     * Update payment method
     */
    public function update($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $method = $this->paymentMethodModel->find($id);
        if (!$method) {
            session()->setFlashdata('error', 'Payment method not found.');
            return redirect()->to('/admin/payment-methods');
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[255]',
            'wallet_address' => 'permit_empty|max_length[500]',
            'payment_information' => 'permit_empty',
            'sort_order' => 'integer|greater_than_equal_to[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => trim($this->request->getPost('name')),
            'wallet_address' => trim($this->request->getPost('wallet_address')),
            'payment_information' => trim($this->request->getPost('payment_information')),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'sort_order' => (int) $this->request->getPost('sort_order')
        ];

        // Handle QR code upload
        $qrFile = $this->request->getFile('qr_code');
        if ($qrFile && $qrFile->isValid() && !$qrFile->hasMoved()) {
            // Delete old QR code if exists
            if (!empty($method['qr_code']) && file_exists(FCPATH . $method['qr_code'])) {
                unlink(FCPATH . $method['qr_code']);
            }
            
            $qrPath = $this->handleQRCodeUpload($qrFile);
            if ($qrPath) {
                $data['qr_code'] = $qrPath;
            }
        }

        if ($this->paymentMethodModel->update($id, $data)) {
            session()->setFlashdata('success', 'Payment method updated successfully.');
        } else {
            session()->setFlashdata('error', 'Failed to update payment method.');
        }

        return redirect()->to('/admin/payment-methods');
    }

    /**
     * Delete payment method
     */
    public function delete($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $method = $this->paymentMethodModel->find($id);
        if (!$method) {
            session()->setFlashdata('error', 'Payment method not found.');
            return redirect()->to('/admin/payment-methods');
        }

        // Delete QR code file if exists
        if (!empty($method['qr_code']) && file_exists(FCPATH . $method['qr_code'])) {
            unlink(FCPATH . $method['qr_code']);
        }

        if ($this->paymentMethodModel->delete($id)) {
            session()->setFlashdata('success', 'Payment method deleted successfully.');
        } else {
            session()->setFlashdata('error', 'Failed to delete payment method.');
        }

        return redirect()->to('/admin/payment-methods');
    }

    /**
     * Toggle payment method status
     */
    public function toggleStatus($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        if ($this->paymentMethodModel->toggleStatus($id)) {
            session()->setFlashdata('success', 'Payment method status updated successfully.');
        } else {
            session()->setFlashdata('error', 'Failed to update payment method status.');
        }

        return redirect()->to('/admin/payment-methods');
    }

    /**
     * Handle QR code file upload
     */
    private function handleQRCodeUpload($file): ?string
    {
        $validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        
        if (!in_array($file->getMimeType(), $validTypes)) {
            session()->setFlashdata('error', 'QR code must be an image file (JPEG, PNG, GIF).');
            return null;
        }

        if ($file->getSize() > 2048000) { // 2MB limit
            session()->setFlashdata('error', 'QR code image must be less than 2MB.');
            return null;
        }

        $uploadPath = 'uploads/qr_codes/';
        if (!is_dir(FCPATH . $uploadPath)) {
            mkdir(FCPATH . $uploadPath, 0755, true);
        }

        $newName = $file->getRandomName();
        if ($file->move(FCPATH . $uploadPath, $newName)) {
            return $uploadPath . $newName;
        }

        return null;
    }
}
