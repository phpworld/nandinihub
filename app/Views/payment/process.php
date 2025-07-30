<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-credit-card"></i> Processing Payment</h4>
                </div>
                <div class="card-body text-center">
                    <div class="mb-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <h5 class="mt-3">Redirecting to Payment Gateway...</h5>
                        <p class="text-muted">Please wait while we redirect you to the secure payment page.</p>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Order Details</h6>
                                    <p class="mb-1"><strong>Order Number:</strong> <?= esc($order['order_number']) ?></p>
                                    <p class="mb-1"><strong>Amount:</strong> $<?= number_format($transaction['amount'], 2) ?></p>
                                    <p class="mb-0"><strong>Transaction ID:</strong> <?= esc($transaction['transaction_id']) ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">Payment Information</h6>
                                    <p class="mb-1"><strong>Gateway:</strong> HDFC Bank</p>
                                    <p class="mb-1"><strong>Currency:</strong> USD</p>
                                    <p class="mb-0"><strong>Status:</strong> <span class="badge bg-warning">Processing</span></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Important:</strong> Do not close this window or press the back button during payment processing.
                        </div>
                    </div>

                    <?php if (isset($paymentRequest['success']) && $paymentRequest['success']): ?>
                        <!-- HDFC SmartGateway Payment Options -->
                        <div class="mt-4">
                            <?php if (!empty($paymentRequest['payment_page_url'])): ?>
                                <!-- Direct payment page URL -->
                                <div class="text-center">
                                    <a href="<?= esc($paymentRequest['payment_page_url']) ?>" class="btn btn-primary btn-lg">
                                        <i class="fas fa-credit-card"></i> Proceed to Payment Gateway
                                    </a>
                                </div>
                            <?php elseif (isset($paymentRequest['payment_links']) && !empty($paymentRequest['payment_links'])): ?>
                                <!-- Multiple payment method links -->
                                <h6>Choose Payment Method:</h6>
                                <div class="row">
                                    <?php foreach ($paymentRequest['payment_links'] as $method => $link): ?>
                                        <div class="col-md-6 mb-2">
                                            <a href="<?= esc($link) ?>" class="btn btn-outline-primary w-100">
                                                <i class="fas fa-credit-card"></i> Pay with <?= ucfirst($method) ?>
                                            </a>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <!-- Fallback to gateway URL -->
                                <div class="text-center">
                                    <a href="<?= esc($paymentRequest['gateway_url']) ?>" class="btn btn-primary btn-lg">
                                        <i class="fas fa-credit-card"></i> Proceed to Payment Gateway
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <!-- Error handling -->
                        <div class="alert alert-danger mt-4">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Error:</strong> Unable to initialize payment.
                            <?= isset($paymentRequest['error']) ? esc($paymentRequest['error']) : 'Please try again.' ?>
                        </div>

                        <!-- Debug information in test mode -->
                        <?php if (ENVIRONMENT === 'development' && isset($paymentRequest['raw_response'])): ?>
                            <div class="alert alert-info mt-3">
                                <strong>Debug Info:</strong>
                                <pre><?= esc(json_encode($paymentRequest['raw_response'], JSON_PRETTY_PRINT)) ?></pre>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <div class="mt-3">
                        <a href="<?= base_url('orders/' . $order['order_number']) ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Order
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Add click tracking for payment buttons
document.addEventListener('DOMContentLoaded', function() {
    const paymentButtons = document.querySelectorAll('a[href*="smartgateway"], a.btn-primary[href], a.btn-outline-primary[href]');

    paymentButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            // Show loading state
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Redirecting to Payment...';
            this.classList.add('disabled');

            // Restore button if redirect fails
            setTimeout(function() {
                button.innerHTML = originalText;
                button.classList.remove('disabled');
            }, 10000);
        });
    });
});

// Auto-redirect if payment page URL is available
<?php if (isset($paymentRequest['success']) && $paymentRequest['success'] &&
          !empty($paymentRequest['payment_page_url'])): ?>
setTimeout(function() {
    const paymentButton = document.querySelector('a.btn-primary[href]');
    if (paymentButton) {
        paymentButton.click();
    }
}, 3000);
<?php endif; ?>
</script>

<?= $this->endSection() ?>
