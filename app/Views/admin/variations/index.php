<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Product Variations</h1>
            <p class="text-muted">Manage variation types and options for your products</p>
        </div>
        <div>
            <a href="<?= base_url('admin/variation-types/create') ?>" class="btn btn-primary me-2">
                <i class="fas fa-plus"></i> Add Variation Type
            </a>
            <a href="<?= base_url('admin/variation-options/create') ?>" class="btn btn-outline-primary">
                <i class="fas fa-plus"></i> Add Option
            </a>
        </div>
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

    <!-- Variation Types Section -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Variation Types</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($variationTypes)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-tags fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No variation types found</p>
                            <a href="<?= base_url('admin/variation-types/create') ?>" class="btn btn-primary">
                                Create First Variation Type
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Required</th>
                                        <th>Options</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($variationTypes as $type): ?>
                                        <tr>
                                            <td>
                                                <strong><?= esc($type['display_name']) ?></strong>
                                                <br><small class="text-muted"><?= esc($type['slug']) ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $type['type'] === 'color' ? 'info' : ($type['type'] === 'button' ? 'primary' : 'secondary') ?>">
                                                    <?= ucfirst($type['type']) ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($type['is_required']): ?>
                                                    <span class="badge bg-warning">Required</span>
                                                <?php else: ?>
                                                    <span class="badge bg-light text-dark">Optional</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-success"><?= count($type['options']) ?> options</span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="<?= base_url('admin/variation-options/create/' . $type['id']) ?>" 
                                                       class="btn btn-outline-primary" title="Add Option">
                                                        <i class="fas fa-plus"></i>
                                                    </a>
                                                    <button class="btn btn-outline-secondary" 
                                                            onclick="editVariationType(<?= $type['id'] ?>)" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" 
                                                            onclick="deleteVariationType(<?= $type['id'] ?>)" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Variation Options Section -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">All Variation Options</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($allOptions)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-list fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No variation options found</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Option</th>
                                        <th>Value</th>
                                        <th>Price</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($allOptions as $option): ?>
                                        <tr>
                                            <td>
                                                <small class="text-muted"><?= esc($option['type_name']) ?></small>
                                            </td>
                                            <td>
                                                <?php if ($option['type_display'] === 'color' && $option['color_code']): ?>
                                                    <div class="d-flex align-items-center">
                                                        <div class="color-swatch me-2" 
                                                             style="width: 20px; height: 20px; background-color: <?= esc($option['color_code']) ?>; border: 1px solid #ddd; border-radius: 3px;"></div>
                                                        <?= esc($option['name']) ?>
                                                    </div>
                                                <?php else: ?>
                                                    <?= esc($option['name']) ?>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <code><?= esc($option['value']) ?></code>
                                            </td>
                                            <td>
                                                <?php if ($option['price_modifier'] != 0): ?>
                                                    <span class="badge <?= $option['price_modifier'] > 0 ? 'bg-success' : 'bg-warning' ?>">
                                                        <?= $option['price_modifier'] > 0 ? '+' : '' ?><?= number_format($option['price_modifier'], 2) ?>
                                                        <?= $option['price_type'] === 'percentage' ? '%' : '' ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="text-muted">No change</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-secondary btn-sm" 
                                                            onclick="editVariationOption(<?= $option['id'] ?>)" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger btn-sm" 
                                                            onclick="deleteVariationOption(<?= $option['id'] ?>)" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Variation Type Modal -->
<div class="modal fade" id="editTypeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Variation Type</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editTypeForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_type_id" name="type_id">

                    <div class="mb-3">
                        <label for="edit_type_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="edit_type_name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_type_display_name" class="form-label">Display Name</label>
                        <input type="text" class="form-control" id="edit_type_display_name" name="display_name" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_type_type" class="form-label">Display Type</label>
                        <select class="form-select" id="edit_type_type" name="type" required>
                            <option value="text">Text/Dropdown</option>
                            <option value="button">Button Selection</option>
                            <option value="color">Color Swatches</option>
                            <option value="image">Image Selection</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="edit_type_sort_order" class="form-label">Sort Order</label>
                        <input type="number" class="form-control" id="edit_type_sort_order" name="sort_order" min="0">
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="edit_type_is_required" name="is_required" value="1">
                            <label class="form-check-label" for="edit_type_is_required">
                                Required Selection
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Type</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Variation Option Modal -->
<div class="modal fade" id="editOptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Variation Option</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editOptionForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_option_id" name="option_id">

                    <div class="mb-3">
                        <label for="edit_option_name" class="form-label">Option Name</label>
                        <input type="text" class="form-control" id="edit_option_name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="edit_option_value" class="form-label">Option Value</label>
                        <input type="text" class="form-control" id="edit_option_value" name="value" required>
                    </div>

                    <div class="mb-3" id="edit_color_field" style="display: none;">
                        <label for="edit_option_color_code" class="form-label">Color Code</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="edit_option_color_code" name="color_code">
                            <input type="text" class="form-control" id="edit_option_color_text" placeholder="#000000">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_option_sort_order" class="form-label">Sort Order</label>
                        <input type="number" class="form-control" id="edit_option_sort_order" name="sort_order" min="0">
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="edit_option_price_modifier" class="form-label">Price Modifier</label>
                                <input type="number" class="form-control" id="edit_option_price_modifier" name="price_modifier" step="0.01">
                                <div class="form-text">Additional cost for this option</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_option_price_type" class="form-label">Type</label>
                                <select class="form-select" id="edit_option_price_type" name="price_type">
                                    <option value="fixed">Fixed Amount</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Option</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editVariationType(id) {
    fetch(`<?= base_url('admin/variation-types/') ?>${id}/edit`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const type = data.data;
            document.getElementById('edit_type_id').value = type.id;
            document.getElementById('edit_type_name').value = type.name;
            document.getElementById('edit_type_display_name').value = type.display_name;
            document.getElementById('edit_type_type').value = type.type;
            document.getElementById('edit_type_sort_order').value = type.sort_order;
            document.getElementById('edit_type_is_required').checked = type.is_required == 1;

            new bootstrap.Modal(document.getElementById('editTypeModal')).show();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while loading the variation type');
    });
}

function deleteVariationType(id) {
    if (confirm('Are you sure you want to delete this variation type? This will also delete all associated options and variants.')) {
        fetch(`<?= base_url('admin/variation-types/') ?>${id}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the variation type');
        });
    }
}

function editVariationOption(id) {
    fetch(`<?= base_url('admin/variation-options/') ?>${id}/edit`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const option = data.data;
            document.getElementById('edit_option_id').value = option.id;
            document.getElementById('edit_option_name').value = option.name;
            document.getElementById('edit_option_value').value = option.value;
            document.getElementById('edit_option_sort_order').value = option.sort_order;
            document.getElementById('edit_option_price_modifier').value = option.price_modifier || 0;
            document.getElementById('edit_option_price_type').value = option.price_type || 'fixed';

            // Handle color field
            const colorField = document.getElementById('edit_color_field');
            if (option.type_display === 'color') {
                colorField.style.display = 'block';
                document.getElementById('edit_option_color_code').value = option.color_code || '#000000';
                document.getElementById('edit_option_color_text').value = option.color_code || '#000000';
            } else {
                colorField.style.display = 'none';
            }

            new bootstrap.Modal(document.getElementById('editOptionModal')).show();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while loading the variation option');
    });
}

function deleteVariationOption(id) {
    if (confirm('Are you sure you want to delete this variation option? This may affect existing product variants.')) {
        fetch(`<?= base_url('admin/variation-options/') ?>${id}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the variation option');
        });
    }
}

// Form submissions
document.getElementById('editTypeForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const typeId = document.getElementById('edit_type_id').value;

    fetch(`<?= base_url('admin/variation-types/') ?>${typeId}/update`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editTypeModal')).hide();
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to update variation type'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the variation type');
    });
});

document.getElementById('editOptionForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const optionId = document.getElementById('edit_option_id').value;

    fetch(`<?= base_url('admin/variation-options/') ?>${optionId}/update`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editOptionModal')).hide();
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to update variation option'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the variation option');
    });
});

// Color picker sync
document.getElementById('edit_option_color_code').addEventListener('input', function() {
    document.getElementById('edit_option_color_text').value = this.value;
});

document.getElementById('edit_option_color_text').addEventListener('input', function() {
    if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
        document.getElementById('edit_option_color_code').value = this.value;
    }
});
</script>

<?= $this->endSection() ?>
