<?= $this->extend('admin/layout/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active">Shipping Methods</li>
    </ol>
</nav>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Shipping Methods</h1>
        <a href="<?= base_url('admin/shipping/create') ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Shipping Method
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

    <!-- Shipping Methods Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Shipping Methods List</h6>
        </div>
        <div class="card-body">
            <?php if (!empty($shippingMethods)): ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="shippingMethodsTable">
                        <thead>
                            <tr>
                                <th width="5%">
                                    <i class="fas fa-sort" title="Drag to reorder"></i>
                                </th>
                                <th width="20%">Name</th>
                                <th width="15%">Delivery Time</th>
                                <th width="10%">Cost</th>
                                <th width="15%">Min Order Amount</th>
                                <th width="10%">Status</th>
                                <th width="10%">Free Shipping</th>
                                <th width="15%">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sortable-shipping">
                            <?php foreach ($shippingMethods as $method): ?>
                                <tr data-id="<?= $method['id'] ?>" data-sort="<?= $method['sort_order'] ?>">
                                    <td class="text-center">
                                        <i class="fas fa-grip-vertical text-muted" style="cursor: move;"></i>
                                    </td>
                                    <td>
                                        <strong><?= esc($method['name']) ?></strong>
                                        <?php if ($method['description']): ?>
                                            <br><small class="text-muted"><?= esc($method['description']) ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-dark"><?= esc($method['delivery_time']) ?></span>
                                    </td>
                                    <td>
                                        <?php if ($method['cost'] > 0): ?>
                                            <span class="text-success">₹<?= number_format($method['cost'], 2) ?></span>
                                        <?php else: ?>
                                            <span class="text-primary">Free</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($method['minimum_order_amount'] > 0): ?>
                                            ₹<?= number_format($method['minimum_order_amount'], 2) ?>
                                        <?php else: ?>
                                            <span class="text-muted">No minimum</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-toggle" 
                                                   type="checkbox" 
                                                   data-id="<?= $method['id'] ?>"
                                                   <?= $method['is_active'] ? 'checked' : '' ?>>
                                            <label class="form-check-label">
                                                <span class="status-text">
                                                    <?= $method['is_active'] ? 'Active' : 'Inactive' ?>
                                                </span>
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($method['is_free_shipping']): ?>
                                            <span class="badge bg-success">Yes</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">No</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('admin/shipping/' . $method['id'] . '/edit') ?>" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger delete-btn" 
                                                    data-id="<?= $method['id'] ?>"
                                                    data-name="<?= esc($method['name']) ?>"
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
                    <i class="fas fa-shipping-fast fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No shipping methods found</h5>
                    <p class="text-muted">Create your first shipping method to get started.</p>
                    <a href="<?= base_url('admin/shipping/create') ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Add Shipping Method
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
                <p>Are you sure you want to delete the shipping method "<span id="deleteMethodName"></span>"?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <?= csrf_field() ?>
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize sortable
    const sortable = new Sortable(document.getElementById('sortable-shipping'), {
        animation: 150,
        handle: '.fa-grip-vertical',
        onEnd: function(evt) {
            updateSortOrder();
        }
    });

    // Status toggle
    $('.status-toggle').change(function() {
        const methodId = $(this).data('id');
        const isChecked = $(this).is(':checked');
        const statusText = $(this).siblings('label').find('.status-text');
        
        $.ajax({
            url: `<?= base_url('admin/shipping') ?>/${methodId}/toggle`,
            type: 'POST',
            data: {
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    statusText.text(response.new_status ? 'Active' : 'Inactive');
                    showAlert('success', response.message);
                } else {
                    // Revert checkbox state
                    $(this).prop('checked', !isChecked);
                    showAlert('error', response.message);
                }
            }.bind(this),
            error: function() {
                // Revert checkbox state
                $(this).prop('checked', !isChecked);
                showAlert('error', 'Failed to update status');
            }.bind(this)
        });
    });

    // Delete button
    $('.delete-btn').click(function() {
        const methodId = $(this).data('id');
        const methodName = $(this).data('name');
        
        $('#deleteMethodName').text(methodName);
        $('#deleteForm').attr('action', `<?= base_url('admin/shipping') ?>/${methodId}/delete`);
        $('#deleteModal').modal('show');
    });

    // Update sort order
    function updateSortOrder() {
        const sortData = {};
        $('#sortable-shipping tr').each(function(index) {
            const methodId = $(this).data('id');
            if (methodId) {
                sortData[methodId] = index + 1;
            }
        });

        $.ajax({
            url: '<?= base_url('admin/shipping/update-sort-order') ?>',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(sortData),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function() {
                showAlert('error', 'Failed to update sort order');
            }
        });
    }

    // Show alert function
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('.container-fluid').prepend(alertHtml);
        
        // Auto-dismiss after 3 seconds
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 3000);
    }
});
</script>
<?= $this->endSection() ?>
