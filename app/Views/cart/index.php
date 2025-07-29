<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                    <li class="breadcrumb-item active">Shopping Cart</li>
                </ol>
            </nav>

            <h1 class="h2">Shopping Cart</h1>
        </div>
    </div>

    <?php if (empty($cartItems)): ?>
        <!-- Empty Cart -->
        <div class="row">
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
                    <h4>Your Cart is Empty</h4>
                    <p class="text-muted">Looks like you haven't added any items to your cart yet.</p>
                    <a href="<?= base_url('products') ?>" class="btn btn-primary btn-lg">Start Shopping</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <!-- Cart Items -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Cart Items (<?= count($cartItems) ?>)</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Product</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cartItems as $item): ?>
                                        <tr id="cart-item-<?= $item['id'] ?>">
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="<?= $item['image'] ? base_url('uploads/products/' . esc($item['image'])) : 'https://via.placeholder.com/80x60/f8f9fa/6c757d?text=' . urlencode($item['name']) ?>"
                                                        class="rounded me-3" style="width: 80px; height: 60px; object-fit: cover;" alt="<?= esc($item['name']) ?>">
                                                    <div>
                                                        <h6 class="mb-1"><?= esc($item['name']) ?></h6>
                                                        <?php if (!empty($item['variant_options_details'])): ?>
                                                            <div class="mb-1">
                                                                <?php foreach ($item['variant_options_details'] as $option): ?>
                                                                    <small class="badge bg-light text-dark me-1">
                                                                        <?= esc($option['type_display_name']) ?>: <?= esc($option['option_name']) ?>
                                                                    </small>
                                                                <?php endforeach; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                        <small class="text-muted">
                                                            <?php if ($item['variant_id']): ?>
                                                                SKU: <?= esc($item['variant_sku']) ?> | Stock: <?= $item['variant_stock'] ?> available
                                                            <?php else: ?>
                                                                Stock: <?= $item['stock_quantity'] ?> available
                                                            <?php endif; ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <?php
                                                $displayPrice = $item['final_price'];
                                                $originalPrice = $item['variant_sale_price'] ?? $item['variant_price'] ?? $item['sale_price'] ?? $item['price'];
                                                ?>
                                                <span class="fw-bold item-price" data-price="<?= $displayPrice ?>">₹<?= number_format($displayPrice, 2) ?></span>
                                                <?php if ($item['price_modifier'] != 0): ?>
                                                    <br><small class="text-muted">
                                                        <del>₹<?= number_format($originalPrice, 2) ?></del>
                                                        <span class="badge <?= $item['price_modifier'] > 0 ? 'bg-warning' : 'bg-success' ?> ms-1">
                                                            <?= $item['price_modifier'] > 0 ? '+' : '' ?>₹<?= number_format(abs($item['price_modifier']), 2) ?>
                                                        </span>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="align-middle">
                                                <?php
                                                $maxQuantity = $item['variant_id'] ? $item['variant_stock'] : $item['stock_quantity'];
                                                ?>
                                                <div class="input-group" style="width: 120px;">
                                                    <button class="btn btn-outline-secondary btn-sm" type="button"
                                                        onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['quantity'] - 1 ?>)">-</button>
                                                    <input type="number" class="form-control form-control-sm text-center"
                                                        value="<?= $item['quantity'] ?>" min="1" max="<?= $maxQuantity ?>"
                                                        data-cart-id="<?= $item['id'] ?>"
                                                        onchange="updateQuantity(<?= $item['id'] ?>, this.value)">
                                                    <button class="btn btn-outline-secondary btn-sm" type="button"
                                                        onclick="updateQuantity(<?= $item['id'] ?>, <?= $item['quantity'] + 1 ?>)">+</button>
                                                </div>
                                            </td>
                                            <td class="align-middle">
                                                <span class="fw-bold item-total" data-cart-id="<?= $item['id'] ?>">₹<?= number_format($displayPrice * $item['quantity'], 2) ?></span>
                                            </td>
                                            <td class="align-middle">
                                                <button class="btn btn-outline-danger btn-sm"
                                                    onclick="removeFromCart(<?= $item['id'] ?>)">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('products') ?>" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left"></i> Continue Shopping
                            </a>
                            <button class="btn btn-outline-danger" onclick="clearCart()">
                                <i class="fas fa-trash"></i> Clear Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart Summary -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span id="cart-subtotal">₹<?= number_format($cartTotal, 2) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span class="text-success">
                                <?php if ($cartTotal >= 500): ?>
                                    Free
                                <?php else: ?>
                                    ₹50.00
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax (18% GST):</span>
                            <span id="cart-tax">₹<?= number_format($cartTotal * 0.18, 2) ?></span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-3">
                            <strong>Total:</strong>
                            <strong id="cart-total">
                                ₹<?= number_format($cartTotal + ($cartTotal >= 500 ? 0 : 50) + ($cartTotal * 0.18), 2) ?>
                            </strong>
                        </div>

                        <?php if ($cartTotal >= 500): ?>
                            <div class="alert alert-success small mb-3">
                                <i class="fas fa-check-circle"></i> You qualify for free shipping!
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info small mb-3">
                                <i class="fas fa-info-circle"></i> Add ₹<?= number_format(500 - $cartTotal, 2) ?> more for free shipping!
                            </div>
                        <?php endif; ?>

                        <div class="d-grid gap-2">
                            <?php if (session()->get('is_logged_in')): ?>
                                <a href="<?= base_url('checkout') ?>" class="btn btn-primary btn-lg">
                                    <i class="fas fa-credit-card"></i> Proceed to Checkout
                                </a>
                            <?php else: ?>
                                <a href="<?= base_url('login?redirect_to=/checkout') ?>" class="btn btn-primary btn-lg">
                                    <i class="fas fa-sign-in-alt"></i> Login to Checkout
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Secure Checkout Features -->
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
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function updateQuantity(cartId, newQuantity) {
        if (newQuantity < 1) {
            removeFromCart(cartId);
            return;
        }

        // Update quantity display immediately for better UX
        const quantityInput = $(`input[data-cart-id="${cartId}"]`);
        const oldQuantity = parseInt(quantityInput.val());
        quantityInput.val(newQuantity);

        // Update item total immediately
        const itemPrice = parseFloat($(`tr:has(input[data-cart-id="${cartId}"]) .item-price`).data('price'));
        const itemTotal = $(`tr:has(input[data-cart-id="${cartId}"]) .item-total`);
        const newItemTotal = itemPrice * newQuantity;
        itemTotal.text('₹' + newItemTotal.toFixed(2));

        // Update cart totals immediately
        updateCartTotalsFromDOM();

        $.post('<?= base_url('cart/update') ?>', {
            cart_id: cartId,
            quantity: newQuantity
        }, function(response) {
            if (response.success) {
                // Update with server response to ensure accuracy
                updateCartTotals(response.cartTotal);
                showAlert('success', response.message);
            } else {
                // Revert changes if server update failed
                quantityInput.val(oldQuantity);
                const revertedTotal = itemPrice * oldQuantity;
                itemTotal.text('₹' + revertedTotal.toFixed(2));
                updateCartTotalsFromDOM();
                showAlert('danger', response.message);
            }
        }).fail(function() {
            // Revert changes if request failed
            quantityInput.val(oldQuantity);
            const revertedTotal = itemPrice * oldQuantity;
            itemTotal.text('₹' + revertedTotal.toFixed(2));
            updateCartTotalsFromDOM();
            showAlert('danger', 'Failed to update cart');
        });
    }

    function removeFromCart(cartId) {
        if (confirm('Are you sure you want to remove this item from your cart?')) {
            $.post('<?= base_url('cart/remove') ?>', {
                cart_id: cartId
            }, function(response) {
                if (response.success) {
                    // Remove the row from the table
                    $('#cart-item-' + cartId).fadeOut(300, function() {
                        $(this).remove();

                        // Check if cart is empty
                        if ($('tbody tr').length === 0) {
                            location.reload();
                        }
                    });

                    // Update cart count and totals
                    $('#cartCount').text(response.cartCount);
                    updateCartTotals(response.cartTotal);
                    showAlert('success', response.message);
                } else {
                    showAlert('danger', response.message);
                }
            }).fail(function() {
                showAlert('danger', 'Failed to remove item from cart');
            });
        }
    }

    function clearCart() {
        if (confirm('Are you sure you want to clear your entire cart?')) {
            // Show loading state
            const clearBtn = $('button[onclick="clearCart()"]');
            const originalText = clearBtn.html();
            clearBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Clearing...');

            $.post('<?= base_url('cart/clear') ?>', {}, function(response) {
                // Reload the page to show empty cart
                location.reload();
            }).fail(function() {
                // If AJAX fails, fallback to direct navigation
                window.location.href = '<?= base_url('cart/clear') ?>';
            }).always(function() {
                // Restore button state
                clearBtn.prop('disabled', false).html(originalText);
            });
        }
    }

    function updateCartTotals(subtotal) {
        const shipping = subtotal >= 500 ? 0 : 50;
        const tax = subtotal * 0.18;
        const total = subtotal + shipping + tax;

        $('#cart-subtotal').text('₹' + subtotal.toFixed(2));
        $('#cart-tax').text('₹' + tax.toFixed(2));
        $('#cart-total').text('₹' + total.toFixed(2));
    }

    function updateCartTotalsFromDOM() {
        let subtotal = 0;

        // Calculate subtotal from all item totals
        $('.item-total').each(function() {
            const itemTotalText = $(this).text().replace('₹', '').replace(',', '');
            subtotal += parseFloat(itemTotalText);
        });

        updateCartTotals(subtotal);
    }
</script>
<?= $this->endSection() ?>