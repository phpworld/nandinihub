<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="<?= base_url('admin/testimonials') ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Back to Testimonials
                    </a>
                </div>
                <h4 class="page-title">Edit Testimonial</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <form action="<?= base_url('admin/testimonials/update/' . $testimonial['id']) ?>" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">Customer Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?= old('name', $testimonial['name']) ?>" required>
                                <?php if (isset($errors['name'])): ?>
                                    <div class="invalid-feedback d-block"><?= $errors['name'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= old('email', $testimonial['email']) ?>">
                                <?php if (isset($errors['email'])): ?>
                                    <div class="invalid-feedback d-block"><?= $errors['email'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="position" class="form-label">Position/Title</label>
                                <input type="text" class="form-control" id="position" name="position" 
                                       value="<?= old('position', $testimonial['position']) ?>" placeholder="e.g., CEO, Artist, Therapist">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="company" class="form-label">Company/Organization</label>
                                <input type="text" class="form-control" id="company" name="company" 
                                       value="<?= old('company', $testimonial['company']) ?>" placeholder="e.g., ABC Corp, Freelance">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" 
                                       value="<?= old('location', $testimonial['location']) ?>" placeholder="e.g., California, USA">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="rating" class="form-label">Rating <span class="text-danger">*</span></label>
                                <select class="form-select" id="rating" name="rating" required>
                                    <option value="">Select Rating</option>
                                    <option value="5" <?= old('rating', $testimonial['rating']) == '5' ? 'selected' : '' ?>>5 Stars - Excellent</option>
                                    <option value="4" <?= old('rating', $testimonial['rating']) == '4' ? 'selected' : '' ?>>4 Stars - Very Good</option>
                                    <option value="3" <?= old('rating', $testimonial['rating']) == '3' ? 'selected' : '' ?>>3 Stars - Good</option>
                                    <option value="2" <?= old('rating', $testimonial['rating']) == '2' ? 'selected' : '' ?>>2 Stars - Fair</option>
                                    <option value="1" <?= old('rating', $testimonial['rating']) == '1' ? 'selected' : '' ?>>1 Star - Poor</option>
                                </select>
                                <?php if (isset($errors['rating'])): ?>
                                    <div class="invalid-feedback d-block"><?= $errors['rating'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="testimonial" class="form-label">Testimonial Text <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="testimonial" name="testimonial" rows="5" 
                                  placeholder="Enter the customer's testimonial..." required><?= old('testimonial', $testimonial['testimonial']) ?></textarea>
                        <?php if (isset($errors['testimonial'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['testimonial'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="image" class="form-label">Customer Photo</label>
                        <?php if (!empty($testimonial['image'])): ?>
                            <div class="mb-2">
                                <img src="<?= base_url('uploads/testimonials/' . esc($testimonial['image'])) ?>" 
                                     alt="Current Image" class="img-thumbnail" style="max-width: 150px;">
                                <p class="text-muted small mt-1">Current image</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*">
                        <small class="text-muted">Upload a new customer photo (optional). Max size: 2MB. Supported formats: JPG, PNG, GIF</small>
                        <?php if (isset($errors['image'])): ?>
                            <div class="invalid-feedback d-block"><?= $errors['image'] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                       value="<?= old('sort_order', $testimonial['sort_order']) ?>" min="0">
                                <small class="text-muted">Lower numbers appear first</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                           value="1" <?= old('is_featured', $testimonial['is_featured']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_featured">
                                        Featured Testimonial
                                    </label>
                                </div>
                                <small class="text-muted">Featured testimonials appear on the homepage</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                           value="1" <?= old('is_active', $testimonial['is_active']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_active">
                                        Active Status
                                    </label>
                                </div>
                                <small class="text-muted">Only active testimonials are displayed</small>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update Testimonial
                        </button>
                        <a href="<?= base_url('admin/testimonials') ?>" class="btn btn-secondary ms-2">Cancel</a>
                    </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Testimonial Info</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Created:</label>
                        <p class="text-muted"><?= date('F j, Y \a\t g:i A', strtotime($testimonial['created_at'])) ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Last Updated:</label>
                        <p class="text-muted"><?= date('F j, Y \a\t g:i A', strtotime($testimonial['updated_at'])) ?></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Current Status:</label>
                        <p>
                            <span class="badge <?= $testimonial['is_active'] ? 'bg-success' : 'bg-secondary' ?>">
                                <?= $testimonial['is_active'] ? 'Active' : 'Inactive' ?>
                            </span>
                            <?php if ($testimonial['is_featured']): ?>
                                <span class="badge bg-warning ms-1">Featured</span>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Tips for Great Testimonials</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Use authentic customer feedback
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Include specific benefits or results
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Keep testimonials concise but detailed
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Add customer photos for credibility
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Include customer's position/company
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            Feature your best testimonials
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();
</script>
<?= $this->endSection() ?>
