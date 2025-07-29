<?= $this->extend('admin/layout/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('admin/coupons') ?>">Coupons</a></li>
        <li class="breadcrumb-item active">Statistics</li>
    </ol>
</nav>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h2 mb-1">Coupon Statistics</h1>
                    <p class="text-muted mb-0">Detailed analytics for coupon: <strong><?= esc($stats['coupon']['code']) ?></strong></p>
                </div>
                <div>
                    <a href="<?= base_url('admin/coupons') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Coupons
                    </a>
                </div>
            </div>

            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Coupon Overview -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-tag me-2"></i>Coupon Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Code:</strong><br>
                                    <span class="badge bg-primary fs-6"><?= esc($stats['coupon']['code']) ?></span>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Status:</strong><br>
                                    <span class="badge bg-<?= $stats['coupon']['is_active'] ? 'success' : 'danger' ?> fs-6">
                                        <?= $stats['coupon']['is_active'] ? 'Active' : 'Inactive' ?>
                                    </span>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Type:</strong><br>
                                    <?= ucfirst(str_replace('_', ' ', $stats['coupon']['type'])) ?>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Value:</strong><br>
                                    <?php if ($stats['coupon']['type'] === 'percentage'): ?>
                                        <?= $stats['coupon']['value'] ?>%
                                    <?php elseif ($stats['coupon']['type'] === 'fixed_amount'): ?>
                                        ₹<?= number_format($stats['coupon']['value'], 2) ?>
                                    <?php else: ?>
                                        Free Shipping
                                    <?php endif; ?>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Min Order:</strong><br>
                                    ₹<?= number_format($stats['coupon']['minimum_order_amount'], 2) ?>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Max Discount:</strong><br>
                                    <?= $stats['coupon']['maximum_discount_amount'] ? '₹' . number_format($stats['coupon']['maximum_discount_amount'], 2) : 'No limit' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-calendar me-2"></i>Validity Period
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Valid From:</strong><br>
                                    <?= $stats['coupon']['valid_from'] ? date('M d, Y', strtotime($stats['coupon']['valid_from'])) : 'No start date' ?>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Valid Until:</strong><br>
                                    <?= $stats['coupon']['valid_until'] ? date('M d, Y', strtotime($stats['coupon']['valid_until'])) : 'No expiry' ?>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-6">
                                    <strong>Usage Limit:</strong><br>
                                    <?= $stats['coupon']['usage_limit'] ?: 'Unlimited' ?>
                                </div>
                                <div class="col-sm-6">
                                    <strong>Per Customer:</strong><br>
                                    <?= $stats['coupon']['usage_limit_per_customer'] ?>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <strong>Usage Progress:</strong><br>
                                    <?php if ($stats['coupon']['usage_limit']): ?>
                                        <div class="progress mt-2">
                                            <div class="progress-bar" role="progressbar"
                                                style="width: <?= $stats['usage_percentage'] ?>%"
                                                aria-valuenow="<?= $stats['usage_percentage'] ?>"
                                                aria-valuemin="0" aria-valuemax="100">
                                                <?= $stats['usage_percentage'] ?>%
                                            </div>
                                        </div>
                                        <small class="text-muted">
                                            <?= $stats['coupon']['used_count'] ?> of <?= $stats['coupon']['usage_limit'] ?> uses
                                            (<?= $stats['remaining_uses'] ?> remaining)
                                        </small>
                                    <?php else: ?>
                                        <span class="text-muted">Unlimited usage</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="display-6 text-primary mb-2">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3 class="card-title"><?= number_format($stats['usage_stats']['total_uses']) ?></h3>
                            <p class="card-text text-muted">Total Uses</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="display-6 text-success mb-2">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3 class="card-title"><?= number_format($stats['usage_stats']['unique_users']) ?></h3>
                            <p class="card-text text-muted">Unique Users</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="display-6 text-warning mb-2">
                                <i class="fas fa-rupee-sign"></i>
                            </div>
                            <h3 class="card-title">₹<?= number_format($stats['usage_stats']['total_discount'], 2) ?></h3>
                            <p class="card-text text-muted">Total Discount</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="display-6 text-info mb-2">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <h3 class="card-title">₹<?= number_format($stats['usage_stats']['total_order_value'], 2) ?></h3>
                            <p class="card-text text-muted">Total Order Value</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Average Statistics -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="display-6 text-secondary mb-2">
                                <i class="fas fa-calculator"></i>
                            </div>
                            <h4 class="card-title">₹<?= number_format($stats['usage_stats']['avg_discount'], 2) ?></h4>
                            <p class="card-text text-muted">Average Discount per Use</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card text-center">
                        <div class="card-body">
                            <div class="display-6 text-secondary mb-2">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <h4 class="card-title">₹<?= number_format($stats['usage_stats']['avg_order_value'], 2) ?></h4>
                            <p class="card-text text-muted">Average Order Value</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Usage History -->
            <?php if (!empty($usage_history)): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i>Recent Usage History
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>User</th>
                                        <th>Order</th>
                                        <th>Order Amount</th>
                                        <th>Discount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($usage_history as $usage): ?>
                                        <tr>
                                            <td><?= date('M d, Y H:i', strtotime($usage['used_at'])) ?></td>
                                            <td>
                                                <?= esc($usage['user_name'] ?? 'User #' . $usage['user_id']) ?>
                                                <br><small class="text-muted"><?= esc($usage['user_email'] ?? '') ?></small>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('admin/orders/' . $usage['order_id']) ?>" class="text-decoration-none">
                                                    #<?= $usage['order_number'] ?? $usage['order_id'] ?>
                                                </a>
                                            </td>
                                            <td>₹<?= number_format($usage['order_amount'], 2) ?></td>
                                            <td>
                                                <span class="badge bg-success">
                                                    -₹<?= number_format($usage['discount_amount'], 2) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <div class="display-1 text-muted mb-3">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4 class="text-muted">No Usage Data</h4>
                        <p class="text-muted">This coupon hasn't been used yet.</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>