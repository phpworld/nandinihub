<?= $this->extend('admin/layout/main') ?>

<?= $this->section('title') ?>
<?= $title ?>
<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('admin/shipping') ?>">Shipping Methods</a></li>
        <li class="breadcrumb-item active"><?= $method ? 'Edit' : 'Add' ?> Shipping Method</li>
    </ol>
</nav>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $method ? 'Edit' : 'Add' ?> Shipping Method</h1>
        <a href="<?= base_url('admin/shipping') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Validation Errors -->
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <h6>Please fix the following errors:</h6>
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Shipping Method Details</h6>
                </div>
                <div class="card-body">
                    <form action="<?= $method ? base_url('admin/shipping/' . $method['id'] . '/update') : base_url('admin/shipping/store') ?>" 
                          method="POST" id="shippingMethodForm">
                        <?= csrf_field() ?>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Method Name <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="name" 
                                       name="name" 
                                       value="<?= old('name', $method['name'] ?? '') ?>" 
                                       required>
                                <div class="form-text">e.g., Standard Shipping, Express Delivery</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="delivery_time" class="form-label">Delivery Time <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="delivery_time" 
                                       name="delivery_time" 
                                       value="<?= old('delivery_time', $method['delivery_time'] ?? '') ?>" 
                                       required>
                                <div class="form-text">e.g., 3-5 Business Days, Same Day</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" 
                                      id="description" 
                                      name="description" 
                                      rows="3"><?= old('description', $method['description'] ?? '') ?></textarea>
                            <div class="form-text">Optional description for customers</div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="cost" class="form-label">Shipping Cost (₹) <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control" 
                                       id="cost" 
                                       name="cost" 
                                       value="<?= old('cost', $method['cost'] ?? '0') ?>" 
                                       min="0" 
                                       step="0.01" 
                                       required>
                                <div class="form-text">Set to 0 for free shipping</div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="minimum_order_amount" class="form-label">Minimum Order Amount (₹)</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="minimum_order_amount" 
                                       name="minimum_order_amount" 
                                       value="<?= old('minimum_order_amount', $method['minimum_order_amount'] ?? '0') ?>" 
                                       min="0" 
                                       step="0.01">
                                <div class="form-text">Minimum order value required</div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <label for="maximum_order_amount" class="form-label">Maximum Order Amount (₹)</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="maximum_order_amount" 
                                       name="maximum_order_amount" 
                                       value="<?= old('maximum_order_amount', $method['maximum_order_amount'] ?? '') ?>" 
                                       min="0" 
                                       step="0.01">
                                <div class="form-text">Leave empty for no maximum</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <input type="number" 
                                       class="form-control" 
                                       id="sort_order" 
                                       name="sort_order" 
                                       value="<?= old('sort_order', $method['sort_order'] ?? '0') ?>" 
                                       min="0">
                                <div class="form-text">Display order (lower numbers first)</div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_free_shipping" 
                                           name="is_free_shipping" 
                                           value="1"
                                           <?= old('is_free_shipping', $method['is_free_shipping'] ?? 0) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_free_shipping">
                                        Free Shipping Method
                                    </label>
                                    <div class="form-text">Mark as a free shipping option</div>
                                </div>
                            </div>
                            
                            <div class="col-md-4 mb-3">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           id="is_active" 
                                           name="is_active" 
                                           value="1"
                                           <?= old('is_active', $method['is_active'] ?? 1) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="is_active">
                                        Active
                                    </label>
                                    <div class="form-text">Enable this shipping method</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('admin/shipping') ?>" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> <?= $method ? 'Update' : 'Create' ?> Shipping Method
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Preview Card -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Preview</h6>
                </div>
                <div class="card-body">
                    <div id="shipping-preview">
                        <div class="shipping-method-preview p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="mb-1" id="preview-name">Shipping Method Name</h6>
                                    <small class="text-muted" id="preview-description">Description will appear here</small>
                                </div>
                                <span class="badge bg-primary" id="preview-cost">₹0.00</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-info" id="preview-delivery">Delivery time</small>
                                <small class="text-muted" id="preview-minimum">Min: ₹0</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <h6 class="text-muted">Tips:</h6>
                        <ul class="small text-muted">
                            <li>Use clear, descriptive names</li>
                            <li>Set appropriate minimum order amounts</li>
                            <li>Consider offering free shipping for higher order values</li>
                            <li>Use sort order to prioritize methods</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Update preview when form fields change
    function updatePreview() {
        const name = $('#name').val() || 'Shipping Method Name';
        const description = $('#description').val() || 'Description will appear here';
        const cost = parseFloat($('#cost').val()) || 0;
        const deliveryTime = $('#delivery_time').val() || 'Delivery time';
        const minimumAmount = parseFloat($('#minimum_order_amount').val()) || 0;
        const isFreeShipping = $('#is_free_shipping').is(':checked');
        
        $('#preview-name').text(name);
        $('#preview-description').text(description);
        $('#preview-delivery').text(deliveryTime);
        $('#preview-minimum').text(`Min: ₹${minimumAmount.toFixed(2)}`);
        
        if (cost === 0 || isFreeShipping) {
            $('#preview-cost').removeClass('bg-primary').addClass('bg-success').text('Free');
        } else {
            $('#preview-cost').removeClass('bg-success').addClass('bg-primary').text(`₹${cost.toFixed(2)}`);
        }
    }

    // Bind events
    $('#name, #description, #cost, #delivery_time, #minimum_order_amount').on('input', updatePreview);
    $('#is_free_shipping').on('change', updatePreview);
    
    // Initial preview update
    updatePreview();

    // Form validation
    $('#shippingMethodForm').on('submit', function(e) {
        const name = $('#name').val().trim();
        const deliveryTime = $('#delivery_time').val().trim();
        const cost = $('#cost').val();
        
        if (!name) {
            e.preventDefault();
            alert('Please enter a shipping method name');
            $('#name').focus();
            return false;
        }
        
        if (!deliveryTime) {
            e.preventDefault();
            alert('Please enter delivery time');
            $('#delivery_time').focus();
            return false;
        }
        
        if (cost === '' || cost < 0) {
            e.preventDefault();
            alert('Please enter a valid cost (0 or greater)');
            $('#cost').focus();
            return false;
        }
    });
});
</script>
<?= $this->endSection() ?>
