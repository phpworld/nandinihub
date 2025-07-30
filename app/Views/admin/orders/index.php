<?= $this->extend('admin/layout/main') ?>

<?= $this->section('breadcrumb') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Orders</li>
    </ol>
</nav>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-shopping-cart me-2"></i>Manage Orders</h2>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary" onclick="refreshOrders()">
            <i class="fas fa-sync-alt me-1"></i>Refresh
        </button>
    </div>
</div>

<!-- Orders Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 text-muted">Total Orders</h6>
                        <h4 class="card-title mb-0"><?= count($orders) ?></h4>
                    </div>
                    <div class="text-primary">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 text-muted">Pending Orders</h6>
                        <h4 class="card-title mb-0">
                            <?= count(array_filter($orders, function ($order) {
                                return $order['status'] === 'pending';
                            })) ?>
                        </h4>
                    </div>
                    <div class="text-warning">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 text-muted">Processing</h6>
                        <h4 class="card-title mb-0">
                            <?= count(array_filter($orders, function ($order) {
                                return $order['status'] === 'processing';
                            })) ?>
                        </h4>
                    </div>
                    <div class="text-info">
                        <i class="fas fa-cog fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-subtitle mb-2 text-muted">Delivered</h6>
                        <h4 class="card-title mb-0">
                            <?= count(array_filter($orders, function ($order) {
                                return $order['status'] === 'delivered';
                            })) ?>
                        </h4>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Orders Table -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">All Orders</h5>
    </div>
    <div class="card-body">
        <?php if (empty($orders)): ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No orders found</h5>
                <p class="text-muted">Orders will appear here once customers start placing them.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover data-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($order['order_number']) ?></strong>
                                </td>
                                <td>
                                    <?= esc($order['first_name'] . ' ' . $order['last_name']) ?>
                                </td>
                                <td>
                                    <small class="text-muted"><?= esc($order['email']) ?></small>
                                </td>
                                <td>
                                    <strong>$<?= number_format($order['total_amount'], 2) ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <?= $order['payment_method'] === 'cod' ? 'Cash on Delivery' : 'Online Payment' ?>
                                    </span>
                                    <?php if ($order['payment_method'] === 'cod'): ?>
                                        <?php
                                        $paymentStatus = $order['payment_status'] ?? 'pending';
                                        $badgeClass = match ($paymentStatus) {
                                            'paid' => 'success',
                                            'confirmed' => 'info',
                                            'pending' => 'warning',
                                            default => 'secondary'
                                        };
                                        $statusText = match ($paymentStatus) {
                                            'paid' => 'Paid',
                                            'confirmed' => 'Confirmed',
                                            'pending' => 'Pending',
                                            default => ucfirst($paymentStatus)
                                        };
                                        ?>
                                        <br>
                                        <small class="badge bg-<?= $badgeClass ?> mt-1">
                                            <?= $statusText ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= getOrderStatusColor($order['status']) ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <small><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= base_url('admin/orders/' . $order['id']) ?>"
                                            class="btn btn-sm btn-outline-primary" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-cog"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <h6 class="dropdown-header">Update Status</h6>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                        onclick="updateOrderStatus(<?= $order['id'] ?>, 'pending')">
                                                        <i class="fas fa-clock text-warning me-2"></i>Pending
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                        onclick="updateOrderStatus(<?= $order['id'] ?>, 'processing')">
                                                        <i class="fas fa-cog text-info me-2"></i>Processing
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                        onclick="updateOrderStatus(<?= $order['id'] ?>, 'shipped')">
                                                        <i class="fas fa-truck text-primary me-2"></i>Shipped
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                        onclick="updateOrderStatus(<?= $order['id'] ?>, 'delivered')">
                                                        <i class="fas fa-check-circle text-success me-2"></i>Delivered
                                                    </a>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="#"
                                                        onclick="updateOrderStatus(<?= $order['id'] ?>, 'cancelled')">
                                                        <i class="fas fa-times-circle text-danger me-2"></i>Cancelled
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
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
    function refreshOrders() {
        location.reload();
    }

    function updateOrderStatus(orderId, status) {
        if (confirm('Are you sure you want to update this order status to ' + status + '?')) {
            // Create a form and submit it
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= base_url('admin/orders') ?>/' + orderId + '/status';

            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'status';
            statusInput.value = status;

            form.appendChild(statusInput);
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
<?= $this->endSection() ?>

<?php
// Helper function for order status colors
function getOrderStatusColor($status)
{
    switch ($status) {
        case 'pending':
            return 'warning';
        case 'processing':
            return 'info';
        case 'shipped':
            return 'primary';
        case 'delivered':
            return 'success';
        case 'cancelled':
            return 'danger';
        default:
            return 'secondary';
    }
}
?>