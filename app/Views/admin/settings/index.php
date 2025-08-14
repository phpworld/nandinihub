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
                                value="<?= esc($settings['site_name'] ?? 'Microdose Mushroom') ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="site_tagline" class="form-label">Site Tagline</label>
                            <input type="text" class="form-control" id="site_tagline" name="site_tagline"
                                value="<?= esc($settings['site_tagline'] ?? 'Your Trusted Microdose Destination') ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="site_description" class="form-label">Site Description</label>
                        <textarea class="form-control" id="site_description" name="site_description"
                            rows="3"><?= esc($settings['site_description'] ?? 'Microdose Mushroom is your one-stop destination for quality microdose products at affordable prices.') ?></textarea>
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
                                <?php
                                $currencyOptions = get_currency_options();
                                $selectedCurrency = $settings['currency'] ?? 'USD';
                                foreach ($currencyOptions as $code => $info): ?>
                                    <option value="<?= $code ?>" <?= $selectedCurrency === $code ? 'selected' : '' ?>>
                                        <?= esc($info['name']) ?> (<?= esc($info['symbol']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="timezone" class="form-label">Timezone</label>
                            <select class="form-select" id="timezone" name="timezone">
                                <?php
                                $timezoneOptions = get_timezone_options();
                                $selectedTimezone = $settings['timezone'] ?? 'Asia/Kolkata';
                                foreach ($timezoneOptions as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= $selectedTimezone === $value ? 'selected' : '' ?>>
                                        <?= esc($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="date_format" class="form-label">Date Format</label>
                            <select class="form-select" id="date_format" name="date_format">
                                <?php
                                $dateFormatOptions = get_date_format_options();
                                $selectedDateFormat = $settings['date_format'] ?? 'd/m/Y';
                                foreach ($dateFormatOptions as $value => $label): ?>
                                    <option value="<?= $value ?>" <?= $selectedDateFormat === $value ? 'selected' : '' ?>>
                                        <?= esc($label) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="currency_position" class="form-label">Currency Symbol Position</label>
                            <select class="form-select" id="currency_position" name="currency_position">
                                <option value="before" <?= ($settings['currency_position'] ?? 'before') === 'before' ? 'selected' : '' ?>>Before Amount ($100)</option>
                                <option value="after" <?= ($settings['currency_position'] ?? 'before') === 'after' ? 'selected' : '' ?>>After Amount (100$)</option>
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

        <!-- Footer Settings -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-align-left me-2"></i>Footer Settings
                </h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/settings') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="footer_text" class="form-label">Footer Description</label>
                        <textarea class="form-control" id="footer_text" name="footer_text" rows="3"
                            placeholder="Enter footer description text"><?= esc($settings['footer_text'] ?? 'Your trusted source for premium quality products. Bringing excellence to your doorstep.') ?></textarea>
                        <small class="text-muted">This text will appear in the footer section of your website</small>
                    </div>

                    <div class="mb-3">
                        <label for="footer_copyright" class="form-label">Copyright Text</label>
                        <input type="text" class="form-control" id="footer_copyright" name="footer_copyright"
                            value="<?= esc($settings['footer_copyright'] ?? '© 2024 Nandini Hub. All rights reserved.') ?>"
                            placeholder="© 2024 Your Company. All rights reserved.">
                        <small class="text-muted">Copyright text displayed in the footer</small>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Footer Settings
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

        <!-- Custom JavaScript Injection -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-code me-2"></i>Custom JavaScript Injection
                </h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('admin/settings') ?>" method="POST">
                    <?= csrf_field() ?>
                    <!-- Hidden field to ensure form always sends custom JS data -->
                    <input type="hidden" name="custom_js_form_submitted" value="1">

                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="custom_js_enabled"
                                name="custom_js_enabled" value="1"
                                <?= ($settings['custom_js_enabled'] ?? false) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="custom_js_enabled">
                                <strong>Enable Custom JavaScript</strong>
                            </label>
                        </div>
                        <small class="text-muted">Enable or disable custom JavaScript injection on your website</small>
                    </div>

                    <div class="mb-3">
                        <label for="custom_js_footer" class="form-label">Custom JavaScript Code</label>
                        <textarea class="form-control font-monospace" id="custom_js_footer" name="custom_js_footer"
                            rows="12" placeholder="<!-- Enter JavaScript code to be injected before closing </body> tag -->
<script>
// Your JavaScript code here
// Example: Tawk.to chat widget, analytics, etc.
</script>"><?= esc($settings['custom_js_footer'] ?? '') ?></textarea>
                        <small class="text-muted">JavaScript code will be injected before the closing &lt;/body&gt; tag on all user-facing pages</small>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> Only add trusted JavaScript code. Malicious code can compromise your website security.
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Common Use Cases:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Chat widgets (Tawk.to, Intercom, etc.)</li>
                            <li>Analytics tracking codes</li>
                            <li>Social media pixels</li>
                            <li>Custom tracking scripts</li>
                        </ul>
                    </div>

                    <div class="alert alert-light">
                        <h6><i class="fas fa-magic me-2"></i>Quick Insert Templates:</h6>
                        <div class="btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary btn-sm me-2" onclick="insertTawkTo()">
                                <i class="fas fa-comments me-1"></i>Tawk.to Chat
                            </button>

                        </div>
                        <small class="text-muted d-block mt-2">Click to insert template code. Remember to replace placeholder IDs with your actual IDs.</small>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save JavaScript Settings
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

    // Custom JavaScript injection functionality
    $(document).ready(function() {
        // Auto-resize textarea
        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }

        // Apply auto-resize to JavaScript textarea
        const jsTextarea = document.querySelector('#custom_js_footer');
        if (jsTextarea) {
            // Initial resize
            autoResize(jsTextarea);

            // Resize on input
            jsTextarea.addEventListener('input', function() {
                autoResize(this);
            });

            // Add styling
            jsTextarea.style.lineHeight = '1.5';
            jsTextarea.style.fontFamily = 'Monaco, Menlo, "Ubuntu Mono", monospace';
            jsTextarea.style.fontSize = '14px';
        }

        // Toggle custom JS sections based on enable checkbox
        $('#custom_js_enabled').change(function() {
            const isEnabled = $(this).is(':checked');
            $('#custom_js_footer').prop('disabled', !isEnabled);

            if (isEnabled) {
                $('#custom_js_footer').removeClass('bg-light');
            } else {
                $('#custom_js_footer').addClass('bg-light');
            }
        }).trigger('change');
    });

    // Quick insert functions
    function insertTawkTo() {
        var tawkCode = '<!--Start of Tawk.to Script-->' + '\n' +
            '<script type="text/javascript">' + '\n' +
            'var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();' + '\n' +
            '(function(){' + '\n' +
            'var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];' + '\n' +
            's1.async=true;' + '\n' +
            's1.src="https://embed.tawk.to/YOUR_TAWK_ID/default";' + '\n' +
            's1.charset="UTF-8";' + '\n' +
            's1.setAttribute("crossorigin","*");' + '\n' +
            's0.parentNode.insertBefore(s1,s0);' + '\n' +
            '})();' + '\n' +
            '<\/script>' + '\n' +
            '<!--End of Tawk.to Script-->';

        var textarea = document.getElementById('custom_js_footer');
        if (textarea) {
            textarea.value = tawkCode;
            textarea.dispatchEvent(new Event('input'));
            showToast('success', 'Tawk.to template inserted! Remember to replace YOUR_TAWK_ID with your actual Tawk.to ID.');
        }
    }



    function showToast(type, message) {
        // Simple toast notification
        var alertClass = type === 'success' ? 'success' : 'danger';
        var toast = $('<div class="alert alert-' + alertClass + ' alert-dismissible fade show position-fixed" ' +
                     'style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">' +
                     message +
                     '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                     '</div>');
        $('body').append(toast);
        setTimeout(function() { toast.alert('close'); }, 5000);
    }
</script>
<?= $this->endSection() ?>