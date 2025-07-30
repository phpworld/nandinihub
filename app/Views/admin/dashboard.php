<?= $this->extend('admin/layout/main') ?>

<?= $this->section('breadcrumb') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
        </li>
    </ol>
</nav>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Dashboard Overview</h1>
    <div class="text-muted">
        <i class="fas fa-calendar me-2"></i><?= date('F j, Y') ?>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title text-muted">Total Products</h5>
                        <h2 class="text-primary"><?= $totalProducts ?></h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-box fa-2x text-primary"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title text-muted">Total Orders</h5>
                        <h2 class="text-success"><?= $totalOrders ?></h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-shopping-bag fa-2x text-success"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title text-muted">Total Users</h5>
                        <h2 class="text-info"><?= $totalUsers ?></h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-users fa-2x text-info"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h5 class="card-title text-muted">Total Revenue</h5>
                        <h2 class="text-warning">$<?= number_format($totalRevenue, 2) ?></h2>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-dollar-sign fa-2x text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Orders -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Recent Orders</h5>
                <a href="<?= base_url('admin/orders') ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <?php if (empty($recentOrders)): ?>
                    <p class="text-muted text-center py-3">No orders yet</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $order): ?>
                                    <tr>
                                        <td>
                                            <a href="<?= base_url('admin/orders/' . $order['id']) ?>" class="text-decoration-none">
                                                <?= esc($order['order_number']) ?>
                                            </a>
                                        </td>
                                        <td><?= esc($order['first_name'] . ' ' . $order['last_name']) ?></td>
                                        <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $order['status'] === 'delivered' ? 'success' : ($order['status'] === 'cancelled' ? 'danger' : 'warning') ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M j, Y', strtotime($order['created_at'])) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Top Products & Pending Reviews -->
    <div class="col-lg-4">
        <!-- Top Products -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Top Selling Products</h5>
            </div>
            <div class="card-body">
                <?php if (empty($topProducts)): ?>
                    <p class="text-muted text-center py-3">No sales data yet</p>
                <?php else: ?>
                    <?php foreach ($topProducts as $product): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <h6 class="mb-0"><?= esc($product['product_name']) ?></h6>
                                <small class="text-muted"><?= $product['total_sold'] ?> sold</small>
                            </div>
                            <span class="text-success">$<?= number_format($product['total_revenue'], 2) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Pending Reviews -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Pending Reviews</h5>
                <a href="<?= base_url('admin/reviews') ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                <?php if (empty($pendingReviews)): ?>
                    <p class="text-muted text-center py-3">No pending reviews</p>
                <?php else: ?>
                    <?php foreach ($pendingReviews as $review): ?>
                        <div class="mb-3 pb-2 border-bottom">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1"><?= esc($review['product_name']) ?></h6>
                                    <div class="text-warning mb-1">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star<?= $i <= $review['rating'] ? '' : '-o' ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <small class="text-muted">by <?= esc($review['first_name']) ?></small>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <form action="<?= base_url('admin/reviews/' . $review['id'] . '/approve') ?>" method="POST" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form action="<?= base_url('admin/reviews/' . $review['id'] . '/reject') ?>" method="POST" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Dashboard specific scripts can go here
    console.log('Dashboard loaded successfully');
</script>
<?= $this->endSection() ?>