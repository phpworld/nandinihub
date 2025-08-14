<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('products') ?>">Products</a></li>
                    <li class="breadcrumb-item active">Search Results</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2">Search Results for "<?= esc($keyword) ?>"</h1>
                    <p class="text-muted">Found <?= count($products) ?> product(s) matching your search</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Form -->
    <div class="row mb-4">
        <div class="col-md-6">
            <form action="<?= base_url('products/search') ?>" method="GET" class="d-flex">
                <input type="text" name="q" class="form-control me-2" placeholder="Search products..." value="<?= esc($keyword) ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Browse Categories</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="<?= base_url('products') ?>" class="text-decoration-none">
                                <i class="fas fa-th-large me-2"></i>All Products
                            </a>
                        </li>
                        <?php foreach ($categories as $category): ?>
                            <li class="mb-2">
                                <a href="<?= base_url('category/' . esc($category['slug'])) ?>" class="text-decoration-none">
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
                                    <i class="<?= $icon ?> me-2"></i><?= esc($category['name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Search Results -->
        <div class="col-lg-9">
            <?php if (empty($products)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-search fa-4x text-muted mb-3"></i>
                    <h4>No Products Found</h4>
                    <p class="text-muted">We couldn't find any products matching "<?= esc($keyword) ?>". Try different keywords or browse our categories.</p>
                    <div class="mt-4">
                        <a href="/products" class="btn btn-primary me-2">Browse All Products</a>
                        <a href="/" class="btn btn-outline-primary">Go to Homepage</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($products as $product): ?>
                        <div class="col-lg-4 col-md-6">
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
                                    <small class="text-muted mb-2">Category: <?= esc($product['category_name']) ?></small>

                                    <div class="mt-auto">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <?php if (!empty($product['sale_price']) && $product['sale_price'] < $product['price']): ?>
                                                    <span class="price-original small"><?= format_currency($product['price']) ?></span>
                                                    <span class="price-sale"><?= format_currency($product['sale_price']) ?></span>
                                                <?php else: ?>
                                                    <span class="price-sale"><?= format_currency($product['price']) ?></span>
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
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>