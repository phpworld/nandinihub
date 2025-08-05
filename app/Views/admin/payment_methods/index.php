<?= $this->extend('admin/layout/main') ?>

<?= $this->section('breadcrumb') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-credit-card me-2"></i>Payment Methods
        </li>
    </ol>
</nav>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-credit-card me-2"></i>Payment Methods
                </h1>
                <a href="<?= base_url('admin/payment-methods/create') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Add Payment Method
                </a>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <?php if (empty($paymentMethods)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-credit-card fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No Payment Methods Found</h5>
                            <p class="text-muted">Start by adding your first payment method.</p>
                            <a href="<?= base_url('admin/payment-methods/create') ?>" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Add Payment Method
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Sort Order</th>
                                        <th>Name</th>
                                        <th>Wallet Address</th>
                                        <th>QR Code</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($paymentMethods as $method): ?>
                                        <tr>
                                            <td>
                                                <span class="badge bg-secondary"><?= $method['sort_order'] ?></span>
                                            </td>
                                            <td>
                                                <strong><?= esc($method['name']) ?></strong>
                                                <?php if (!empty($method['payment_information'])): ?>
                                                    <br><small class="text-muted">
                                                        <?= esc(substr($method['payment_information'], 0, 50)) ?>
                                                        <?= strlen($method['payment_information']) > 50 ? '...' : '' ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($method['wallet_address'])): ?>
                                                    <code class="small">
                                                        <?= esc(substr($method['wallet_address'], 0, 20)) ?>...
                                                    </code>
                                                <?php else: ?>
                                                    <span class="text-muted">Not set</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($method['qr_code'])): ?>
                                                    <img src="<?= base_url($method['qr_code']) ?>" 
                                                         alt="QR Code" 
                                                         class="img-thumbnail" 
                                                         style="width: 40px; height: 40px;">
                                                <?php else: ?>
                                                    <span class="text-muted">No QR</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($method['is_active']): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('M j, Y', strtotime($method['created_at'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= base_url('admin/payment-methods/' . $method['id'] . '/edit') ?>" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?= base_url('admin/payment-methods/' . $method['id'] . '/toggle') ?>" 
                                                       class="btn btn-sm btn-outline-<?= $method['is_active'] ? 'warning' : 'success' ?>" 
                                                       title="<?= $method['is_active'] ? 'Disable' : 'Enable' ?>"
                                                       onclick="return confirm('Are you sure you want to <?= $method['is_active'] ? 'disable' : 'enable' ?> this payment method?')">
                                                        <i class="fas fa-<?= $method['is_active'] ? 'eye-slash' : 'eye' ?>"></i>
                                                    </a>
                                                    <a href="<?= base_url('admin/payment-methods/' . $method['id'] . '/delete') ?>" 
                                                       class="btn btn-sm btn-outline-danger" 
                                                       title="Delete"
                                                       onclick="return confirm('Are you sure you want to delete this payment method? This action cannot be undone.')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($pager): ?>
                            <div class="d-flex justify-content-center mt-4">
                                <?= $pager->links() ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.btn-group .btn {
    border-radius: 0.375rem;
    margin-right: 2px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

code {
    background-color: #f8f9fa;
    color: #e83e8c;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
}
</style>
<?= $this->endSection() ?>
