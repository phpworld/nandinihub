<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Product Variants</h1>
            <p class="text-muted">
                Manage variants for: <strong><?= esc($product['name']) ?></strong>
                <span class="badge bg-secondary ms-2"><?= esc($product['sku']) ?></span>
            </p>
        </div>
        <div>
            <a href="<?= base_url('admin/products/' . $product['id'] . '/edit') ?>" class="btn btn-outline-secondary me-2">
                <i class="fas fa-edit"></i> Edit Product
            </a>
            <a href="<?= base_url('admin/products') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Products
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

    <!-- Variant Generation Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Generate Variants</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($variationTypes)): ?>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            No variation types available. 
                            <a href="<?= base_url('admin/product-variations') ?>" class="alert-link">Create variation types</a> 
                            first to generate product variants.
                        </div>
                    <?php else: ?>
                        <form id="variant-generator-form">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            
                            <div class="row">
                                <?php foreach ($variationTypes as $type): ?>
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card border">
                                            <div class="card-body">
                                                <h6 class="card-title">
                                                    <?= esc($type['display_name']) ?>
                                                    <?php if ($type['is_required']): ?>
                                                        <span class="badge bg-warning ms-1">Required</span>
                                                    <?php endif; ?>
                                                </h6>
                                                
                                                <?php if (empty($type['options'])): ?>
                                                    <p class="text-muted small">No options available</p>
                                                <?php else: ?>
                                                    <div class="variation-options" data-type-id="<?= $type['id'] ?>">
                                                        <?php foreach ($type['options'] as $option): ?>
                                                            <div class="form-check">
                                                                <input class="form-check-input variation-option" 
                                                                       type="checkbox" 
                                                                       name="options[<?= $type['id'] ?>][]" 
                                                                       value="<?= $option['id'] ?>"
                                                                       id="option_<?= $option['id'] ?>">
                                                                <label class="form-check-label" for="option_<?= $option['id'] ?>">
                                                                    <?php if ($type['type'] === 'color' && $option['color_code']): ?>
                                                                        <div class="d-flex align-items-center">
                                                                            <div class="color-swatch me-2" 
                                                                                 style="width: 16px; height: 16px; background-color: <?= esc($option['color_code']) ?>; border: 1px solid #ddd; border-radius: 3px;"></div>
                                                                            <?= esc($option['name']) ?>
                                                                        </div>
                                                                    <?php else: ?>
                                                                        <?= esc($option['name']) ?>
                                                                    <?php endif; ?>
                                                                </label>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted">Combinations to generate: </span>
                                    <span id="combination-count" class="fw-bold">0</span>
                                </div>
                                <button type="button" class="btn btn-primary" onclick="generateVariants()">
                                    <i class="fas fa-magic"></i> Generate Variants
                                </button>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Existing Variants Section -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Existing Variants (<?= count($variants) ?>)</h5>
                    <button class="btn btn-outline-primary btn-sm" onclick="addVariantManually()">
                        <i class="fas fa-plus"></i> Add Manually
                    </button>
                </div>
                <div class="card-body">
                    <?php if (empty($variants)): ?>
                        <div class="text-center py-4">
                            <i class="fas fa-boxes fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No variants created yet</p>
                            <p class="text-muted small">Use the generator above to create variants automatically</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>SKU</th>
                                        <th>Options</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Default</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($variants as $variant): ?>
                                        <tr>
                                            <td>
                                                <strong><?= esc($variant['sku']) ?></strong>
                                                <?php if ($variant['image']): ?>
                                                    <br><small class="text-muted"><i class="fas fa-image"></i> Has image</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($variant['options'])): ?>
                                                    <?php foreach ($variant['options'] as $option): ?>
                                                        <span class="badge bg-light text-dark me-1">
                                                            <?= esc($option['type_display_name']) ?>: <?= esc($option['option_name']) ?>
                                                        </span>
                                                    <?php endforeach; ?>
                                                <?php else: ?>
                                                    <span class="text-muted">No options</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($variant['sale_price']): ?>
                                                    <span class="text-decoration-line-through text-muted">₹<?= number_format($variant['price'] ?? $product['price'], 2) ?></span><br>
                                                    <strong class="text-success">₹<?= number_format($variant['sale_price'], 2) ?></strong>
                                                <?php else: ?>
                                                    <strong>₹<?= number_format($variant['price'] ?? $product['price'], 2) ?></strong>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="badge bg-<?= $variant['stock_quantity'] > 0 ? 'success' : 'danger' ?>">
                                                        <?= $variant['stock_quantity'] > 0 ? $variant['stock_quantity'] . ' in stock' : 'Out of stock' ?>
                                                    </span>
                                                    <?php if ($variant['stock_quantity'] == 0): ?>
                                                        <button class="btn btn-sm btn-outline-warning" onclick="quickStockUpdate(<?= $variant['id'] ?>)" title="Quick stock update">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($variant['is_default']): ?>
                                                    <span class="badge bg-primary">Default</span>
                                                <?php else: ?>
                                                    <button class="btn btn-outline-secondary btn-sm" 
                                                            onclick="setDefaultVariant(<?= $variant['id'] ?>)">
                                                        Set Default
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?= $variant['is_active'] ? 'success' : 'secondary' ?>">
                                                    <?= $variant['is_active'] ? 'Active' : 'Inactive' ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-outline-primary" 
                                                            onclick="editVariant(<?= $variant['id'] ?>)" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-outline-secondary" 
                                                            onclick="duplicateVariant(<?= $variant['id'] ?>)" title="Duplicate">
                                                        <i class="fas fa-copy"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" 
                                                            onclick="deleteVariant(<?= $variant['id'] ?>)" title="Delete">
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

<!-- Edit Variant Modal -->
<div class="modal fade" id="editVariantModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product Variant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editVariantForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_variant_id" name="variant_id">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_variant_sku" class="form-label">SKU</label>
                                <input type="text" class="form-control" id="edit_variant_sku" name="sku" required maxlength="100" pattern="[A-Za-z0-9\-_]+" title="SKU can only contain letters, numbers, hyphens, and underscores">
                                <div class="form-text">Only letters, numbers, hyphens, and underscores allowed</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_variant_stock" class="form-label">Stock Quantity</label>
                                <input type="number" class="form-control" id="edit_variant_stock" name="stock_quantity" min="0" step="1">
                                <div class="form-text">Leave empty for unlimited stock</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_variant_price" class="form-label">Price</label>
                                <input type="number" class="form-control" id="edit_variant_price" name="price" step="0.01" min="0" max="999999.99">
                                <div class="form-text">Leave empty to use product price</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_variant_sale_price" class="form-label">Sale Price</label>
                                <input type="number" class="form-control" id="edit_variant_sale_price" name="sale_price" step="0.01" min="0" max="999999.99">
                                <div class="form-text">Optional sale price</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_variant_weight" class="form-label">Weight (kg)</label>
                                <input type="number" class="form-control" id="edit_variant_weight" name="weight" step="0.01" min="0" max="9999.99">
                                <div class="form-text">Product weight in kilograms</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_variant_dimensions" class="form-label">Dimensions</label>
                                <input type="text" class="form-control" id="edit_variant_dimensions" name="dimensions" placeholder="L x W x H" maxlength="255">
                                <div class="form-text">e.g., 10cm x 5cm x 2cm</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Selected Options</label>
                        <div id="edit_variant_options" class="border rounded p-3 bg-light">
                            <!-- Options will be displayed here -->
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="allow_duplicate_sku" name="allow_duplicate_sku" value="1">
                            <label class="form-check-label" for="allow_duplicate_sku">
                                Allow duplicate SKU (skip uniqueness check)
                            </label>
                            <div class="form-text">Check this if you want to use the same SKU for multiple variants</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Variant</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Calculate combination count
function updateCombinationCount() {
    const selectedOptions = {};
    document.querySelectorAll('.variation-option:checked').forEach(checkbox => {
        const typeId = checkbox.closest('.variation-options').dataset.typeId;
        if (!selectedOptions[typeId]) selectedOptions[typeId] = 0;
        selectedOptions[typeId]++;
    });

    let combinations = 1;
    Object.values(selectedOptions).forEach(count => {
        combinations *= count;
    });

    document.getElementById('combination-count').textContent = combinations > 0 ? combinations : 0;
}

// Event listeners for checkboxes
document.querySelectorAll('.variation-option').forEach(checkbox => {
    checkbox.addEventListener('change', updateCombinationCount);
});

function generateVariants() {
    const formData = new FormData(document.getElementById('variant-generator-form'));
    
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating...';
    button.disabled = true;

    fetch('<?= base_url('admin/products/generate-variants') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + (data.message || 'Failed to generate variants'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while generating variants');
    })
    .finally(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    });
}

function editVariant(variantId) {
    fetch(`<?= base_url('admin/product-variants/') ?>${variantId}/edit`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const variant = data.data;
            document.getElementById('edit_variant_id').value = variant.id;
            document.getElementById('edit_variant_sku').value = variant.sku;
            document.getElementById('edit_variant_stock').value = variant.stock_quantity;
            document.getElementById('edit_variant_price').value = variant.price || '';
            document.getElementById('edit_variant_sale_price').value = variant.sale_price || '';
            document.getElementById('edit_variant_weight').value = variant.weight || '';
            document.getElementById('edit_variant_dimensions').value = variant.dimensions || '';

            // Display options
            const optionsDiv = document.getElementById('edit_variant_options');
            if (variant.options && variant.options.length > 0) {
                optionsDiv.innerHTML = variant.options.map(option =>
                    `<span class="badge bg-primary me-2">${option.type_display_name}: ${option.option_name}</span>`
                ).join('');
            } else {
                optionsDiv.innerHTML = '<span class="text-muted">No options selected</span>';
            }

            new bootstrap.Modal(document.getElementById('editVariantModal')).show();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while loading the variant');
    });
}

function duplicateVariant(variantId) {
    if (confirm('Create a copy of this variant?')) {
        fetch(`<?= base_url('admin/product-variants/') ?>${variantId}/duplicate`, {
            method: 'POST',
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
            alert('An error occurred while duplicating the variant');
        });
    }
}

function deleteVariant(variantId) {
    if (confirm('Are you sure you want to delete this variant? This action cannot be undone.')) {
        fetch(`<?= base_url('admin/product-variants/') ?>${variantId}`, {
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
            alert('An error occurred while deleting the variant');
        });
    }
}

function setDefaultVariant(variantId) {
    fetch(`<?= base_url('admin/product-variants/') ?>${variantId}/set-default`, {
        method: 'POST',
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
        alert('An error occurred while setting the default variant');
    });
}

function addVariantManually() {
    // TODO: Open modal for manual variant creation
    alert('Manual variant creation will be implemented in the next update');
}

function quickStockUpdate(variantId) {
    const newStock = prompt('Enter new stock quantity:', '10');

    if (newStock !== null && !isNaN(newStock) && parseInt(newStock) >= 0) {
        const formData = new FormData();
        formData.append('stock_quantity', parseInt(newStock));

        fetch(`<?= base_url('admin/product-variants/') ?>${variantId}/quick-stock`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to update stock'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating stock');
        });
    }
}

// Form submission for edit variant
document.getElementById('editVariantForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const variantId = document.getElementById('edit_variant_id').value;

    fetch(`<?= base_url('admin/product-variants/') ?>${variantId}/update`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editVariantModal')).hide();
            location.reload();
        } else if (data.action === 'merge_stock') {
            // Handle duplicate SKU - offer to merge stock
            const currentStock = parseInt(document.getElementById('edit_variant_stock').value) || 0;
            const existingStock = data.existing_variant.stock_quantity;
            const totalStock = currentStock + existingStock;

            const confirmMessage = `This SKU already exists for this product.\n\n` +
                                 `Existing variant stock: ${existingStock}\n` +
                                 `New stock to add: ${currentStock}\n` +
                                 `Total stock after merge: ${totalStock}\n\n` +
                                 `Would you like to merge the stock quantities and delete this variant?`;

            if (confirm(confirmMessage)) {
                // Merge the stock
                const variantId = document.getElementById('edit_variant_id').value;
                const formData = new FormData();
                formData.append('target_variant_id', data.existing_variant.id);
                formData.append('stock_quantity', currentStock);

                fetch(`<?= base_url('admin/product-variants/') ?>${variantId}/merge-stock`, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(mergeData => {
                    if (mergeData.success) {
                        bootstrap.Modal.getInstance(document.getElementById('editVariantModal')).hide();
                        alert(`Stock merged successfully! Total stock: ${mergeData.merged_stock}`);
                        location.reload();
                    } else {
                        alert('Failed to merge stock: ' + mergeData.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while merging stock');
                });
            }
        } else {
            let errorMessage = data.message || 'Failed to update variant';

            // Show detailed validation errors if available
            if (data.errors) {
                errorMessage += '\n\nValidation Errors:\n';
                for (const field in data.errors) {
                    errorMessage += `• ${field}: ${data.errors[field]}\n`;
                }
            }

            // Show submitted data for debugging
            if (data.submitted_data) {
                console.log('Submitted data:', data.submitted_data);
            }

            alert(errorMessage);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the variant');
    });
});

// Initialize
updateCombinationCount();
</script>

<?= $this->endSection() ?>
