<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coupon System Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h4><i class="fas fa-tag"></i> Coupon System Test</h4>
                    </div>
                    <div class="card-body">
                        <!-- Test Cart -->
                        <div class="mb-4">
                            <h5>Test Cart</h5>
                            <div class="alert alert-info">
                                <strong>Cart Total:</strong> $<span id="cart_total">250.00</span>
                            </div>
                        </div>

                        <!-- Coupon Input -->
                        <div class="mb-4">
                            <h5>Apply Coupon</h5>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="coupon_code" placeholder="Enter coupon code">
                                <button class="btn btn-primary" type="button" id="apply_coupon">Apply</button>
                            </div>
                            <div id="coupon_message"></div>
                        </div>

                        <!-- Applied Coupon Display -->
                        <div id="applied_coupon" class="alert alert-success" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center">
                                <span id="coupon_details"></span>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="remove_coupon">
                                    <i class="fas fa-times"></i> Remove
                                </button>
                            </div>
                        </div>

                        <!-- Order Summary -->
                        <div class="card">
                            <div class="card-header">
                                <h6>Order Summary</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal:</span>
                                    <span id="subtotal">$250.00</span>
                                </div>
                                <div id="discount_row" class="d-flex justify-content-between mb-2 text-success" style="display: none;">
                                    <span>Discount:</span>
                                    <span id="discount_amount">-$0.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Shipping:</span>
                                    <span id="shipping">$50.00</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tax (18%):</span>
                                    <span id="tax">$45.00</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <strong>Total:</strong>
                                    <strong id="final_total">$345.00</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Available Coupons -->
                        <div class="mt-4">
                            <h5>Available Test Coupons</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-2">
                                        <div class="card-body p-2">
                                            <small><strong>WELCOME10</strong> - 10% off (min $100)</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card mb-2">
                                        <div class="card-body p-2">
                                            <small><strong>SAVE50</strong> - $50 off (min $200)</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card mb-2">
                                        <div class="card-body p-2">
                                            <small><strong>FREESHIP</strong> - Free shipping</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card mb-2">
                                        <div class="card-body p-2">
                                            <small><strong>MEGA20</strong> - 20% off (min $500)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let originalTotal = 250.00;
        let currentDiscount = 0;

        // Apply coupon
        document.getElementById('apply_coupon').addEventListener('click', function() {
            const code = document.getElementById('coupon_code').value.trim();
            
            if (!code) {
                showMessage('Please enter a coupon code', 'danger');
                return;
            }

            const button = this;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Applying...';
            button.disabled = true;

            // Simulate cart data
            const cartData = { total: originalTotal };

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
                    showAppliedCoupon(data);
                    updateTotals(data.discount_amount);
                    showMessage(data.message, 'success');
                    document.getElementById('coupon_code').value = '';
                } else {
                    showMessage(data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred. Please try again.', 'danger');
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
                    showMessage(data.message, 'success');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showMessage('An error occurred. Please try again.', 'danger');
            });
        });

        function showAppliedCoupon(data) {
            const display = document.getElementById('applied_coupon');
            const details = document.getElementById('coupon_details');
            
            let text = `${data.coupon.code} - $${parseFloat(data.discount_amount).toFixed(2)} off`;
            if (data.coupon.type === 'percentage') {
                text = `${data.coupon.code} - ${data.coupon.value}% off ($${parseFloat(data.discount_amount).toFixed(2)})`;
            }
            
            details.textContent = text;
            display.style.display = 'block';
        }

        function hideAppliedCoupon() {
            document.getElementById('applied_coupon').style.display = 'none';
        }

        function updateTotals(discountAmount) {
            currentDiscount = parseFloat(discountAmount) || 0;
            const subtotalAfterDiscount = originalTotal - currentDiscount;
            
            // Update discount row
            const discountRow = document.getElementById('discount_row');
            const discountAmountSpan = document.getElementById('discount_amount');
            
            if (currentDiscount > 0) {
                discountRow.style.display = 'flex';
                discountAmountSpan.textContent = `-$${currentDiscount.toFixed(2)}`;
            } else {
                discountRow.style.display = 'none';
            }
            
            // Calculate shipping
            const shipping = subtotalAfterDiscount >= 500 ? 0 : 50;
            document.getElementById('shipping').textContent = shipping === 0 ? 'Free' : `$${shipping.toFixed(2)}`;
            
            // Calculate tax
            const tax = subtotalAfterDiscount * 0.18;
            document.getElementById('tax').textContent = `$${tax.toFixed(2)}`;
            
            // Calculate final total
            const finalTotal = subtotalAfterDiscount + shipping + tax;
            document.getElementById('final_total').textContent = `$${finalTotal.toFixed(2)}`;
        }

        function showMessage(message, type) {
            const messageDiv = document.getElementById('coupon_message');
            messageDiv.className = `alert alert-${type}`;
            messageDiv.textContent = message;
            
            setTimeout(() => {
                messageDiv.textContent = '';
                messageDiv.className = '';
            }, 5000);
        }

        // Enter key support
        document.getElementById('coupon_code').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                document.getElementById('apply_coupon').click();
            }
        });
    </script>
</body>
</html>
