<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Create Variation Type</h1>
            <p class="text-muted">Add a new variation type for your products</p>
        </div>
        <a href="<?= base_url('admin/product-variations') ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back to Variations
        </a>
    </div>

    <!-- Form -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Variation Type Details</h5>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= esc(session()->getFlashdata('error')) ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= esc(session()->getFlashdata('success')) ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('admin/variation-types') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?= old('name') ?>" required>
                                    <div class="form-text">Internal name for the variation type (e.g., "size", "color")</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="display_name" class="form-label">Display Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="display_name" name="display_name" 
                                           value="<?= old('display_name') ?>" required>
                                    <div class="form-text">Name shown to customers (e.g., "Size", "Color")</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Display Type <span class="text-danger">*</span></label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="">Select display type</option>
                                        <option value="text" <?= old('type') === 'text' ? 'selected' : '' ?>>Text/Dropdown</option>
                                        <option value="button" <?= old('type') === 'button' ? 'selected' : '' ?>>Button Selection</option>
                                        <option value="color" <?= old('type') === 'color' ? 'selected' : '' ?>>Color Swatches</option>
                                        <option value="image" <?= old('type') === 'image' ? 'selected' : '' ?>>Image Selection</option>
                                    </select>
                                    <div class="form-text">How options will be displayed to customers</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="sort_order" class="form-label">Sort Order</label>
                                    <input type="number" class="form-control" id="sort_order" name="sort_order" 
                                           value="<?= old('sort_order', 0) ?>" min="0">
                                    <div class="form-text">Order in which this variation appears (0 = first)</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_required" name="is_required" 
                                       value="1" <?= old('is_required') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_required">
                                    Required Selection
                                </label>
                                <div class="form-text">Customers must select an option from this variation type</div>
                            </div>
                        </div>

                        <!-- Preview Section -->
                        <div class="mb-4">
                            <h6>Preview</h6>
                            <div class="border rounded p-3 bg-light">
                                <div id="preview-container">
                                    <label class="form-label fw-bold">
                                        <span id="preview-display-name">Variation Name</span>
                                        <span id="preview-required" class="text-danger" style="display: none;">*</span>
                                    </label>
                                    <div id="preview-options">
                                        <div class="text-muted">Select a display type to see preview</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('admin/product-variations') ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Variation Type
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const displayNameInput = document.getElementById('display_name');
    const typeSelect = document.getElementById('type');
    const isRequiredCheck = document.getElementById('is_required');
    
    const previewDisplayName = document.getElementById('preview-display-name');
    const previewRequired = document.getElementById('preview-required');
    const previewOptions = document.getElementById('preview-options');

    function updatePreview() {
        const displayName = displayNameInput.value || 'Variation Name';
        const type = typeSelect.value;
        const isRequired = isRequiredCheck.checked;

        previewDisplayName.textContent = displayName;
        previewRequired.style.display = isRequired ? 'inline' : 'none';

        let optionsHtml = '';
        switch (type) {
            case 'text':
                optionsHtml = `
                    <select class="form-select" disabled>
                        <option>Choose ${displayName}</option>
                        <option>Option 1</option>
                        <option>Option 2</option>
                    </select>
                `;
                break;
            case 'button':
                optionsHtml = `
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-primary btn-sm" disabled>Option 1</button>
                        <button class="btn btn-outline-primary btn-sm" disabled>Option 2</button>
                        <button class="btn btn-outline-primary btn-sm" disabled>Option 3</button>
                    </div>
                `;
                break;
            case 'color':
                optionsHtml = `
                    <div class="d-flex gap-2">
                        <div class="color-swatch" style="width: 30px; height: 30px; background-color: #ff0000; border: 2px solid #ddd; border-radius: 50%; cursor: pointer;"></div>
                        <div class="color-swatch" style="width: 30px; height: 30px; background-color: #00ff00; border: 2px solid #ddd; border-radius: 50%; cursor: pointer;"></div>
                        <div class="color-swatch" style="width: 30px; height: 30px; background-color: #0000ff; border: 2px solid #ddd; border-radius: 50%; cursor: pointer;"></div>
                    </div>
                `;
                break;
            case 'image':
                optionsHtml = `
                    <div class="d-flex gap-2">
                        <div class="border rounded p-2" style="width: 60px; height: 60px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                            <i class="fas fa-image text-muted"></i>
                        </div>
                        <div class="border rounded p-2" style="width: 60px; height: 60px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center; cursor: pointer;">
                            <i class="fas fa-image text-muted"></i>
                        </div>
                    </div>
                `;
                break;
            default:
                optionsHtml = '<div class="text-muted">Select a display type to see preview</div>';
        }

        previewOptions.innerHTML = optionsHtml;
    }

    displayNameInput.addEventListener('input', updatePreview);
    typeSelect.addEventListener('change', updatePreview);
    isRequiredCheck.addEventListener('change', updatePreview);

    // Auto-generate slug from name
    document.getElementById('name').addEventListener('input', function() {
        if (!document.getElementById('display_name').value) {
            document.getElementById('display_name').value = this.value;
            updatePreview();
        }
    });
});
</script>

<?= $this->endSection() ?>
