<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?><?= esc($title) ?><?= $this->endSection() ?>

<?= $this->section('meta') ?>
<?php if (!empty($meta_description)): ?>
    <meta name="description" content="<?= esc($meta_description) ?>">
<?php endif; ?>
<?php if (!empty($meta_keywords)): ?>
    <meta name="keywords" content="<?= esc($meta_keywords) ?>">
<?php endif; ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container my-5">
    <!-- Page Header -->
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold text-primary"><?= esc($page['title']) ?></h1>
        <hr class="w-25 mx-auto">
    </div>

    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Page Content -->
            <div class="page-content">
                <?= $page['content'] ?>
            </div>
        </div>
    </div>

    <!-- Additional About Sections -->
    <div class="row mt-5">
        <div class="col-lg-4 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="feature-icon bg-primary bg-gradient text-white rounded-circle mb-3 mx-auto">
                        <i class="fas fa-shipping-fast"></i>
                    </div>
                    <h5 class="card-title">Fast Shipping</h5>
                    <p class="card-text text-muted">We offer fast and reliable shipping options to get your orders to you quickly.</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="feature-icon bg-success bg-gradient text-white rounded-circle mb-3 mx-auto">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h5 class="card-title">Secure Shopping</h5>
                    <p class="card-text text-muted">Your personal information and payment details are always protected with us.</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="feature-icon bg-info bg-gradient text-white rounded-circle mb-3 mx-auto">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h5 class="card-title">24/7 Support</h5>
                    <p class="card-text text-muted">Our customer support team is available around the clock to help you.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="row mt-5">
        <div class="col-lg-8 mx-auto text-center">
            <div class="bg-light rounded-3 p-5">
                <h3 class="mb-3">Ready to Start Shopping?</h3>
                <p class="lead mb-4">Explore our wide range of products and find exactly what you're looking for.</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="<?= base_url() ?>" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Shop Now
                    </a>
                    <a href="<?= base_url('pages/contact-us') ?>" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-envelope me-2"></i>Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .page-content {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #333;
    }

    .page-content h1,
    .page-content h2,
    .page-content h3,
    .page-content h4,
    .page-content h5,
    .page-content h6 {
        color: #2c3e50;
        margin-top: 2rem;
        margin-bottom: 1rem;
    }

    .page-content h2 {
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 0.5rem;
    }

    .page-content p {
        margin-bottom: 1.5rem;
        text-align: justify;
    }

    .page-content ul,
    .page-content ol {
        margin-bottom: 1.5rem;
        padding-left: 2rem;
    }

    .page-content li {
        margin-bottom: 0.5rem;
    }

    .page-content blockquote {
        border-left: 4px solid #007bff;
        padding-left: 1rem;
        margin: 2rem 0;
        font-style: italic;
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 0.25rem;
    }

    .page-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin: 1rem 0;
    }

    .feature-icon {
        width: 4rem;
        height: 4rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-radius: 1rem;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
    }

    .btn-primary {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border: none;
        border-radius: 0.5rem;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
    }

    .btn-outline-primary {
        border-radius: 0.5rem;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
    }

    .bg-light {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
    }
</style>
<?= $this->endSection() ?>