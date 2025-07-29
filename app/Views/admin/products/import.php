<?= $this->extend('admin/layout/main') ?>

<?= $this->section('breadcrumb') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('admin/products') ?>">Products</a></li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-upload me-2"></i>Import
        </li>
    </ol>
</nav>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Import Products</h1>
            <p class="text-muted mb-0">Upload CSV or Excel files to bulk import products</p>
        </div>
        <div>
            <a href="<?= base_url('admin/products') ?>" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Back to Products
            </a>
            <a href="<?= base_url('admin/products/import/sample') ?>" class="btn btn-success">
                <i class="fas fa-download me-2"></i>Download Sample CSV
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Import Instructions -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-upload"></i> Upload Import File
                    </h6>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/products/import') ?>" method="post" enctype="multipart/form-data" id="importForm">
                        <?= csrf_field() ?>

                        <div class="mb-4">
                            <label for="import_file" class="form-label">Select CSV or Excel File</label>
                            <input type="file" class="form-control" id="import_file" name="import_file"
                                accept=".csv,.xlsx,.xls" required>
                            <div class="form-text">
                                Supported formats: CSV (.csv), Excel (.xlsx, .xls). Maximum file size: 10MB
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload"></i> Upload & Preview
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-info-circle"></i> Import Instructions
                    </h6>
                </div>
                <div class="card-body">
                    <h6>Required Columns:</h6>
                    <ul class="list-unstyled">
                        <li><strong>name</strong> - Product name</li>
                        <li><strong>price</strong> - Regular price</li>
                        <li><strong>sku</strong> - Unique product code</li>
                    </ul>

                    <h6 class="mt-3">Optional Columns:</h6>
                    <ul class="list-unstyled small">
                        <li>category_id - Category ID</li>
                        <li>description - Full description</li>
                        <li>short_description - Brief description</li>
                        <li>sale_price - Discounted price</li>
                        <li>stock_quantity - Available stock</li>
                        <li>weight - Product weight</li>
                        <li>dimensions - Product dimensions</li>
                        <li>is_featured - Featured (1/0)</li>
                        <li>is_active - Active status (1/0)</li>
                        <li>meta_title - SEO title</li>
                        <li>meta_description - SEO description</li>
                    </ul>

                    <div class="alert alert-warning mt-3">
                        <small>
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Note:</strong> Download the sample CSV to see the exact format required.
                        </small>
                    </div>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-list"></i> Available Categories
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Category Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                    <tr>
                                        <td><?= $category['id'] ?></td>
                                        <td><?= esc($category['name']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('importForm').addEventListener('submit', function(e) {
        const fileInput = document.getElementById('import_file');
        const file = fileInput.files[0];

        if (!file) {
            e.preventDefault();
            alert('Please select a file to import.');
            return;
        }

        // Check file size (10MB limit)
        if (file.size > 10 * 1024 * 1024) {
            e.preventDefault();
            alert('File size must be less than 10MB.');
            return;
        }

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        submitBtn.disabled = true;
    });
</script>

<?= $this->endSection() ?>