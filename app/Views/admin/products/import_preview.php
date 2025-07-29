<?= $this->extend('admin/layout/main') ?>

<?= $this->section('breadcrumb') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('admin/products') ?>">Products</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('admin/products/import') ?>">Import</a></li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-eye me-2"></i>Preview
        </li>
    </ol>
</nav>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Import Preview</h1>
            <p class="text-muted mb-0">Review your data before importing</p>
        </div>
        <div>
            <a href="<?= base_url('admin/products/import') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Import
            </a>
        </div>
    </div>

    <!-- Import Summary -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar"></i> Import Summary
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-primary"><?= $importData['total_rows'] ?></h4>
                                <p class="mb-0">Total Products</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-success"><?= count($importData['data']) ?></h4>
                                <p class="mb-0">Valid Products</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-warning"><?= $importData['total_rows'] - count($importData['data']) ?></h4>
                                <p class="mb-0">Skipped Rows</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-info"><?= count($importData['headers']) ?></h4>
                                <p class="mb-0">Columns Detected</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Data -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-eye"></i> Data Preview (First 10 rows)
            </h6>
            <form action="<?= base_url('admin/products/import/execute') ?>" method="post" style="display: inline;">
                <?= csrf_field() ?>
                <button type="submit" class="btn btn-success" onclick="return confirm('Are you sure you want to import these products?')">
                    <i class="fas fa-check"></i> Confirm Import
                </button>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Sale Price</th>
                            <th>SKU</th>
                            <th>Stock</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $previewData = array_slice($importData['data'], 0, 10);
                        foreach ($previewData as $index => $product):
                        ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <strong><?= esc($product['name']) ?></strong>
                                    <?php if (!empty($product['short_description'])): ?>
                                        <br><small class="text-muted"><?= esc(substr($product['short_description'], 0, 50)) ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $category = null;
                                    foreach ($categories as $cat) {
                                        if ($cat['id'] == $product['category_id']) {
                                            $category = $cat;
                                            break;
                                        }
                                    }
                                    echo $category ? esc($category['name']) : 'Unknown';
                                    ?>
                                </td>
                                <td>₹<?= number_format($product['price'], 2) ?></td>
                                <td>
                                    <?php if ($product['sale_price']): ?>
                                        ₹<?= number_format($product['sale_price'], 2) ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><code><?= esc($product['sku']) ?></code></td>
                                <td><?= $product['stock_quantity'] ?></td>
                                <td>
                                    <?php if ($product['is_active']): ?>
                                        <span class="badge bg-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Inactive</span>
                                    <?php endif; ?>

                                    <?php if ($product['is_featured']): ?>
                                        <span class="badge bg-warning">Featured</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php if (count($importData['data']) > 10): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    Showing first 10 rows. Total <?= count($importData['data']) ?> products will be imported.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Detected Columns -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-info">
                <i class="fas fa-columns"></i> Detected Columns
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($importData['headers'] as $header): ?>
                    <div class="col-md-3 mb-2">
                        <span class="badge bg-light text-dark"><?= esc($header) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
    // Add confirmation for import
    document.querySelector('form[action*="execute"]').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Importing...';
        submitBtn.disabled = true;
    });
</script>

<?= $this->endSection() ?>