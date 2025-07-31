<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <a href="<?= base_url('admin/testimonials/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Add New Testimonial
                    </a>
                </div>
                <h4 class="page-title">Manage Testimonials</h4>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted fw-normal mt-0" title="Total Testimonials">Total</h5>
                            <h3 class="mt-3 mb-3"><?= $stats['total'] ?></h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-primary text-primary rounded">
                                <i class="fas fa-comments"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted fw-normal mt-0" title="Active Testimonials">Active</h5>
                            <h3 class="mt-3 mb-3"><?= $stats['active'] ?></h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-success text-success rounded">
                                <i class="fas fa-check-circle"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted fw-normal mt-0" title="Featured Testimonials">Featured</h5>
                            <h3 class="mt-3 mb-3"><?= $stats['featured'] ?></h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-warning text-warning rounded">
                                <i class="fas fa-star"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="text-muted fw-normal mt-0" title="Average Rating">Avg Rating</h5>
                            <h3 class="mt-3 mb-3"><?= $stats['average_rating'] ?> <small class="text-muted">/ 5</small></h3>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-soft-info text-info rounded">
                                <i class="fas fa-star-half-alt"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonials Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Customer</th>
                                    <th>Testimonial</th>
                                    <th>Rating</th>
                                    <th>Status</th>
                                    <th>Featured</th>
                                    <th>Sort Order</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($testimonials)): ?>
                                    <?php foreach ($testimonials as $testimonial): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if (!empty($testimonial['image'])): ?>
                                                        <img src="<?= base_url('uploads/testimonials/' . esc($testimonial['image'])) ?>" 
                                                             alt="<?= esc($testimonial['name']) ?>" 
                                                             class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                                    <?php else: ?>
                                                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                             style="width: 40px; height: 40px; color: white; font-weight: bold;">
                                                            <?= strtoupper(substr($testimonial['name'], 0, 1)) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    <div>
                                                        <h6 class="mb-0"><?= esc($testimonial['name']) ?></h6>
                                                        <?php if (!empty($testimonial['position'])): ?>
                                                            <small class="text-muted">
                                                                <?= esc($testimonial['position']) ?>
                                                                <?php if (!empty($testimonial['company'])): ?>
                                                                    at <?= esc($testimonial['company']) ?>
                                                                <?php endif; ?>
                                                            </small>
                                                        <?php endif; ?>
                                                        <?php if (!empty($testimonial['location'])): ?>
                                                            <br><small class="text-muted">
                                                                <i class="fas fa-map-marker-alt me-1"></i><?= esc($testimonial['location']) ?>
                                                            </small>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div style="max-width: 300px;">
                                                    <?= strlen($testimonial['testimonial']) > 100 ? 
                                                        esc(substr($testimonial['testimonial'], 0, 100)) . '...' : 
                                                        esc($testimonial['testimonial']) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-warning">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <i class="fas fa-star <?= $i <= $testimonial['rating'] ? '' : 'text-muted' ?>"></i>
                                                    <?php endfor; ?>
                                                    <span class="ms-1">(<?= $testimonial['rating'] ?>)</span>
                                                </div>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('admin/testimonials/toggle-active/' . $testimonial['id']) ?>" 
                                                   class="btn btn-sm <?= $testimonial['is_active'] ? 'btn-success' : 'btn-secondary' ?>">
                                                    <?= $testimonial['is_active'] ? 'Active' : 'Inactive' ?>
                                                </a>
                                            </td>
                                            <td>
                                                <a href="<?= base_url('admin/testimonials/toggle-featured/' . $testimonial['id']) ?>" 
                                                   class="btn btn-sm <?= $testimonial['is_featured'] ? 'btn-warning' : 'btn-outline-warning' ?>">
                                                    <i class="fas fa-star"></i>
                                                    <?= $testimonial['is_featured'] ? 'Featured' : 'Not Featured' ?>
                                                </a>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?= $testimonial['sort_order'] ?></span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?= date('M j, Y', strtotime($testimonial['created_at'])) ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= base_url('admin/testimonials/edit/' . $testimonial['id']) ?>" 
                                                       class="btn btn-sm btn-outline-primary" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="<?= base_url('admin/testimonials/delete/' . $testimonial['id']) ?>" 
                                                       class="btn btn-sm btn-outline-danger" title="Delete"
                                                       onclick="return confirm('Are you sure you want to delete this testimonial?')">
                                                        <i class="fas fa-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-comments fa-3x mb-3"></i>
                                                <h5>No testimonials found</h5>
                                                <p>Start by adding your first testimonial.</p>
                                                <a href="<?= base_url('admin/testimonials/create') ?>" class="btn btn-primary">
                                                    <i class="fas fa-plus me-1"></i> Add Testimonial
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
