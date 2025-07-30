<?php
$settingModel = new \App\Models\SettingModel();
$siteLogo = $settingModel->getSetting('site_logo', '');
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <?php if (!empty($siteLogo) && file_exists(FCPATH . $siteLogo)): ?>
                            <img src="<?= base_url($siteLogo) ?>" alt="Site Logo" style="height:60px;max-width:180px;object-fit:contain;" class="mb-3">
                        <?php else: ?>
                            <i class="fas fa-mushroom fa-3x text-primary mb-3"></i>
                        <?php endif; ?>
                        <h2 class="h4">Join Microdose Mushroom</h2>
                        <p class="text-muted">Create your account to start shopping</p>
                    </div>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('register') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name"
                                    value="<?= old('first_name') ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name"
                                    value="<?= old('last_name') ?>" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?= old('email') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number (Optional)</label>
                            <input type="tel" class="form-control" id="phone" name="phone"
                                value="<?= old('phone') ?>" placeholder="+91 12345 67890">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <small class="form-text text-muted">Minimum 6 characters</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="<?= base_url('terms') ?>" target="_blank">Terms of Service</a> and
                                <a href="<?= base_url('privacy') ?>" target="_blank">Privacy Policy</a>
                            </label>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="newsletter">
                            <label class="form-check-label" for="newsletter">
                                Subscribe to our newsletter for updates and special offers
                            </label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus"></i> Create Account
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="mb-2">Already have an account?</p>
                        <a href="<?= base_url('login') ?>" class="btn btn-outline-primary">
                            <i class="fas fa-sign-in-alt"></i> Sign In
                        </a>
                    </div>
                </div>
            </div>

            <!-- Benefits -->
            <div class="row mt-4 text-center">
                <div class="col-md-3 col-6 mb-3">
                    <i class="fas fa-shipping-fast text-primary fa-2x mb-2"></i>
                    <small class="d-block text-muted">Free Shipping on orders above ₹500</small>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <i class="fas fa-shield-alt text-primary fa-2x mb-2"></i>
                    <small class="d-block text-muted">100% Authentic Products</small>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <i class="fas fa-headset text-primary fa-2x mb-2"></i>
                    <small class="d-block text-muted">24/7 Customer Support</small>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <i class="fas fa-undo text-primary fa-2x mb-2"></i>
                    <small class="d-block text-muted">Easy Returns & Exchanges</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Password confirmation validation
    document.getElementById('confirm_password').addEventListener('input', function() {
        const password = document.getElementById('password').value;
        const confirmPassword = this.value;

        if (password !== confirmPassword) {
            this.setCustomValidity('Passwords do not match');
        } else {
            this.setCustomValidity('');
        }
    });
</script>
<?= $this->endSection() ?>