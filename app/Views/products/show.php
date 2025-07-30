<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('products') ?>">Products</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url('category/' . esc($product['category_slug'])) ?>"><?= esc($product['category_name']) ?></a></li>
            <li class="breadcrumb-item active"><?= esc($product['name']) ?></li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6 mb-4">
            <div class="product-image-container">
                <img src="<?= $product['image'] ? base_url('uploads/products/' . esc($product['image'])) : 'https://via.placeholder.com/500x400/f8f9fa/6c757d?text=' . urlencode($product['name']) ?>"
                    class="img-fluid rounded shadow" alt="<?= esc($product['name']) ?>" id="mainProductImage">

                <!-- Gallery thumbnails would go here if we had multiple images -->
                <?php if (!empty($product['gallery'])): ?>
                    <div class="row mt-3">
                        <?php
                        $gallery = json_decode($product['gallery'], true);
                        if ($gallery):
                        ?>
                            <?php foreach ($gallery as $image): ?>
                                <div class="col-3">
                                    <img src="<?= base_url('uploads/products/' . esc($image)) ?>" class="img-fluid rounded thumbnail-image"
                                        alt="<?= esc($product['name']) ?>" onclick="changeMainImage(this.src)">
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Details -->
        <div class="col-lg-6">
            <div class="product-details">
                <h1 class="h2 mb-3"><?= esc($product['name']) ?></h1>

                <!-- Price -->
                <div class="price-section mb-3">
                    <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                        <span class="h4 text-muted text-decoration-line-through me-2">$<?= number_format($product['price'], 2) ?></span>
                        <span class="h3 text-primary">$<?= number_format($product['sale_price'], 2) ?></span>
                        <span class="badge bg-danger ms-2">
                            <?= round((($product['price'] - $product['sale_price']) / $product['price']) * 100) ?>% OFF
                        </span>
                    <?php else: ?>
                        <span class="h3 text-primary">$<?= number_format($product['price'], 2) ?></span>
                    <?php endif; ?>
                </div>

                <!-- Short Description -->
                <?php if (!empty($product['short_description'])): ?>
                    <p class="lead text-muted mb-3"><?= esc($product['short_description']) ?></p>
                <?php endif; ?>

                <!-- Product Info -->
                <div class="product-info mb-4">
                    <div class="row">
                        <div class="col-6">
                            <strong>SKU:</strong> <?= esc($product['sku']) ?>
                        </div>
                        <div class="col-6">
                            <strong>Stock:</strong>
                            <?php if ($product['stock_quantity'] > 0): ?>
                                <span class="text-success"><?= $product['stock_quantity'] ?> in stock</span>
                            <?php else: ?>
                                <span class="text-danger">Out of stock</span>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($product['weight'])): ?>
                            <div class="col-6 mt-2">
                                <strong>Weight:</strong> <?= esc($product['weight']) ?> kg
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($product['dimensions'])): ?>
                            <div class="col-6 mt-2">
                                <strong>Dimensions:</strong> <?= esc($product['dimensions']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Product Variations -->
                <?php if (!empty($product['variants']) && !empty($variationTypes)): ?>
                    <div class="product-variations mb-4">
                        <h5>Select Options:</h5>
                        <?php foreach ($variationTypes as $type): ?>
                            <div class="variation-group mb-3">
                                <label class="form-label fw-bold">
                                    <?= esc($type['display_name']) ?>
                                    <?php if ($type['is_required']): ?>
                                        <span class="text-danger">*</span>
                                    <?php endif; ?>
                                </label>

                                <?php
                                // Get options for this type from variants
                                $typeOptions = [];
                                foreach ($product['variants'] as $variant) {
                                    foreach ($variant['options'] as $option) {
                                        if ($option['variation_type_id'] == $type['id']) {
                                            $typeOptions[$option['variation_option_id']] = $option;
                                        }
                                    }
                                }
                                $typeOptions = array_values($typeOptions); // Remove duplicate keys
                                ?>

                                <div class="variation-options" data-type-id="<?= $type['id'] ?>" data-required="<?= $type['is_required'] ?>">
                                    <?php if ($type['type'] === 'color'): ?>
                                        <!-- Color swatches -->
                                        <?php foreach ($typeOptions as $option): ?>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input variation-option"
                                                       type="radio"
                                                       name="variation_<?= $type['id'] ?>"
                                                       id="option_<?= $option['variation_option_id'] ?>"
                                                       value="<?= $option['variation_option_id'] ?>"
                                                       data-type-id="<?= $type['id'] ?>">
                                                <label class="form-check-label color-swatch"
                                                       for="option_<?= $option['variation_option_id'] ?>"
                                                       style="background-color: <?= esc($option['color_code']) ?>; width: 30px; height: 30px; border-radius: 50%; display: inline-block; border: 2px solid #ddd; cursor: pointer;"
                                                       title="<?= esc($option['option_name']) ?>">
                                                </label>
                                            </div>
                                        <?php endforeach; ?>

                                    <?php elseif ($type['type'] === 'button'): ?>
                                        <!-- Button options -->
                                        <?php foreach ($typeOptions as $option): ?>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input variation-option d-none"
                                                       type="radio"
                                                       name="variation_<?= $type['id'] ?>"
                                                       id="option_<?= $option['variation_option_id'] ?>"
                                                       value="<?= $option['variation_option_id'] ?>"
                                                       data-type-id="<?= $type['id'] ?>">
                                                <label class="btn btn-outline-primary variation-btn"
                                                       for="option_<?= $option['variation_option_id'] ?>">
                                                    <?= esc($option['option_name']) ?>
                                                </label>
                                            </div>
                                        <?php endforeach; ?>

                                    <?php else: ?>
                                        <!-- Text/Select options -->
                                        <select class="form-select variation-option"
                                                name="variation_<?= $type['id'] ?>"
                                                data-type-id="<?= $type['id'] ?>">
                                            <option value="">Choose <?= esc($type['display_name']) ?></option>
                                            <?php foreach ($typeOptions as $option): ?>
                                                <option value="<?= $option['variation_option_id'] ?>">
                                                    <?= esc($option['option_name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <!-- Selected variant info -->
                        <div id="selected-variant-info" class="mt-3" style="display: none;">
                            <div class="alert alert-info">
                                <strong>Selected:</strong> <span id="variant-details"></span><br>
                                <strong>Price:</strong> ₹<span id="variant-price"></span><br>
                                <strong>Stock:</strong> <span id="variant-stock"></span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Add to Cart Section -->
                <div class="add-to-cart-section mb-4">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <label for="quantity" class="form-label">Quantity:</label>
                            <input type="number" id="quantity" class="form-control" value="1" min="1" max="<?= $product['stock_quantity'] ?>">
                        </div>
                        <div class="col-md-8">
                            <?php if ($product['stock_quantity'] > 0): ?>
                                <button class="btn btn-primary btn-lg w-100" onclick="addToCartWithQuantity(<?= $product['id'] ?>)">
                                    <i class="fas fa-cart-plus"></i> Add to Cart
                                </button>
                            <?php else: ?>
                                <button class="btn btn-secondary btn-lg w-100" disabled>
                                    <i class="fas fa-times"></i> Out of Stock
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Wishlist Section -->
                <div class="wishlist-section mb-4">
                    <?php if (session()->get('is_logged_in')): ?>
                        <?php
                        $wishlistModel = new \App\Models\WishlistModel();
                        $inWishlist = $wishlistModel->isInWishlist(session()->get('user_id'), $product['id']);
                        ?>
                        <button class="btn btn-lg <?= $inWishlist ? 'btn-danger' : 'btn-outline-danger' ?> w-100"
                            id="wishlistBtn"
                            onclick="toggleWishlist(<?= $product['id'] ?>, $(this))"
                            title="<?= $inWishlist ? 'Remove from wishlist' : 'Add to wishlist' ?>">
                            <i class="<?= $inWishlist ? 'fas' : 'far' ?> fa-heart me-2"></i><?= $inWishlist ? 'Remove from Wishlist' : 'Add to Wishlist' ?>
                        </button>
                    <?php else: ?>
                        <a href="<?= base_url('login') ?>" class="btn btn-outline-danger btn-lg w-100">
                            <i class="far fa-heart me-2"></i>Login to Add to Wishlist
                        </a>
                    <?php endif; ?>
                </div>

                <!-- Features -->
                <div class="product-features">
                    <div class="row text-center">
                        <div class="col-3">
                            <i class="fas fa-shipping-fast text-primary mb-2"></i>
                            <small class="d-block">Free Shipping</small>
                        </div>
                        <div class="col-3">
                            <i class="fas fa-shield-alt text-primary mb-2"></i>
                            <small class="d-block">Authentic</small>
                        </div>
                        <div class="col-3">
                            <i class="fas fa-undo text-primary mb-2"></i>
                            <small class="d-block">Easy Returns</small>
                        </div>
                        <div class="col-3">
                            <i class="fas fa-headset text-primary mb-2"></i>
                            <small class="d-block">24/7 Support</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Description & Reviews -->
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" type="button" role="tab">
                        Description
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button" role="tab">
                        Reviews <span class="badge bg-secondary" id="reviewCount">0</span>
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="productTabsContent">
                <!-- Description Tab -->
                <div class="tab-pane fade show active" id="description" role="tabpanel">
                    <div class="card border-top-0">
                        <div class="card-body">
                            <?php if (!empty($product['description'])): ?>
                                <p><?= nl2br(esc($product['description'])) ?></p>
                            <?php else: ?>
                                <p class="text-muted">No description available for this product.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Reviews Tab -->
                <div class="tab-pane fade" id="reviews" role="tabpanel">
                    <div class="card border-top-0">
                        <div class="card-body">
                            <!-- Review Summary -->
                            <div id="reviewSummary" class="mb-4">
                                <!-- Will be loaded via AJAX -->
                            </div>

                            <!-- Write Review Button -->
                            <?php if (session()->get('user_id')): ?>
                                <div class="mb-4">
                                    <a href="<?= base_url('product/' . esc($product['slug']) . '/review') ?>" class="btn btn-primary">
                                        <i class="fas fa-star"></i> Write a Review
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="mb-4">
                                    <p class="text-muted">
                                        <a href="<?= base_url('login') ?>">Login</a> to write a review for this product.
                                    </p>
                                </div>
                            <?php endif; ?>

                            <!-- Reviews List -->
                            <div id="reviewsList">
                                <!-- Will be loaded via AJAX -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($relatedProducts)): ?>
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">Related Products</h3>
                <div class="row g-4">
                    <?php foreach ($relatedProducts as $relatedProduct): ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="card product-card h-100">
                                <div class="position-relative">
                                    <img src="<?= $relatedProduct['image'] ? base_url('uploads/products/' . esc($relatedProduct['image'])) : 'https://via.placeholder.com/300x200/f8f9fa/6c757d?text=' . urlencode($relatedProduct['name']) ?>"
                                        class="card-img-top product-image" alt="<?= esc($relatedProduct['name']) ?>">

                                    <!-- Wishlist Button -->
                                    <?php if (session()->get('is_logged_in')): ?>
                                        <?php
                                        $wishlistModel = new \App\Models\WishlistModel();
                                        $inWishlist = $wishlistModel->isInWishlist(session()->get('user_id'), $relatedProduct['id']);
                                        ?>
                                        <button class="btn btn-sm position-absolute wishlist-btn <?= $inWishlist ? 'btn-danger' : 'btn-outline-danger' ?>"
                                            style="top: 10px; right: 10px; z-index: 10;"
                                            onclick="toggleWishlist(<?= $relatedProduct['id'] ?>, $(this))"
                                            title="<?= $inWishlist ? 'Remove from wishlist' : 'Add to wishlist' ?>">
                                            <i class="<?= $inWishlist ? 'fas' : 'far' ?> fa-heart"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title"><?= esc($relatedProduct['name']) ?></h6>
                                    <p class="card-text text-muted small flex-grow-1"><?= esc($relatedProduct['short_description']) ?></p>

                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <?php if (!empty($relatedProduct['sale_price']) && $relatedProduct['sale_price'] < $relatedProduct['price']): ?>
                                                    <span class="price-original small">₹<?= number_format($relatedProduct['price'], 2) ?></span>
                                                    <span class="price-sale">₹<?= number_format($relatedProduct['sale_price'], 2) ?></span>
                                                <?php else: ?>
                                                    <span class="price-sale">₹<?= number_format($relatedProduct['price'], 2) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="d-grid gap-2">
                                            <button class="btn btn-primary btn-sm" onclick="addToCart(<?= $relatedProduct['id'] ?>)">
                                                <i class="fas fa-cart-plus"></i> Add to Cart
                                            </button>
                                            <a href="<?= base_url('product/' . esc($relatedProduct['slug'])) ?>" class="btn btn-outline-primary btn-sm">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function changeMainImage(src) {
        document.getElementById('mainProductImage').src = src;
    }

    function addToCartWithQuantity(productId) {
        const quantity = document.getElementById('quantity').value;
        addToCart(productId, quantity);
    }

    // Add thumbnail hover effect
    document.querySelectorAll('.thumbnail-image').forEach(img => {
        img.style.cursor = 'pointer';
        img.addEventListener('mouseenter', function() {
            this.style.opacity = '0.8';
        });
        img.addEventListener('mouseleave', function() {
            this.style.opacity = '1';
        });
    });

    // Load reviews when reviews tab is clicked
    document.getElementById('reviews-tab').addEventListener('click', function() {
        loadProductReviews(<?= $product['id'] ?>);
    });

    function loadProductReviews(productId) {
        $.get('<?= base_url('api/products/') ?>' + productId + '/reviews', function(data) {
            // Update review count
            $('#reviewCount').text(data.stats.total_reviews || 0);

            // Display review summary
            if (data.stats.total_reviews > 0) {
                const avgRating = parseFloat(data.stats.average_rating).toFixed(1);
                const summaryHtml = `
                    <div class="row">
                        <div class="col-md-4 text-center">
                            <h2 class="text-primary">${avgRating}</h2>
                            <div class="text-warning mb-2">
                                ${generateStarRating(avgRating)}
                            </div>
                            <p class="text-muted">${data.stats.total_reviews} review(s)</p>
                        </div>
                        <div class="col-md-8">
                            <div class="rating-breakdown">
                                ${generateRatingBreakdown(data.stats)}
                            </div>
                        </div>
                    </div>
                `;
                $('#reviewSummary').html(summaryHtml);
            } else {
                $('#reviewSummary').html('<p class="text-muted">No reviews yet. Be the first to review this product!</p>');
            }

            // Display reviews
            if (data.reviews.length > 0) {
                let reviewsHtml = '';
                data.reviews.forEach(review => {
                    reviewsHtml += generateReviewHtml(review);
                });
                $('#reviewsList').html(reviewsHtml);
            } else {
                $('#reviewsList').html('<p class="text-muted">No reviews available.</p>');
            }
        });
    }

    function generateStarRating(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= rating) {
                stars += '<i class="fas fa-star"></i>';
            } else if (i - 0.5 <= rating) {
                stars += '<i class="fas fa-star-half-alt"></i>';
            } else {
                stars += '<i class="far fa-star"></i>';
            }
        }
        return stars;
    }

    function generateRatingBreakdown(stats) {
        let breakdown = '';
        for (let i = 5; i >= 1; i--) {
            const count = stats[`${i === 1 ? 'one' : i === 2 ? 'two' : i === 3 ? 'three' : i === 4 ? 'four' : 'five'}_star`] || 0;
            const percent = stats[`${i === 1 ? 'one' : i === 2 ? 'two' : i === 3 ? 'three' : i === 4 ? 'four' : 'five'}_star_percent`] || 0;
            breakdown += `
                <div class="d-flex align-items-center mb-1">
                    <span class="me-2">${i} star</span>
                    <div class="progress flex-grow-1 me-2" style="height: 8px;">
                        <div class="progress-bar bg-warning" style="width: ${percent}%"></div>
                    </div>
                    <span class="text-muted small">${count}</span>
                </div>
            `;
        }
        return breakdown;
    }

    function generateReviewHtml(review) {
        return `
            <div class="review-item border-bottom pb-3 mb-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="mb-1">${review.first_name} ${review.last_name}</h6>
                        <div class="text-warning mb-1">
                            ${generateStarRating(review.rating)}
                        </div>
                        ${review.title ? `<h6 class="mb-2">${review.title}</h6>` : ''}
                    </div>
                    <small class="text-muted">${new Date(review.created_at).toLocaleDateString()}</small>
                </div>
                ${review.review ? `<p class="mb-2">${review.review}</p>` : ''}
                ${review.is_verified ? '<span class="badge bg-success small">Verified Purchase</span>' : ''}
                <div class="mt-2">
                    <button class="btn btn-sm btn-outline-secondary" onclick="markHelpful(${review.id})">
                        <i class="fas fa-thumbs-up"></i> Helpful (${review.helpful_count})
                    </button>
                </div>
            </div>
        `;
    }

    function markHelpful(reviewId) {
        $.post('<?= base_url('reviews/') ?>' + reviewId + '/helpful', function(response) {
            if (response.success) {
                showAlert('success', 'Thank you for your feedback!');
                // Reload reviews to update count
                loadProductReviews(<?= $product['id'] ?>);
            }
        });
    }

    // Product Variations Handling
    <?php if (!empty($product['variants'])): ?>
    const productVariants = <?= json_encode($product['variants']) ?>;
    let selectedVariant = null;

    // Handle variation option changes
    $(document).on('change', '.variation-option', function() {
        updateSelectedVariant();
    });

    // Handle color swatch clicks
    $(document).on('click', '.color-swatch', function() {
        const input = $(this).prev('input');
        input.prop('checked', true).trigger('change');
    });

    // Handle button variation clicks
    $(document).on('click', '.variation-btn', function() {
        const input = $(this).prev('input');
        const typeId = input.data('type-id');

        // Uncheck all other options in this type
        $(`input[name="variation_${typeId}"]`).prop('checked', false);
        $('.variation-btn').removeClass('btn-primary').addClass('btn-outline-primary');

        // Check this option and update button style
        input.prop('checked', true);
        $(this).removeClass('btn-outline-primary').addClass('btn-primary');

        updateSelectedVariant();
    });

    function updateSelectedVariant() {
        const selectedOptions = [];

        // Get selected options from radio buttons and checkboxes
        $('.variation-option:checked').each(function() {
            const value = $(this).val();
            if (value) {
                selectedOptions.push(parseInt(value));
            }
        });

        // Get selected options from select dropdowns
        $('.variation-option').each(function() {
            if ($(this).is('select')) {
                const value = $(this).val();
                if (value) {
                    selectedOptions.push(parseInt(value));
                }
            }
        });

        // Find matching variant
        selectedVariant = null;
        for (const variant of productVariants) {
            const variantOptionIds = variant.options.map(opt => parseInt(opt.variation_option_id));

            if (arraysEqual(selectedOptions.sort(), variantOptionIds.sort())) {
                selectedVariant = variant;
                break;
            }
        }

        updateVariantDisplay();
    }

    function updateVariantDisplay() {
        const infoDiv = $('#selected-variant-info');

        if (selectedVariant) {
            const optionNames = selectedVariant.options.map(opt => opt.option_name).join(', ');
            let price = selectedVariant.sale_price || selectedVariant.price || <?= $product['sale_price'] ?? $product['price'] ?>;
            const stock = selectedVariant.stock_quantity;

            // Calculate price with option modifiers
            const basePrice = parseFloat(<?= $product['sale_price'] ?? $product['price'] ?>);
            let totalPriceModifier = 0;

            selectedVariant.options.forEach(option => {
                if (option.price_modifier && option.price_modifier != 0) {
                    if (option.price_type === 'percentage') {
                        totalPriceModifier += (basePrice * parseFloat(option.price_modifier) / 100);
                    } else {
                        totalPriceModifier += parseFloat(option.price_modifier);
                    }
                }
            });

            const finalPrice = Math.max(0, basePrice + totalPriceModifier);

            $('#variant-details').text(optionNames);
            $('#variant-price').text(finalPrice.toFixed(2));
            $('#variant-stock').text(stock > 0 ? stock + ' in stock' : 'Out of stock');

            infoDiv.show();

            // Update add to cart button
            const addToCartBtn = $('button[onclick*="addToCartWithQuantity"]');
            if (stock > 0) {
                addToCartBtn.prop('disabled', false)
                           .removeClass('btn-secondary')
                           .addClass('btn-primary')
                           .html('<i class="fas fa-cart-plus"></i> Add to Cart');
            } else {
                addToCartBtn.prop('disabled', true)
                           .removeClass('btn-primary')
                           .addClass('btn-secondary')
                           .html('<i class="fas fa-times"></i> Out of Stock');
            }

            // Update quantity max
            $('#quantity').attr('max', stock);
        } else {
            // Show price calculation even without variant match
            const selectedOptions = [];
            $('.variation-option:checked').each(function() {
                const value = $(this).val();
                if (value) {
                    selectedOptions.push(parseInt(value));
                }
            });
            $('.variation-option').each(function() {
                if ($(this).is('select')) {
                    const value = $(this).val();
                    if (value) {
                        selectedOptions.push(parseInt(value));
                    }
                }
            });

            if (selectedOptions.length > 0) {
                // Calculate price with selected options
                const basePrice = parseFloat(<?= $product['sale_price'] ?? $product['price'] ?>);
                let totalPriceModifier = 0;

                // Get option data from productVariants
                const allOptions = [];
                productVariants.forEach(variant => {
                    variant.options.forEach(option => {
                        if (!allOptions.find(opt => opt.variation_option_id === option.variation_option_id)) {
                            allOptions.push(option);
                        }
                    });
                });

                selectedOptions.forEach(optionId => {
                    const option = allOptions.find(opt => opt.variation_option_id == optionId);
                    if (option && option.price_modifier && option.price_modifier != 0) {
                        if (option.price_type === 'percentage') {
                            totalPriceModifier += (basePrice * parseFloat(option.price_modifier) / 100);
                        } else {
                            totalPriceModifier += parseFloat(option.price_modifier);
                        }
                    }
                });

                const finalPrice = Math.max(0, basePrice + totalPriceModifier);

                // Update main product price display
                $('.product-price .price').text('$' + finalPrice.toFixed(2));
                if (totalPriceModifier !== 0) {
                    $('.product-price .original-price').text('$' + basePrice.toFixed(2)).show();
                } else {
                    $('.product-price .original-price').hide();
                }
            } else {
                // Reset to original price
                const basePrice = parseFloat(<?= $product['sale_price'] ?? $product['price'] ?>);
                $('.product-price .price').text('$' + basePrice.toFixed(2));
                $('.product-price .original-price').hide();
            }

            infoDiv.hide();
        }
    }

    function arraysEqual(a, b) {
        return a.length === b.length && a.every((val, i) => val === b[i]);
    }

    // Override addToCartWithQuantity for variants
    window.addToCartWithQuantity = function(productId) {
        let quantity = parseInt($('#quantity').val());
        let data = {};

        <?php if (!empty($product['variants'])): ?>
        // Check if all required options are selected
        let allRequiredSelected = true;
        let missingTypes = [];

        $('.variation-options[data-required="1"]').each(function() {
            const typeId = $(this).data('type-id');
            const typeName = $(this).closest('.variation-group').find('label').text().replace('*', '').trim();
            let hasSelection = false;

            // Check radio buttons and checkboxes
            if ($(this).find('.variation-option:checked').length > 0) {
                hasSelection = true;
            }

            // Check select dropdowns
            $(this).find('select.variation-option').each(function() {
                if ($(this).val()) {
                    hasSelection = true;
                }
            });

            if (!hasSelection) {
                allRequiredSelected = false;
                missingTypes.push(typeName);
            }
        });

        if (!allRequiredSelected) {
            showAlert('warning', 'Please select: ' + missingTypes.join(', '));
            return;
        }

        if (!selectedVariant) {
            showAlert('warning', 'Please select a valid combination of options');
            return;
        }

        if (quantity > selectedVariant.stock_quantity) {
            showAlert('error', 'Not enough stock available');
            return;
        }

        // Get selected option IDs
        let selectedOptionIds = [];
        $('.variation-option:checked').each(function() {
            selectedOptionIds.push(parseInt($(this).val()));
        });
        $('.variation-option option:selected').each(function() {
            if ($(this).val()) {
                selectedOptionIds.push(parseInt($(this).val()));
            }
        });

        data = {
            product_id: productId,
            variant_id: selectedVariant.id,
            variant_options: selectedOptionIds,
            quantity: quantity
        };
        <?php else: ?>
        data = {
            product_id: productId,
            quantity: quantity
        };
        <?php endif; ?>

        $.post('<?= base_url('cart/add') ?>', data, function(response) {
            if (response.success) {
                showAlert('success', 'Product added to cart successfully!');
                updateCartCount();
            } else {
                showAlert('error', response.message || 'Failed to add product to cart');
            }
        }).fail(function() {
            showAlert('error', 'An error occurred while adding to cart');
        });
    };

    // Initialize variant selection on page load
    updateSelectedVariant();
    <?php endif; ?>
</script>
<?= $this->endSection() ?>