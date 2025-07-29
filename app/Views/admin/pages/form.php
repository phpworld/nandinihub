<?= $this->extend('admin/layout/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('admin/pages') ?>">Pages</a></li>
                    <li class="breadcrumb-item active"><?= $page ? 'Edit' : 'Create' ?></li>
                </ol>
            </nav>
        </div>
        <a href="<?= base_url('admin/pages') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Pages
        </a>
    </div>

    <!-- Error Messages -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('debug_post')): ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <strong>POST Data:</strong> <small><?= session()->getFlashdata('debug_post') ?></small>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('debug_update_data')): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>Update Data:</strong> <small><?= session()->getFlashdata('debug_update_data') ?></small>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>



    <!-- Form -->
    <div class="row">
        <div class="col-lg-8">
            <form method="POST" action="<?= $page ? base_url('admin/pages/' . $page['id'] . '/update') : base_url('admin/pages/store') ?>">
                <?= csrf_field() ?>
                <?php if ($page): ?>
                    <input type="hidden" name="_method" value="POST">
                <?php endif; ?>

                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Basic Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Page Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control <?= $validation && $validation->hasError('title') ? 'is-invalid' : '' ?>"
                                        id="title" name="title" value="<?= old('title', $page['title'] ?? '') ?>" required>
                                    <?php if ($validation && $validation->hasError('title')): ?>
                                        <div class="invalid-feedback"><?= $validation->getError('title') ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="template" class="form-label">Template</label>
                                    <select class="form-select" id="template" name="template">
                                        <option value="default" <?= old('template', $page['template'] ?? 'default') === 'default' ? 'selected' : '' ?>>Default</option>
                                        <option value="about" <?= old('template', $page['template'] ?? '') === 'about' ? 'selected' : '' ?>>About Page</option>
                                        <option value="contact" <?= old('template', $page['template'] ?? '') === 'contact' ? 'selected' : '' ?>>Contact Page</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="slug" class="form-label">URL Slug</label>
                            <div class="input-group">
                                <span class="input-group-text"><?= base_url('pages/') ?></span>
                                <input type="text" class="form-control <?= $validation && $validation->hasError('slug') ? 'is-invalid' : '' ?>"
                                    id="slug" name="slug" value="<?= old('slug', $page['slug'] ?? '') ?>"
                                    placeholder="Leave empty to auto-generate from title">
                                <?php if ($validation && $validation->hasError('slug')): ?>
                                    <div class="invalid-feedback"><?= $validation->getError('slug') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="form-text">URL-friendly version of the title. Leave empty to auto-generate.</div>
                        </div>

                        <div class="mb-3">
                            <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
                            <div class="content-editor-wrapper">
                                <div class="editor-loading" style="display: none;">
                                    <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                    Loading professional editor...
                                </div>
                                <textarea class="form-control <?= $validation && $validation->hasError('content') ? 'is-invalid' : '' ?>"
                                    id="content" name="content" rows="20" required
                                    placeholder="Enter your page content here. The professional editor will load automatically..."><?= old('content', $page['content'] ?? '') ?></textarea>
                            </div>
                            <?php if ($validation && $validation->hasError('content')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('content') ?></div>
                            <?php endif; ?>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Use the professional rich text editor to format your content with headings, lists, links, images, tables, and more.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">SEO Settings</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="meta_title" class="form-label">Meta Title</label>
                            <input type="text" class="form-control <?= $validation && $validation->hasError('meta_title') ? 'is-invalid' : '' ?>"
                                id="meta_title" name="meta_title" value="<?= old('meta_title', $page['meta_title'] ?? '') ?>"
                                maxlength="255" placeholder="Leave empty to use page title">
                            <?php if ($validation && $validation->hasError('meta_title')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('meta_title') ?></div>
                            <?php endif; ?>
                            <div class="form-text">Recommended: 50-60 characters</div>
                        </div>

                        <div class="mb-3">
                            <label for="meta_description" class="form-label">Meta Description</label>
                            <textarea class="form-control <?= $validation && $validation->hasError('meta_description') ? 'is-invalid' : '' ?>"
                                id="meta_description" name="meta_description" rows="3" maxlength="500"
                                placeholder="Brief description for search engines"><?= old('meta_description', $page['meta_description'] ?? '') ?></textarea>
                            <?php if ($validation && $validation->hasError('meta_description')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('meta_description') ?></div>
                            <?php endif; ?>
                            <div class="form-text">Recommended: 150-160 characters</div>
                        </div>

                        <div class="mb-3">
                            <label for="meta_keywords" class="form-label">Meta Keywords</label>
                            <input type="text" class="form-control <?= $validation && $validation->hasError('meta_keywords') ? 'is-invalid' : '' ?>"
                                id="meta_keywords" name="meta_keywords" value="<?= old('meta_keywords', $page['meta_keywords'] ?? '') ?>"
                                placeholder="keyword1, keyword2, keyword3">
                            <?php if ($validation && $validation->hasError('meta_keywords')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('meta_keywords') ?></div>
                            <?php endif; ?>
                            <div class="form-text">Separate keywords with commas</div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-flex justify-content-between">
                    <a href="<?= base_url('admin/pages') ?>" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <i class="fas fa-save me-2"></i><?= $page ? 'Update Page' : 'Create Page' ?>
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status & Visibility -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Status & Visibility</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="hidden" name="is_active" value="0">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                <?= old('is_active', $page['is_active'] ?? 1) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">
                                Active
                            </label>
                        </div>
                        <div class="form-text">Page will be visible to visitors when active</div>
                    </div>
                </div>
            </div>

            <!-- Navigation Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Navigation Settings</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="hidden" name="show_in_header" value="0">
                            <input class="form-check-input" type="checkbox" id="show_in_header" name="show_in_header" value="1"
                                <?= old('show_in_header', $page['show_in_header'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="show_in_header">
                                Show in Header Navigation
                            </label>
                        </div>
                    </div>

                    <div class="mb-3" id="header_order_group" style="display: <?= old('show_in_header', $page['show_in_header'] ?? 0) ? 'block' : 'none' ?>">
                        <label for="header_order" class="form-label">Header Order</label>
                        <input type="number" class="form-control" id="header_order" name="header_order"
                            value="<?= old('header_order', $page['header_order'] ?? 0) ?>" min="0">
                        <div class="form-text">Lower numbers appear first</div>
                    </div>

                    <!-- Always submit header_order, even if hidden -->
                    <input type="hidden" id="header_order_hidden" name="header_order" value="0">

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input type="hidden" name="show_in_footer" value="0">
                            <input class="form-check-input" type="checkbox" id="show_in_footer" name="show_in_footer" value="1"
                                <?= old('show_in_footer', $page['show_in_footer'] ?? 0) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="show_in_footer">
                                Show in Footer Navigation
                            </label>
                        </div>
                    </div>

                    <div class="mb-3" id="footer_order_group" style="display: <?= old('show_in_footer', $page['show_in_footer'] ?? 0) ? 'block' : 'none' ?>">
                        <label for="footer_order" class="form-label">Footer Order</label>
                        <input type="number" class="form-control" id="footer_order" name="footer_order"
                            value="<?= old('footer_order', $page['footer_order'] ?? 0) ?>" min="0">
                        <div class="form-text">Lower numbers appear first</div>
                    </div>

                    <!-- Always submit footer_order, even if hidden -->
                    <input type="hidden" id="footer_order_hidden" name="footer_order" value="0">
                </div>
            </div>

            <?php if ($page): ?>
                <!-- Page Info -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Page Information</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Created:</strong><br><?= date('M j, Y g:i A', strtotime($page['created_at'])) ?></p>
                        <p><strong>Last Updated:</strong><br><?= date('M j, Y g:i A', strtotime($page['updated_at'])) ?></p>
                        <p><strong>Page URL:</strong><br>
                            <a href="<?= base_url('pages/' . $page['slug']) ?>" target="_blank" class="text-primary">
                                <?= base_url('pages/' . $page['slug']) ?>
                                <i class="fas fa-external-link-alt ms-1"></i>
                            </a>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-generate slug from title
        const titleInput = document.getElementById('title');
        const slugInput = document.getElementById('slug');

        titleInput.addEventListener('input', function() {
            if (!slugInput.value || slugInput.dataset.autoGenerated) {
                const slug = this.value.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim('-');
                slugInput.value = slug;
                slugInput.dataset.autoGenerated = 'true';
            }
        });

        slugInput.addEventListener('input', function() {
            this.dataset.autoGenerated = 'false';
        });

        // Show/hide order fields based on navigation checkboxes
        const showInHeaderCheckbox = document.getElementById('show_in_header');
        const showInFooterCheckbox = document.getElementById('show_in_footer');
        const headerOrderGroup = document.getElementById('header_order_group');
        const footerOrderGroup = document.getElementById('footer_order_group');
        const headerOrderField = document.getElementById('header_order');
        const footerOrderField = document.getElementById('footer_order');
        const headerOrderHidden = document.getElementById('header_order_hidden');
        const footerOrderHidden = document.getElementById('footer_order_hidden');

        showInHeaderCheckbox.addEventListener('change', function() {
            headerOrderGroup.style.display = this.checked ? 'block' : 'none';
            // Enable/disable the visible field and use hidden field when not visible
            if (this.checked) {
                headerOrderField.disabled = false;
                headerOrderHidden.disabled = true;
            } else {
                headerOrderField.disabled = true;
                headerOrderHidden.disabled = false;
            }
        });

        showInFooterCheckbox.addEventListener('change', function() {
            footerOrderGroup.style.display = this.checked ? 'block' : 'none';
            // Enable/disable the visible field and use hidden field when not visible
            if (this.checked) {
                footerOrderField.disabled = false;
                footerOrderHidden.disabled = true;
            } else {
                footerOrderField.disabled = true;
                footerOrderHidden.disabled = false;
            }
        });

        // Initialize the state based on current checkbox values
        if (showInHeaderCheckbox.checked) {
            headerOrderHidden.disabled = true;
        } else {
            headerOrderField.disabled = true;
        }

        if (showInFooterCheckbox.checked) {
            footerOrderHidden.disabled = true;
        } else {
            footerOrderField.disabled = true;
        }
    });
</script>

<!-- TinyMCE Rich Text Editor -->
<style>
    .tox-tinymce {
        border-radius: 0.375rem !important;
        border: 1px solid #dee2e6 !important;
    }

    .tox-editor-header {
        border-bottom: 1px solid #dee2e6 !important;
    }

    .tox-statusbar {
        border-top: 1px solid #dee2e6 !important;
    }

    .content-editor-wrapper {
        position: relative;
    }

    .editor-loading {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 1000;
        background: rgba(255, 255, 255, 0.9);
        padding: 20px;
        border-radius: 8px;
        display: none;
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing form and TinyMCE...');

        // Debug form submission
        const form = document.querySelector('form');
        const submitBtn = document.getElementById('submitBtn');

        if (form && submitBtn) {
            form.addEventListener('submit', function(e) {
                console.log('Form submission started');
                console.log('Form action:', form.action);
                console.log('Form method:', form.method);

                // Check if required fields are filled
                const title = document.getElementById('title').value;
                const content = document.getElementById('content').value;

                console.log('Title:', title);
                console.log('Content length:', content.length);

                if (!title || !content) {
                    console.error('Required fields missing');
                    e.preventDefault();
                    alert('Please fill in all required fields');
                    return false;
                }

                // Disable submit button to prevent double submission
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
            });
        }

        // Show loading indicator
        const loadingEl = document.querySelector('.editor-loading');
        if (loadingEl) {
            loadingEl.style.display = 'block';
        }

        // Check if TinyMCE is loaded
        if (typeof tinymce === 'undefined') {
            console.error('TinyMCE is not loaded!');
            return;
        }

        // Initialize TinyMCE
        tinymce.init({
            selector: '#content',
            height: 500,
            menubar: true,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | bold italic underline strikethrough | ' +
                'alignleft aligncenter alignright alignjustify | ' +
                'bullist numlist outdent indent | removeformat | ' +
                'link image media table | forecolor backcolor | ' +
                'code preview fullscreen help',
            content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px; line-height: 1.6; }',
            block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6',
            image_advtab: true,
            setup: function(editor) {
                editor.on('init', function() {
                    // Hide loading indicator when editor is ready
                    const loadingEl = document.querySelector('.editor-loading');
                    if (loadingEl) {
                        loadingEl.style.display = 'none';
                    }
                    console.log('TinyMCE editor initialized successfully');
                });

                editor.on('change', function() {
                    editor.save();
                });
            },
            branding: false,
            resize: true,
            statusbar: true
        });
    });
</script>
<?= $this->endSection() ?>