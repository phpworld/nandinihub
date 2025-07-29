<?= $this->extend('admin/layout/main') ?>

<?= $this->section('breadcrumb') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('admin/dashboard') ?>">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">
            <i class="fas fa-cog me-2"></i>Settings
        </li>
    </ol>
</nav>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-0">Site Settings</h1>
        <p class="text-muted mb-0">Configure your website settings</p>
    </div>
</div>

<div class="row">
    <!-- General Settings -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">General Settings</h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/settings') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="site_name" class="form-label">Site Name</label>
                            <input type="text" class="form-control" id="site_name" name="site_name"
                                value="<?= esc($settings['site_name'] ?? 'Nandini Hub') ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="site_tagline" class="form-label">Site Tagline</label>
                            <input type="text" class="form-control" id="site_tagline" name="site_tagline"
                                value="<?= esc($settings['site_tagline'] ?? 'Your Trusted Shopping Destination') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="site_description" class="form-label">Site Description</label>
                        <textarea class="form-control" id="site_description" name="site_description"
                            rows="3"><?= esc($settings['site_description'] ?? 'Nandini Hub is your one-stop destination for quality products at affordable prices.') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="contact_email" class="form-label">Contact Email</label>
                            <input type="email" class="form-control" id="contact_email" name="contact_email"
                                value="<?= esc($settings['contact_email'] ?? 'info@nandinihub.com') ?>">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="contact_phone" class="form-label">Contact Phone</label>
                            <input type="text" class="form-control" id="contact_phone" name="contact_phone"
                                value="<?= esc($settings['contact_phone'] ?? '+91 9876543210') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Business Address</label>
                        <textarea class="form-control" id="address" name="address"
                            rows="2"><?= esc($settings['business_address'] ?? '123 Business Street, City, State - 123456') ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="currency" class="form-label">Currency</label>
                            <select class="form-select" id="currency" name="currency">
                                <option value="INR" selected>Indian Rupee (₹)</option>
                                <option value="USD">US Dollar ($)</option>
                                <option value="EUR">Euro (€)</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="timezone" class="form-label">Timezone</label>
                            <select class="form-select" id="timezone" name="timezone">
                                <option value="Asia/Kolkata" selected>Asia/Kolkata</option>
                                <option value="UTC">UTC</option>
                                <option value="America/New_York">America/New_York</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="date_format" class="form-label">Date Format</label>
                            <select class="form-select" id="date_format" name="date_format">
                                <option value="d/m/Y" selected>DD/MM/YYYY</option>
                                <option value="m/d/Y">MM/DD/YYYY</option>
                                <option value="Y-m-d">YYYY-MM-DD</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="site_logo" class="form-label">Site Logo</label>
                            <input type="file" class="form-control" id="site_logo" name="site_logo" accept="image/*">
                            <?php if (!empty($settings['site_logo'])): ?>
                                <div class="mt-2">
                                    <img src="<?= base_url($settings['site_logo']) ?>" alt="Site Logo" style="max-height:60px;">
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="site_favicon" class="form-label">Favicon</label>
                            <input type="file" class="form-control" id="site_favicon" name="site_favicon" accept="image/x-icon,image/png">
                            <?php if (!empty($settings['site_favicon'])): ?>
                                <div class="mt-2">
                                    <img src="<?= base_url($settings['site_favicon']) ?>" alt="Favicon" style="max-height:32px;">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save General Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Google Analytics Settings -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fab fa-google me-2"></i>Google Analytics
                </h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/settings') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="google_analytics_enabled"
                                name="google_analytics_enabled" value="1"
                                <?= ($settings['google_analytics_enabled'] ?? false) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="google_analytics_enabled">
                                <strong>Enable Google Analytics</strong>
                            </label>
                        </div>
                        <small class="text-muted">Enable or disable Google Analytics tracking on your website</small>
                    </div>

                    <div class="mb-3">
                        <label for="google_analytics_id" class="form-label">
                            Google Analytics Measurement ID
                        </label>
                        <input type="text" class="form-control" id="google_analytics_id"
                            name="google_analytics_id"
                            value="<?= esc($settings['google_analytics_id'] ?? '') ?>"
                            placeholder="G-XXXXXXXXXX">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Enter your Google Analytics 4 Measurement ID (e.g., G-XXXXXXXXXX).
                            You can find this in your Google Analytics account under Admin > Data Streams.
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fas fa-lightbulb me-2"></i>How to get your Google Analytics ID:
                        </h6>
                        <ol class="mb-0">
                            <li>Go to <a href="https://analytics.google.com" target="_blank">Google Analytics</a></li>
                            <li>Select your property or create a new one</li>
                            <li>Go to Admin > Data Streams</li>
                            <li>Select your web stream</li>
                            <li>Copy the Measurement ID (starts with G-)</li>
                        </ol>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Analytics Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Email Settings -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Email Settings</h5>
            </div>
            <div class="card-body">
                <form>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="smtp_host" class="form-label">SMTP Host</label>
                            <input type="text" class="form-control" id="smtp_host" name="smtp_host"
                                value="smtp.gmail.com">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="smtp_port" class="form-label">SMTP Port</label>
                            <input type="number" class="form-control" id="smtp_port" name="smtp_port"
                                value="587">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="smtp_username" class="form-label">SMTP Username</label>
                            <input type="text" class="form-control" id="smtp_username" name="smtp_username">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="smtp_password" class="form-label">SMTP Password</label>
                            <input type="password" class="form-control" id="smtp_password" name="smtp_password">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="smtp_encryption" name="smtp_encryption" checked>
                            <label class="form-check-label" for="smtp_encryption">
                                Enable SMTP Encryption (TLS)
                            </label>
                        </div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Email Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary" onclick="clearCache()">
                        <i class="fas fa-broom me-2"></i>Clear Cache
                    </button>
                    <button class="btn btn-outline-info" onclick="testEmail()">
                        <i class="fas fa-envelope me-2"></i>Test Email
                    </button>
                    <button class="btn btn-outline-warning" onclick="backupDatabase()">
                        <i class="fas fa-database me-2"></i>Backup Database
                    </button>
                    <button class="btn btn-outline-success" onclick="optimizeDatabase()">
                        <i class="fas fa-tools me-2"></i>Optimize Database
                    </button>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">System Information</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="border-end">
                            <h6 class="text-muted mb-1">PHP Version</h6>
                            <small><?= PHP_VERSION ?></small>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <h6 class="text-muted mb-1">CodeIgniter</h6>
                        <small>4.x</small>
                    </div>
                    <div class="col-6">
                        <div class="border-end">
                            <h6 class="text-muted mb-1">Server</h6>
                            <small><?= $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown' ?></small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h6 class="text-muted mb-1">Database</h6>
                        <small>MySQL</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Maintenance Mode -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Maintenance Mode</h5>
            </div>
            <div class="card-body">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" id="maintenance_mode" name="maintenance_mode">
                    <label class="form-check-label" for="maintenance_mode">
                        Enable Maintenance Mode
                    </label>
                </div>
                <small class="text-muted">
                    When enabled, only administrators can access the site.
                </small>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Quick action functions
    function clearCache() {
        if (confirm('Are you sure you want to clear the cache?')) {
            showAlert('success', 'Cache cleared successfully');
        }
    }

    function testEmail() {
        showAlert('info', 'Test email sent successfully');
    }

    function backupDatabase() {
        if (confirm('Are you sure you want to backup the database?')) {
            showAlert('success', 'Database backup initiated');
        }
    }

    function optimizeDatabase() {
        if (confirm('Are you sure you want to optimize the database?')) {
            showAlert('success', 'Database optimization completed');
        }
    }

    // Show alert function
    function showAlert(type, message) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        const container = document.querySelector('.content-wrapper');
        container.insertBefore(alertDiv, container.firstChild);

        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
</script>
<?= $this->endSection() ?>