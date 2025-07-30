<?= $this->extend('admin/layout/main') ?>

<?= $this->section('breadcrumb') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-box me-2"></i>Products
        </li>
    </ol>
</nav>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Products Management</h1>
        <p class="text-muted mb-0">Manage your product catalog</p>
    </div>
    <div>
        <a href="<?= base_url('admin/products/import') ?>" class="btn btn-success me-2">
            <i class="fas fa-upload me-2"></i>Import Products
        </a>
        <a href="<?= base_url('admin/products/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Product
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= $category['id'] ?>" <?= (request()->getGet('category') == $category['id']) ? 'selected' : '' ?>>
                            <?= esc($category['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Status</option>
                    <option value="1" <?= (request()->getGet('status') === '1') ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= (request()->getGet('status') === '0') ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search"
                    placeholder="Search by name, SKU..." value="<?= esc(request()->getGet('search')) ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-grid">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Products List</h5>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" onclick="exportProducts()">
                <i class="fas fa-download me-2"></i>Export
            </button>
            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-cog me-2"></i>Actions
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="bulkAction('activate')">
                            <i class="fas fa-check me-2"></i>Bulk Activate
                        </a></li>
                    <li><a class="dropdown-item" href="#" onclick="bulkAction('deactivate')">
                            <i class="fas fa-times me-2"></i>Bulk Deactivate
                        </a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item text-danger" href="#" onclick="bulkAction('delete')">
                            <i class="fas fa-trash me-2"></i>Bulk Delete
                        </a></li>
                </ul>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php if (empty($products)): ?>
            <div class="text-center py-5">
                <i class="fas fa-box fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No products found</h5>
                <p class="text-muted">Start by adding your first product to the catalog.</p>
                <a href="<?= base_url('admin/products/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add First Product
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover data-table" id="productsTable">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th width="80">Image</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Featured</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input product-checkbox"
                                        value="<?= $product['id'] ?>">
                                </td>
                                <td>
                                    <?php if (!empty($product['image'])): ?>
                                        <img src="<?= base_url('uploads/products/' . $product['image']) ?>"
                                            alt="<?= esc($product['name']) ?>"
                                            class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center"
                                            style="width: 50px; height: 50px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-0"><?= esc($product['name']) ?></h6>
                                        <?php if (!empty($product['short_description'])): ?>
                                            <small class="text-muted"><?= esc(substr($product['short_description'], 0, 50)) ?>...</small>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= esc($product['category_name']) ?></span>
                                </td>
                                <td>
                                    <code><?= esc($product['sku']) ?></code>
                                </td>
                                <td>
                                    <div>
                                        <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                                            <span class="text-decoration-line-through text-muted">$<?= number_format($product['price'], 2) ?></span><br>
                                            <strong class="text-success">$<?= number_format($product['sale_price'], 2) ?></strong>
                                        <?php else: ?>
                                            <strong>$<?= number_format($product['price'], 2) ?></strong>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $product['stock_quantity'] > 10 ? 'success' : ($product['stock_quantity'] > 0 ? 'warning' : 'danger') ?>">
                                        <?= $product['stock_quantity'] ?> units
                                    </span>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input status-toggle" type="checkbox"
                                            data-id="<?= $product['id'] ?>"
                                            <?= $product['is_active'] ? 'checked' : '' ?>>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input featured-toggle" type="checkbox"
                                            data-id="<?= $product['id'] ?>"
                                            <?= $product['is_featured'] ? 'checked' : '' ?>>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?= base_url('admin/products/' . $product['id'] . '/edit') ?>"
                                            class="btn btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= base_url('admin/products/' . $product['id'] . '/variants') ?>"
                                            class="btn btn-outline-secondary" title="Manage Variants">
                                            <i class="fas fa-boxes"></i>
                                        </a>
                                        <a href="<?= base_url('product/' . $product['slug']) ?>"
                                            class="btn btn-outline-info" title="View" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button class="btn btn-outline-danger"
                                            onclick="deleteProduct(<?= $product['id'] ?>, '<?= esc($product['name']) ?>')"
                                            title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Select all functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.product-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Status toggle
    document.querySelectorAll('.status-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const productId = this.dataset.id;
            const isActive = this.checked ? 1 : 0;

            fetch(`<?= base_url('admin/products/') ?>${productId}/toggle-status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        is_active: isActive
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        showAlert('success', 'Product status updated successfully');
                    } else {
                        // Revert toggle
                        this.checked = !this.checked;
                        showAlert('error', 'Failed to update product status');
                    }
                })
                .catch(error => {
                    // Revert toggle
                    this.checked = !this.checked;
                    showAlert('error', 'An error occurred');
                });
        });
    });

    // Featured toggle
    document.querySelectorAll('.featured-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const productId = this.dataset.id;
            const isFeatured = this.checked ? 1 : 0;

            fetch(`<?= base_url('admin/products/') ?>${productId}/toggle-featured`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        is_featured: isFeatured
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('success', 'Product featured status updated successfully');
                    } else {
                        this.checked = !this.checked;
                        showAlert('error', 'Failed to update featured status');
                    }
                })
                .catch(error => {
                    this.checked = !this.checked;
                    showAlert('error', 'An error occurred');
                });
        });
    });

    // Delete product
    function deleteProduct(id, name) {
        if (confirm(`Are you sure you want to delete "${name}"? This action cannot be undone.`)) {
            fetch(`<?= base_url('admin/products/') ?>${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        showAlert('error', 'Failed to delete product');
                    }
                })
                .catch(error => {
                    showAlert('error', 'An error occurred');
                });
        }
    }

    // Bulk actions
    function bulkAction(action) {
        const selectedProducts = Array.from(document.querySelectorAll('.product-checkbox:checked'))
            .map(cb => cb.value);

        if (selectedProducts.length === 0) {
            showAlert('warning', 'Please select at least one product');
            return;
        }

        let confirmMessage = '';
        switch (action) {
            case 'activate':
                confirmMessage = `Activate ${selectedProducts.length} selected products?`;
                break;
            case 'deactivate':
                confirmMessage = `Deactivate ${selectedProducts.length} selected products?`;
                break;
            case 'delete':
                confirmMessage = `Delete ${selectedProducts.length} selected products? This action cannot be undone.`;
                break;
        }

        if (confirm(confirmMessage)) {
            fetch(`<?= base_url('admin/products/bulk-action') ?>`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        action: action,
                        products: selectedProducts
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        showAlert('error', 'Failed to perform bulk action');
                    }
                })
                .catch(error => {
                    showAlert('error', 'An error occurred');
                });
        }
    }

    // Export products
    function exportProducts() {
        window.open(`<?= base_url('admin/products/export') ?>`, '_blank');
    }

    // Show alert function
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        const container = document.querySelector('.content-wrapper');
        container.insertBefore(alertDiv, container.firstChild);

        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
</script>
<?= $this->endSection() ?>