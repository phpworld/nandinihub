<?= $this->extend('admin/layout/main') ?>

<?= $this->section('breadcrumb') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('admin/users') ?>">Users</a></li>
        <li class="breadcrumb-item active"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></li>
    </ol>
</nav>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-user me-2"></i><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></h2>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Users
        </a>
        <?php if ($user['role'] !== 'admin'): ?>
            <button class="btn btn-outline-primary" onclick="sendEmail('<?= esc($user['email']) ?>')">
                <i class="fas fa-envelope me-1"></i>Send Email
            </button>
        <?php endif; ?>
    </div>
</div>

<div class="row">
    <!-- User Information -->
    <div class="col-lg-4">
        <!-- User Profile Card -->
        <div class="card mb-4">
            <div class="card-body text-center">
                <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; font-size: 1.5rem;">
                    <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                </div>
                <h5 class="card-title"><?= esc($user['first_name'] . ' ' . $user['last_name']) ?></h5>
                <p class="text-muted mb-2"><?= esc($user['email']) ?></p>
                <div class="mb-3">
                    <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'primary' ?> me-2">
                        <?= ucfirst($user['role']) ?>
                    </span>
                    <span class="badge bg-<?= $user['is_active'] ? 'success' : 'secondary' ?>">
                        <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                    </span>
                </div>
                <?php if ($user['role'] !== 'admin'): ?>
                    <div class="form-check form-switch d-flex justify-content-center">
                        <input class="form-check-input" type="checkbox" id="userStatus" 
                               <?= $user['is_active'] ? 'checked' : '' ?>
                               onchange="toggleUserStatus(<?= $user['id'] ?>, this.checked)">
                        <label class="form-check-label ms-2" for="userStatus">
                            Account Active
                        </label>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Contact Information</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-4"><strong>Email:</strong></div>
                    <div class="col-8">
                        <a href="mailto:<?= esc($user['email']) ?>" class="text-decoration-none">
                            <?= esc($user['email']) ?>
                        </a>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4"><strong>Phone:</strong></div>
                    <div class="col-8">
                        <?php if (!empty($user['phone'])): ?>
                            <a href="tel:<?= esc($user['phone']) ?>" class="text-decoration-none">
                                <?= esc($user['phone']) ?>
                            </a>
                        <?php else: ?>
                            <span class="text-muted">Not provided</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-4"><strong>Address:</strong></div>
                    <div class="col-8">
                        <?php if (!empty($user['address'])): ?>
                            <?= esc($user['address']) ?><br>
                            <?php if (!empty($user['city'])): ?>
                                <?= esc($user['city']) ?>
                                <?php if (!empty($user['state'])): ?>, <?= esc($user['state']) ?><?php endif; ?>
                                <?php if (!empty($user['pincode'])): ?> - <?= esc($user['pincode']) ?><?php endif; ?>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="text-muted">Not provided</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4"><strong>Joined:</strong></div>
                    <div class="col-8"><?= date('M j, Y g:i A', strtotime($user['created_at'])) ?></div>
                </div>
            </div>
        </div>

        <!-- Account Statistics -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Account Statistics</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-primary mb-0"><?= count($orders) ?></h4>
                            <small class="text-muted">Total Orders</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success mb-0">
                            ₹<?= number_format(array_sum(array_column($orders, 'total_amount')), 2) ?>
                        </h4>
                        <small class="text-muted">Total Spent</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- User Orders -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Order History</h6>
                <span class="badge bg-primary"><?= count($orders) ?> Orders</span>
            </div>
            <div class="card-body">
                <?php if (empty($orders)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No orders found</h5>
                        <p class="text-muted">This user hasn't placed any orders yet.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th>Status</th>
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
                                            <small><?= date('M j, Y', strtotime($order['created_at'])) ?></small>
                                        </td>
                                        <td>
                                            <strong>$<?= number_format($order['total_amount'], 2) ?></strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                <?= $order['payment_method'] === 'cod' ? 'COD' : 'Online' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= getOrderStatusColor($order['status']) ?>">
                                                <?= ucfirst($order['status']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="<?= base_url('admin/orders/' . $order['id']) ?>" 
                                               class="btn btn-sm btn-outline-primary" title="View Order">
                                                <i class="fas fa-eye"></i>
                                            </a>
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
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .avatar-circle {
        border-radius: 50%;
        background: var(--primary-color);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function toggleUserStatus(userId, isActive) {
    fetch(`<?= base_url('admin/users/') ?>${userId}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ is_active: isActive ? 1 : 0 })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update the badge
            const statusBadge = document.querySelector('.badge.bg-success, .badge.bg-secondary');
            if (statusBadge) {
                statusBadge.className = isActive ? 'badge bg-success' : 'badge bg-secondary';
                statusBadge.textContent = isActive ? 'Active' : 'Inactive';
            }
            alert('User status updated successfully');
        } else {
            // Revert toggle
            document.getElementById('userStatus').checked = !isActive;
            alert('Failed to update user status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Revert toggle
        document.getElementById('userStatus').checked = !isActive;
        alert('An error occurred while updating user status');
    });
}

function sendEmail(email) {
    const subject = prompt('Enter email subject:');
    if (subject) {
        const message = prompt('Enter email message:');
        if (message) {
            // Here you would typically send an AJAX request to send the email
            alert(`Email would be sent to ${email}\nSubject: ${subject}\nMessage: ${message}`);
        }
    }
}
</script>
<?= $this->endSection() ?>

<?php
// Helper function for order status colors
function getOrderStatusColor($status) {
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
