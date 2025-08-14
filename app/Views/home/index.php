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
        <div class="hero-slide" style="background-image: url('<?= base_url('uploads/banners/banner.jpg') ?>');">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-lg-8 text-center">
                            <h1 class="hero-title display-3 fw-bold mb-4">DISCOVER MAGIC MUSHROOMS PRODUCTS</h1>
                            <p class="hero-subtitle lead mb-5"> Explore a vast selection of premium magic mushrooms from top suppliers.</p>
                            <div class="hero-buttons d-flex flex-column flex-sm-row gap-3 justify-content-center">
                                <a href="<?= base_url('products') ?>" class="btn btn-primary btn-lg px-5 py-3">
                                    <i class="fas fa-shopping-bag me-2"></i>Shop Now
                                </a>
                                <a href="<?= base_url('category/microdose-mushrooms') ?>" class="btn btn-outline-light btn-lg px-5 py-3">
                                    <i class="fas fa-eye me-2"></i>View MAGIC MUSHROOMS
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
<section class="category-section py-5" style="background-color: #fef5f3;">
  <style>
    .category-section {
      font-family: 'Georgia', serif;
      color: #1a1a1a;
    }
    .category-section .section-title {
      font-size: 2rem;
      font-weight: 500;
      text-align: center;
      margin-bottom: 3rem;
    }
    .category-section .card-icon {
      max-width: 96px;
      height: auto;
      margin: 0 auto 1.5rem auto;
      display: block;
      stroke: currentColor;
      fill: none;
    }
    .category-section .category-image {
      width: 150px;
      height: 150px;
      object-fit: cover;
      margin: 0 auto 1.5rem auto;
      display: block;
      border-radius: 8px;
      transition: transform 0.3s ease;
    }
    .category-section .category-image:hover {
      transform: scale(1.05);
    }
    .category-section .card-title {
      font-size: 1.25rem;
      font-weight: 500;
      margin-bottom: 0.5rem;
      text-align: center;
    }
    .category-section .card-text {
      font-size: 0.95rem;
      text-align: center;
      margin-bottom: 1rem;
      color: #4a4a4a;
    }
    .category-section .read-more {
      display: block;
      text-align: center;
      font-weight: 700;
      letter-spacing: 0.06em;
      font-size: 0.85rem;
      color: #1a1a1a;
      cursor: pointer;
      text-decoration: none;
      transition: all 0.3s ease;
    }
    .category-section .read-more:hover {
      text-decoration: underline;
      color: #9c5539;
      transform: translateY(-2px);
    }
    .category-section .card {
      transition: transform 0.3s ease;
    }
    .category-section .card:hover {
      transform: translateY(-5px);
    }
  </style>

  <div class="container">
    <h2 class="section-title">Best Shop for Magic Mushrooms &amp; Edibles</h2>
    <div class="row justify-content-center g-4">
      <?php
      // Get first 4 categories for display, or all if less than 4
      $displayCategories = array_slice($categories, 0, 4);

      // Default SVG icons for categories without images
      $defaultIcons = [
        'dried-mushrooms' => '<path stroke-width="2" stroke-linejoin="round" d="M32 4c-4 12-10 32-8 40 2 8 8 8 12 8s10 0 12-8c2-8-4-28-8-40zM24 32c4 0 0-14 8-14s4 14 8 14" /><path stroke-width="2" stroke-linejoin="round" d="M20 48c4-6 24-6 28 0" />',
        'microdose-mushrooms' => '<path d="M19 20c6.5-8 20 8 14 14l-14 14c-7 7-12-7-6-11l18-18"/><line x1="26" y1="31" x2="38" y2="43"/>',
        'mushroom-edibles' => '<rect x="16" y="12" width="32" height="40" /><path d="M16 28h32"/><path d="M16 44h32"/>',
        'magic-mushrooms' => '<path d="M32 8c4 6 18 22 10 36-1 2-6 4-10 4s-9-2-10-4c-9-14 6-30 10-36z" /><path d="M22 50c-5 0-12-5-8-10s6-6 8-6" /><path d="M42 50c5 0 12-5 8-10s-6-6-8-6" /><circle cx="32" cy="12" r="2"/><circle cx="28" cy="18" r="1"/><circle cx="36" cy="18" r="1"/>',
        'default' => '<circle cx="32" cy="32" r="20"/><path d="M32 12v40"/><path d="M12 32h40"/>'
      ];
      ?>

      <?php foreach ($displayCategories as $category): ?>
      <div class="col-sm-6 col-md-3">
        <div class="card border-0 bg-transparent h-100">
          <?php if (!empty($category['image'])): ?>
            <img src="<?= base_url('uploads/categories/' . esc($category['image'])) ?>"
                 alt="<?= esc($category['name']) ?>"
                 class="category-image">
          <?php else: ?>
            <svg class="card-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" aria-hidden="true" role="img" fill="none" stroke="currentColor" stroke-width="2" stroke-linejoin="round">
              <?= $defaultIcons[$category['slug']] ?? $defaultIcons['default'] ?>
            </svg>
          <?php endif; ?>
          <div class="card-body px-0 d-flex flex-column">
            <h5 class="card-title"><?= esc($category['name']) ?></h5>
            <p class="card-text flex-grow-1"><?= esc($category['description']) ?: 'Explore our ' . esc($category['name']) . ' collection' ?></p>
            <?php if (isset($category['product_count']) && $category['product_count'] > 0): ?>
              <small class="text-muted d-block text-center mb-2"><?= $category['product_count'] ?> product<?= $category['product_count'] != 1 ? 's' : '' ?></small>
            <?php endif; ?>
            <a href="<?= base_url('category/' . esc($category['slug'])) ?>" class="read-more mt-auto">READ MORE</a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA Section -->
<section class="cta-section py-5" style="background-color: #e8ebdd;">
  <style>
    :root {
      --bg-cream: #f5f3f0;
      --text-dark: #2d2d2d;
      --text-muted: #6b7280;
      --accent-brown: #b8956a;
      --accent-green: #86b85e;
    }

    .cta-section {
      color: #1c1c1c;
    }
    .cta-section .container-custom {
      max-width: 1200px;
      padding-top: 2rem;
      padding-bottom: 2rem;
    }
    .cta-section .title-sm {
      font-size: 0.85rem;
      letter-spacing: 0.12em;
      color: #5a5a5a;
      font-weight: 600;
      text-transform: uppercase;
      margin-bottom: 0.4rem;
    }
    .cta-section h1.display-5 {
      font-weight: 700;
      font-family: "Georgia", serif;
      margin-bottom: 1rem;
      letter-spacing: 0.02em;
    }
    .cta-section p.desc {
      font-size: 1rem;
      color: #5b5b5b;
      max-width: 420px;
      margin-bottom: 2.5rem;
      line-height: 1.4;
    }
    .cta-section img.mushroom-img {
      max-width: 100%;
      height: auto;
      filter: drop-shadow(1px 1px 1px rgba(0,0,0,0.15));
      transition: transform 0.3s ease;
    }
    .cta-section img.mushroom-img:hover {
      transform: scale(1.05);
    }
    .cta-section .features-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 2rem;
      margin-bottom: 3rem;
    }
    .cta-section .feature-item {
      display: flex;
      align-items: center;
      gap: 1rem;
      transition: transform 0.3s ease;
    }
    .cta-section .feature-item:hover {
      transform: translateX(5px);
    }
    .cta-section .feature-icon {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      border: 2px solid var(--text-muted);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.25rem;
      flex-shrink: 0;
      transition: all 0.3s ease;
    }
    .cta-section .feature-item:hover .feature-icon {
      border-color: var(--accent-brown);
      background-color: var(--accent-brown);
      color: white;
    }
    .cta-section .feature-text {
      font-weight: 500;
      color: var(--text-dark);
    }
    .cta-section .shop-button {
      background-color: var(--accent-brown);
      color: white;
      border: none;
      padding: 12px 30px;
      font-size: 0.8rem;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border-radius: 4px;
      transition: all 0.3s ease;
      text-decoration: none;
      display: inline-block;
    }
    .cta-section .shop-button:hover {
      background-color: var(--accent-green);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
      color: white;
    }
    .cta-section .contact-info {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
    }
    .cta-section .contact-item {
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    .cta-section .contact-icon {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: var(--accent-brown);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1rem;
    }
    .cta-section .contact-details h6 {
      font-size: 0.875rem;
      color: var(--text-muted);
      margin-bottom: 0.25rem;
    }
    .cta-section .contact-details p {
      font-weight: 500;
      font-size: 14px;
      color: var(--text-dark);
      margin: 0;
    }

    @media (max-width: 768px) {
      .cta-section .features-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
      }
      .cta-section .contact-info {
        flex-direction: column;
        gap: 1.5rem;
      }
      .cta-section .contact-item {
        justify-content: center;
      }
    }
  </style>

  <div class="container container-custom">
    <div class="row align-items-center g-4">
      <div class="col-lg-6 order-lg-1 order-2 text-center">
        <div class="mushroom-image">
          <img class="mushroom-img"
               src="<?= base_url('uploads/cta/mushroom.png') ?>"
               alt="Premium magic mushrooms artwork with vibrant patterns"
               onerror="this.style.display='none'" />
        </div>
      </div>
      <div class="col-lg-6 order-lg-2 order-1">
        <div class="content-section">
          <div class="title-sm">Carefully Formulated Products</div>
          <h1 class="display-5">Shop USA's Best Magic Mushrooms</h1>
          <p class="desc">
            Explore a vast selection of premium magic mushrooms from the USA's top suppliers.
            Elevate your experience with our high-quality products.
          </p>

          <div class="features-grid">
            <div class="feature-item">
              <div class="feature-icon">🌿</div>
              <div class="feature-text">Natural & Pure</div>
            </div>
            <div class="feature-item">
              <div class="feature-icon">🚫</div>
              <div class="feature-text">Lab Tested</div>
            </div>
            <div class="feature-item">
              <div class="feature-icon">✓</div>
              <div class="feature-text">Certified Product</div>
            </div>
            <div class="feature-item">
              <div class="feature-icon">⚡</div>
              <div class="feature-text">Fast Delivery</div>
            </div>
          </div>

          <div class="contact-info">
            <div class="contact-item">
              <a href="<?= base_url('products') ?>" class="shop-button">Shop All</a>
            </div>
            <div class="contact-item">
              <div class="contact-icon">📞</div>
              <div class="contact-details">
                <h6>Call Us Anytime</h6>
                <p>+1 (720) 619-1262</p>
              </div>
            </div>
            <div class="contact-item">
              <div class="contact-icon">✉️</div>
              <div class="contact-details">
                <h6>Email Us Anytime</h6>
                <p>contact@shroomwonders.com</p>
              </div>
            </div>
          </div>
        </div>
      </div>
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

        <div class="text-center mt-5">
            <a href="<?= base_url('products') ?>" class="btn btn-primary btn-lg">View All Products</a>
        </div>
    </div>
</section>
 
<!-- Benefits Section -->
<section class="benefits-section py-5" style="background-color: #fef9f6;">
  <style>
    .benefits-section {
      font-family: 'Segoe UI', sans-serif;
      color: #333;
    }
    .benefits-section .section-title {
      font-size: 2.5rem;
      font-weight: bold;
      margin-bottom: 0.5rem;
    }
    .benefits-section .subheadline {
      font-size: 0.95rem;
      color: #7a7a7a;
      margin-bottom: 3rem;
    }
    .benefits-section .benefit-title {
      font-weight: 600;
      font-size: 1rem;
      margin-bottom: 0.3rem;
    }
    .benefits-section .benefit-box {
      margin-bottom: 3rem;
      position: relative;
      padding-left: 3rem;
      transition: all 0.4s ease;
      opacity: 0;
      transform: translateY(30px);
      animation: fadeSlideUp 1s ease forwards;
    }
    .benefits-section .benefit-box:hover {
      transform: translateY(-5px) scale(1.02);
    }
    .benefits-section .number-label {
      position: absolute;
      left: 0;
      top: -5px;
      font-size: 2.8rem;
      font-weight: bold;
      color: #f0e6df;
      z-index: 0;
    }
    .benefits-section .benefit-text {
      position: relative;
      z-index: 1;
    }
    .benefits-section .benefit-text p {
      font-size: 0.87rem;
      margin-bottom: 0;
      color: #5e5e5e;
    }
    .benefits-section .center-image-wrapper {
      text-align: center;
      transition: transform 0.6s ease;
    }
    .benefits-section .center-image-wrapper:hover {
      transform: scale(1.05);
    }
    .benefits-section .center-image {
      width: 100%;
      max-width: 600px;
      height: auto;
      opacity: 0;
      transform: scale(0.85);
      animation: zoomIn 1.5s ease forwards;
    }
    @keyframes fadeSlideUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    @keyframes zoomIn {
      to {
        opacity: 1;
        transform: scale(1);
      }
    }
    .benefits-section .delay-1 { animation-delay: 0.2s; }
    .benefits-section .delay-2 { animation-delay: 0.4s; }
    .benefits-section .delay-3 { animation-delay: 0.6s; }
    .benefits-section .delay-4 { animation-delay: 0.8s; }
    .benefits-section .delay-5 { animation-delay: 1s; }
    .benefits-section .delay-6 { animation-delay: 1.2s; }
    .benefits-section .delay-7 { animation-delay: 1.4s; }
    .benefits-section .delay-8 { animation-delay: 1.6s; }
  </style>

  <div class="container text-center">
    <h6 class="text-uppercase text-muted">Magic Mushrooms for Holistic Well-Being</h6>
    <h1 class="section-title">8 Benefits of Magic Mushroom</h1>
    <p class="subheadline">Discover the myriad ways Magic mushrooms can enhance mental health and personal growth.</p>
  </div>

  <div class="container">
    <div class="row align-items-center">
      <!-- Left Column -->
      <div class="col-md-4">
        <div class="benefit-box delay-1">
          <div class="number-label">01</div>
          <div class="benefit-text">
            <div class="benefit-title">MENTAL HEALTH SUPPORT</div>
            <p>Magic mushrooms have shown promise in treating various mental health conditions e.g. depression, anxiety, and PTSD.</p>
          </div>
        </div>
        <div class="benefit-box delay-2">
          <div class="number-label">02</div>
          <div class="benefit-text">
            <div class="benefit-title">SPIRITUAL EXPLORATION</div>
            <p>Many individuals report profound spiritual experiences and enhanced introspection when consuming psilocybin mushrooms.</p>
          </div>
        </div>
        <div class="benefit-box delay-3">
          <div class="number-label">03</div>
          <div class="benefit-text">
            <div class="benefit-title">CREATIVITY ENHANCEMENT</div>
            <p>Some users claim that psilocybin mushrooms can boost creativity and problem-solving abilities, making them popular among artists and writers.</p>
          </div>
        </div>
        <div class="benefit-box delay-4">
          <div class="number-label">04</div>
          <div class="benefit-text">
            <div class="benefit-title">REDUCED FEAR OF DEATH</div>
            <p>Research suggests that psilocybin can induce mystical experiences that lead to a decreased fear of death.</p>
          </div>
        </div>
      </div>

      <!-- Center Image -->
      <div class="col-md-4 center-image-wrapper">
        <img src="<?= base_url('public/assets/images/benifits.png') ?>" alt="Magic Mushrooms" class="center-image">
      </div>

      <!-- Right Column -->
      <div class="col-md-4">
        <div class="benefit-box delay-5">
          <div class="number-label">05</div>
          <div class="benefit-text">
            <div class="benefit-title">NEUROPLASTICITY PROMOTION</div>
            <p>Magic Shrooms has been linked to increased neuroplasticity, the brain's ability to reorganize and form new neural connections.</p>
          </div>
        </div>
        <div class="benefit-box delay-6">
          <div class="number-label">06</div>
          <div class="benefit-text">
            <div class="benefit-title">ADDICTION TREATMENT</div>
            <p>Studies indicate that psilocybin therapy may aid in overcoming substance addiction by disrupting harmful thought patterns.</p>
          </div>
        </div>
        <div class="benefit-box delay-7">
          <div class="number-label">07</div>
          <div class="benefit-text">
            <div class="benefit-title">IMPROVED MOOD</div>
            <p>Magic mushrooms have been associated with a temporary elevation in mood, leading to feelings of happiness, contentment, and emotional stability.</p>
          </div>
        </div>
        <div class="benefit-box delay-8">
          <div class="number-label">08</div>
          <div class="benefit-text">
            <div class="benefit-title">CONNECTION WITH NATURE</div>
            <p>Many users report feeling a stronger connection to nature and the environment after consuming psilocybin mushrooms.</p>
          </div>
        </div>
      </div>
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
    </div>
</section>

<!-- Features Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3">
                <i class="fas fa-shipping-fast fa-3x mb-3"></i>
                <h5>Free Shipping</h5>
                <p>Free delivery on orders above <?= format_currency(500) ?></p>
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

<!-- Testimonials Section -->
<section class="testimonials-section py-5" style="background: linear-gradient(135deg, #fef9f6 0%, #f8f4f1 100%);">
  <style>
    .testimonials-section {
      font-family: 'Segoe UI', sans-serif;
      color: #333;
    }
    .testimonials-section .section-title {
      font-size: 2.5rem;
      font-weight: bold;
      margin-bottom: 0.5rem;
      color: #2c3e50;
    }
    .testimonials-section .section-subtitle {
      font-size: 1.1rem;
      color: #7a7a7a;
      margin-bottom: 3rem;
    }
    .testimonials-section .testimonial-card {
      background: #ffffff;
      border-radius: 20px;
      padding: 2rem;
      box-shadow: 0 10px 30px rgba(0,0,0,0.08);
      border: 1px solid rgba(156, 85, 57, 0.1);
      transition: all 0.4s ease;
      height: 100%;
      position: relative;
      overflow: hidden;
    }
    .testimonials-section .testimonial-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #9c5539, #d4a574);
    }
    .testimonials-section .testimonial-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(156, 85, 57, 0.15);
    }
    .testimonials-section .quote-icon {
      font-size: 3rem;
      color: #9c5539;
      opacity: 0.3;
      margin-bottom: 1rem;
    }
    .testimonials-section .testimonial-text {
      font-size: 1rem;
      line-height: 1.7;
      color: #555;
      margin-bottom: 1.5rem;
      font-style: italic;
    }
    .testimonials-section .client-info {
      display: flex;
      align-items: center;
      margin-top: auto;
    }
    .testimonials-section .client-avatar {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      object-fit: cover;
      margin-right: 1rem;
      border: 3px solid #9c5539;
    }
    .testimonials-section .client-avatar-placeholder {
      width: 60px;
      height: 60px;
      border-radius: 50%;
      background: linear-gradient(135deg, #9c5539, #d4a574);
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 1rem;
      color: white;
      font-weight: bold;
      font-size: 1.5rem;
    }
    .testimonials-section .client-details h6 {
      margin: 0;
      font-weight: 600;
      color: #2c3e50;
      font-size: 1.1rem;
    }
    .testimonials-section .client-details .position {
      color: #9c5539;
      font-size: 0.9rem;
      margin: 0;
    }
    .testimonials-section .client-details .location {
      color: #7a7a7a;
      font-size: 0.85rem;
      margin: 0;
    }
    .testimonials-section .rating {
      margin-bottom: 1rem;
    }
    .testimonials-section .rating .star {
      color: #ffc107;
      font-size: 1.2rem;
      margin-right: 2px;
    }
    .testimonials-section .rating .star.empty {
      color: #e9ecef;
    }
    .testimonials-section .testimonial-carousel {
      position: relative;
    }
    .testimonials-section .carousel-control-prev,
    .testimonials-section .carousel-control-next {
      width: 50px;
      height: 50px;
      background: #9c5539;
      border-radius: 50%;
      top: 50%;
      transform: translateY(-50%);
      border: none;
      opacity: 0.8;
    }
    .testimonials-section .carousel-control-prev:hover,
    .testimonials-section .carousel-control-next:hover {
      opacity: 1;
      background: #7a4229;
    }
    .testimonials-section .carousel-control-prev {
      left: -25px;
    }
    .testimonials-section .carousel-control-next {
      right: -25px;
    }
    .testimonials-section .carousel-indicators {
      bottom: -50px;
    }
    .testimonials-section .carousel-indicators button {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      background: #9c5539;
      opacity: 0.5;
      border: none;
    }
    .testimonials-section .carousel-indicators button.active {
      opacity: 1;
    }
    @media (max-width: 768px) {
      .testimonials-section .carousel-control-prev,
      .testimonials-section .carousel-control-next {
        display: none;
      }
    }
  </style>

  <div class="container">
    <div class="text-center mb-5">
      <h2 class="section-title">What Our Customers Say</h2>
      <p class="section-subtitle">Real experiences from our valued customers who trust our premium magic mushroom products</p>
    </div>

    <?php if (!empty($testimonials)): ?>
    <div id="testimonialsCarousel" class="carousel slide testimonial-carousel" data-bs-ride="carousel" data-bs-interval="5000">
      <div class="carousel-inner">
        <?php
        $chunks = array_chunk($testimonials, 3); // Show 3 testimonials per slide
        foreach ($chunks as $index => $chunk):
        ?>
        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
          <div class="row g-4">
            <?php foreach ($chunk as $testimonial): ?>
            <div class="col-lg-4 col-md-6">
              <div class="testimonial-card">
                <div class="quote-icon">
                  <i class="fas fa-quote-left"></i>
                </div>

                <div class="rating">
                  <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fas fa-star star <?= $i <= $testimonial['rating'] ? '' : 'empty' ?>"></i>
                  <?php endfor; ?>
                </div>

                <p class="testimonial-text">
                  "<?= esc($testimonial['testimonial']) ?>"
                </p>

                <div class="client-info">
                  <?php if (!empty($testimonial['image'])): ?>
                    <img src="<?= base_url('uploads/testimonials/' . esc($testimonial['image'])) ?>"
                         alt="<?= esc($testimonial['name']) ?>"
                         class="client-avatar">
                  <?php else: ?>
                    <div class="client-avatar-placeholder">
                      <?= strtoupper(substr($testimonial['name'], 0, 1)) ?>
                    </div>
                  <?php endif; ?>

                  <div class="client-details">
                    <h6><?= esc($testimonial['name']) ?></h6>
                    <?php if (!empty($testimonial['position'])): ?>
                      <p class="position">
                        <?= esc($testimonial['position']) ?>
                        <?php if (!empty($testimonial['company'])): ?>
                          at <?= esc($testimonial['company']) ?>
                        <?php endif; ?>
                      </p>
                    <?php endif; ?>
                    <?php if (!empty($testimonial['location'])): ?>
                      <p class="location">
                        <i class="fas fa-map-marker-alt me-1"></i><?= esc($testimonial['location']) ?>
                      </p>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Carousel Controls -->
      <?php if (count($chunks) > 1): ?>
      <button class="carousel-control-prev" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>
      <button class="carousel-control-next" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>

      <!-- Carousel Indicators -->
      <div class="carousel-indicators">
        <?php foreach ($chunks as $index => $chunk): ?>
          <button type="button" data-bs-target="#testimonialsCarousel" data-bs-slide-to="<?= $index ?>"
                  class="<?= $index === 0 ? 'active' : '' ?>" aria-current="<?= $index === 0 ? 'true' : 'false' ?>"
                  aria-label="Slide <?= $index + 1 ?>"></button>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
    <?php else: ?>
    <div class="text-center">
      <p class="text-muted">No testimonials available at the moment.</p>
    </div>
    <?php endif; ?>
  </div>
</section>

<?= $this->endSection() ?>