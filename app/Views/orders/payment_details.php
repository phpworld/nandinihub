<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-credit-card me-2"></i>Payment Required
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle me-2"></i>Order Created Successfully!</h5>
                        <p class="mb-0">Your order <strong>#<?= esc($order['order_number']) ?></strong> has been created. Please complete the payment to confirm your order.</p>
                    </div>

                    <?php if (!empty($order['payment_method_name'])): ?>
                        <div class="row">
                            <div class="col-md-8">
                                <h5><i class="fas fa-wallet me-2"></i><?= esc($order['payment_method_name']) ?></h5>
                                
                                <?php if (!empty($order['payment_information'])): ?>
                                    <div class="alert alert-warning">
                                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Payment Instructions:</h6>
                                        <p class="mb-0"><?= nl2br(esc($order['payment_information'])) ?></p>
                                    </div>
                                <?php endif; ?>

                                <div class="mb-3">
                                    <label class="form-label"><strong>Wallet Address:</strong></label>
                                    <div class="input-group">
                                        <input type="text" 
                                               id="wallet_address" 
                                               class="form-control" 
                                               value="<?= esc($order['wallet_address']) ?>" 
                                               readonly>
                                        <button class="btn btn-outline-secondary" 
                                                type="button" 
                                                onclick="copyWalletAddress()">
                                            <i class="fas fa-copy"></i> Copy
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><strong>Amount to Pay:</strong></label>
                                    <div class="h4 text-success">$<?= number_format($order['total_amount'], 2) ?></div>
                                </div>
                            </div>

                            <?php if (!empty($order['qr_code'])): ?>
                                <div class="col-md-4 text-center">
                                    <label class="form-label"><strong>QR Code:</strong></label>
                                    <div class="border rounded p-3 bg-light">
                                        <img src="<?= base_url($order['qr_code']) ?>" 
                                             alt="Payment QR Code" 
                                             class="img-fluid" 
                                             style="max-width: 200px;">
                                    </div>
                                    <small class="text-muted">Scan with your crypto wallet</small>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Payment Screenshot Upload -->
                        <div class="card mt-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">
                                    <i class="fas fa-camera me-2"></i>Upload Payment Proof
                                </h6>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($order['payment_screenshot'])): ?>
                                    <div class="alert alert-success">
                                        <h6><i class="fas fa-check-circle me-2"></i>Payment Screenshot Uploaded</h6>
                                        <p class="mb-2">You have already uploaded a payment screenshot. Our team will verify it shortly.</p>
                                        <div class="text-center">
                                            <img src="<?= base_url($order['payment_screenshot']) ?>"
                                                 alt="Payment Screenshot"
                                                 class="img-fluid rounded border"
                                                 style="max-width: 300px; max-height: 200px;">
                                        </div>
                                        <?php if ($order['payment_verified']): ?>
                                            <div class="alert alert-success mt-3 mb-0">
                                                <i class="fas fa-check-double me-2"></i>
                                                <strong>Payment Verified!</strong> Your payment has been confirmed by our team.
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-warning mt-3 mb-0">
                                                <i class="fas fa-clock me-2"></i>
                                                <strong>Verification Pending</strong> - Our team is reviewing your payment proof.
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <form id="screenshotUploadForm" enctype="multipart/form-data">
                                        <div class="mb-3">
                                            <label for="payment_screenshot" class="form-label">
                                                <strong>Upload Payment Screenshot</strong>
                                            </label>
                                            <input type="file"
                                                   class="form-control"
                                                   id="payment_screenshot"
                                                   name="payment_screenshot"
                                                   accept="image/*"
                                                   required>
                                            <div class="form-text">
                                                Upload a screenshot of your payment transaction. Accepted formats: JPG, PNG, GIF (Max 5MB)
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       id="confirm_payment"
                                                       required>
                                                <label class="form-check-label" for="confirm_payment">
                                                    I confirm that I have completed the payment and uploaded a valid screenshot
                                                </label>
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-success w-100" id="uploadBtn">
                                            <i class="fas fa-upload me-2"></i>Upload Payment Proof
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="alert alert-success mt-4">
                            <h6><i class="fas fa-check-circle me-2"></i>After Payment:</h6>
                            <ul class="mb-0">
                                <li>Complete your cryptocurrency payment</li>
                                <li>Upload a screenshot of the transaction</li>
                                <li>Our team will verify your payment within 24 hours</li>
                                <li>You will receive confirmation and your order will be processed</li>
                            </ul>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <a href="<?= base_url('orders/' . $order['order_number']) ?>" class="btn btn-primary">
                                <i class="fas fa-eye me-2"></i>View Order Details
                            </a>
                            <a href="<?= base_url('orders') ?>" class="btn btn-secondary">
                                <i class="fas fa-list me-2"></i>My Orders
                            </a>
                            <a href="<?= base_url() ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-home me-2"></i>Continue Shopping
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>Payment Method Not Available</h6>
                            <p class="mb-0">The selected payment method is no longer available. Please contact support.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Order Number:</strong> #<?= esc($order['order_number']) ?></p>
                            <p><strong>Order Date:</strong> <?= date('F j, Y \a\t g:i A', strtotime($order['created_at'])) ?></p>
                            <p><strong>Payment Status:</strong> 
                                <span class="badge bg-warning">Pending Payment</span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Subtotal:</strong> $<?= number_format($order['subtotal_amount'], 2) ?></p>
                            <p><strong>Shipping:</strong> $<?= number_format($order['shipping_amount'], 2) ?></p>
                            <p><strong>Total:</strong> <span class="h5 text-success">$<?= number_format($order['total_amount'], 2) ?></span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyWalletAddress() {
    const walletInput = document.getElementById('wallet_address');
    walletInput.select();
    walletInput.setSelectionRange(0, 99999);

    try {
        document.execCommand('copy');
        const button = event.target.closest('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> Copied!';
        button.classList.remove('btn-outline-secondary');
        button.classList.add('btn-success');

        setTimeout(() => {
            button.innerHTML = originalHTML;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-secondary');
        }, 2000);
    } catch (err) {
        alert('Failed to copy wallet address. Please copy manually.');
    }
}

// Handle screenshot upload
document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.getElementById('screenshotUploadForm');
    if (uploadForm) {
        uploadForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData();
            const fileInput = document.getElementById('payment_screenshot');
            const uploadBtn = document.getElementById('uploadBtn');

            if (!fileInput.files[0]) {
                alert('Please select a screenshot to upload.');
                return;
            }

            // Validate file size (5MB limit)
            if (fileInput.files[0].size > 5 * 1024 * 1024) {
                alert('File size must be less than 5MB.');
                return;
            }

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(fileInput.files[0].type)) {
                alert('Please upload a valid image file (JPG, PNG, GIF).');
                return;
            }

            formData.append('payment_screenshot', fileInput.files[0]);
            formData.append('order_number', '<?= esc($order['order_number']) ?>');

            // Show loading state
            const originalText = uploadBtn.innerHTML;
            uploadBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';
            uploadBtn.disabled = true;

            fetch('<?= base_url('orders/upload-payment-screenshot') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Payment screenshot uploaded successfully! Our team will verify it shortly.');
                    location.reload();
                } else {
                    alert('Upload failed: ' + (data.message || 'Unknown error'));
                    uploadBtn.innerHTML = originalText;
                    uploadBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Upload failed. Please try again.');
                uploadBtn.innerHTML = originalText;
                uploadBtn.disabled = false;
            });
        });
    }
});
</script>

<style>
.card-header.bg-primary {
    background-color: #0d6efd !important;
}

.alert h6 {
    margin-bottom: 0.5rem;
}

.alert ul {
    padding-left: 1.2rem;
}

.border.rounded {
    border: 2px solid #dee2e6 !important;
}

.btn-group .btn {
    margin-right: 0.5rem;
}
</style>
<?= $this->endSection() ?>
