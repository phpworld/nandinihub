<?= $this->extend('admin/layout/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Coupons</li>
    </ol>
</nav>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-tags me-2"></i>
                        Manage Coupons
                    </h5>
                    <a href="<?= base_url('admin/coupons/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add New Coupon
                    </a>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (empty($coupons)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No coupons found</h5>
                            <p class="text-muted">Create your first coupon to get started.</p>
                            <a href="<?= base_url('admin/coupons/create') ?>" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Create Coupon
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Value</th>
                                        <th>Min Order</th>
                                        <th>Usage</th>
                                        <th>Valid Until</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($coupons as $coupon): ?>
                                        <tr>
                                            <td>
                                                <code class="bg-light px-2 py-1 rounded"><?= esc($coupon['code']) ?></code>
                                            </td>
                                            <td><?= esc($coupon['name']) ?></td>
                                            <td>
                                                <?php
                                                $typeLabels = [
                                                    'percentage' => '<span class="badge bg-info">Percentage</span>',
                                                    'fixed_amount' => '<span class="badge bg-success">Fixed Amount</span>',
                                                    'free_shipping' => '<span class="badge bg-warning">Free Shipping</span>'
                                                ];
                                                echo $typeLabels[$coupon['type']] ?? $coupon['type'];
                                                ?>
                                            </td>
                                            <td>
                                                <?php if ($coupon['type'] === 'percentage'): ?>
                                                    <?= $coupon['value'] ?>%
                                                <?php elseif ($coupon['type'] === 'fixed_amount'): ?>
                                                    $<?= number_format($coupon['value'], 2) ?>
                                                <?php else: ?>
                                                    Free
                                                <?php endif; ?>
                                            </td>
                                            <td>$<?= number_format($coupon['minimum_order_amount'], 2) ?></td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= $coupon['used_count'] ?> / 
                                                    <?= $coupon['usage_limit'] ? $coupon['usage_limit'] : '∞' ?>
                                                </small>
                                            </td>
                                            <td>
                                                <?php if ($coupon['valid_until']): ?>
                                                    <?php
                                                    $validUntil = new DateTime($coupon['valid_until']);
                                                    $now = new DateTime();
                                                    $isExpired = $validUntil < $now;
                                                    ?>
                                                    <small class="<?= $isExpired ? 'text-danger' : 'text-muted' ?>">
                                                        <?= $validUntil->format('M d, Y') ?>
                                                        <?php if ($isExpired): ?>
                                                            <br><span class="badge bg-danger">Expired</span>
                                                        <?php endif; ?>
                                                    </small>
                                                <?php else: ?>
                                                    <small class="text-muted">No expiry</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input status-toggle" 
                                                           type="checkbox" 
                                                           data-coupon-id="<?= $coupon['id'] ?>"
                                                           <?= $coupon['is_active'] ? 'checked' : '' ?>>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= base_url('admin/coupons/' . $coupon['id'] . '/stats') ?>" 
                                                       class="btn btn-sm btn-outline-info" 
                                                       title="View Statistics">
                                                        <i class="fas fa-chart-bar"></i>
                                                    </a>
                                                    <a href="<?= base_url('admin/coupons/' . $coupon['id'] . '/edit') ?>" 
                                                       class="btn btn-sm btn-outline-primary" 
                                                       title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-sm btn-outline-danger delete-coupon" 
                                                            data-coupon-id="<?= $coupon['id'] ?>"
                                                            data-coupon-code="<?= esc($coupon['code']) ?>"
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
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the coupon <strong id="coupon-code-display"></strong>?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="delete-form" method="POST" style="display: inline;">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Status toggle
document.querySelectorAll('.status-toggle').forEach(function(toggle) {
    toggle.addEventListener('change', function() {
        const couponId = this.dataset.couponId;
        const isActive = this.checked;
        
        fetch(`<?= base_url('admin/coupons') ?>/${couponId}/toggle`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showAlert(data.message, 'success');
            } else {
                // Revert toggle and show error
                this.checked = !isActive;
                showAlert(data.message, 'danger');
            }
        })
        .catch(error => {
            // Revert toggle and show error
            this.checked = !isActive;
            showAlert('An error occurred. Please try again.', 'danger');
        });
    });
});

// Delete coupon
document.querySelectorAll('.delete-coupon').forEach(function(button) {
    button.addEventListener('click', function() {
        const couponId = this.dataset.couponId;
        const couponCode = this.dataset.couponCode;
        
        document.getElementById('coupon-code-display').textContent = couponCode;
        document.getElementById('delete-form').action = `<?= base_url('admin/coupons') ?>/${couponId}/delete`;
        
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    });
});

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    const container = document.querySelector('.card-body');
    container.insertBefore(alertDiv, container.firstChild);
    
    // Auto dismiss after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
<?= $this->endSection() ?>
