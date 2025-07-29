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

            <!-- Page Content -->
            <div class="page-content">
                <?= $page['content'] ?>
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

    .page-content table {
        width: 100%;
        margin: 2rem 0;
        border-collapse: collapse;
    }

    .page-content table th,
    .page-content table td {
        padding: 0.75rem;
        border: 1px solid #dee2e6;
        text-align: left;
    }

    .page-content table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }

    .page-content table tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    .page-content code {
        background-color: #f8f9fa;
        padding: 0.2rem 0.4rem;
        border-radius: 0.25rem;
        font-size: 0.9em;
        color: #e83e8c;
    }

    .page-content pre {
        background-color: #f8f9fa;
        padding: 1rem;
        border-radius: 0.5rem;
        overflow-x: auto;
        margin: 1.5rem 0;
    }

    .page-content pre code {
        background-color: transparent;
        padding: 0;
        color: #333;
    }
</style>
<?= $this->endSection() ?>