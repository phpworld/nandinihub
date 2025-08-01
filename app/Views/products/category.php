<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container py-4">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item"><a href="/products">Products</a></li>
                    <li class="breadcrumb-item active"><?= esc($category['name']) ?></li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h2"><?= esc($category['name']) ?></h1>
                    <?php if (!empty($category['description'])): ?>
                        <p class="text-muted"><?= esc($category['description']) ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <span class="badge bg-primary"><?= count($products) ?> Products</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <!-- Filters -->
            <div class="card mb-3">
                <div class="card-header">
                    <h5 class="mb-0">Filters</h5>
                </div>
                <div class="card-body">
                    <form id="filterForm" method="GET" action="<?= base_url('category/' . esc($category['slug'])) ?>">
                        <!-- Price Range Filter -->
                        <div class="mb-4">
                            <h6>Price Range</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="min_price" class="form-control form-control-sm"
                                           placeholder="Min $" value="<?= esc($currentFilters['min_price'] ?? '') ?>"
                                           min="<?= $priceRange['min'] ?>" max="<?= $priceRange['max'] ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="max_price" class="form-control form-control-sm"
                                           placeholder="Max $" value="<?= esc($currentFilters['max_price'] ?? '') ?>"
                                           min="<?= $priceRange['min'] ?>" max="<?= $priceRange['max'] ?>">
                                </div>
                            </div>
                            <small class="text-muted">Range: $<?= $priceRange['min'] ?> - $<?= $priceRange['max'] ?></small>
                        </div>

                        <!-- Sort Filter -->
                        <div class="mb-4">
                            <h6>Sort By</h6>
                            <select name="sort" class="form-select form-select-sm">
                                <option value="newest" <?= ($currentFilters['sort'] ?? '') == 'newest' ? 'selected' : '' ?>>Newest First</option>
                                <option value="price_low" <?= ($currentFilters['sort'] ?? '') == 'price_low' ? 'selected' : '' ?>>Price: Low to High</option>
                                <option value="price_high" <?= ($currentFilters['sort'] ?? '') == 'price_high' ? 'selected' : '' ?>>Price: High to Low</option>
                                <option value="name" <?= ($currentFilters['sort'] ?? '') == 'name' ? 'selected' : '' ?>>Name A-Z</option>
                                <option value="featured" <?= ($currentFilters['sort'] ?? '') == 'featured' ? 'selected' : '' ?>>Featured</option>
                            </select>
                        </div>

                        <!-- Hidden search field to preserve search query -->
                        <?php if (!empty($currentFilters['search'])): ?>
                            <input type="hidden" name="q" value="<?= esc($currentFilters['search']) ?>">
                        <?php endif; ?>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">Apply Filters</button>
                            <a href="<?= base_url('category/' . esc($category['slug'])) ?>" class="btn btn-outline-secondary btn-sm">Clear All</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Categories -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">All Categories</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="<?= base_url('products') ?>" class="text-decoration-none">
                                <i class="fas fa-th-large me-2"></i>All Products
                            </a>
                        </li>
                        <?php foreach ($categories as $cat): ?>
                            <li class="mb-2">
                                <a href="<?= base_url('category/' . esc($cat['slug'])) ?>"
                                   class="text-decoration-none d-flex align-items-center <?= $cat['id'] == $category['id'] ? 'text-primary fw-bold' : '' ?>">
                                    <?php if (!empty($cat['image'])): ?>
                                        <img src="<?= base_url('uploads/categories/' . esc($cat['image'])) ?>"
                                             alt="<?= esc($cat['name']) ?>"
                                             class="rounded me-2" style="width: 24px; height: 24px; object-fit: cover;">
                                    <?php else: ?>
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
                                        $icon = $icons[$cat['slug']] ?? 'fas fa-star';
                                        ?>
                                        <i class="<?= $icon ?> me-2"></i>
                                    <?php endif; ?>
                                    <?= esc($cat['name']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <!-- Results Info and Active Filters -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="mb-0"><?= count($products) ?> Products Found in <?= esc($category['name']) ?></h5>
                </div>

                <!-- Active Filters -->
                <?php if (!empty($currentFilters) && (isset($currentFilters['min_price']) || isset($currentFilters['max_price']) || isset($currentFilters['sort']) || isset($currentFilters['search']))): ?>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($currentFilters as $key => $value): ?>
                            <?php if ($key === 'category_id') continue; // Skip category filter as it's already shown ?>
                            <?php if ($key === 'search'): ?>
                                <span class="badge bg-primary">Search: "<?= esc($value) ?>"</span>
                            <?php elseif ($key === 'min_price'): ?>
                                <span class="badge bg-success">Min: $<?= esc($value) ?></span>
                            <?php elseif ($key === 'max_price'): ?>
                                <span class="badge bg-success">Max: $<?= esc($value) ?></span>
                            <?php elseif ($key === 'sort'): ?>
                                <span class="badge bg-warning">Sort: <?= esc(ucfirst(str_replace('_', ' ', $value))) ?></span>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (empty($products)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                    <h4>No Products in This Category</h4>
                    <p class="text-muted">We're working on adding more products to this category. Please check back soon!</p>
                    <a href="<?= base_url('products') ?>" class="btn btn-primary">Browse All Products</a>
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
                                            style="top: 10px; right: 10px; z-index: 10;"
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
                                                    <span class="price-original small">$<?= number_format($product['price'], 2) ?></span>
                                                    <span class="price-sale">$<?= number_format($product['sale_price'], 2) ?></span>
                                                <?php else: ?>
                                                    <span class="price-sale">$<?= number_format($product['price'], 2) ?></span>
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
