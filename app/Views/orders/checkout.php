<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('cart') ?>">Cart</a></li>
                    <li class="breadcrumb-item active">Checkout</li>
                </ol>
            </nav>

            <h1 class="h2 mb-4">Checkout</h1>

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

            <!-- Validation Errors -->
            <?php if (isset($errors) && !empty($errors)): ?>
                <div class="alert alert-danger">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Please fix the following errors:</h6>
                    <ul class="mb-0">
                        <?php foreach ($errors as $field => $error): ?>
                            <li><strong><?= ucfirst(str_replace('_', ' ', $field)) ?>:</strong> <?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <form action="<?= base_url('checkout') ?>" method="POST" id="checkoutForm">
        <?= csrf_field() ?>

        <div class="row">
            <!-- Checkout Form -->
            <div class="col-lg-8">
                <!-- Delivery Address -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Delivery Address</h5>
                        <a href="<?= base_url('addresses/add') ?>" class="btn btn-outline-primary btn-sm" target="_blank">
                            <i class="fas fa-plus me-1"></i>Add New Address
                        </a>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($userAddresses)): ?>
                            <?php foreach ($userAddresses as $index => $address): ?>
                                <div class="form-check mb-3">
                                    <input class="form-check-input delivery-address"
                                        type="radio"
                                        name="delivery_address_id"
                                        id="address_<?= $address['id'] ?>"
                                        value="<?= $address['id'] ?>"
                                        <?= ($address['is_default'] || $index === 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label w-100" for="address_<?= $address['id'] ?>">
                                        <div class="border rounded p-3">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h6 class="mb-1">
                                                        <?= esc($address['name']) ?>
                                                        <?php if ($address['is_default']): ?>
                                                            <span class="badge bg-success ms-2">Default</span>
                                                        <?php endif; ?>
                                                    </h6>
                                                    <p class="mb-1 text-muted"><?= esc($address['phone']) ?></p>
                                                    <p class="mb-0"><?= esc($address['full_address']) ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                No delivery addresses found. Please <a href="<?= base_url('addresses/add') ?>" target="_blank">add a delivery address</a> first.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Billing Address -->
                <div class="card mb-4">
                    <div class="card-header">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="same_as_shipping" checked>
                            <label class="form-check-label" for="same_as_shipping">
                                <h5 class="mb-0">Billing address same as shipping</h5>
                            </label>
                        </div>
                    </div>
                    <div class="card-body" id="billing_address_section" style="display: none;">
                        <div class="mb-3">
                            <label for="billing_address_text" class="form-label">Billing Address</label>
                            <textarea class="form-control" id="billing_address_text" rows="3"><?= old('billing_address') ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Shipping Method -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Shipping Method</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($shippingMethods)): ?>
                            <?php foreach ($shippingMethods as $index => $method): ?>
                                <div class="form-check mb-3">
                                    <input class="form-check-input shipping-method"
                                        type="radio"
                                        name="shipping_method_id"
                                        id="shipping_<?= $method['id'] ?>"
                                        value="<?= $method['id'] ?>"
                                        data-cost="<?= $method['cost'] ?>"
                                        <?= $index === 0 ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="shipping_<?= $method['id'] ?>">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong><?= esc($method['name']) ?></strong>
                                                <small class="d-block text-muted"><?= esc($method['delivery_time']) ?></small>
                                                <?php if (!empty($method['description'])): ?>
                                                    <small class="d-block text-muted"><?= esc($method['description']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge <?= $method['is_free'] ? 'bg-success' : 'bg-primary' ?>">
                                                    <?= $method['cost_formatted'] ?>
                                                </span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                No shipping methods available. Please contact support.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>



                <!-- Payment Method -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Payment Method</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($paymentMethods)): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                No payment methods are currently available. Please contact support.
                            </div>
                        <?php else: ?>
                            <?php foreach ($paymentMethods as $index => $method): ?>
                                <div class="form-check mb-3">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="payment_method_id"
                                           id="payment_<?= $method['id'] ?>"
                                           value="<?= $method['id'] ?>"
                                           <?= $index === 0 ? 'checked' : '' ?>
                                           data-wallet="<?= esc($method['wallet_address']) ?>"
                                           data-qr="<?= esc($method['qr_code']) ?>"
                                           data-info="<?= esc($method['payment_information']) ?>">
                                    <label class="form-check-label w-100" for="payment_<?= $method['id'] ?>">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong><?= esc($method['name']) ?></strong>
                                                <?php if (!empty($method['payment_information'])): ?>
                                                    <small class="d-block text-muted">
                                                        <?= esc(substr($method['payment_information'], 0, 100)) ?>
                                                        <?= strlen($method['payment_information']) > 100 ? '...' : '' ?>
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($method['qr_code'])): ?>
                                                <img src="<?= base_url($method['qr_code']) ?>"
                                                     alt="QR Code"
                                                     class="img-thumbnail"
                                                     style="width: 40px; height: 40px;">
                                            <?php endif; ?>
                                        </div>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Order Notes -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Order Notes (Optional)</h5>
                    </div>
                    <div class="card-body">
                        <textarea class="form-control" name="notes" rows="3" placeholder="Any special instructions for your order..."><?= old('notes') ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <!-- Cart Items -->
                        <div class="mb-3">
                            <?php foreach ($cartItems as $item): ?>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0"><?= esc($item['name']) ?></h6>
                                        <small class="text-muted">Qty: <?= $item['quantity'] ?></small>
                                    </div>
                                    <div>
                                        <?php $price = $item['final_price'] ?? ($item['sale_price'] ?? $item['price']); ?>
                                        <span>$<?= number_format($price * $item['quantity'], 2) ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <hr>

                        <!-- Coupon Section -->
                        <div class="mb-3">
                            <h6 class="mb-3">Have a Coupon Code?</h6>
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" id="coupon_code" placeholder="Enter coupon code" maxlength="50">
                                <button type="button" class="btn btn-outline-primary" id="apply_coupon">Apply</button>
                            </div>
                            <div id="coupon_message" class="small"></div>

                            <!-- Applied Coupon Display -->
                            <div id="applied_coupon_display" class="alert alert-success mt-2" style="display: none;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="fas fa-tag me-2"></i>
                                        <span id="applied_coupon_text"></span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-danger" id="remove_coupon">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Totals -->
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="subtotal_amount">$<?= number_format($cartTotal, 2) ?></span>
                        </div>

                        <div id="discount_row" class="d-flex justify-content-between mb-2 text-success" style="display: none;">
                            <span>Discount:</span>
                            <span id="discount_amount">-$0.00</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span id="shipping_amount" class="<?php
                                $defaultShippingCost = 0;
                                if (!empty($shippingMethods)) {
                                    $defaultShippingCost = $shippingMethods[0]['cost'];
                                }
                                echo $defaultShippingCost > 0 ? 'text-primary' : 'text-success';
                                ?>">
                                <?php echo $defaultShippingCost > 0 ? '$' . number_format($defaultShippingCost, 2) : 'Free'; ?>
                            </span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax (18% GST):</span>
                            <span id="tax_amount">$<?= number_format($cartTotal * 0.18, 2) ?></span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong class="text-primary" id="final_total">
                                <?php
                                $defaultShippingCost = 0;
                                if (!empty($shippingMethods)) {
                                    $defaultShippingCost = $shippingMethods[0]['cost'];
                                }
                                $finalTotal = $cartTotal + $defaultShippingCost + ($cartTotal * 0.18);
                                echo '$' . number_format($finalTotal, 2);
                                ?>
                            </strong>
                        </div>

                        <!-- Hidden field for billing address -->
                        <input type="hidden" name="billing_address" id="billing_address" value="">

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-shopping-bag"></i> Place Order
                            </button>
                            <a href="<?= base_url('cart') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Cart
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Security Features -->
                <div class="card mt-3">
                    <div class="card-body">
                        <h6 class="card-title">Secure Checkout</h6>
                        <div class="row text-center">
                            <div class="col-4">
                                <i class="fas fa-shield-alt text-success mb-2"></i>
                                <small class="d-block">SSL Secure</small>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-lock text-success mb-2"></i>
                                <small class="d-block">Safe Payment</small>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-truck text-success mb-2"></i>
                                <small class="d-block">Fast Delivery</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Coupon functionality
    let originalSubtotal = <?= $cartTotal ?>;
    let currentDiscount = 0;
    let appliedCoupon = null;

    // Check if coupon is already applied from session
    <?php if (session()->get('applied_coupon')): ?>
        appliedCoupon = <?= json_encode(session()->get('applied_coupon')) ?>;
        if (appliedCoupon) {
            showAppliedCoupon(appliedCoupon);
            updateTotals(appliedCoupon.discount_amount);
        }
    <?php endif; ?>

    // Apply coupon
    document.getElementById('apply_coupon').addEventListener('click', function() {
        const code = document.getElementById('coupon_code').value.trim();

        if (!code) {
            showCouponMessage('Please enter a coupon code', 'danger');
            return;
        }

        const button = this;
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Applying...';
        button.disabled = true;

        fetch('<?= base_url('coupon/apply') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: 'code=' + encodeURIComponent(code)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    appliedCoupon = {
                        code: code,
                        discount_amount: data.discount_amount,
                        coupon_data: data.coupon
                    };
                    showAppliedCoupon(appliedCoupon);
                    updateTotals(data.discount_amount);
                    showCouponMessage(data.message, 'success');
                    document.getElementById('coupon_code').value = '';
                } else {
                    showCouponMessage(data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showCouponMessage('An error occurred. Please try again.', 'danger');
            })
            .finally(() => {
                button.innerHTML = originalText;
                button.disabled = false;
            });
    });

    // Remove coupon
    document.getElementById('remove_coupon').addEventListener('click', function() {
        fetch('<?= base_url('coupon/remove') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    hideAppliedCoupon();
                    updateTotals(0);
                    showCouponMessage(data.message, 'success');
                    appliedCoupon = null;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showCouponMessage('An error occurred. Please try again.', 'danger');
            });
    });

    // Enter key support for coupon input
    document.getElementById('coupon_code').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('apply_coupon').click();
        }
    });

    function showAppliedCoupon(coupon) {
        const display = document.getElementById('applied_coupon_display');
        const text = document.getElementById('applied_coupon_text');

        let couponText = `${coupon.code} - $${parseFloat(coupon.discount_amount).toFixed(2)} off`;
        if (coupon.coupon_data && coupon.coupon_data.type === 'percentage') {
            couponText = `${coupon.code} - ${coupon.coupon_data.value}% off ($${parseFloat(coupon.discount_amount).toFixed(2)})`;
        }

        text.textContent = couponText;
        display.style.display = 'block';
    }

    function hideAppliedCoupon() {
        document.getElementById('applied_coupon_display').style.display = 'none';
    }

    // SIMPLIFIED WORKING VERSION - Based on test_shipping.html
    function updateTotalsSimple() {
        const subtotalAfterDiscount = originalSubtotal - currentDiscount;

        // Update discount row
        const discountRow = document.getElementById('discount_row');
        const discountAmountSpan = document.getElementById('discount_amount');

        if (currentDiscount > 0) {
            discountRow.style.display = 'flex';
            discountAmountSpan.textContent = `-$${currentDiscount.toFixed(2)}`;
        } else {
            discountRow.style.display = 'none';
        }

        // Calculate shipping based on selected method - EXACT SAME LOGIC AS TEST FILE
        const selectedShippingMethod = document.querySelector('input[name="shipping_method_id"]:checked');
        const shipping = selectedShippingMethod ? parseFloat(selectedShippingMethod.dataset.cost) : 0;
        const shippingSpan = document.getElementById('shipping_amount');



        // Update shipping display - EXACT SAME LOGIC AS TEST FILE
        if (shipping === 0) {
            shippingSpan.textContent = 'Free';
            shippingSpan.className = 'text-success';
        } else {
            shippingSpan.textContent = `$${shipping.toFixed(2)}`;
            shippingSpan.className = 'text-primary';
        }

        // Calculate tax on discounted amount
        const tax = subtotalAfterDiscount * 0.18;
        document.getElementById('tax_amount').textContent = `$${tax.toFixed(2)}`;

        // Calculate final total
        const finalTotal = subtotalAfterDiscount + shipping + tax;
        document.getElementById('final_total').textContent = `$${finalTotal.toFixed(2)}`;
    }

    // Keep original function for coupon compatibility
    function updateTotals(discountAmount) {
        currentDiscount = parseFloat(discountAmount) || 0;
        updateTotalsSimple();
    }

    function showCouponMessage(message, type) {
        const messageDiv = document.getElementById('coupon_message');
        messageDiv.className = `small text-${type}`;
        messageDiv.textContent = message;

        // Clear message after 5 seconds
        setTimeout(() => {
            messageDiv.textContent = '';
            messageDiv.className = 'small';
        }, 5000);
    }

    // Handle billing address toggle
    document.getElementById('same_as_shipping').addEventListener('change', function() {
        const billingSection = document.getElementById('billing_address_section');
        const billingAddressField = document.getElementById('billing_address');

        if (this.checked) {
            billingSection.style.display = 'none';
            // Get selected delivery address text
            const selectedAddress = document.querySelector('input[name="delivery_address_id"]:checked');
            if (selectedAddress) {
                const addressLabel = selectedAddress.nextElementSibling.textContent.trim();
                billingAddressField.value = addressLabel;
            }
        } else {
            billingSection.style.display = 'block';
            billingAddressField.value = '';
        }
    });

    // Update billing address when delivery address changes
    document.querySelectorAll('input[name="delivery_address_id"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const sameAsShipping = document.getElementById('same_as_shipping');
            const billingAddressField = document.getElementById('billing_address');

            if (sameAsShipping.checked) {
                const addressLabel = this.nextElementSibling.textContent.trim();
                billingAddressField.value = addressLabel;
            }
        });
    });

    // Handle billing address textarea
    document.getElementById('billing_address_text').addEventListener('input', function() {
        const billingAddressField = document.getElementById('billing_address');
        billingAddressField.value = this.value;
    });

    // Handle shipping method selection
    document.querySelectorAll('input[name="shipping_method_id"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            updateTotalsSimple();
        });
    });

    // Handle payment method selection
    document.querySelectorAll('input[name="payment_method_id"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            updatePaymentDetails();
        });
    });

    // Initialize everything on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateTotalsSimple();
        updatePaymentDetails();

        // Set initial billing address
        const sameAsShipping = document.getElementById('same_as_shipping');
        const billingAddressField = document.getElementById('billing_address');
        if (sameAsShipping && sameAsShipping.checked) {
            const selectedAddress = document.querySelector('input[name="delivery_address_id"]:checked');
            if (selectedAddress && billingAddressField) {
                const addressLabel = selectedAddress.nextElementSibling.textContent.trim();
                billingAddressField.value = addressLabel;
            }
        }
    });

    function updatePaymentDetails() {
        const selectedPayment = document.querySelector('input[name="payment_method_id"]:checked');
        const paymentDetails = document.getElementById('payment_details');

        if (selectedPayment) {
            const walletAddress = selectedPayment.getAttribute('data-wallet');
            const qrCode = selectedPayment.getAttribute('data-qr');
            const paymentInfo = selectedPayment.getAttribute('data-info');

            if (walletAddress) {
                document.getElementById('wallet_address_display').value = walletAddress;
                document.getElementById('payment_info_text').innerHTML = paymentInfo || 'Send payment to the wallet address below.';

                const qrDisplay = document.getElementById('qr_code_display');
                if (qrCode) {
                    qrDisplay.innerHTML = `<img src="<?= base_url() ?>${qrCode}" alt="QR Code" class="img-fluid" style="max-width: 120px;">`;
                } else {
                    qrDisplay.innerHTML = '<span class="text-muted">No QR code available</span>';
                }

                paymentDetails.style.display = 'block';
            } else {
                paymentDetails.style.display = 'none';
            }
        }
    }

    function copyWalletAddress() {
        const walletInput = document.getElementById('wallet_address_display');
        walletInput.select();
        walletInput.setSelectionRange(0, 99999);

        try {
            document.execCommand('copy');
            const button = event.target.closest('button');
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-check"></i>';
            button.classList.remove('btn-outline-secondary');
            button.classList.add('btn-success');

            setTimeout(() => {
                button.innerHTML = originalHTML;
                button.classList.remove('btn-success');
                button.classList.add('btn-outline-secondary');
            }, 2000);
        } catch (err) {
            alert('Failed to copy wallet address');
        }
    }

    // Form validation before submission
    document.getElementById('checkoutForm').addEventListener('submit', function(e) {
        const deliveryAddress = document.querySelector('input[name="delivery_address_id"]:checked');
        const shippingMethod = document.querySelector('input[name="shipping_method_id"]:checked');
        const paymentMethod = document.querySelector('input[name="payment_method_id"]:checked');

        if (!deliveryAddress) {
            e.preventDefault();
            alert('Please select a delivery address');
            return false;
        }

        if (!shippingMethod) {
            e.preventDefault();
            alert('Please select a shipping method');
            return false;
        }

        if (!paymentMethod) {
            e.preventDefault();
            alert('Please select a payment method');
            return false;
        }

        // Show loading state
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing Order...';
        submitBtn.disabled = true;

        // Re-enable button after 10 seconds (in case of error)
        setTimeout(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }, 10000);
    });
</script>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .delivery-address:checked+label .border {
        border-color: #007bff !important;
        background-color: #f8f9fa;
    }

    .delivery-address+label {
        cursor: pointer;
    }

    .delivery-address+label .border {
        transition: all 0.2s ease;
    }

    .delivery-address+label:hover .border {
        border-color: #007bff !important;
        background-color: #f8f9fa;
    }

    .shipping-method:checked+label {
        background-color: #f8f9fa;
        border-color: #007bff;
    }

    .shipping-method+label {
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .shipping-method+label:hover {
        background-color: #f8f9fa;
    }
</style>
<?= $this->endSection() ?>