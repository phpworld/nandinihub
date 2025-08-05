<?= $this->extend('admin/layout/main') ?>

<?= $this->section('breadcrumb') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="<?= base_url('admin/payment-methods') ?>">Payment Methods</a></li>
        <li class="breadcrumb-item active" aria-current="page">
            <?= $method ? 'Edit Payment Method' : 'Add Payment Method' ?>
        </li>
    </ol>
</nav>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-credit-card me-2"></i>
                    <?= $method ? 'Edit Payment Method' : 'Add Payment Method' ?>
                </h1>
                <a href="<?= base_url('admin/payment-methods') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Payment Method Details</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" 
                                  action="<?= $method ? base_url('admin/payment-methods/' . $method['id'] . '/update') : base_url('admin/payment-methods/store') ?>"
                                  enctype="multipart/form-data">
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">
                                                Payment Method Name <span class="text-danger">*</span>
                                            </label>
                                            <input type="text" 
                                                   class="form-control" 
                                                   id="name" 
                                                   name="name" 
                                                   value="<?= old('name', $method['name'] ?? '') ?>" 
                                                   required
                                                   placeholder="e.g., Bitcoin (BTC), USDT (Tether), Ethereum (ETH)">
                                            <div class="form-text">Enter a descriptive name for this payment method</div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="sort_order" class="form-label">Sort Order</label>
                                            <input type="number" 
                                                   class="form-control" 
                                                   id="sort_order" 
                                                   name="sort_order" 
                                                   value="<?= old('sort_order', $method['sort_order'] ?? $nextSortOrder ?? 0) ?>" 
                                                   min="0">
                                            <div class="form-text">Lower numbers appear first</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="wallet_address" class="form-label">
                                        Wallet Address <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control" 
                                              id="wallet_address" 
                                              name="wallet_address" 
                                              rows="3" 
                                              placeholder="Enter the cryptocurrency wallet address where payments should be sent"><?= old('wallet_address', $method['wallet_address'] ?? '') ?></textarea>
                                    <div class="form-text">This address will be displayed to customers for payments</div>
                                </div>

                                <div class="mb-3">
                                    <label for="qr_code" class="form-label">QR Code Image</label>
                                    <input type="file" 
                                           class="form-control" 
                                           id="qr_code" 
                                           name="qr_code" 
                                           accept="image/*">
                                    <div class="form-text">Upload a QR code image for easy payment scanning (JPEG, PNG, GIF - Max 2MB)</div>
                                    
                                    <?php if ($method && !empty($method['qr_code'])): ?>
                                        <div class="mt-2">
                                            <small class="text-muted">Current QR Code:</small><br>
                                            <img src="<?= base_url($method['qr_code']) ?>" 
                                                 alt="Current QR Code" 
                                                 class="img-thumbnail mt-1" 
                                                 style="max-width: 150px;">
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3">
                                    <label for="payment_information" class="form-label">Payment Information</label>
                                    <textarea class="form-control" 
                                              id="payment_information" 
                                              name="payment_information" 
                                              rows="4" 
                                              placeholder="Enter additional payment instructions or information for customers"><?= old('payment_information', $method['payment_information'] ?? '') ?></textarea>
                                    <div class="form-text">Additional instructions or information that will be shown to customers</div>
                                </div>

                                <div class="mb-4">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" 
                                               type="checkbox" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1" 
                                               <?= old('is_active', $method['is_active'] ?? 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="is_active">
                                            <strong>Active</strong>
                                            <small class="text-muted d-block">Enable this payment method for customers</small>
                                        </label>
                                    </div>
                                </div>

                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>
                                        <?= $method ? 'Update Payment Method' : 'Create Payment Method' ?>
                                    </button>
                                    <a href="<?= base_url('admin/payment-methods') ?>" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-info-circle me-2"></i>Tips
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-lightbulb me-2"></i>Best Practices:</h6>
                                <ul class="mb-0 small">
                                    <li><strong>Name:</strong> Use clear, recognizable names like "Bitcoin (BTC)"</li>
                                    <li><strong>Wallet Address:</strong> Double-check addresses for accuracy</li>
                                    <li><strong>QR Code:</strong> Generate QR codes that include the wallet address</li>
                                    <li><strong>Instructions:</strong> Include network information (e.g., TRC20, ERC20)</li>
                                    <li><strong>Sort Order:</strong> Popular methods should have lower numbers</li>
                                </ul>
                            </div>

                            <div class="alert alert-warning">
                                <h6><i class="fas fa-exclamation-triangle me-2"></i>Security Notes:</h6>
                                <ul class="mb-0 small">
                                    <li>Always verify wallet addresses before saving</li>
                                    <li>Use dedicated wallets for business transactions</li>
                                    <li>Keep private keys secure and never share them</li>
                                    <li>Test with small amounts first</li>
                                </ul>
                            </div>

                            <?php if ($method): ?>
                                <div class="alert alert-secondary">
                                    <h6><i class="fas fa-clock me-2"></i>Last Updated:</h6>
                                    <small><?= date('F j, Y \a\t g:i A', strtotime($method['updated_at'])) ?></small>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}

.alert h6 {
    margin-bottom: 0.5rem;
}

.alert ul {
    padding-left: 1.2rem;
}

.img-thumbnail {
    border: 1px solid #dee2e6;
}
</style>
<?= $this->endSection() ?>
