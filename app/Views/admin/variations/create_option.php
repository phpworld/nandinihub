<?= $this->extend('admin/layout/main') ?>

<?= $this->section('content') ?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Create Variation Option</h1>
            <p class="text-muted">Add a new option to a variation type</p>
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
                    <h5 class="card-title mb-0">Variation Option Details</h5>
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

                    <form action="<?= base_url('admin/variation-options') ?>" method="POST" enctype="multipart/form-data">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="variation_type_id" class="form-label">Variation Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="variation_type_id" name="variation_type_id" required>
                                <option value="">Select variation type</option>
                                <?php foreach ($variationTypes as $type): ?>
                                    <option value="<?= $type['id'] ?>" 
                                            data-type="<?= $type['type'] ?>"
                                            <?= (old('variation_type_id') == $type['id'] || $selectedTypeId == $type['id']) ? 'selected' : '' ?>>
                                        <?= esc($type['display_name']) ?> (<?= ucfirst($type['type']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Option Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?= old('name') ?>" required>
                                    <div class="form-text">Display name for this option (e.g., "Small", "Red", "Cotton")</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="value" class="form-label">Option Value <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="value" name="value" 
                                           value="<?= old('value') ?>" required>
                                    <div class="form-text">Internal value (e.g., "S", "red", "cotton")</div>
                                </div>
                            </div>
                        </div>

                        <!-- Color-specific field -->
                        <div class="mb-3" id="color_field" style="display: none;">
                            <label for="color_code" class="form-label">Color Code</label>
                            <div class="input-group">
                                <input type="color" class="form-control form-control-color" id="color_code" name="color_code" 
                                       value="<?= old('color_code', '#000000') ?>">
                                <input type="text" class="form-control" id="color_code_text" 
                                       value="<?= old('color_code', '#000000') ?>" placeholder="#000000">
                            </div>
                            <div class="form-text">Hex color code for color swatches</div>
                        </div>

                        <!-- Image-specific field -->
                        <div class="mb-3" id="image_field" style="display: none;">
                            <label for="image" class="form-label">Option Image</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            <div class="form-text">Upload an image for this option (optional)</div>
                        </div>

                        <div class="mb-3">
                            <label for="sort_order" class="form-label">Sort Order</label>
                            <input type="number" class="form-control" id="sort_order" name="sort_order"
                                   value="<?= old('sort_order', 0) ?>" min="0">
                            <div class="form-text">Order in which this option appears (0 = first)</div>
                        </div>

                        <!-- Pricing Section -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="card-title mb-0">Pricing (Optional)</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="price_modifier" class="form-label">Price Modifier</label>
                                            <input type="number" class="form-control" id="price_modifier" name="price_modifier"
                                                   value="<?= old('price_modifier', 0) ?>" step="0.01">
                                            <div class="form-text">Additional cost for this option (can be negative for discounts)</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="price_type" class="form-label">Type</label>
                                            <select class="form-select" id="price_type" name="price_type">
                                                <option value="fixed" <?= old('price_type') === 'fixed' ? 'selected' : '' ?>>Fixed Amount</option>
                                                <option value="percentage" <?= old('price_type') === 'percentage' ? 'selected' : '' ?>>Percentage</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info">
                                    <small>
                                        <strong>Examples:</strong><br>
                                        • Fixed: +5.00 adds $5 to the base price<br>
                                        • Fixed: -2.00 reduces price by $2<br>
                                        • Percentage: +10 adds 10% to the base price<br>
                                        • Percentage: -5 gives 5% discount
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Preview Section -->
                        <div class="mb-4">
                            <h6>Preview</h6>
                            <div class="border rounded p-3 bg-light">
                                <div id="preview-container">
                                    <div class="text-muted">Select a variation type to see preview</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('admin/product-variations') ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Create Option
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
    const variationTypeSelect = document.getElementById('variation_type_id');
    const nameInput = document.getElementById('name');
    const valueInput = document.getElementById('value');
    const colorField = document.getElementById('color_field');
    const imageField = document.getElementById('image_field');
    const colorCodeInput = document.getElementById('color_code');
    const colorCodeTextInput = document.getElementById('color_code_text');
    const priceModifierInput = document.getElementById('price_modifier');
    const priceTypeSelect = document.getElementById('price_type');
    const previewContainer = document.getElementById('preview-container');

    function updateFieldsAndPreview() {
        const selectedOption = variationTypeSelect.options[variationTypeSelect.selectedIndex];
        const type = selectedOption ? selectedOption.dataset.type : '';

        // Show/hide type-specific fields
        colorField.style.display = type === 'color' ? 'block' : 'none';
        imageField.style.display = type === 'image' ? 'block' : 'none';

        // Update preview
        updatePreview();
    }

    function updatePreview() {
        const selectedOption = variationTypeSelect.options[variationTypeSelect.selectedIndex];
        const type = selectedOption ? selectedOption.dataset.type : '';
        const typeName = selectedOption ? selectedOption.textContent.split(' (')[0] : '';
        const optionName = nameInput.value || 'Option Name';
        const colorCode = colorCodeInput.value;
        const priceModifier = parseFloat(priceModifierInput.value) || 0;
        const priceType = priceTypeSelect.value;

        let previewHtml = '';

        if (!type) {
            previewHtml = '<div class="text-muted">Select a variation type to see preview</div>';
        } else {
            previewHtml = `<label class="form-label fw-bold">${typeName}</label><div>`;
            
            switch (type) {
                case 'text':
                    previewHtml += `
                        <select class="form-select" disabled style="max-width: 200px;">
                            <option>${optionName}${getPriceDisplay()}</option>
                        </select>
                    `;
                    break;
                case 'button':
                    previewHtml += `
                        <button class="btn btn-outline-primary btn-sm" disabled>${optionName}${getPriceDisplay()}</button>
                    `;
                    break;
                case 'color':
                    previewHtml += `
                        <div class="d-flex align-items-center gap-2">
                            <div class="color-swatch" style="width: 30px; height: 30px; background-color: ${colorCode}; border: 2px solid #ddd; border-radius: 50%; cursor: pointer;"></div>
                            <span>${optionName}${getPriceDisplay()}</span>
                        </div>
                    `;
                    break;
                case 'image':
                    previewHtml += `
                        <div class="d-flex align-items-center gap-2">
                            <div class="border rounded p-2" style="width: 50px; height: 50px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-image text-muted"></i>
                            </div>
                            <span>${optionName}${getPriceDisplay()}</span>
                        </div>
                    `;
                    break;
            }
            previewHtml += '</div>';
        }

        previewContainer.innerHTML = previewHtml;

        function getPriceDisplay() {
            if (priceModifier === 0) return '';
            const sign = priceModifier > 0 ? '+' : '';
            const suffix = priceType === 'percentage' ? '%' : '';
            return ` <small class="text-muted">(${sign}${priceModifier}${suffix})</small>`;
        }
    }

    // Event listeners
    variationTypeSelect.addEventListener('change', updateFieldsAndPreview);
    nameInput.addEventListener('input', updatePreview);
    priceModifierInput.addEventListener('input', updatePreview);
    priceTypeSelect.addEventListener('change', updatePreview);
    colorCodeInput.addEventListener('input', function() {
        colorCodeTextInput.value = this.value;
        updatePreview();
    });
    colorCodeTextInput.addEventListener('input', function() {
        if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
            colorCodeInput.value = this.value;
            updatePreview();
        }
    });

    // Auto-generate value from name
    nameInput.addEventListener('input', function() {
        if (!valueInput.value || valueInput.value === valueInput.dataset.oldValue) {
            valueInput.value = this.value.toLowerCase().replace(/[^a-z0-9]/g, '');
            valueInput.dataset.oldValue = valueInput.value;
        }
    });

    // Initialize
    updateFieldsAndPreview();
});
</script>

<?= $this->endSection() ?>
