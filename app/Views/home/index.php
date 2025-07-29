<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- Hero Slider Section -->
<section class="hero-slider-section">
    <?php if (!empty($banners)): ?>
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
            <!-- Carousel Indicators -->
            <div class="carousel-indicators">
                <?php foreach ($banners as $index => $banner): ?>
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="<?= $index ?>"
                        class="<?= $index === 0 ? 'active' : '' ?>" aria-current="<?= $index === 0 ? 'true' : 'false' ?>"
                        aria-label="Slide <?= $index + 1 ?>"></button>
                <?php endforeach; ?>
            </div>

            <!-- Carousel Items -->
            <div class="carousel-inner">
                <?php foreach ($banners as $index => $banner): ?>
                    <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                        <!-- Background Image -->
                        <div class="hero-slide" style="background-image: url('<?= $banner['image'] ? base_url('uploads/banners/' . esc($banner['image'])) : 'https://images.unsplash.com/photo-1582735689369-4fe89db7114c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80' ?>');">
                            <!-- Overlay -->
                            <div class="hero-overlay"></div>

                            <!-- Content -->
                            <div class="hero-content">
                                <div class="container">
                                    <div class="row justify-content-center">
                                        <div class="col-lg-8 text-center">
                                            <h1 class="hero-title display-3 fw-bold mb-4" data-aos="fade-up">
                                                <?= $banner['title'] ? esc($banner['title']) : 'Premium Puja Samagri Online' ?>
                                            </h1>
                                            <p class="hero-subtitle lead mb-5" data-aos="fade-up" data-aos-delay="200">
                                                <?= $banner['subtitle'] ? esc($banner['subtitle']) : 'Discover authentic and high-quality puja items including agarbatti, dhoop, diyas, and all essential spiritual accessories for your divine worship.' ?>
                                            </p>
                                            <?php if ($banner['description']): ?>
                                                <p class="hero-description mb-5" data-aos="fade-up" data-aos-delay="300">
                                                    <?= esc($banner['description']) ?>
                                                </p>
                                            <?php endif; ?>

                                            <div class="hero-buttons d-flex flex-column flex-sm-row gap-3 justify-content-center" data-aos="fade-up" data-aos-delay="400">
                                                <?php if ($banner['button_text'] && $banner['button_link']): ?>
                                                    <?php
                                                    // Convert relative URLs to absolute URLs
                                                    $buttonUrl = $banner['button_link'];
                                                    if (strpos($buttonUrl, 'http') !== 0) {
                                                        $buttonUrl = base_url(ltrim($buttonUrl, '/'));
                                                    }
                                                    ?>
                                                    <a href="<?= esc($buttonUrl) ?>" class="btn btn-primary btn-lg px-5 py-3">
                                                        <i class="fas fa-shopping-bag me-2"></i><?= esc($banner['button_text']) ?>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= base_url('products') ?>" class="btn btn-primary btn-lg px-5 py-3">
                                                        <i class="fas fa-shopping-bag me-2"></i>Shop Now
                                                    </a>
                                                <?php endif; ?>

                                                <?php if ($banner['button_text_2'] && $banner['button_link_2']): ?>
                                                    <?php
                                                    // Convert relative URLs to absolute URLs
                                                    $buttonUrl2 = $banner['button_link_2'];
                                                    if (strpos($buttonUrl2, 'http') !== 0) {
                                                        $buttonUrl2 = base_url(ltrim($buttonUrl2, '/'));
                                                    }
                                                    ?>
                                                    <a href="<?= esc($buttonUrl2) ?>" class="btn btn-outline-light btn-lg px-5 py-3">
                                                        <i class="fas fa-eye me-2"></i><?= esc($banner['button_text_2']) ?>
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= base_url('category/agarbatti-incense') ?>" class="btn btn-outline-light btn-lg px-5 py-3">
                                                        <i class="fas fa-eye me-2"></i>View Agarbatti
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Carousel Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    <?php else: ?>
        <!-- Default Banner if no banners exist -->
        <div class="hero-slide" style="background-image: url('https://images.unsplash.com/photo-1582735689369-4fe89db7114c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 text-center">
                            <h1 class="hero-title display-3 fw-bold mb-4">Premium Puja Samagri Online</h1>
                            <p class="hero-subtitle lead mb-5">Discover authentic and high-quality puja items including agarbatti, dhoop, diyas, and all essential spiritual accessories for your divine worship.</p>
                            <div class="hero-buttons d-flex flex-column flex-sm-row gap-3 justify-content-center">
                                <a href="<?= base_url('products') ?>" class="btn btn-primary btn-lg px-5 py-3">
                                    <i class="fas fa-shopping-bag me-2"></i>Shop Now
                                </a>
                                <a href="<?= base_url('category/agarbatti-incense') ?>" class="btn btn-outline-light btn-lg px-5 py-3">
                                    <i class="fas fa-eye me-2"></i>View Agarbatti
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

<!-- Categories Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Shop by Categories</h2>
            <p class="text-muted">Explore our wide range of spiritual and puja items</p>
        </div>

        <div class="row g-4">
            <?php foreach ($categories as $category): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card category-card h-100">
                        <div class="position-relative">
                            <?php if (!empty($category['image'])): ?>
                                <img src="<?= base_url('uploads/categories/' . esc($category['image'])) ?>"
                                    alt="<?= esc($category['name']) ?>"
                                    class="card-img-top category-image">
                            <?php else: ?>
                                <div class="card-img-top category-image-placeholder d-flex align-items-center justify-content-center">
                                    <?php
                                    $icons = [
                                        'agarbatti-incense' => 'fas fa-fire',
                                        'dhoop-sambrani' => 'fas fa-smoke',
                                        'puja-thali-accessories' => 'fas fa-circle',
                                        'diyas-candles' => 'fas fa-candle-holder',
                                        'flowers-garlands' => 'fas fa-seedling',
                                        'puja-oils-ghee' => 'fas fa-oil-can',
                                        'idols-statues' => 'fas fa-praying-hands',
                                        'puja-books-mantras' => 'fas fa-book'
                                    ];
                                    $icon = $icons[$category['slug']] ?? 'fas fa-star';
                                    ?>
                                    <i class="<?= $icon ?> fa-4x text-primary"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body d-flex flex-column text-center">
                            <h5 class="card-title"><?= esc($category['name']) ?></h5>
                            <p class="card-text text-muted flex-grow-1"><?= esc($category['description']) ?></p>
                            <a href="<?= base_url('category/' . esc($category['slug'])) ?>" class="btn btn-primary mt-auto">Explore</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Featured Products</h2>
            <p class="text-muted">Handpicked premium items for your spiritual needs</p>
        </div>

        <div class="row g-4">
            <?php foreach ($featuredProducts as $product): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card product-card h-100">
                        <div class="position-relative">
                            <img src="<?= $product['image'] ? base_url('uploads/products/' . esc($product['image'])) : 'https://via.placeholder.com/300x200/f8f9fa/6c757d?text=' . urlencode($product['name']) ?>"
                                class="card-img-top product-image" alt="<?= esc($product['name']) ?>">

                            <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                                <span class="position-absolute top-0 start-0 badge bg-danger m-2">
                                    <?= round((($product['price'] - $product['sale_price']) / $product['price']) * 100) ?>% OFF
                                </span>
                            <?php endif; ?>

                            <?php if ($product['is_featured']): ?>
                                <span class="position-absolute top-0 end-0 badge bg-warning m-2">
                                    <i class="fas fa-star"></i> Featured
                                </span>
                            <?php endif; ?>

                            <!-- Wishlist Button -->
                            <?php if (session()->get('is_logged_in')): ?>
                                <?php
                                $wishlistModel = new \App\Models\WishlistModel();
                                $inWishlist = $wishlistModel->isInWishlist(session()->get('user_id'), $product['id']);
                                ?>
                                <button class="btn btn-sm position-absolute wishlist-btn <?= $inWishlist ? 'btn-danger' : 'btn-outline-danger' ?>"
                                    style="bottom: 10px; right: 10px; z-index: 10;"
                                    onclick="toggleWishlist(<?= $product['id'] ?>, $(this))"
                                    title="<?= $inWishlist ? 'Remove from wishlist' : 'Add to wishlist' ?>">
                                    <i class="<?= $inWishlist ? 'fas' : 'far' ?> fa-heart"></i>
                                </button>
                            <?php endif; ?>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title"><?= esc($product['name']) ?></h6>
                            <p class="card-text text-muted small flex-grow-1"><?= esc($product['short_description']) ?></p>

                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                                            <span class="price-original small">₹<?= number_format($product['price'], 2) ?></span>
                                            <span class="price-sale">₹<?= number_format($product['sale_price'], 2) ?></span>
                                        <?php else: ?>
                                            <span class="price-sale">₹<?= number_format($product['price'], 2) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted">Stock: <?= $product['stock_quantity'] ?></small>
                                </div>

                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary btn-sm" onclick="addToCart(<?= $product['id'] ?>)">
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                    <a href="<?= base_url('product/' . esc($product['slug'])) ?>" class="btn btn-outline-primary btn-sm">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="text-center mt-5">
            <a href="<?= base_url('products') ?>" class="btn btn-primary btn-lg">View All Products</a>
        </div>
    </div>
</section>

<!-- Latest Products Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Latest Arrivals</h2>
            <p class="text-muted">Newest additions to our spiritual collection</p>
        </div>

        <div class="row g-4">
            <?php foreach (array_slice($latestProducts, 0, 8) as $product): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card product-card h-100">
                        <div class="position-relative">
                            <img src="<?= $product['image'] ? base_url('uploads/products/' . esc($product['image'])) : 'https://via.placeholder.com/300x200/f8f9fa/6c757d?text=' . urlencode($product['name']) ?>"
                                class="card-img-top product-image" alt="<?= esc($product['name']) ?>">

                            <span class="position-absolute top-0 start-0 badge bg-success m-2">New</span>

                            <!-- Wishlist Button -->
                            <?php if (session()->get('is_logged_in')): ?>
                                <?php
                                $wishlistModel = new \App\Models\WishlistModel();
                                $inWishlist = $wishlistModel->isInWishlist(session()->get('user_id'), $product['id']);
                                ?>
                                <button class="btn btn-sm position-absolute wishlist-btn <?= $inWishlist ? 'btn-danger' : 'btn-outline-danger' ?>"
                                    style="bottom: 10px; right: 10px; z-index: 10;"
                                    onclick="toggleWishlist(<?= $product['id'] ?>, $(this))"
                                    title="<?= $inWishlist ? 'Remove from wishlist' : 'Add to wishlist' ?>">
                                    <i class="<?= $inWishlist ? 'fas' : 'far' ?> fa-heart"></i>
                                </button>
                            <?php endif; ?>
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title"><?= esc($product['name']) ?></h6>
                            <p class="card-text text-muted small flex-grow-1"><?= esc($product['short_description']) ?></p>

                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                                            <span class="price-original small">₹<?= number_format($product['price'], 2) ?></span>
                                            <span class="price-sale">₹<?= number_format($product['sale_price'], 2) ?></span>
                                        <?php else: ?>
                                            <span class="price-sale">₹<?= number_format($product['price'], 2) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted">Stock: <?= $product['stock_quantity'] ?></small>
                                </div>

                                <div class="d-grid gap-2">
                                    <button class="btn btn-primary btn-sm" onclick="addToCart(<?= $product['id'] ?>)">
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                    <a href="<?= base_url('product/' . esc($product['slug'])) ?>" class="btn btn-outline-primary btn-sm">
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
</section>

<!-- Features Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3">
                <i class="fas fa-shipping-fast fa-3x mb-3"></i>
                <h5>Free Shipping</h5>
                <p>Free delivery on orders above ₹500</p>
            </div>
            <div class="col-md-3">
                <i class="fas fa-shield-alt fa-3x mb-3"></i>
                <h5>Authentic Products</h5>
                <p>100% genuine and blessed items</p>
            </div>
            <div class="col-md-3">
                <i class="fas fa-headset fa-3x mb-3"></i>
                <h5>24/7 Support</h5>
                <p>Round the clock customer service</p>
            </div>
            <div class="col-md-3">
                <i class="fas fa-undo fa-3x mb-3"></i>
                <h5>Easy Returns</h5>
                <p>Hassle-free return policy</p>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>