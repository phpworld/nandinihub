<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-3">
            <?= $this->include('user/sidebar') ?>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Wishlist (<?= $wishlistCount ?> items)</h5>
                    <?php if (!empty($wishlistItems)): ?>
                        <a href="<?= base_url('wishlist/clear') ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure you want to clear your entire wishlist?')">
                            <i class="fas fa-trash me-1"></i>Clear All
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php if (empty($wishlistItems)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Your wishlist is empty</h5>
                            <p class="text-muted">Start adding products you love to your wishlist!</p>
                            <a href="<?= base_url('products') ?>" class="btn btn-primary">
                                <i class="fas fa-shopping-bag me-1"></i>Browse Products
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($wishlistItems as $item): ?>
                                <div class="col-md-6 col-lg-4 mb-4" id="wishlist-item-<?= $item['product_id'] ?>">
                                    <div class="card h-100 product-card">
                                        <div class="position-relative">
                                            <a href="<?= base_url('product/' . $item['slug']) ?>">
                                                <img src="<?= base_url('uploads/products/' . ($item['image'] ?: 'default.jpg')) ?>" 
                                                     class="card-img-top" alt="<?= esc($item['name']) ?>" 
                                                     style="height: 200px; object-fit: cover;">
                                            </a>
                                            <button class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 m-2 remove-from-wishlist" 
                                                    data-product-id="<?= $item['product_id'] ?>" 
                                                    title="Remove from wishlist">
                                                <i class="fas fa-times"></i>
                                            </button>
                                            <?php if ($item['stock_quantity'] <= 0): ?>
                                                <div class="position-absolute top-0 start-0 m-2">
                                                    <span class="badge bg-danger">Out of Stock</span>
                                                </div>
                                            <?php elseif (!empty($item['sale_price']) && $item['sale_price'] < $item['price']): ?>
                                                <div class="position-absolute top-0 start-0 m-2">
                                                    <span class="badge bg-success">Sale</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="card-body d-flex flex-column">
                                            <h6 class="card-title">
                                                <a href="<?= base_url('product/' . $item['slug']) ?>" class="text-decoration-none text-dark">
                                                    <?= esc($item['name']) ?>
                                                </a>
                                            </h6>
                                            <div class="price-section mt-auto">
                                                <?php if (!empty($item['sale_price']) && $item['sale_price'] < $item['price']): ?>
                                                    <div class="d-flex align-items-center">
                                                        <span class="h6 text-primary mb-0">$<?= number_format($item['sale_price'], 2) ?></span>
                                                        <span class="text-muted text-decoration-line-through ms-2">$<?= number_format($item['price'], 2) ?></span>
                                                    </div>
                                                    <small class="text-success">
                                                        Save $<?= number_format($item['price'] - $item['sale_price'], 2) ?>
                                                    </small>
                                                <?php else: ?>
                                                    <span class="h6 text-primary">$<?= number_format($item['price'], 2) ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="mt-3">
                                                <?php if ($item['stock_quantity'] > 0): ?>
                                                    <button class="btn btn-primary btn-sm w-100 add-to-cart" 
                                                            data-product-id="<?= $item['product_id'] ?>">
                                                        <i class="fas fa-shopping-cart me-1"></i>Add to Cart
                                                    </button>
                                                <?php else: ?>
                                                    <button class="btn btn-secondary btn-sm w-100" disabled>
                                                        <i class="fas fa-times me-1"></i>Out of Stock
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Remove from wishlist
    $('.remove-from-wishlist').on('click', function() {
        const productId = $(this).data('product-id');
        const button = $(this);
        
        button.prop('disabled', true);
        
        $.ajax({
            url: '<?= base_url('wishlist/remove') ?>',
            type: 'POST',
            data: {
                product_id: productId,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Remove the item from the page
                    $('#wishlist-item-' + productId).fadeOut(300, function() {
                        $(this).remove();
                        
                        // Update wishlist count in header
                        updateWishlistCount(response.wishlistCount);
                        
                        // Check if wishlist is empty
                        if (response.wishlistCount === 0) {
                            location.reload();
                        }
                    });
                    
                    showToast('success', response.message);
                } else {
                    showToast('error', response.message);
                }
            },
            error: function() {
                showToast('error', 'An error occurred. Please try again.');
            },
            complete: function() {
                button.prop('disabled', false);
            }
        });
    });
    
    // Add to cart from wishlist
    $('.add-to-cart').on('click', function() {
        const productId = $(this).data('product-id');
        addToCart(productId, 1);
    });
});
</script>
<?= $this->endSection() ?>
