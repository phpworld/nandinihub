<?= $this->extend('admin/layout/main') ?>

<?= $this->section('title') ?><?= $title ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
            <p class="mb-0 text-muted">Manage your website pages and content</p>
        </div>
        <a href="<?= base_url('admin/pages/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Create New Page
        </a>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Search and Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="<?= base_url('admin/pages') ?>">
                <div class="row">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search"
                                placeholder="Search pages..." value="<?= esc($search) ?>">
                            <button class="btn btn-outline-secondary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <?php if (!empty($search)): ?>
                            <a href="<?= base_url('admin/pages') ?>" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>Clear Search
                            </a>
                        <?php endif; ?>
                        <span class="text-muted ms-3">Total: <?= $total ?> pages</span>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Pages Table -->
    <div class="card">
        <div class="card-body">
            <?php if (!empty($pages)): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Slug</th>
                                <th>Status</th>
                                <th>Navigation</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pages as $page): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h6 class="mb-0"><?= esc($page['title']) ?></h6>
                                                <small class="text-muted">
                                                    Template: <?= esc($page['template']) ?>
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <code><?= esc($page['slug']) ?></code>
                                        <br>
                                        <a href="<?= base_url('pages/' . $page['slug']) ?>"
                                            target="_blank" class="small text-primary">
                                            <i class="fas fa-external-link-alt me-1"></i>View Page
                                        </a>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle"
                                                type="checkbox"
                                                data-id="<?= $page['id'] ?>"
                                                <?= $page['is_active'] ? 'checked' : '' ?>>
                                            <label class="form-check-label">
                                                <span class="badge bg-<?= $page['is_active'] ? 'success' : 'secondary' ?>">
                                                    <?= $page['is_active'] ? 'Active' : 'Inactive' ?>
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column gap-1">
                                            <?php if ($page['show_in_header']): ?>
                                                <span class="badge bg-info">Header (<?= $page['header_order'] ?>)</span>
                                            <?php endif; ?>
                                            <?php if ($page['show_in_footer']): ?>
                                                <span class="badge bg-secondary">Footer (<?= $page['footer_order'] ?>)</span>
                                            <?php endif; ?>
                                            <?php if (!$page['show_in_header'] && !$page['show_in_footer']): ?>
                                                <span class="text-muted small">Not in navigation</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= date('M j, Y', strtotime($page['created_at'])) ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('admin/pages/' . $page['id'] . '/edit') ?>"
                                                class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger delete-page"
                                                data-id="<?= $page['id'] ?>"
                                                data-title="<?= esc($page['title']) ?>"
                                                title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <?php if ($pager): ?>
                    <div class="d-flex justify-content-center mt-4">
                        <?= $pager->links() ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No pages found</h5>
                    <p class="text-muted">
                        <?php if (!empty($search)): ?>
                            No pages match your search criteria.
                        <?php else: ?>
                            Start by creating your first page.
                        <?php endif; ?>
                    </p>
                    <a href="<?= base_url('admin/pages/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create New Page
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the page "<span id="deletePageTitle"></span>"?</p>
                <p class="text-danger small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Status toggle
        document.querySelectorAll('.status-toggle').forEach(function(toggle) {
            toggle.addEventListener('change', function() {
                const pageId = this.dataset.id;
                const isActive = this.checked;

                fetch(`<?= base_url('admin/pages') ?>/${pageId}/toggle-status`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update badge
                            const badge = this.parentNode.querySelector('.badge');
                            if (isActive) {
                                badge.className = 'badge bg-success';
                                badge.textContent = 'Active';
                            } else {
                                badge.className = 'badge bg-secondary';
                                badge.textContent = 'Inactive';
                            }
                        } else {
                            // Revert toggle if failed
                            this.checked = !isActive;
                            alert('Failed to update status: ' + data.message);
                        }
                    })
                    .catch(error => {
                        // Revert toggle if failed
                        this.checked = !isActive;
                        alert('Error updating status');
                    });
            });
        });

        // Delete page
        let deletePageId = null;

        document.querySelectorAll('.delete-page').forEach(function(button) {
            button.addEventListener('click', function() {
                deletePageId = this.dataset.id;
                document.getElementById('deletePageTitle').textContent = this.dataset.title;
                new bootstrap.Modal(document.getElementById('deleteModal')).show();
            });
        });

        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (deletePageId) {
                fetch(`<?= base_url('admin/pages') ?>/${deletePageId}/delete`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Failed to delete page: ' + data.message);
                        }
                    })
                    .catch(error => {
                        alert('Error deleting page');
                    });
            }
        });
    });
</script>
<?= $this->endSection() ?>