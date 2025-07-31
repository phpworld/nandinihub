<?php
$settingModel = new \App\Models\SettingModel();
$siteLogo = $settingModel->getSetting('site_logo', '');
$siteFavicon = $settingModel->getSetting('site_favicon', '');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Microdose Mushroom' ?></title>
    <meta name="description" content="<?= $meta_description ?? '' ?>">

    <?php
    // Load Google Analytics if enabled
    $settingModel = new \App\Models\SettingModel();
    $gaEnabled = $settingModel->getSetting('google_analytics_enabled', false);
    $gaId = $settingModel->getSetting('google_analytics_id', '');

    if ($gaEnabled && !empty($gaId)):
    ?>
        <!-- Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?= esc($gaId) ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }
            gtag('js', new Date());
            gtag('config', '<?= esc($gaId) ?>');
        </script>
    <?php endif; ?>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- AOS (Animate On Scroll) CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #b8956a;
            --secondary-color: #86b85e;
            --accent-color: #ffd23f;
            --dark-color: #e2dfcc;
            --light-color: #ecf0f1;
        }

        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        .product-card {
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        .product-image {
            height: 200px;
            object-fit: cover;
        }

        .price-original {
            text-decoration: line-through;
            color: #6c757d;
        }

        .price-sale {
            color: var(--primary-color);
            font-weight: bold;
        }

        .cart-badge,
        .wishlist-badge {
            background-color: var(--primary-color);
        }

        .wishlist-btn {
            border-radius: 50%;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .wishlist-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
        }

        .wishlist-btn i {
            font-size: 14px;
        }

        footer {
            background-color: var(--dark-color);
            color: black;
        }

        /* Hero Slider Styles */
        .hero-slider-section {
            position: relative;
            width: 100%;
            height: 600px;
            overflow: hidden;
        }

        .hero-slide {
            position: relative;
            width: 100%;
            height: 600px;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.4));
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            color: white;
            text-align: center;
            width: 100%;
            padding: 2rem 0;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            margin-bottom: 1.5rem;
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            margin-bottom: 2rem;
            opacity: 0.95;
        }

        .hero-description {
            font-size: 1.1rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
            opacity: 0.9;
        }

        .hero-buttons .btn {
            border-radius: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .hero-buttons .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
        }

        .hero-buttons .btn-primary {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border: none;
        }

        .hero-buttons .btn-outline-light {
            border: 2px solid rgba(255, 255, 255, 0.8);
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .hero-buttons .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: white;
        }

        /* Carousel Controls */
        .carousel-control-prev,
        .carousel-control-next {
            width: 5%;
            opacity: 0.8;
        }

        .carousel-control-prev:hover,
        .carousel-control-next:hover {
            opacity: 1;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            width: 2rem;
            height: 2rem;
            background-size: 100%, 100%;
        }

        /* Carousel Indicators */
        .carousel-indicators {
            bottom: 2rem;
        }

        .carousel-indicators [data-bs-target] {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin: 0 5px;
            background-color: rgba(255, 255, 255, 0.5);
            border: 2px solid rgba(255, 255, 255, 0.8);
        }

        .carousel-indicators .active {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .hero-slide {
                height: 500px;
            }

            .hero-slider-section {
                height: 500px;
            }

            .hero-title {
                font-size: 3rem;
            }
        }

        @media (max-width: 768px) {
            .hero-slide {
                height: 450px;
            }

            .hero-slider-section {
                height: 450px;
            }

            .hero-title {
                font-size: 2.5rem;
            }

            .hero-subtitle {
                font-size: 1.1rem;
            }

            .hero-buttons .btn {
                padding: 0.75rem 2rem !important;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            .hero-slide {
                height: 400px;
            }

            .hero-slider-section {
                height: 400px;
            }

            .hero-title {
                font-size: 2rem;
            }

            .hero-subtitle {
                font-size: 1rem;
            }

            .hero-buttons {
                flex-direction: column !important;
            }

            .hero-buttons .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }
        }

        .category-card {
            transition: all 0.3s ease;
            overflow: hidden;
            border: none;
            border-radius:0px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        .category-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .category-image-placeholder {
            height: 200px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 1px solid #dee2e6;
        }

        .alert-fixed-top-right {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1055;
            min-width: 300px;
            max-width: 90vw;
        }
    </style>

    <?php
    if (!empty($siteFavicon) && file_exists(FCPATH . $siteFavicon)) {
        echo '<link rel="icon" type="image/x-icon" href="' . base_url($siteFavicon) . '">';
    }
    ?>
</head>

<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?= base_url('/') ?>">
                <?php if (!empty($siteLogo) && file_exists(FCPATH . $siteLogo)): ?>
                    <img src="<?= base_url($siteLogo) ?>" alt="Site Logo" style="height:50px;max-width:200px;object-fit:contain;" class="me-2">
                <?php else: ?>
                    <i class="fas fa-mushroom me-2"></i>Microdose Mushroom
                <?php endif; ?>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('/') ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('products') ?>">All Products</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown">
                            Categories
                        </a>
                        <ul class="dropdown-menu" id="categoriesMenu">
                            <?php
                            // Load categories for navbar
                            $categoryModel = new \App\Models\CategoryModel();
                            $navbarCategories = $categoryModel->getActiveCategories();
                            ?>
                            <?php foreach ($navbarCategories as $category): ?>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center" href="<?= base_url('category/' . esc($category['slug'])) ?>">
                                        <?php if (!empty($category['image'])): ?>
                                            <img src="<?= base_url('uploads/categories/' . esc($category['image'])) ?>"
                                                alt="<?= esc($category['name']) ?>"
                                                class="rounded me-2" style="width: 20px; height: 20px; object-fit: cover;">
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
                                            $icon = $icons[$category['slug']] ?? 'fas fa-star';
                                            ?>
                                            <i class="<?= $icon ?> me-2 text-primary" style="width: 20px;"></i>
                                        <?php endif; ?>
                                        <?= esc($category['name']) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= base_url('products') ?>">
                                    <i class="fas fa-th-large me-2 text-primary" style="width: 20px;"></i>
                                    View All Products
                                </a>
                            </li>
                        </ul>
                    </li>

                    <?php
                    // Load pages for header navigation
                    $pageModel = new \App\Models\PageModel();
                    $headerPages = $pageModel->getHeaderPages();
                    ?>
                    <?php foreach ($headerPages as $page): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('pages/' . esc($page['slug'])) ?>">
                                <?= esc($page['title']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <!-- Search Form -->
                <form class="d-flex me-3" action="<?= base_url('products/search') ?>" method="GET">
                    <input class="form-control me-2" type="search" name="q" placeholder="Search products..." value="<?= esc($keyword ?? '') ?>">
                    <button class="btn btn-outline-primary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </form>

                <!-- User Menu -->
                <ul class="navbar-nav">
                    <?php if (session()->get('is_logged_in')): ?>
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="<?= base_url('wishlist') ?>">
                                <i class="fas fa-heart"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill wishlist-badge" id="wishlistCount">
                                    0
                                </span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link position-relative" href="<?= base_url('cart') ?>">
                            <i class="fas fa-shopping-cart"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill cart-badge" id="cartCount">
                                0
                            </span>
                        </a>
                    </li>

                    <?php if (session()->get('is_logged_in')): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> <?= esc(session()->get('user_name')) ?>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= base_url('profile') ?>">My Profile</a></li>
                                <li><a class="dropdown-item" href="<?= base_url('orders') ?>">My Orders</a></li>
                                <li><a class="dropdown-item" href="<?= base_url('addresses') ?>">My Addresses</a></li>
                                <li><a class="dropdown-item" href="<?= base_url('wishlist') ?>">My Wishlist</a></li>
                                <?php
                                $userModel = new \App\Models\UserModel();
                                $user = $userModel->find(session()->get('user_id'));
                                if ($user && $user['role'] === 'admin'):
                                ?>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li><a class="dropdown-item" href="<?= base_url('admin/dashboard') ?>">
                                            <i class="fas fa-cog me-2"></i>Admin Panel
                                        </a></li>
                                <?php endif; ?>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item" href="<?= base_url('logout') ?>">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('login') ?>">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?= base_url('register') ?>">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>
                        <?php if (!empty($siteLogo) && file_exists(FCPATH . $siteLogo)): ?>
                            <img src="<?= base_url($siteLogo) ?>" alt="Site Logo" style="height:90px;max-width:270px;object-fit:contain;" class="me-2 mb-2">
                        <?php else: ?>
                            <i class="fas fa-mushroom me-2"></i>Microdose Mushroom
                        <?php endif; ?>
                    </h5>
                    <p>Your trusted source for premium quality microdose mushrooms and psychedelic products. Bringing natural wellness to your doorstep.</p>
                </div>
                <div class="col-md-2">
                    <h6>Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= base_url('/') ?>" class="text-dark text-decoration-none">Home</a></li>
                        <li><a href="<?= base_url('products') ?>" class="text-dark text-decoration-none">Products</a></li>
                        <?php
                        // Load pages for footer navigation
                        $pageModel = new \App\Models\PageModel();
                        $footerPages = $pageModel->getFooterPages();
                        ?>
                        <?php foreach ($footerPages as $page): ?>
                            <li><a href="<?= base_url('pages/' . esc($page['slug'])) ?>" class="text-dark text-decoration-none"><?= esc($page['title']) ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Categories</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?= base_url('category/microdose-capsules') ?>" class="text-dark text-decoration-none">Microdose Capsules</a></li>
                        <li><a href="<?= base_url('category/dried-mushrooms') ?>" class="text-dark text-decoration-none">Dried Mushrooms</a></li>
                        <li><a href="<?= base_url('category/puja-thali-accessories') ?>" class="text-dark text-decoration-none">Puja Thali</a></li>
                        <li><a href="<?= base_url('category/diyas-candles') ?>" class="text-dark text-decoration-none">Diyas & Candles</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h6>Contact Info</h6>
                    <p><i class="fas fa-phone me-2"></i>+1 (xxx)xxx xxxx</p>
                    <p><i class="fas fa-envelope me-2"></i>info@microdosemushroom.com</p>
                    <div class="mt-3">
                        <a href="#" class="text-dark me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-dark me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-dark me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-dark"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p>&copy; 2025 Microdose Mushroom. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- AOS (Animate On Scroll) JS -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        // CSRF Token for AJAX requests
        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type) && !this.crossDomain) {
                    xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
                }
            }
        });

        // Load cart count on page load
        $(document).ready(function() {
            loadCartCount();
            loadWishlistCount();
            loadCategories();

            // Initialize AOS
            AOS.init({
                duration: 1000,
                easing: 'ease-in-out',
                once: true,
                mirror: false
            });
        });

        function loadCartCount() {
            $.get('<?= base_url('cart/count') ?>', function(data) {
                $('#cartCount').text(data.cartCount || 0);
            });
        }

        function loadWishlistCount() {
            <?php if (session()->get('is_logged_in')): ?>
                $.get('<?= base_url('wishlist/count') ?>', function(data) {
                    $('#wishlistCount').text(data.count || 0);
                });
            <?php endif; ?>
        }

        function updateWishlistCount(count) {
            $('#wishlistCount').text(count || 0);
        }

        function loadCategories() {
            // This would typically load from an API endpoint
            // For now, we'll use static categories
        }

        // Add to cart function
        function addToCart(productId, quantity = 1) {
            $.post('<?= base_url('cart/add') ?>', {
                product_id: productId,
                quantity: quantity,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            }, function(response) {
                if (response.success) {
                    $('#cartCount').text(response.cartCount);
                    showToast('success', response.message);
                } else {
                    showToast('error', response.message);
                }
            }).fail(function() {
                showToast('error', 'Failed to add item to cart');
            });
        }

        // Toggle wishlist function
        function toggleWishlist(productId, button) {
            <?php if (!session()->get('is_logged_in')): ?>
                window.location.href = '<?= base_url('login') ?>';
                return;
            <?php endif; ?>

            const originalHtml = button.html();
            button.prop('disabled', true);

            $.post('<?= base_url('wishlist/toggle') ?>', {
                product_id: productId,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            }, function(response) {
                if (response.success) {
                    updateWishlistCount(response.wishlistCount);

                    // Update button appearance
                    if (response.in_wishlist) {
                        button.removeClass('btn-outline-danger').addClass('btn-danger');
                        button.find('i').removeClass('far').addClass('fas');
                    } else {
                        button.removeClass('btn-danger').addClass('btn-outline-danger');
                        button.find('i').removeClass('fas').addClass('far');
                    }

                    showToast('success', response.message);
                } else {
                    showToast('error', response.message);
                }
            }).fail(function() {
                showToast('error', 'Failed to update wishlist');
            }).always(function() {
                button.prop('disabled', false);
            });
        }

        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show alert-fixed-top-right" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            $('body').append(alertHtml);

            // Auto dismiss after 3 seconds
            setTimeout(function() {
                $('.alert.alert-fixed-top-right').alert('close');
            }, 3000);
        }
    </script>

    <?= $this->renderSection('scripts') ?>
</body>

</html>