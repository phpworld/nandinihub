<?= $this->extend('admin/layout/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('admin/coupons') ?>">Coupons</a></li>
        <li class="breadcrumb-item active"><?= $coupon ? 'Edit' : 'Create' ?> Coupon</li>
    </ol>
</nav>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-tag me-2"></i>
                        <?= $coupon ? 'Edit' : 'Create New' ?> Coupon
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($validation && $validation->getErrors()): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($validation->getErrors() as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="<?= $coupon ? base_url('admin/coupons/' . $coupon['id'] . '/update') : base_url('admin/coupons/store') ?>">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="code" class="form-label">Coupon Code *</label>
                                    <input type="text" class="form-control" id="code" name="code"
                                        value="<?= old('code', $coupon['code'] ?? '') ?>"
                                        placeholder="e.g., SAVE20" maxlength="50" required>
                                    <div class="form-text">Use uppercase letters and numbers only</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Coupon Name *</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                        value="<?= old('name', $coupon['name'] ?? '') ?>"
                                        placeholder="e.g., Save 20% Off" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="2"
                                placeholder="Brief description of the coupon"><?= old('description', $coupon['description'] ?? '') ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Discount Type *</label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="">Select Type</option>
                                        <option value="percentage" <?= old('type', $coupon['type'] ?? '') === 'percentage' ? 'selected' : '' ?>>
                                            Percentage (%)
                                        </option>
                                        <option value="fixed_amount" <?= old('type', $coupon['type'] ?? '') === 'fixed_amount' ? 'selected' : '' ?>>
                                            Fixed Amount (₹)
                                        </option>
                                        <option value="free_shipping" <?= old('type', $coupon['type'] ?? '') === 'free_shipping' ? 'selected' : '' ?>>
                                            Free Shipping
                                        </option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="value" class="form-label">Value *</label>
                                    <input type="number" class="form-control" id="value" name="value"
                                        value="<?= old('value', $coupon['value'] ?? '') ?>"
                                        step="0.01" min="0" required>
                                    <div class="form-text" id="value-help">Enter percentage or amount</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="minimum_order_amount" class="form-label">Minimum Order Amount</label>
                                    <input type="number" class="form-control" id="minimum_order_amount" name="minimum_order_amount"
                                        value="<?= old('minimum_order_amount', $coupon['minimum_order_amount'] ?? '0') ?>"
                                        step="0.01" min="0">
                                    <div class="form-text">₹0 for no minimum</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="maximum_discount_amount" class="form-label">Maximum Discount Amount</label>
                                    <input type="number" class="form-control" id="maximum_discount_amount" name="maximum_discount_amount"
                                        value="<?= old('maximum_discount_amount', $coupon['maximum_discount_amount'] ?? '') ?>"
                                        step="0.01" min="0">
                                    <div class="form-text">Leave empty for no limit</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="usage_limit_per_customer" class="form-label">Usage Limit Per Customer *</label>
                                    <input type="number" class="form-control" id="usage_limit_per_customer" name="usage_limit_per_customer"
                                        value="<?= old('usage_limit_per_customer', $coupon['usage_limit_per_customer'] ?? '1') ?>"
                                        min="1" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="usage_limit" class="form-label">Total Usage Limit</label>
                                    <input type="number" class="form-control" id="usage_limit" name="usage_limit"
                                        value="<?= old('usage_limit', $coupon['usage_limit'] ?? '') ?>"
                                        min="1">
                                    <div class="form-text">Leave empty for unlimited</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="valid_from" class="form-label">Valid From</label>
                                    <input type="datetime-local" class="form-control" id="valid_from" name="valid_from"
                                        value="<?= old('valid_from', isset($coupon['valid_from']) && $coupon['valid_from'] ? date('Y-m-d\TH:i', strtotime($coupon['valid_from'])) : '') ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="valid_until" class="form-label">Valid Until</label>
                                    <input type="datetime-local" class="form-control" id="valid_until" name="valid_until"
                                        value="<?= old('valid_until', isset($coupon['valid_until']) && $coupon['valid_until'] ? date('Y-m-d\TH:i', strtotime($coupon['valid_until'])) : '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                    <?= old('is_active', $coupon['is_active'] ?? true) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active">
                                    Active (coupon can be used)
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?= $coupon ? 'Update' : 'Create' ?> Coupon
                            </button>
                            <a href="<?= base_url('admin/coupons') ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Coupon Preview</h6>
                </div>
                <div class="card-body">
                    <div id="coupon-preview" class="border rounded p-3 text-center bg-light">
                        <div class="h5 mb-2" id="preview-code">COUPON CODE</div>
                        <div class="text-muted mb-2" id="preview-name">Coupon Name</div>
                        <div class="h4 text-primary" id="preview-value">0% OFF</div>
                        <div class="small text-muted" id="preview-conditions">No conditions</div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Tips</h6>
                </div>
                <div class="card-body">
                    <ul class="small mb-0">
                        <li>Use clear, memorable coupon codes</li>
                        <li>Set appropriate minimum order amounts</li>
                        <li>Consider maximum discount limits for percentage coupons</li>
                        <li>Test coupons before making them active</li>
                        <li>Monitor usage statistics regularly</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Update preview when form changes
    function updatePreview() {
        const code = document.getElementById('code').value || 'COUPON CODE';
        const name = document.getElementById('name').value || 'Coupon Name';
        const type = document.getElementById('type').value;
        const value = document.getElementById('value').value || '0';
        const minOrder = document.getElementById('minimum_order_amount').value || '0';

        document.getElementById('preview-code').textContent = code;
        document.getElementById('preview-name').textContent = name;

        let valueText = '0% OFF';
        if (type === 'percentage') {
            valueText = value + '% OFF';
        } else if (type === 'fixed_amount') {
            valueText = '₹' + value + ' OFF';
        } else if (type === 'free_shipping') {
            valueText = 'FREE SHIPPING';
        }
        document.getElementById('preview-value').textContent = valueText;

        let conditions = 'No conditions';
        if (parseFloat(minOrder) > 0) {
            conditions = 'Min order: ₹' + minOrder;
        }
        document.getElementById('preview-conditions').textContent = conditions;
    }

    // Update value help text based on type
    function updateValueHelp() {
        const type = document.getElementById('type').value;
        const helpText = document.getElementById('value-help');

        if (type === 'percentage') {
            helpText.textContent = 'Enter percentage (e.g., 10 for 10%)';
        } else if (type === 'fixed_amount') {
            helpText.textContent = 'Enter amount in rupees';
        } else if (type === 'free_shipping') {
            helpText.textContent = 'Enter shipping amount to waive';
        } else {
            helpText.textContent = 'Enter percentage or amount';
        }
    }

    // Add event listeners
    document.getElementById('code').addEventListener('input', updatePreview);
    document.getElementById('name').addEventListener('input', updatePreview);
    document.getElementById('type').addEventListener('change', function() {
        updateValueHelp();
        updatePreview();
    });
    document.getElementById('value').addEventListener('input', updatePreview);
    document.getElementById('minimum_order_amount').addEventListener('input', updatePreview);

    // Initialize
    updateValueHelp();
    updatePreview();

    // Auto-uppercase coupon code
    document.getElementById('code').addEventListener('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    });
</script>
<?= $this->endSection() ?>