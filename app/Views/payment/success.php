<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-success">
                <div class="card-header bg-success text-white text-center">
                    <h3 class="mb-0">
                        <i class="fas fa-check-circle fa-2x mb-2"></i><br>
                        Payment Successful!
                    </h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h5 class="text-success">Thank you for your payment!</h5>
                        <p class="text-muted">Your order has been confirmed and will be processed shortly.</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-success">
                                        <i class="fas fa-shopping-bag"></i> Order Information
                                    </h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>Order Number:</strong></td>
                                            <td><?= esc($order['order_number']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Order Date:</strong></td>
                                            <td><?= date('d M Y, h:i A', strtotime($order['created_at'])) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Total Amount:</strong></td>
                                            <td><strong>$<?= number_format($order['total_amount'], 2) ?></strong></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Payment Status:</strong></td>
                                            <td><span class="badge bg-success">Paid</span></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-primary">
                                        <i class="fas fa-credit-card"></i> Payment Details
                                    </h6>
                                    <table class="table table-sm table-borderless">
                                        <tr>
                                            <td><strong>Transaction ID:</strong></td>
                                            <td><?= esc($transaction['transaction_id']) ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Gateway Ref:</strong></td>
                                            <td><?= esc($transaction['gateway_transaction_id'] ?? 'N/A') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Payment Method:</strong></td>
                                            <td><?= esc($transaction['payment_method'] ?? 'Online Payment') ?></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Bank Ref No:</strong></td>
                                            <td><?= esc($transaction['bank_ref_no'] ?? 'N/A') ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-success mt-4">
                        <h6><i class="fas fa-info-circle"></i> What's Next?</h6>
                        <ul class="mb-0">
                            <li>You will receive an order confirmation email shortly</li>
                            <li>Your order will be processed and shipped within 1-2 business days</li>
                            <li>You can track your order status in the "My Orders" section</li>
                            <li>For any queries, contact our customer support</li>
                        </ul>
                    </div>

                    <div class="text-center mt-4">
                        <a href="<?= base_url('orders/' . $order['order_number']) ?>" class="btn btn-primary">
                            <i class="fas fa-eye"></i> View Order Details
                        </a>
                        <a href="<?= base_url('orders') ?>" class="btn btn-outline-primary ms-2">
                            <i class="fas fa-list"></i> My Orders
                        </a>
                        <a href="<?= base_url('/') ?>" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-home"></i> Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Auto-redirect to order details after 10 seconds
setTimeout(function() {
    if (confirm('Would you like to view your order details now?')) {
        window.location.href = '<?= base_url('orders/' . $order['order_number']) ?>';
    }
}, 10000);
</script>

<?= $this->endSection() ?>
