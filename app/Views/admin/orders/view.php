<?= $this->extend('admin/layout/main') ?>

<?= $this->section('breadcrumb') ?>
<nav aria-label="breadcrumb" class="no-print">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('admin/orders') ?>">Orders</a></li>
        <li class="breadcrumb-item active">Order #<?= esc($order['order_number']) ?></li>
    </ol>
</nav>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-receipt me-2"></i>Order #<?= esc($order['order_number']) ?></h2>
    <div class="d-flex gap-2">
        <a href="<?= base_url('admin/orders') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Orders
        </a>
        <a href="<?= base_url('admin/orders/' . $order['id'] . '/print') ?>" target="_blank" class="btn btn-outline-primary">
            <i class="fas fa-print me-1"></i>Print Invoice
        </a>
    </div>
</div>

<div class="row">
    <!-- Order Information -->
    <div class="col-lg-8">
        <!-- Order Details Card -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Order Details</h5>
                <span class="badge bg-<?= getOrderStatusColor($order['status']) ?> fs-6">
                    <?= ucfirst($order['status']) ?>
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Order Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Order Number:</strong></td>
                                <td><?= esc($order['order_number']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Order Date:</strong></td>
                                <td><?= date('M j, Y g:i A', strtotime($order['created_at'])) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Payment Method:</strong></td>
                                <td>
                                    <?php if (!empty($order['payment_method_name'])): ?>
                                        <?= esc($order['payment_method_name']) ?>
                                    <?php else: ?>
                                        <span class="text-muted">Not specified</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Payment Status:</strong></td>
                                <td>
                                    <?php
                                    $paymentStatus = $order['payment_status'] ?? 'pending';
                                    $badgeClass = match ($paymentStatus) {
                                        'paid' => 'success',
                                        'failed' => 'danger',
                                        'expired' => 'secondary',
                                        'pending' => 'warning',
                                        default => 'secondary'
                                    };
                                    $statusText = match ($paymentStatus) {
                                        'paid' => 'Paid',
                                        'failed' => 'Failed',
                                        'expired' => 'Expired',
                                        'pending' => 'Pending',
                                        default => ucfirst($paymentStatus)
                                    };
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?>">
                                        <?= $statusText ?>
                                    </span>
                                    <?php if ($order['payment_verified']): ?>
                                        <br><small class="text-success">
                                            <i class="fas fa-check-circle me-1"></i>Verified
                                            <?php if (!empty($order['payment_verified_at'])): ?>
                                                on <?= date('M j, Y', strtotime($order['payment_verified_at'])) ?>
                                            <?php endif; ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                            </tr>

                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Customer Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td><?= esc($order['first_name'] . ' ' . $order['last_name']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td><?= esc($order['email']) ?></td>
                            </tr>
                            <tr>
                                <td><strong>Phone:</strong></td>
                                <td><?= esc($order['phone'] ?? 'N/A') ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Screenshot -->
        <?php if (!empty($order['payment_screenshot'])): ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-camera me-2"></i>Payment Screenshot
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="text-center">
                                <img src="<?= base_url($order['payment_screenshot']) ?>"
                                     alt="Payment Screenshot"
                                     class="img-fluid rounded border shadow-sm"
                                     style="max-width: 100%; max-height: 400px; cursor: pointer;"
                                     onclick="openImageModal('<?= base_url($order['payment_screenshot']) ?>')">
                                <p class="text-muted mt-2 small">Click to view full size</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6>Payment Verification Status:</h6>
                            <?php if ($order['payment_verified']): ?>
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Payment Verified</strong>
                                    <br><small>
                                        Verified on <?= date('F j, Y \a\t g:i A', strtotime($order['payment_verified_at'])) ?>
                                    </small>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-clock me-2"></i>
                                    <strong>Verification Pending</strong>
                                    <br><small>Customer has uploaded payment proof. Please verify the payment.</small>
                                </div>

                                <!-- Verification Actions -->
                                <div class="d-grid gap-2">
                                    <button type="button"
                                            class="btn btn-success"
                                            onclick="verifyPayment(<?= $order['id'] ?>)">
                                        <i class="fas fa-check me-2"></i>Verify Payment
                                    </button>
                                    <button type="button"
                                            class="btn btn-danger"
                                            onclick="rejectPayment(<?= $order['id'] ?>)">
                                        <i class="fas fa-times me-2"></i>Reject Payment
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Order Items -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Order Items</h5>
            </div>
            <div class="card-body">
                <?php if (empty($orderItems)): ?>
                    <p class="text-muted text-center py-3">No items found for this order.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>SKU</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orderItems as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if (!empty($item['product_image'])): ?>
                                                    <img src="<?= base_url('uploads/products/' . $item['product_image']) ?>"
                                                        alt="<?= esc($item['product_name']) ?>"
                                                        class="me-3" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                                <?php else: ?>
                                                    <div class="me-3 bg-light d-flex align-items-center justify-content-center"
                                                        style="width: 50px; height: 50px; border-radius: 5px;">
                                                        <i class="fas fa-image text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <strong><?= esc($item['product_name']) ?></strong>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <small class="text-muted"><?= esc($item['product_sku'] ?? 'N/A') ?></small>
                                        </td>
                                        <td>$<?= number_format($item['price'], 2) ?></td>
                                        <td><?= $item['quantity'] ?></td>
                                        <td><strong>$<?= number_format($item['total'], 2) ?></strong></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Addresses -->
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Shipping Address</h6>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($order['shipping_address'])): ?>
                            <?php
                            // Try to decode as JSON first, if that fails, treat as plain text
                            $shippingAddress = json_decode($order['shipping_address'], true);
                            if (json_last_error() !== JSON_ERROR_NONE) {
                                // It's a formatted string, display as is
                            ?>
                                <address class="mb-0">
                                    <?= nl2br(esc($order['shipping_address'])) ?>
                                </address>
                            <?php
                            } else {
                                // It's JSON data, display structured
                            ?>
                                <address class="mb-0">
                                    <?php if (!empty($shippingAddress['full_name'])): ?>
                                        <?= esc($shippingAddress['full_name']) ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($shippingAddress['address_line1'])): ?>
                                        <?= esc($shippingAddress['address_line1']) ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($shippingAddress['address_line2'])): ?>
                                        <?= esc($shippingAddress['address_line2']) ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($shippingAddress['landmark'])): ?>
                                        Near <?= esc($shippingAddress['landmark']) ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($shippingAddress['city']) || !empty($shippingAddress['state']) || !empty($shippingAddress['pincode'])): ?>
                                        <?= esc($shippingAddress['city'] ?? '') ?><?= !empty($shippingAddress['city']) && (!empty($shippingAddress['state']) || !empty($shippingAddress['pincode'])) ? ', ' : '' ?><?= esc($shippingAddress['state'] ?? '') ?><?= !empty($shippingAddress['state']) && !empty($shippingAddress['pincode']) ? ' ' : '' ?><?= esc($shippingAddress['pincode'] ?? '') ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($shippingAddress['country']) && $shippingAddress['country'] !== 'India'): ?>
                                        <?= esc($shippingAddress['country']) ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($shippingAddress['phone'])): ?>
                                        Phone: <?= esc($shippingAddress['phone']) ?>
                                    <?php endif; ?>
                                </address>
                            <?php
                            }
                            ?>
                        <?php else: ?>
                            <p class="text-muted mb-0">No shipping address provided</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Billing Address</h6>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($order['billing_address'])): ?>
                            <?php
                            // Try to decode as JSON first, if that fails, treat as plain text
                            $billingAddress = json_decode($order['billing_address'], true);
                            if (json_last_error() !== JSON_ERROR_NONE) {
                                // It's a formatted string, display as is
                            ?>
                                <address class="mb-0">
                                    <?= nl2br(esc($order['billing_address'])) ?>
                                </address>
                            <?php
                            } else {
                                // It's JSON data, display structured
                            ?>
                                <address class="mb-0">
                                    <?php if (!empty($billingAddress['full_name'])): ?>
                                        <?= esc($billingAddress['full_name']) ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($billingAddress['address_line1'])): ?>
                                        <?= esc($billingAddress['address_line1']) ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($billingAddress['address_line2'])): ?>
                                        <?= esc($billingAddress['address_line2']) ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($billingAddress['landmark'])): ?>
                                        Near <?= esc($billingAddress['landmark']) ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($billingAddress['city']) || !empty($billingAddress['state']) || !empty($billingAddress['pincode'])): ?>
                                        <?= esc($billingAddress['city'] ?? '') ?><?= !empty($billingAddress['city']) && (!empty($billingAddress['state']) || !empty($billingAddress['pincode'])) ? ', ' : '' ?><?= esc($billingAddress['state'] ?? '') ?><?= !empty($billingAddress['state']) && !empty($billingAddress['pincode']) ? ' ' : '' ?><?= esc($billingAddress['pincode'] ?? '') ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($billingAddress['country']) && $billingAddress['country'] !== 'India'): ?>
                                        <?= esc($billingAddress['country']) ?><br>
                                    <?php endif; ?>
                                    <?php if (!empty($billingAddress['phone'])): ?>
                                        Phone: <?= esc($billingAddress['phone']) ?>
                                    <?php endif; ?>
                                </address>
                            <?php
                            }
                            ?>
                        <?php else: ?>
                            <p class="text-muted mb-0">Same as shipping address</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Summary & Actions -->
    <div class="col-lg-4">
        <!-- Order Summary -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Order Summary</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm table-borderless">
                    <tr>
                        <td>Subtotal:</td>
                        <td class="text-end">$<?= number_format(($order['total_amount'] - ($order['shipping_amount'] ?? 0) - ($order['tax_amount'] ?? 0) + ($order['discount_amount'] ?? 0)), 2) ?></td>
                    </tr>
                    <?php if (!empty($order['discount_amount']) && $order['discount_amount'] > 0): ?>
                        <tr>
                            <td>Discount:</td>
                            <td class="text-end text-success">-$<?= number_format($order['discount_amount'], 2) ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (!empty($order['tax_amount']) && $order['tax_amount'] > 0): ?>
                        <tr>
                            <td>Tax:</td>
                            <td class="text-end">$<?= number_format($order['tax_amount'], 2) ?></td>
                        </tr>
                    <?php endif; ?>
                    <?php if (!empty($order['shipping_amount']) && $order['shipping_amount'] > 0): ?>
                        <tr>
                            <td>Shipping:</td>
                            <td class="text-end">$<?= number_format($order['shipping_amount'], 2) ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr class="border-top">
                        <td><strong>Total:</strong></td>
                        <td class="text-end"><strong>$<?= number_format($order['total_amount'], 2) ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Order Actions -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Order Actions</h6>
            </div>
            <div class="card-body">
                <!-- Order Status Update -->
                <form method="POST" action="<?= base_url('admin/orders/' . $order['id'] . '/status') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="status" class="form-label">Update Order Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                            <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                            <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="fas fa-save me-1"></i>Update Order Status
                    </button>
                </form>

                <!-- Payment Status Update -->
                <form method="POST" action="<?= base_url('admin/orders/' . $order['id'] . '/payment-status') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label for="payment_status" class="form-label">
                            <i class="fas fa-credit-card me-1"></i>Update Payment Status
                        </label>
                        <select class="form-select" id="payment_status" name="payment_status">
                            <option value="pending" <?= ($order['payment_status'] ?? 'pending') === 'pending' ? 'selected' : '' ?>>
                                Pending - Payment not received
                            </option>
                            <option value="paid" <?= ($order['payment_status'] ?? '') === 'paid' ? 'selected' : '' ?>>
                                Paid - Payment confirmed
                            </option>
                            <option value="failed" <?= ($order['payment_status'] ?? '') === 'failed' ? 'selected' : '' ?>>
                                Failed - Payment failed
                            </option>
                            <option value="expired" <?= ($order['payment_status'] ?? '') === 'expired' ? 'selected' : '' ?>>
                                Expired - Payment window expired
                            </option>
                        </select>
                        <div class="form-text">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Update payment status when cryptocurrency payment is confirmed
                            </small>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success w-100 mb-3">
                        <i class="fas fa-money-bill-wave me-1"></i>Update Payment Status
                    </button>
                </form>

                <?php if (!empty($order['notes'])): ?>
                    <div class="mt-3 border-top pt-3">
                        <h6><i class="fas fa-sticky-note me-1"></i>Order Notes</h6>
                        <p class="text-muted small"><?= esc($order['notes']) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Payment Screenshot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Payment Screenshot" class="img-fluid">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a id="downloadLink" href="" download class="btn btn-primary">
                    <i class="fas fa-download me-2"></i>Download
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function openImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('downloadLink').href = imageSrc;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

function verifyPayment(orderId) {
    if (confirm('Are you sure you want to verify this payment? This will mark the order as paid.')) {
        fetch('<?= base_url('admin/orders/') ?>' + orderId + '/verify-payment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: '<?= csrf_token() ?>=<?= csrf_hash() ?>'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Payment verified successfully!');
                location.reload();
            } else {
                alert('Failed to verify payment: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
    }
}

function rejectPayment(orderId) {
    if (confirm('Are you sure you want to reject this payment? This will mark the payment as failed.')) {
        fetch('<?= base_url('admin/orders/') ?>' + orderId + '/reject-payment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: '<?= csrf_token() ?>=<?= csrf_hash() ?>'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Payment rejected successfully!');
                location.reload();
            } else {
                alert('Failed to reject payment: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
        });
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