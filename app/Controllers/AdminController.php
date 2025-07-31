<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;
use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\UserModel;
use App\Models\BannerModel;
use App\Models\ReviewModel;
use App\Models\SettingModel;

class AdminController extends BaseController
{
    protected $productModel;
    protected $categoryModel;
    protected $orderModel;
    protected $orderItemModel;
    protected $userModel;
    protected $reviewModel;
    protected $bannerModel;
    protected $settingModel;

    public function __construct()
    {
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
        $this->orderModel = new OrderModel();
        $this->orderItemModel = new OrderItemModel();
        $this->userModel = new UserModel();
        $this->reviewModel = new ReviewModel();
        $this->bannerModel = new BannerModel();
        $this->settingModel = new SettingModel();
    }

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
        $user = $this->userModel->find(session()->get('user_id'));
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

    private function getAdminData($activeSection = 'dashboard')
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);
        $sidebarItems = $this->getSidebarItems();

        // Debug logging
        log_message('debug', 'getAdminData called with activeSection: ' . $activeSection);
        log_message('debug', 'User ID from session: ' . ($userId ?? 'null'));
        log_message('debug', 'User found: ' . ($user ? 'yes' : 'no'));
        log_message('debug', 'Sidebar items count: ' . count($sidebarItems));

        return [
            'activeSection' => $activeSection,
            'user' => $user,
            'sidebarItems' => $sidebarItems
        ];
    }

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

    public function index()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck; // Return redirect response
        }

        // Get dashboard statistics
        $totalProducts = $this->productModel->countAll();
        $totalOrders = $this->orderModel->countAll();
        $totalUsers = $this->userModel->where('role', 'customer')->countAllResults();
        $totalRevenue = $this->orderModel->selectSum('total_amount')->first()['total_amount'] ?? 0;

        $recentOrders = $this->orderModel->getRecentOrders(5);
        $topProducts = $this->orderItemModel->getTopSellingProducts(5);
        $pendingReviews = $this->reviewModel->getPendingReviews(5);

        $data = array_merge($this->getAdminData('dashboard'), [
            'title' => 'Admin Dashboard - Microdose Mushroom',
            'totalProducts' => $totalProducts,
            'totalOrders' => $totalOrders,
            'totalUsers' => $totalUsers,
            'totalRevenue' => $totalRevenue,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
            'pendingReviews' => $pendingReviews
        ]);

        return view('admin/dashboard', $data);
    }

    // Product Management
    public function products()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        // Get filter parameters
        $categoryFilter = $this->request->getGet('category');
        $statusFilter = $this->request->getGet('status');
        $searchFilter = $this->request->getGet('search');

        // Build query with filters
        $builder = $this->productModel->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id');

        if ($categoryFilter) {
            $builder->where('products.category_id', $categoryFilter);
        }

        if ($statusFilter !== null && $statusFilter !== '') {
            $builder->where('products.is_active', $statusFilter);
        }

        if ($searchFilter) {
            $builder->groupStart()
                ->like('products.name', $searchFilter)
                ->orLike('products.sku', $searchFilter)
                ->orLike('products.description', $searchFilter)
                ->groupEnd();
        }

        $products = $builder->orderBy('products.created_at', 'DESC')->findAll();
        $categories = $this->categoryModel->getActiveCategories();

        $data = array_merge($this->getAdminData('products'), [
            'title' => 'Manage Products - Admin',
            'products' => $products,
            'categories' => $categories
        ]);

        return view('admin/products/index', $data);
    }

    public function createProduct()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $categories = $this->categoryModel->getActiveCategories();

        $data = array_merge($this->getAdminData('products'), [
            'title' => 'Add New Product - Admin',
            'categories' => $categories
        ]);

        return view('admin/products/create', $data);
    }

    public function importProducts()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $categories = $this->categoryModel->getActiveCategories();

        $data = array_merge($this->getAdminData('products'), [
            'title' => 'Import Products - Admin',
            'categories' => $categories
        ]);

        return view('admin/products/import', $data);
    }

    public function processImport()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $file = $this->request->getFile('import_file');

        if (!$file || !$file->isValid()) {
            session()->setFlashdata('error', 'Please select a valid file to import.');
            return redirect()->to('/admin/products/import');
        }

        $allowedExtensions = ['csv', 'xlsx', 'xls'];
        $fileExtension = $file->getClientExtension();

        if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
            session()->setFlashdata('error', 'Please upload a CSV or Excel file (.csv, .xlsx, .xls).');
            return redirect()->to('/admin/products/import');
        }

        try {
            // Move file to temp location
            $tempPath = WRITEPATH . 'uploads/temp/';
            if (!is_dir($tempPath)) {
                mkdir($tempPath, 0755, true);
            }

            $fileName = 'import_' . time() . '.' . $fileExtension;
            $file->move($tempPath, $fileName);
            $filePath = $tempPath . $fileName;

            // Process the file
            $importData = $this->parseImportFile($filePath, $fileExtension);

            if (empty($importData['data'])) {
                session()->setFlashdata('error', 'No valid data found in the file.');
                return redirect()->to('/admin/products/import');
            }

            // Store import data in session for preview
            session()->set('import_data', $importData);
            session()->set('import_file_path', $filePath);

            return redirect()->to('/admin/products/import/preview');
        } catch (\Exception $e) {
            session()->setFlashdata('error', 'Error processing file: ' . $e->getMessage());
            return redirect()->to('/admin/products/import');
        }
    }

    public function importPreview()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $importData = session()->get('import_data');
        if (!$importData) {
            session()->setFlashdata('error', 'No import data found. Please upload a file first.');
            return redirect()->to('/admin/products/import');
        }

        $categories = $this->categoryModel->getActiveCategories();

        $data = array_merge($this->getAdminData('products'), [
            'title' => 'Import Preview - Admin',
            'importData' => $importData,
            'categories' => $categories
        ]);

        return view('admin/products/import_preview', $data);
    }

    public function executeImport()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $importData = session()->get('import_data');
        if (!$importData) {
            session()->setFlashdata('error', 'No import data found. Please upload a file first.');
            return redirect()->to('/admin/products/import');
        }

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        foreach ($importData['data'] as $index => $row) {
            try {
                // Skip validation for bulk import
                $this->productModel->skipValidation(true);

                if ($this->productModel->insert($row)) {
                    $successCount++;
                } else {
                    $errorCount++;
                    $errors[] = "Row " . ($index + 2) . ": " . implode(', ', $this->productModel->errors());
                }
            } catch (\Exception $e) {
                $errorCount++;
                $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
            }
        }

        // Clean up
        $filePath = session()->get('import_file_path');
        if ($filePath && file_exists($filePath)) {
            unlink($filePath);
        }
        session()->remove(['import_data', 'import_file_path']);

        $message = "Import completed: {$successCount} products imported successfully";
        if ($errorCount > 0) {
            $message .= ", {$errorCount} errors occurred.";
            session()->setFlashdata('import_errors', $errors);
        }

        session()->setFlashdata('success', $message);
        return redirect()->to('/admin/products');
    }

    private function parseImportFile($filePath, $extension)
    {
        $data = [];
        $headers = [];
        $errors = [];

        if ($extension === 'csv') {
            $data = $this->parseCsvFile($filePath);
        } else {
            // For Excel files, we'll use a simple approach
            // In a production environment, you might want to use PhpSpreadsheet
            throw new \Exception('Excel file support requires PhpSpreadsheet library. Please use CSV format.');
        }

        return [
            'data' => $data['rows'],
            'headers' => $data['headers'],
            'errors' => $errors,
            'total_rows' => count($data['rows'])
        ];
    }

    private function parseCsvFile($filePath)
    {
        $rows = [];
        $headers = [];

        if (($handle = fopen($filePath, "r")) !== FALSE) {
            $lineNumber = 0;

            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $lineNumber++;

                if ($lineNumber === 1) {
                    // First row contains headers
                    $headers = array_map('trim', $data);
                    continue;
                }

                if (count($data) !== count($headers)) {
                    continue; // Skip malformed rows
                }

                $row = array_combine($headers, array_map('trim', $data));
                $processedRow = $this->processImportRow($row);

                if ($processedRow) {
                    $rows[] = $processedRow;
                }
            }
            fclose($handle);
        }

        return ['headers' => $headers, 'rows' => $rows];
    }

    private function processImportRow($row)
    {
        // Map CSV columns to database fields
        $columnMapping = [
            'name' => ['name', 'product_name', 'title'],
            'category_id' => ['category_id', 'category'],
            'description' => ['description', 'desc'],
            'short_description' => ['short_description', 'short_desc', 'summary'],
            'price' => ['price', 'regular_price'],
            'sale_price' => ['sale_price', 'discount_price'],
            'sku' => ['sku', 'product_code'],
            'stock_quantity' => ['stock_quantity', 'stock', 'quantity'],
            'weight' => ['weight'],
            'dimensions' => ['dimensions', 'size'],
            'is_featured' => ['is_featured', 'featured'],
            'is_active' => ['is_active', 'active', 'status'],
            'meta_title' => ['meta_title', 'seo_title'],
            'meta_description' => ['meta_description', 'seo_description']
        ];

        $processedRow = [];

        foreach ($columnMapping as $dbField => $possibleColumns) {
            $value = null;

            foreach ($possibleColumns as $column) {
                if (isset($row[$column]) && $row[$column] !== '') {
                    $value = $row[$column];
                    break;
                }
            }

            // Process specific fields
            switch ($dbField) {
                case 'category_id':
                    if (is_numeric($value)) {
                        $processedRow[$dbField] = (int)$value;
                    } else if ($value) {
                        // Try to find category by name
                        $category = $this->categoryModel->where('name', $value)->first();
                        $processedRow[$dbField] = $category ? $category['id'] : 1; // Default to first category
                    } else {
                        $processedRow[$dbField] = 1; // Default category
                    }
                    break;

                case 'price':
                case 'sale_price':
                case 'weight':
                    $processedRow[$dbField] = $value ? (float)$value : null;
                    break;

                case 'stock_quantity':
                    $processedRow[$dbField] = $value ? (int)$value : 0;
                    break;

                case 'is_featured':
                case 'is_active':
                    $processedRow[$dbField] = in_array(strtolower($value), ['1', 'true', 'yes', 'active']) ? 1 : 0;
                    break;

                case 'sku':
                    if (!$value) {
                        // Generate SKU if not provided
                        $processedRow[$dbField] = 'SKU' . time() . rand(100, 999);
                    } else {
                        $processedRow[$dbField] = $value;
                    }
                    break;

                default:
                    $processedRow[$dbField] = $value;
            }
        }

        // Generate slug if name is provided
        if (!empty($processedRow['name'])) {
            $processedRow['slug'] = url_title($processedRow['name'], '-', true);
        }

        // Validate required fields
        if (empty($processedRow['name']) || empty($processedRow['price'])) {
            return null; // Skip invalid rows
        }

        return $processedRow;
    }

    public function downloadSampleCsv()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $categories = $this->categoryModel->getActiveCategories();

        $sampleData = [
            [
                'name' => 'Sample Product 1',
                'category_id' => $categories[0]['id'] ?? 1,
                'description' => 'This is a sample product description',
                'short_description' => 'Sample short description',
                'price' => '99.99',
                'sale_price' => '79.99',
                'sku' => 'SAMPLE001',
                'stock_quantity' => '50',
                'weight' => '0.5',
                'dimensions' => '10x10x5',
                'is_featured' => '1',
                'is_active' => '1',
                'meta_title' => 'Sample Product 1 - Best Quality',
                'meta_description' => 'Buy Sample Product 1 at best price'
            ],
            [
                'name' => 'Sample Product 2',
                'category_id' => $categories[1]['id'] ?? 1,
                'description' => 'Another sample product description',
                'short_description' => 'Another sample short description',
                'price' => '149.99',
                'sale_price' => '',
                'sku' => 'SAMPLE002',
                'stock_quantity' => '25',
                'weight' => '1.0',
                'dimensions' => '15x15x8',
                'is_featured' => '0',
                'is_active' => '1',
                'meta_title' => 'Sample Product 2 - Premium Quality',
                'meta_description' => 'Buy Sample Product 2 at best price'
            ]
        ];

        $filename = 'product_import_sample.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Write headers
        fputcsv($output, array_keys($sampleData[0]));

        // Write sample data
        foreach ($sampleData as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }

    public function toggleProductStatus($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        if (!$this->request->isAJAX()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        $product = $this->productModel->find($id);
        if (!$product) {
            return $this->response->setJSON(['success' => false, 'message' => 'Product not found']);
        }

        $input = json_decode($this->request->getBody(), true);
        $isActive = $input['is_active'] ?? 0;

        if ($this->productModel->update($id, ['is_active' => $isActive])) {
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update status']);
    }

    public function toggleProductFeatured($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        if (!$this->request->isAJAX()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        $product = $this->productModel->find($id);
        if (!$product) {
            return $this->response->setJSON(['success' => false, 'message' => 'Product not found']);
        }

        $input = json_decode($this->request->getBody(), true);
        $isFeatured = $input['is_featured'] ?? 0;

        if ($this->productModel->update($id, ['is_featured' => $isFeatured])) {
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update featured status']);
    }

    public function bulkProductAction()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        if (!$this->request->isAJAX()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        $input = json_decode($this->request->getBody(), true);
        $action = $input['action'] ?? '';
        $productIds = $input['products'] ?? [];

        if (empty($productIds) || empty($action)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $success = false;
        switch ($action) {
            case 'activate':
                $success = $this->productModel->whereIn('id', $productIds)->set(['is_active' => 1])->update();
                break;
            case 'deactivate':
                $success = $this->productModel->whereIn('id', $productIds)->set(['is_active' => 0])->update();
                break;
            case 'delete':
                $success = $this->productModel->whereIn('id', $productIds)->delete();
                break;
        }

        return $this->response->setJSON(['success' => $success]);
    }

    private function uploadCategoryImage($imageFile)
    {
        // Create uploads directory if it doesn't exist
        $uploadPath = ROOTPATH . 'uploads/categories/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Create .htaccess file to allow image access
        $htaccessPath = ROOTPATH . 'uploads/.htaccess';
        if (!file_exists($htaccessPath)) {
            $htaccessContent = "# Allow access to uploaded files\n";
            $htaccessContent .= "Options -Indexes\n";
            $htaccessContent .= "RewriteEngine Off\n";
            $htaccessContent .= "\n";
            $htaccessContent .= "# Allow image files\n";
            $htaccessContent .= "<FilesMatch \"\\.(jpg|jpeg|png|gif|webp|svg)$\">\n";
            $htaccessContent .= "    Order allow,deny\n";
            $htaccessContent .= "    Allow from all\n";
            $htaccessContent .= "</FilesMatch>\n";
            file_put_contents($htaccessPath, $htaccessContent);
        }

        // Generate unique filename
        $extension = $imageFile->getClientExtension();
        $fileName = 'category_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;

        try {
            // Move the file to uploads directory
            if ($imageFile->move($uploadPath, $fileName)) {
                return $fileName;
            }
        } catch (\Exception $e) {
            log_message('error', 'Category image upload failed: ' . $e->getMessage());
        }

        return false;
    }

    private function deleteCategoryImage($imageName)
    {
        if ($imageName && file_exists(ROOTPATH . 'uploads/categories/' . $imageName)) {
            return unlink(ROOTPATH . 'uploads/categories/' . $imageName);
        }
        return true;
    }

    private function uploadProductImage($imageFile)
    {
        // Create uploads directory if it doesn't exist
        $uploadPath = ROOTPATH . 'uploads/products/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Create .htaccess file to allow image access
        $htaccessPath = ROOTPATH . 'uploads/.htaccess';
        if (!file_exists($htaccessPath)) {
            $htaccessContent = "# Allow access to uploaded files\n";
            $htaccessContent .= "Options -Indexes\n";
            $htaccessContent .= "RewriteEngine Off\n";
            $htaccessContent .= "\n";
            $htaccessContent .= "# Allow image files\n";
            $htaccessContent .= "<FilesMatch \"\\.(jpg|jpeg|png|gif|webp|svg)$\">\n";
            $htaccessContent .= "    Order allow,deny\n";
            $htaccessContent .= "    Allow from all\n";
            $htaccessContent .= "</FilesMatch>\n";
            file_put_contents($htaccessPath, $htaccessContent);
        }

        // Generate unique filename
        $extension = $imageFile->getClientExtension();
        $fileName = 'product_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;

        try {
            // Move the file to uploads directory
            if ($imageFile->move($uploadPath, $fileName)) {
                return $fileName;
            }
        } catch (\Exception $e) {
            log_message('error', 'Product image upload failed: ' . $e->getMessage());
        }

        return false;
    }

    private function deleteProductImage($imageName)
    {
        if ($imageName && file_exists(ROOTPATH . 'uploads/products/' . $imageName)) {
            return unlink(ROOTPATH . 'uploads/products/' . $imageName);
        }
        return true;
    }

    public function exportProducts()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $products = $this->productModel->select('products.*, categories.name as category_name')
            ->join('categories', 'categories.id = products.category_id')
            ->orderBy('products.created_at', 'DESC')
            ->findAll();

        $filename = 'products_export_' . date('Y-m-d_H-i-s') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // CSV headers
        fputcsv($output, [
            'ID',
            'Name',
            'SKU',
            'Category',
            'Price',
            'Sale Price',
            'Stock',
            'Status',
            'Featured',
            'Created'
        ]);

        // CSV data
        foreach ($products as $product) {
            fputcsv($output, [
                $product['id'],
                $product['name'],
                $product['sku'],
                $product['category_name'],
                $product['price'],
                $product['sale_price'],
                $product['stock_quantity'],
                $product['is_active'] ? 'Active' : 'Inactive',
                $product['is_featured'] ? 'Yes' : 'No',
                $product['created_at']
            ]);
        }

        fclose($output);
        exit;
    }

    // Category Management
    public function categories()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $categories = $this->categoryModel->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();

        $data = array_merge($this->getAdminData('products'), [
            'title' => 'Manage Categories - Admin',
            'categories' => $categories
        ]);

        return view('admin/categories/index', $data);
    }

    public function createCategory()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $data = array_merge($this->getAdminData('products'), [
            'title' => 'Add New Category - Admin'
        ]);

        return view('admin/categories/create', $data);
    }

    public function storeCategory()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $rules = [
            'name' => 'required|min_length[2]|max_length[255]',
            'slug' => 'permit_empty|alpha_dash|is_unique[categories.slug]',
            'sort_order' => 'permit_empty|integer',
            'image' => 'permit_empty|uploaded[image]|max_size[image,2048]|is_image[image]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $categoryData = [
            'name' => $this->request->getPost('name'),
            'slug' => $this->request->getPost('slug') ?: url_title($this->request->getPost('name'), '-', true),
            'description' => $this->request->getPost('description'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'sort_order' => $this->request->getPost('sort_order') ?: 0
        ];

        // Handle image upload
        $imageFile = $this->request->getFile('image');
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $imageName = $this->uploadCategoryImage($imageFile);
            if ($imageName) {
                $categoryData['image'] = $imageName;
            }
        }

        // Temporarily disable model validation since we're validating in controller
        $this->categoryModel->skipValidation(true);

        if ($this->categoryModel->insert($categoryData)) {
            session()->setFlashdata('success', 'Category created successfully');
            return redirect()->to('/admin/categories');
        } else {
            // If category creation failed and image was uploaded, delete the image
            if (isset($categoryData['image']) && file_exists(ROOTPATH . 'uploads/categories/' . $categoryData['image'])) {
                unlink(ROOTPATH . 'uploads/categories/' . $categoryData['image']);
            }
            session()->setFlashdata('error', 'Failed to create category');
            return redirect()->back()->withInput();
        }
    }

    public function editCategory($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $category = $this->categoryModel->find($id);
        if (!$category) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Category not found');
        }

        $data = array_merge($this->getAdminData('products'), [
            'title' => 'Edit Category - Admin',
            'category' => $category
        ]);

        return view('admin/categories/edit', $data);
    }

    public function updateCategory($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        // Debug: Log the incoming data
        log_message('info', 'Category update attempt for ID: ' . $id);
        log_message('info', 'POST data: ' . json_encode($this->request->getPost()));

        $category = $this->categoryModel->find($id);
        if (!$category) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Category not found');
        }

        $rules = [
            'name' => 'required|min_length[2]|max_length[255]',
            'slug' => "required|alpha_dash|is_unique[categories.slug,id,{$id}]",
            'sort_order' => 'permit_empty|integer',
            'image' => 'permit_empty|uploaded[image]|max_size[image,2048]|is_image[image]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $categoryData = [
            'name' => $this->request->getPost('name'),
            'slug' => $this->request->getPost('slug') ?: url_title($this->request->getPost('name'), '-', true),
            'description' => $this->request->getPost('description'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'sort_order' => $this->request->getPost('sort_order') ?: 0
        ];

        // Handle image upload
        $imageFile = $this->request->getFile('image');
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $newImageName = $this->uploadCategoryImage($imageFile);
            if ($newImageName) {
                // Delete old image if exists
                if (!empty($category['image'])) {
                    $this->deleteCategoryImage($category['image']);
                }
                $categoryData['image'] = $newImageName;
            }
        }

        // Temporarily disable model validation since we're validating in controller
        $this->categoryModel->skipValidation(true);

        try {
            if ($this->categoryModel->update($id, $categoryData)) {
                session()->setFlashdata('success', 'Category updated successfully');
                return redirect()->to('/admin/categories');
            } else {
                // Log the error for debugging
                $errors = $this->categoryModel->errors();
                log_message('error', 'Category update failed: ' . json_encode($errors));

                // If update failed and new image was uploaded, delete the new image
                if (isset($categoryData['image']) && $categoryData['image'] !== $category['image']) {
                    $this->deleteCategoryImage($categoryData['image']);
                }
                session()->setFlashdata('error', 'Failed to update category. Please check the form data.');
                return redirect()->back()->withInput();
            }
        } catch (\Exception $e) {
            // Log the exception
            log_message('error', 'Category update exception: ' . $e->getMessage());

            // If update failed and new image was uploaded, delete the new image
            if (isset($categoryData['image']) && $categoryData['image'] !== $category['image']) {
                $this->deleteCategoryImage($categoryData['image']);
            }
            session()->setFlashdata('error', 'An error occurred while updating the category: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function deleteCategory($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        // Get category data before deletion
        $category = $this->categoryModel->find($id);
        if (!$category) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Category not found']);
            }
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Category not found');
        }

        // Check if this is an AJAX request
        if ($this->request->isAJAX()) {
            // Check if category has products
            $productCount = $this->productModel->where('category_id', $id)->countAllResults();
            if ($productCount > 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => "Cannot delete category with {$productCount} existing products"
                ]);
            }

            if ($this->categoryModel->delete($id)) {
                // Delete associated image
                if (!empty($category['image'])) {
                    $this->deleteCategoryImage($category['image']);
                }
                return $this->response->setJSON(['success' => true]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete category']);
            }
        }

        // Non-AJAX request (fallback)
        $productCount = $this->productModel->where('category_id', $id)->countAllResults();
        if ($productCount > 0) {
            session()->setFlashdata('error', 'Cannot delete category with existing products');
            return redirect()->to('/admin/categories');
        }

        if ($this->categoryModel->delete($id)) {
            // Delete associated image
            if (!empty($category['image'])) {
                $this->deleteCategoryImage($category['image']);
            }
            session()->setFlashdata('success', 'Category deleted successfully');
        } else {
            session()->setFlashdata('error', 'Failed to delete category');
        }

        return redirect()->to('/admin/categories');
    }

    public function toggleCategoryStatus($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        if (!$this->request->isAJAX()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        $category = $this->categoryModel->find($id);
        if (!$category) {
            return $this->response->setJSON(['success' => false, 'message' => 'Category not found']);
        }

        $input = json_decode($this->request->getBody(), true);
        $isActive = $input['is_active'] ?? 0;

        if ($this->categoryModel->update($id, ['is_active' => $isActive])) {
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update category status']);
    }

    public function getCategoryProductCount($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        if (!$this->request->isAJAX()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        $count = $this->productModel->where('category_id', $id)->countAllResults();
        return $this->response->setJSON(['count' => $count]);
    }

    public function storeProduct()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $rules = [
            'category_id' => 'required|integer',
            'name' => 'required|min_length[2]|max_length[255]',
            'price' => 'required|decimal',
            'sku' => 'required|is_unique[products.sku]',
            'stock_quantity' => 'required|integer',
            'image' => 'permit_empty|uploaded[image]|max_size[image,2048]|is_image[image]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle sale_price - set to null if empty to avoid setting it to 0
        // But preserve '0' as a valid sale price (free item)
        $salePrice = $this->request->getPost('sale_price');
        $salePrice = ($salePrice !== '' && $salePrice !== null) ? $salePrice : null;

        $productData = [
            'category_id' => $this->request->getPost('category_id'),
            'name' => $this->request->getPost('name'),
            'slug' => $this->request->getPost('slug') ?: url_title($this->request->getPost('name'), '-', true),
            'description' => $this->request->getPost('description'),
            'short_description' => $this->request->getPost('short_description'),
            'price' => $this->request->getPost('price'),
            'sale_price' => $salePrice,
            'sku' => $this->request->getPost('sku'),
            'stock_quantity' => $this->request->getPost('stock_quantity'),
            'weight' => $this->request->getPost('weight'),
            'dimensions' => $this->request->getPost('dimensions'),
            'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'meta_title' => $this->request->getPost('meta_title'),
            'meta_description' => $this->request->getPost('meta_description')
        ];

        // Handle image upload
        $imageFile = $this->request->getFile('image');
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $imageName = $this->uploadProductImage($imageFile);
            if ($imageName) {
                $productData['image'] = $imageName;
            }
        }

        // Temporarily disable model validation since we're validating in controller
        $this->productModel->skipValidation(true);

        try {
            if ($this->productModel->insert($productData)) {
                session()->setFlashdata('success', 'Product created successfully');
                return redirect()->to('/admin/products');
            } else {
                // If product creation failed and image was uploaded, delete the image
                if (isset($productData['image']) && file_exists(ROOTPATH . 'uploads/products/' . $productData['image'])) {
                    $this->deleteProductImage($productData['image']);
                }
                session()->setFlashdata('error', 'Failed to create product');
                return redirect()->back()->withInput();
            }
        } catch (\Exception $e) {
            // Log the exception
            log_message('error', 'Product creation exception: ' . $e->getMessage());

            // If product creation failed and image was uploaded, delete the image
            if (isset($productData['image'])) {
                $this->deleteProductImage($productData['image']);
            }
            session()->setFlashdata('error', 'An error occurred while creating the product: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function editProduct($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $product = $this->productModel->find($id);
        if (!$product) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Product not found');
        }

        $categories = $this->categoryModel->getActiveCategories();

        $data = array_merge($this->getAdminData('products'), [
            'title' => 'Edit Product - Admin',
            'product' => $product,
            'categories' => $categories
        ]);

        return view('admin/products/edit', $data);
    }

    public function updateProduct($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        // Debug: Log the incoming data
        log_message('info', 'Product update attempt for ID: ' . $id);
        log_message('info', 'POST data: ' . json_encode($this->request->getPost()));

        $product = $this->productModel->find($id);
        if (!$product) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Product not found');
        }

        $rules = [
            'category_id' => 'required|integer',
            'name' => 'required|min_length[2]|max_length[255]',
            'price' => 'required|decimal',
            'sku' => "required|is_unique[products.sku,id,{$id}]",
            'stock_quantity' => 'required|integer',
            'image' => 'permit_empty|uploaded[image]|max_size[image,2048]|is_image[image]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Handle sale_price - set to null if empty to avoid setting it to 0
        // But preserve '0' as a valid sale price (free item)
        $salePrice = $this->request->getPost('sale_price');
        $salePrice = ($salePrice !== '' && $salePrice !== null) ? $salePrice : null;

        $productData = [
            'category_id' => $this->request->getPost('category_id'),
            'name' => $this->request->getPost('name'),
            'slug' => $this->request->getPost('slug') ?: url_title($this->request->getPost('name'), '-', true),
            'description' => $this->request->getPost('description'),
            'short_description' => $this->request->getPost('short_description'),
            'price' => $this->request->getPost('price'),
            'sale_price' => $salePrice,
            'sku' => $this->request->getPost('sku'),
            'stock_quantity' => $this->request->getPost('stock_quantity'),
            'weight' => $this->request->getPost('weight'),
            'dimensions' => $this->request->getPost('dimensions'),
            'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'meta_title' => $this->request->getPost('meta_title'),
            'meta_description' => $this->request->getPost('meta_description')
        ];

        // Handle image upload
        $imageFile = $this->request->getFile('image');
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $newImageName = $this->uploadProductImage($imageFile);
            if ($newImageName) {
                // Delete old image if exists
                if (!empty($product['image'])) {
                    $this->deleteProductImage($product['image']);
                }
                $productData['image'] = $newImageName;
            }
        }

        // Temporarily disable model validation since we're validating in controller
        $this->productModel->skipValidation(true);

        try {
            if ($this->productModel->update($id, $productData)) {
                session()->setFlashdata('success', 'Product updated successfully');
                return redirect()->to('/admin/products');
            } else {
                // Log the error for debugging
                $errors = $this->productModel->errors();
                log_message('error', 'Product update failed: ' . json_encode($errors));

                // If update failed and new image was uploaded, delete the new image
                if (isset($productData['image']) && $productData['image'] !== $product['image']) {
                    $this->deleteProductImage($productData['image']);
                }
                session()->setFlashdata('error', 'Failed to update product. Please check the form data.');
                return redirect()->back()->withInput();
            }
        } catch (\Exception $e) {
            // Log the exception
            log_message('error', 'Product update exception: ' . $e->getMessage());

            // If update failed and new image was uploaded, delete the new image
            if (isset($productData['image']) && $productData['image'] !== $product['image']) {
                $this->deleteProductImage($productData['image']);
            }
            session()->setFlashdata('error', 'An error occurred while updating the product: ' . $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    public function deleteProduct($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        // Get product data before deletion
        $product = $this->productModel->find($id);
        if (!$product) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Product not found']);
            }
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Product not found');
        }

        // Check if this is an AJAX request
        if ($this->request->isAJAX()) {
            if ($this->productModel->delete($id)) {
                // Delete associated image
                if (!empty($product['image'])) {
                    $this->deleteProductImage($product['image']);
                }
                return $this->response->setJSON(['success' => true]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete product']);
            }
        }

        // Non-AJAX request (fallback)
        if ($this->productModel->delete($id)) {
            // Delete associated image
            if (!empty($product['image'])) {
                $this->deleteProductImage($product['image']);
            }
            session()->setFlashdata('success', 'Product deleted successfully');
        } else {
            session()->setFlashdata('error', 'Failed to delete product');
        }

        return redirect()->to('/admin/products');
    }

    // Product Variation Management
    public function productVariations()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $variationTypeModel = new \App\Models\ProductVariationTypeModel();
        $variationOptionModel = new \App\Models\ProductVariationOptionModel();

        $data = array_merge($this->getAdminData('products'), [
            'title' => 'Product Variations - Admin',
            'variationTypes' => $variationTypeModel->getTypesWithOptions(),
            'allOptions' => $variationOptionModel->getOptionsWithTypes()
        ]);

        return view('admin/variations/index', $data);
    }

    public function createVariationType()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $data = array_merge($this->getAdminData('products'), [
            'title' => 'Create Variation Type - Admin'
        ]);

        return view('admin/variations/create_type', $data);
    }

    public function storeVariationType()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
            'display_name' => 'required|min_length[2]|max_length[100]',
            'type' => 'required|in_list[text,color,image,button]',
            'is_required' => 'permit_empty|in_list[0,1]',
            'sort_order' => 'permit_empty|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $variationTypeModel = new \App\Models\ProductVariationTypeModel();

        $name = $this->request->getPost('name');
        $slug = url_title($name, '-', true);

        // Ensure slug uniqueness
        $counter = 1;
        $originalSlug = $slug;
        while ($variationTypeModel->where('slug', $slug)->first()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $data = [
            'name' => $name,
            'slug' => $slug,
            'display_name' => $this->request->getPost('display_name'),
            'type' => $this->request->getPost('type'),
            'is_required' => $this->request->getPost('is_required') ?? 0,
            'sort_order' => $this->request->getPost('sort_order') ?? 0,
            'is_active' => 1
        ];

        try {
            if ($variationTypeModel->insert($data)) {
                return redirect()->to('/admin/product-variations')->with('success', 'Variation type created successfully');
            } else {
                $errors = $variationTypeModel->errors();
                return redirect()->back()->withInput()->with('errors', $errors);
            }
        } catch (\Exception $e) {
            log_message('error', 'Failed to create variation type: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Failed to create variation type: ' . $e->getMessage());
        }
    }

    public function manageProductVariants($productId)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $product = $this->productModel->find($productId);
        if (!$product) {
            return redirect()->to('/admin/products')->with('error', 'Product not found');
        }

        $variantModel = new \App\Models\ProductVariantModel();
        $variationTypeModel = new \App\Models\ProductVariationTypeModel();

        $data = array_merge($this->getAdminData('products'), [
            'title' => 'Manage Product Variants - Admin',
            'product' => $product,
            'variants' => $variantModel->getVariantsWithOptionsByProduct($productId),
            'variationTypes' => $variationTypeModel->getTypesWithOptions()
        ]);

        return view('admin/products/variants', $data);
    }

    public function createVariationOption($typeId = null)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $variationTypeModel = new \App\Models\ProductVariationTypeModel();
        $variationTypes = $variationTypeModel->getActiveTypes();

        $data = array_merge($this->getAdminData('products'), [
            'title' => 'Create Variation Option - Admin',
            'variationTypes' => $variationTypes,
            'selectedTypeId' => $typeId
        ]);

        return view('admin/variations/create_option', $data);
    }

    public function storeVariationOption()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $rules = [
            'variation_type_id' => 'required|integer',
            'name' => 'required|min_length[1]|max_length[100]',
            'value' => 'required|min_length[1]|max_length[255]',
            'color_code' => 'permit_empty|regex_match[/^#[0-9A-Fa-f]{6}$/]',
            'price_modifier' => 'permit_empty|decimal',
            'price_type' => 'in_list[fixed,percentage]',
            'sort_order' => 'integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $variationOptionModel = new \App\Models\ProductVariationOptionModel();

        $data = [
            'variation_type_id' => $this->request->getPost('variation_type_id'),
            'name' => $this->request->getPost('name'),
            'value' => $this->request->getPost('value'),
            'color_code' => $this->request->getPost('color_code'),
            'price_modifier' => $this->request->getPost('price_modifier') ?? 0,
            'price_type' => $this->request->getPost('price_type') ?? 'fixed',
            'sort_order' => $this->request->getPost('sort_order') ?? 0,
            'is_active' => 1
        ];

        // Handle image upload if provided
        $imageFile = $this->request->getFile('image');
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $newName = $imageFile->getRandomName();
            $imageFile->move(WRITEPATH . 'uploads/variations', $newName);
            $data['image'] = $newName;
        }

        if ($variationOptionModel->insert($data)) {
            return redirect()->to('/admin/product-variations')->with('success', 'Variation option created successfully');
        }

        return redirect()->back()->withInput()->with('error', 'Failed to create variation option');
    }

    public function generateProductVariants()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $productId = $this->request->getPost('product_id');
        $selectedOptions = $this->request->getPost('options') ?? [];

        if (empty($selectedOptions)) {
            return $this->response->setJSON(['success' => false, 'message' => 'No options selected']);
        }

        // Get product
        $product = $this->productModel->find($productId);
        if (!$product) {
            return $this->response->setJSON(['success' => false, 'message' => 'Product not found']);
        }

        // Generate all combinations
        $combinations = $this->generateOptionCombinations($selectedOptions);

        $variantModel = new \App\Models\ProductVariantModel();
        $variantOptionModel = new \App\Models\ProductVariantOptionModel();
        $createdCount = 0;

        foreach ($combinations as $combination) {
            // Check if variant already exists
            $existingVariant = $variantOptionModel->getVariantByOptions($productId, $combination);
            if ($existingVariant) {
                continue; // Skip if already exists
            }

            // Generate SKU
            $optionModel = new \App\Models\ProductVariationOptionModel();
            $options = $optionModel->getOptionsByIds($combination);
            $skuSuffix = implode('-', array_column($options, 'value'));
            $sku = $product['sku'] . '-' . strtoupper($skuSuffix);

            // Create variant
            $variantData = [
                'product_id' => $productId,
                'sku' => $sku,
                'price' => null, // Will use product price
                'stock_quantity' => 0,
                'is_default' => $createdCount === 0 ? 1 : 0,
                'is_active' => 1
            ];

            $variantId = $variantModel->insert($variantData);
            if ($variantId) {
                // Create variant options
                $variantOptionModel->createVariantOptions($variantId, $combination);
                $createdCount++;
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => "Created {$createdCount} variants successfully"
        ]);
    }

    private function generateOptionCombinations($selectedOptions)
    {
        $combinations = [[]];

        foreach ($selectedOptions as $typeId => $options) {
            $newCombinations = [];
            foreach ($combinations as $combination) {
                foreach ($options as $option) {
                    $newCombinations[] = array_merge($combination, [$option]);
                }
            }
            $combinations = $newCombinations;
        }

        return $combinations;
    }

    // AJAX Endpoints for Variation Management

    public function editVariationType($id = null)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $variationTypeModel = new \App\Models\ProductVariationTypeModel();
        $type = $variationTypeModel->find($id);

        if (!$type) {
            return $this->response->setJSON(['success' => false, 'message' => 'Variation type not found']);
        }

        return $this->response->setJSON(['success' => true, 'data' => $type]);
    }

    public function updateVariationType($id = null)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $rules = [
            'name' => 'required|min_length[2]|max_length[100]',
            'display_name' => 'required|min_length[2]|max_length[100]',
            'type' => 'required|in_list[text,color,image,button]',
            'is_required' => 'permit_empty|in_list[0,1]',
            'sort_order' => 'permit_empty|integer'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $variationTypeModel = new \App\Models\ProductVariationTypeModel();

        $name = $this->request->getPost('name');
        $slug = url_title($name, '-', true);

        // Ensure slug uniqueness (excluding current record)
        $counter = 1;
        $originalSlug = $slug;
        while ($variationTypeModel->where('slug', $slug)->where('id !=', $id)->first()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $data = [
            'name' => $name,
            'slug' => $slug,
            'display_name' => $this->request->getPost('display_name'),
            'type' => $this->request->getPost('type'),
            'is_required' => $this->request->getPost('is_required') ?? 0,
            'sort_order' => $this->request->getPost('sort_order') ?? 0,
        ];

        try {
            if ($variationTypeModel->update($id, $data)) {
                return $this->response->setJSON(['success' => true, 'message' => 'Variation type updated successfully']);
            } else {
                $errors = $variationTypeModel->errors();
                return $this->response->setJSON(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Failed to update variation type: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update variation type: ' . $e->getMessage()]);
        }
    }

    public function deleteVariationType($id = null)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $variationTypeModel = new \App\Models\ProductVariationTypeModel();
        $variationOptionModel = new \App\Models\ProductVariationOptionModel();

        // Check if type has options
        $options = $variationOptionModel->where('variation_type_id', $id)->findAll();
        if (!empty($options)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cannot delete variation type. It has ' . count($options) . ' options. Delete options first.'
            ]);
        }

        if ($variationTypeModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Variation type deleted successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete variation type']);
    }

    public function editVariationOption($id = null)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $variationOptionModel = new \App\Models\ProductVariationOptionModel();
        $option = $variationOptionModel->getOptionWithType($id);

        if (!$option) {
            return $this->response->setJSON(['success' => false, 'message' => 'Variation option not found']);
        }

        return $this->response->setJSON(['success' => true, 'data' => $option]);
    }

    public function updateVariationOption($id = null)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $rules = [
            'name' => 'required|min_length[1]|max_length[100]',
            'value' => 'required|min_length[1]|max_length[255]',
            'color_code' => 'permit_empty|regex_match[/^#[0-9A-Fa-f]{6}$/]',
            'price_modifier' => 'permit_empty|decimal',
            'price_type' => 'in_list[fixed,percentage]',
            'sort_order' => 'integer'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['success' => false, 'errors' => $this->validator->getErrors()]);
        }

        $variationOptionModel = new \App\Models\ProductVariationOptionModel();

        $data = [
            'name' => $this->request->getPost('name'),
            'value' => $this->request->getPost('value'),
            'color_code' => $this->request->getPost('color_code'),
            'price_modifier' => $this->request->getPost('price_modifier') ?? 0,
            'price_type' => $this->request->getPost('price_type') ?? 'fixed',
            'sort_order' => $this->request->getPost('sort_order') ?? 0,
        ];

        if ($variationOptionModel->update($id, $data)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Variation option updated successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update variation option']);
    }

    public function deleteVariationOption($id = null)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $variationOptionModel = new \App\Models\ProductVariationOptionModel();
        $variantOptionModel = new \App\Models\ProductVariantOptionModel();

        // Check if option is used in variants
        $usedInVariants = $variantOptionModel->where('variation_option_id', $id)->findAll();
        if (!empty($usedInVariants)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cannot delete option. It is used in ' . count($usedInVariants) . ' product variants.'
            ]);
        }

        if ($variationOptionModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Variation option deleted successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete variation option']);
    }

    public function editProductVariant($id = null)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        if (!$id) {
            return $this->response->setJSON(['success' => false, 'message' => 'Variant ID is required']);
        }

        try {
            $variantModel = new \App\Models\ProductVariantModel();
            $variant = $variantModel->getVariantWithOptions($id);

            if (!$variant) {
                return $this->response->setJSON(['success' => false, 'message' => 'Product variant not found']);
            }

            return $this->response->setJSON(['success' => true, 'data' => $variant]);
        } catch (\Exception $e) {
            log_message('error', 'Failed to load variant for editing: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to load variant: ' . $e->getMessage()]);
        }
    }

    public function updateProductVariant($id = null)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        // Get the variant to check its product_id
        $variantModel = new \App\Models\ProductVariantModel();
        $currentVariant = $variantModel->find($id);

        if (!$currentVariant) {
            return $this->response->setJSON(['success' => false, 'message' => 'Variant not found']);
        }

        $rules = [
            'sku' => 'required|min_length[1]|max_length[100]',
            'price' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'sale_price' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'stock_quantity' => 'permit_empty|integer|greater_than_equal_to[0]',
            'weight' => 'permit_empty|decimal|greater_than_equal_to[0]',
            'dimensions' => 'permit_empty|max_length[255]'
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            log_message('error', 'Variant validation failed: ' . json_encode($errors));
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $errors,
                'submitted_data' => $this->request->getPost()
            ]);
        }

        // Custom SKU validation - only check for duplicates if SKU is being changed
        $sku = trim($this->request->getPost('sku'));
        $allowDuplicateSku = $this->request->getPost('allow_duplicate_sku');

        // Only check for duplicates if the SKU is actually being changed
        if (!$allowDuplicateSku && $sku !== $currentVariant['sku']) {
            $existingVariant = $variantModel->where('sku', $sku)
                                           ->where('product_id', $currentVariant['product_id'])
                                           ->where('id !=', $id)
                                           ->first();

            if ($existingVariant) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'SKU already exists for this product',
                    'action' => 'merge_stock',
                    'existing_variant' => $existingVariant,
                    'errors' => ['sku' => 'This SKU already exists for this product. Would you like to merge the stock quantities?']
                ]);
            }
        }

        $variantModel = new \App\Models\ProductVariantModel();

        // Clean and prepare data
        $sku = trim($this->request->getPost('sku'));
        $price = $this->request->getPost('price');
        $salePrice = $this->request->getPost('sale_price');
        $stockQuantity = $this->request->getPost('stock_quantity');
        $weight = $this->request->getPost('weight');
        $dimensions = trim($this->request->getPost('dimensions'));

        $data = [
            'sku' => $sku,
            'price' => !empty($price) ? floatval($price) : null,
            'sale_price' => !empty($salePrice) ? floatval($salePrice) : null,
            'stock_quantity' => !empty($stockQuantity) ? intval($stockQuantity) : 0,
            'weight' => !empty($weight) ? floatval($weight) : null,
            'dimensions' => !empty($dimensions) ? $dimensions : null,
        ];

        try {
            if ($variantModel->update($id, $data)) {
                return $this->response->setJSON(['success' => true, 'message' => 'Product variant updated successfully']);
            } else {
                $errors = $variantModel->errors();
                return $this->response->setJSON(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Failed to update product variant: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update product variant: ' . $e->getMessage()]);
        }
    }

    public function deleteProductVariant($id = null)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $variantModel = new \App\Models\ProductVariantModel();
        $variantOptionModel = new \App\Models\ProductVariantOptionModel();

        // Delete variant options first
        $variantOptionModel->deleteVariantOptions($id);

        // Delete the variant
        if ($variantModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Product variant deleted successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete product variant']);
    }

    public function duplicateProductVariant($id = null)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $variantModel = new \App\Models\ProductVariantModel();
        $variantOptionModel = new \App\Models\ProductVariantOptionModel();

        $originalVariant = $variantModel->getVariantWithOptions($id);
        if (!$originalVariant) {
            return $this->response->setJSON(['success' => false, 'message' => 'Original variant not found']);
        }

        // Create new variant data
        $newVariantData = [
            'product_id' => $originalVariant['product_id'],
            'sku' => $originalVariant['sku'] . '-COPY',
            'price' => $originalVariant['price'],
            'sale_price' => $originalVariant['sale_price'],
            'stock_quantity' => 0, // Start with 0 stock
            'weight' => $originalVariant['weight'],
            'dimensions' => $originalVariant['dimensions'],
            'is_default' => 0,
            'is_active' => 1
        ];

        $newVariantId = $variantModel->insert($newVariantData);
        if ($newVariantId) {
            // Copy variant options
            $optionIds = array_column($originalVariant['options'], 'variation_option_id');
            $variantOptionModel->createVariantOptions($newVariantId, $optionIds);

            return $this->response->setJSON(['success' => true, 'message' => 'Product variant duplicated successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to duplicate product variant']);
    }

    public function setDefaultVariant($id = null)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $variantModel = new \App\Models\ProductVariantModel();
        $variant = $variantModel->find($id);

        if (!$variant) {
            return $this->response->setJSON(['success' => false, 'message' => 'Variant not found']);
        }

        // Remove default from all variants of this product
        $variantModel->where('product_id', $variant['product_id'])->set(['is_default' => 0])->update();

        // Set this variant as default
        if ($variantModel->update($id, ['is_default' => 1])) {
            return $this->response->setJSON(['success' => true, 'message' => 'Default variant updated successfully']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to set default variant']);
    }

    public function mergeVariantStock($id = null)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $variantModel = new \App\Models\ProductVariantModel();
        $variantOptionModel = new \App\Models\ProductVariantOptionModel();

        $targetVariantId = $this->request->getPost('target_variant_id');
        $sourceVariantId = $id;
        $newStockQuantity = intval($this->request->getPost('stock_quantity'));

        try {
            $targetVariant = $variantModel->find($targetVariantId);
            $sourceVariant = $variantModel->find($sourceVariantId);

            if (!$targetVariant || !$sourceVariant) {
                return $this->response->setJSON(['success' => false, 'message' => 'Variant not found']);
            }

            // Merge stock quantities
            $totalStock = $targetVariant['stock_quantity'] + $newStockQuantity;

            // Update target variant with merged stock
            $variantModel->update($targetVariantId, ['stock_quantity' => $totalStock]);

            // Delete the source variant and its options
            $variantOptionModel->deleteVariantOptions($sourceVariantId);
            $variantModel->delete($sourceVariantId);

            return $this->response->setJSON([
                'success' => true,
                'message' => "Stock merged successfully. Total stock: {$totalStock}",
                'merged_stock' => $totalStock
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Failed to merge variant stock: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to merge stock: ' . $e->getMessage()]);
        }
    }

    public function quickStockUpdate($id = null)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        if (!$this->request->isAJAX()) {
            return redirect()->back();
        }

        $variantModel = new \App\Models\ProductVariantModel();
        $stockQuantity = intval($this->request->getPost('stock_quantity'));

        if ($stockQuantity < 0) {
            return $this->response->setJSON(['success' => false, 'message' => 'Stock quantity cannot be negative']);
        }

        try {
            if ($variantModel->update($id, ['stock_quantity' => $stockQuantity])) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Stock updated successfully',
                    'new_stock' => $stockQuantity
                ]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to update stock']);
            }
        } catch (\Exception $e) {
            log_message('error', 'Failed to update variant stock: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update stock: ' . $e->getMessage()]);
        }
    }

    // Order Management
    public function orders()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $orders = $this->orderModel->getOrdersWithItems();

        $data = array_merge($this->getAdminData('orders'), [
            'title' => 'Manage Orders - Admin',
            'orders' => $orders
        ]);

        return view('admin/orders/index', $data);
    }

    public function viewOrder($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $order = $this->orderModel->getOrderWithDetails($id);
        if (!$order) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Order not found');
        }

        $orderItems = $this->orderItemModel->getOrderItems($id);

        log_message('debug', 'viewOrder: About to call getAdminData');
        $adminData = $this->getAdminData('orders');
        log_message('debug', 'viewOrder: getAdminData returned: ' . json_encode(array_keys($adminData)));

        $data = array_merge($adminData, [
            'title' => 'Order #' . $order['order_number'] . ' - Admin',
            'order' => $order,
            'orderItems' => $orderItems
        ]);

        log_message('debug', 'viewOrder: Final data keys: ' . json_encode(array_keys($data)));
        return view('admin/orders/view', $data);
    }

    public function printOrder($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $order = $this->orderModel->getOrderWithDetails($id);
        if (!$order) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Order not found');
        }

        $orderItems = $this->orderItemModel->getOrderItems($id);

        $data = [
            'title' => 'Invoice #' . $order['order_number'],
            'order' => $order,
            'orderItems' => $orderItems
        ];

        return view('admin/orders/print', $data);
    }

    public function downloadOrderPdf($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $order = $this->orderModel->getOrderWithDetails($id);
        if (!$order) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Order not found');
        }

        $orderItems = $this->orderItemModel->getOrderItems($id);

        // Load DOMPDF
        require_once ROOTPATH . 'vendor/autoload.php';

        $dompdf = new \Dompdf\Dompdf();

        // Generate HTML for PDF
        $html = $this->generateInvoiceHtml($order, $orderItems);
        $dompdf->loadHtml($html);

        // Setup the paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $filename = 'Invoice_' . $order['order_number'] . '.pdf';
        $dompdf->stream($filename, ['Attachment' => true]);
    }

    private function generateInvoiceHtml($order, $orderItems)
    {
        $settingModel = new \App\Models\SettingModel();
        $siteName = $settingModel->getSetting('site_name', 'MICRODOSE MUSHROOM');
        $siteTagline = $settingModel->getSetting('site_tagline', 'Your Trusted Microdose Destination');
        $contactEmail = $settingModel->getSetting('contact_email', 'info@microdosemushroom.com');
        $contactPhone = $settingModel->getSetting('contact_phone', '+1 (xxx)xxx xxxx');
        $siteLogo = $settingModel->getSetting('site_logo', '');
        $businessAddress = $settingModel->getSetting('business_address', '123 Business Street, City, State - 123456');

        $data = [
            'order' => $order,
            'orderItems' => $orderItems,
            'siteName' => $siteName,
            'siteTagline' => $siteTagline,
            'contactEmail' => $contactEmail,
            'contactPhone' => $contactPhone,
            'siteLogo' => $siteLogo,
            'businessAddress' => $businessAddress
        ];

        return view('admin/orders/pdf_template', $data);
    }

    public function updateOrderStatus($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $status = $this->request->getPost('status');

        if ($this->orderModel->updateOrderStatus($id, $status)) {
            session()->setFlashdata('success', 'Order status updated successfully');
        } else {
            session()->setFlashdata('error', 'Failed to update order status');
        }

        return redirect()->back();
    }

    public function updatePaymentStatus($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $paymentStatus = $this->request->getPost('payment_status');

        if ($this->orderModel->updatePaymentStatus($id, $paymentStatus)) {
            session()->setFlashdata('success', 'Payment status updated successfully');
        } else {
            session()->setFlashdata('error', 'Failed to update payment status');
        }

        return redirect()->back();
    }

    // Review Management
    public function reviews()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        // Get pagination parameters
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 10; // Reviews per page
        $status = $this->request->getGet('status'); // 'pending', 'approved', or 'all'
        $search = $this->request->getGet('search'); // Search term

        // Get pending reviews (only show when not filtering or when filtering pending)
        $pendingReviews = [];
        if (empty($status) || $status === 'all' || $status === 'pending') {
            $pendingReviews = $this->reviewModel->getPendingReviews();
        }

        // Get paginated reviews based on status filter
        $reviewsQuery = $this->reviewModel->select('reviews.*, users.first_name, users.last_name, products.name as product_name, products.slug as product_slug')
            ->join('users', 'users.id = reviews.user_id')
            ->join('products', 'products.id = reviews.product_id')
            ->orderBy('reviews.created_at', 'DESC');

        // Apply status filter
        if ($status === 'pending') {
            $reviewsQuery->where('reviews.is_approved', 0);
        } elseif ($status === 'approved') {
            $reviewsQuery->where('reviews.is_approved', 1);
        }
        // If status is 'all' or not set, show all reviews

        // Apply search filter
        if (!empty($search)) {
            $reviewsQuery->groupStart()
                ->like('products.name', $search)
                ->orLike('reviews.title', $search)
                ->orLike('reviews.review', $search)
                ->orLike('CONCAT(users.first_name, " ", users.last_name)', $search)
                ->groupEnd();
        }

        // Get total count for pagination
        $totalReviews = $reviewsQuery->countAllResults(false);

        // Get paginated results
        $reviews = $reviewsQuery->paginate($perPage, 'default', $page);

        // Get pager
        $pager = $this->reviewModel->pager;

        // Get counts for tabs
        $allReviewsCount = $this->reviewModel->countAllResults();
        $pendingCount = $this->reviewModel->where('is_approved', 0)->countAllResults();
        $approvedCount = $this->reviewModel->where('is_approved', 1)->countAllResults();

        $data = array_merge($this->getAdminData('reviews'), [
            'title' => 'Manage Reviews - Admin',
            'pendingReviews' => $pendingReviews,
            'reviews' => $reviews,
            'pager' => $pager,
            'currentStatus' => $status,
            'currentSearch' => $search,
            'totalReviews' => $totalReviews,
            'allReviewsCount' => $allReviewsCount,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'currentPage' => $page,
            'perPage' => $perPage
        ]);

        return view('admin/reviews/index', $data);
    }

    public function approveReview($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        if ($this->reviewModel->approveReview($id)) {
            session()->setFlashdata('success', 'Review approved successfully');
        } else {
            session()->setFlashdata('error', 'Failed to approve review');
        }

        return redirect()->back();
    }

    public function rejectReview($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        // Instead of just rejecting, delete the review automatically
        if ($this->reviewModel->delete($id)) {
            session()->setFlashdata('success', 'Review rejected and deleted successfully');
        } else {
            session()->setFlashdata('error', 'Failed to reject review');
        }

        return redirect()->back();
    }

    public function deleteReview($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        if (!$this->request->isAJAX()) {
            // Non-AJAX request
            if ($this->reviewModel->delete($id)) {
                session()->setFlashdata('success', 'Review deleted successfully');
            } else {
                session()->setFlashdata('error', 'Failed to delete review');
            }
            return redirect()->back();
        }

        // AJAX request
        if ($this->reviewModel->delete($id)) {
            return $this->response->setJSON(['success' => true]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete review']);
        }
    }

    // User Management
    public function users()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $users = $this->userModel->orderBy('created_at', 'DESC')->findAll();

        $data = array_merge($this->getAdminData('users'), [
            'title' => 'Manage Users - Admin',
            'users' => $users
        ]);

        return view('admin/users/index', $data);
    }

    public function viewUser($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('User not found');
        }

        // Get user's orders
        $orders = $this->orderModel->where('user_id', $id)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $data = array_merge($this->getAdminData('users'), [
            'title' => 'User Details - Admin',
            'user' => $user,
            'orders' => $orders
        ]);

        return view('admin/users/view', $data);
    }

    public function toggleUserStatus($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        if (!$this->request->isAJAX()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        $user = $this->userModel->find($id);
        if (!$user) {
            return $this->response->setJSON(['success' => false, 'message' => 'User not found']);
        }

        $input = json_decode($this->request->getBody(), true);
        $isActive = $input['is_active'] ?? 0;

        if ($this->userModel->update($id, ['is_active' => $isActive])) {
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update user status']);
    }

    // Settings Management
    public function settings()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        // Get all settings from database
        $settings = $this->settingModel->getAllSettings();

        $data = array_merge($this->getAdminData('settings'), [
            'title' => 'Site Settings - Admin',
            'settings' => $settings
        ]);

        return view('admin/settings/index', $data);
    }

    public function updateSettings()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $rules = [
            'site_name' => 'required|min_length[2]|max_length[255]',
            'site_tagline' => 'permit_empty|max_length[255]',
            'site_description' => 'permit_empty|max_length[1000]',
            'contact_email' => 'permit_empty|valid_email',
            'contact_phone' => 'permit_empty|max_length[20]',
            'google_analytics_id' => 'permit_empty|max_length[50]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Get form data
        $settingsData = [
            'site_name' => $this->request->getPost('site_name'),
            'site_tagline' => $this->request->getPost('site_tagline'),
            'site_description' => $this->request->getPost('site_description'),
            'contact_email' => $this->request->getPost('contact_email'),
            'contact_phone' => $this->request->getPost('contact_phone'),
            'business_address' => $this->request->getPost('address'),
            'google_analytics_id' => $this->request->getPost('google_analytics_id'),
            'google_analytics_enabled' => $this->request->getPost('google_analytics_enabled') ? true : false,
        ];

        // Handle logo upload (save to uploads/logo/)
        $logoFile = $this->request->getFile('site_logo');
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            $logoName = 'logo_' . time() . '.' . $logoFile->getExtension();
            $logoPath = 'uploads/logo/' . $logoName;
            $logoFile->move(ROOTPATH . 'uploads/logo', $logoName, true);
            $settingsData['site_logo'] = $logoPath;
        }
        // Handle favicon upload (save to uploads/favicon/)
        $faviconFile = $this->request->getFile('site_favicon');
        if ($faviconFile && $faviconFile->isValid() && !$faviconFile->hasMoved()) {
            $faviconName = 'favicon_' . time() . '.' . $faviconFile->getExtension();
            $faviconPath = 'uploads/favicon/' . $faviconName;
            $faviconFile->move(ROOTPATH . 'uploads/favicon', $faviconName, true);
            $settingsData['site_favicon'] = $faviconPath;
        }

        // Update settings in database
        if ($this->settingModel->updateSettings($settingsData)) {
            session()->setFlashdata('success', 'Settings updated successfully');
        } else {
            session()->setFlashdata('error', 'Failed to update settings');
        }

        return redirect()->back();
    }

    // Banner Management
    public function banners()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $banners = $this->bannerModel->orderBy('sort_order', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $data = array_merge($this->getAdminData('banners'), [
            'title' => 'Manage Banners - Admin',
            'banners' => $banners
        ]);

        return view('admin/banners/index', $data);
    }

    public function createBanner()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $data = array_merge($this->getAdminData('banners'), [
            'title' => 'Add New Banner - Admin'
        ]);

        return view('admin/banners/create', $data);
    }

    public function storeBanner()
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $rules = [
            'title' => 'required|min_length[2]|max_length[255]',
            'background_color' => 'permit_empty|regex_match[/^#[0-9A-Fa-f]{6}$/]',
            'text_color' => 'permit_empty|regex_match[/^#[0-9A-Fa-f]{6}$/]',
            'sort_order' => 'permit_empty|integer',
            'image' => 'permit_empty|uploaded[image]|max_size[image,2048]|is_image[image]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $bannerData = [
            'title' => $this->request->getPost('title'),
            'subtitle' => $this->request->getPost('subtitle'),
            'description' => $this->request->getPost('description'),
            'button_text' => $this->request->getPost('button_text'),
            'button_link' => $this->request->getPost('button_link'),
            'button_text_2' => $this->request->getPost('button_text_2'),
            'button_link_2' => $this->request->getPost('button_link_2'),
            'background_color' => $this->request->getPost('background_color') ?: '#ff6b35',
            'text_color' => $this->request->getPost('text_color') ?: '#ffffff',
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'sort_order' => $this->request->getPost('sort_order') ?: 0
        ];

        // Handle image upload
        $imageFile = $this->request->getFile('image');
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $imageName = $this->uploadBannerImage($imageFile);
            if ($imageName) {
                $bannerData['image'] = $imageName;
            }
        }

        if ($this->bannerModel->insert($bannerData)) {
            session()->setFlashdata('success', 'Banner created successfully');
            return redirect()->to('/admin/banners');
        } else {
            // If banner creation failed and image was uploaded, delete the image
            if (isset($bannerData['image'])) {
                $this->deleteBannerImage($bannerData['image']);
            }
            session()->setFlashdata('error', 'Failed to create banner');
            return redirect()->back()->withInput();
        }
    }

    public function editBanner($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $banner = $this->bannerModel->find($id);
        if (!$banner) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Banner not found');
        }

        $data = array_merge($this->getAdminData('banners'), [
            'title' => 'Edit Banner - Admin',
            'banner' => $banner
        ]);

        return view('admin/banners/edit', $data);
    }

    public function updateBanner($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $banner = $this->bannerModel->find($id);
        if (!$banner) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Banner not found');
        }

        $rules = [
            'title' => 'required|min_length[2]|max_length[255]',
            'background_color' => 'permit_empty|regex_match[/^#[0-9A-Fa-f]{6}$/]',
            'text_color' => 'permit_empty|regex_match[/^#[0-9A-Fa-f]{6}$/]',
            'sort_order' => 'permit_empty|integer',
            'image' => 'permit_empty|uploaded[image]|max_size[image,2048]|is_image[image]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $bannerData = [
            'title' => $this->request->getPost('title'),
            'subtitle' => $this->request->getPost('subtitle'),
            'description' => $this->request->getPost('description'),
            'button_text' => $this->request->getPost('button_text'),
            'button_link' => $this->request->getPost('button_link'),
            'button_text_2' => $this->request->getPost('button_text_2'),
            'button_link_2' => $this->request->getPost('button_link_2'),
            'background_color' => $this->request->getPost('background_color') ?: '#ff6b35',
            'text_color' => $this->request->getPost('text_color') ?: '#ffffff',
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
            'sort_order' => $this->request->getPost('sort_order') ?: 0
        ];

        // Handle image upload
        $imageFile = $this->request->getFile('image');
        if ($imageFile && $imageFile->isValid() && !$imageFile->hasMoved()) {
            $newImageName = $this->uploadBannerImage($imageFile);
            if ($newImageName) {
                // Delete old image if exists
                if (!empty($banner['image'])) {
                    $this->deleteBannerImage($banner['image']);
                }
                $bannerData['image'] = $newImageName;
            }
        }

        if ($this->bannerModel->update($id, $bannerData)) {
            session()->setFlashdata('success', 'Banner updated successfully');
            return redirect()->to('/admin/banners');
        } else {
            // If update failed and new image was uploaded, delete the new image
            if (isset($bannerData['image']) && $bannerData['image'] !== $banner['image']) {
                $this->deleteBannerImage($bannerData['image']);
            }
            session()->setFlashdata('error', 'Failed to update banner');
            return redirect()->back()->withInput();
        }
    }

    public function deleteBanner($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        $banner = $this->bannerModel->find($id);
        if (!$banner) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Banner not found']);
            }
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Banner not found');
        }

        if ($this->request->isAJAX()) {
            if ($this->bannerModel->delete($id)) {
                // Delete associated image
                if (!empty($banner['image'])) {
                    $this->deleteBannerImage($banner['image']);
                }
                return $this->response->setJSON(['success' => true]);
            } else {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete banner']);
            }
        }

        if ($this->bannerModel->delete($id)) {
            // Delete associated image
            if (!empty($banner['image'])) {
                $this->deleteBannerImage($banner['image']);
            }
            session()->setFlashdata('success', 'Banner deleted successfully');
        } else {
            session()->setFlashdata('error', 'Failed to delete banner');
        }

        return redirect()->to('/admin/banners');
    }

    public function toggleBannerStatus($id)
    {
        $accessCheck = $this->checkAdminAccess();
        if ($accessCheck !== true) {
            return $accessCheck;
        }

        if (!$this->request->isAJAX()) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }

        $banner = $this->bannerModel->find($id);
        if (!$banner) {
            return $this->response->setJSON(['success' => false, 'message' => 'Banner not found']);
        }

        $input = json_decode($this->request->getBody(), true);
        $isActive = $input['is_active'] ?? 0;

        if ($this->bannerModel->update($id, ['is_active' => $isActive])) {
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update banner status']);
    }

    private function uploadBannerImage($imageFile)
    {
        // Create uploads directory if it doesn't exist
        $uploadPath = ROOTPATH . 'uploads/banners/';
        if (!is_dir($uploadPath)) {
            if (!mkdir($uploadPath, 0755, true)) {
                log_message('error', 'Failed to create banner upload directory: ' . $uploadPath);
                return false;
            }
        }

        // Generate unique filename
        $extension = $imageFile->getClientExtension();
        $fileName = 'banner_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $extension;

        try {
            // Move the file to uploads directory
            if ($imageFile->move($uploadPath, $fileName)) {
                return $fileName;
            }
        } catch (\Exception $e) {
            log_message('error', 'Banner image upload failed: ' . $e->getMessage());
        }

        return false;
    }

    private function deleteBannerImage($imageName)
    {
        if ($imageName && file_exists(ROOTPATH . 'uploads/banners/' . $imageName)) {
            return unlink(ROOTPATH . 'uploads/banners/' . $imageName);
        }
        return true;
    }
}
