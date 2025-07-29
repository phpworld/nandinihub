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
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Page Header -->
            <div class="text-center mb-5">
                <h1 class="display-4 fw-bold text-primary"><?= esc($page['title']) ?></h1>
                <hr class="w-25 mx-auto">
            </div>

            <!-- Flash Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Page Content -->
            <div class="page-content mb-5">
                <?= $page['content'] ?>
            </div>

            <!-- Contact Form -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-envelope me-2"></i>Send us a Message</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= base_url('pages/contact/submit') ?>">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="<?= old('name') ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        value="<?= old('email') ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="subject" name="subject"
                                value="<?= old('subject') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="message" name="message" rows="6"
                                required><?= old('message') ?></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Back to Home -->
            <div class="text-center mt-5">
                <a href="<?= base_url() ?>" class="btn btn-outline-primary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Home
                </a>
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
    }

    .page-content ul,
    .page-content ol {
        margin-bottom: 1.5rem;
        padding-left: 2rem;
    }

    .page-content li {
        margin-bottom: 0.5rem;
    }

    .card {
        border: none;
        border-radius: 1rem;
    }

    .card-header {
        border-radius: 1rem 1rem 0 0 !important;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
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

    .alert {
        border-radius: 0.75rem;
        border: none;
    }
</style>
<?= $this->endSection() ?>