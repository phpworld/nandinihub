<?php
$settingModel = new \App\Models\SettingModel();
$siteLogo = $settingModel->getSetting('site_logo', '');
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <?php if (!empty($siteLogo) && file_exists(FCPATH . $siteLogo)): ?>
                            <img src="<?= base_url($siteLogo) ?>" alt="Site Logo" style="height:60px;max-width:180px;object-fit:contain;" class="mb-3">
                        <?php else: ?>
                            <i class="fas fa-om fa-3x text-primary mb-3"></i>
                        <?php endif; ?>
                        <h2 class="h4">Welcome Back</h2>
                        <p class="text-muted">Sign in to your Nandini Hub account</p>
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

                    <form action="<?= base_url('login') ?>" method="POST">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?= old('email') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">
                                Remember me
                            </label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt"></i> Sign In
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="mb-2">Don't have an account?</p>
                        <a href="<?= base_url('register') ?>" class="btn btn-outline-primary">
                            <i class="fas fa-user-plus"></i> Create Account
                        </a>
                    </div>

                    <div class="text-center mt-3">
                        <a href="<?= base_url('forgot-password') ?>" class="text-muted text-decoration-none">
                            Forgot your password?
                        </a>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div class="row mt-4 text-center">
                <div class="col-4">
                    <i class="fas fa-shield-alt text-primary mb-2"></i>
                    <small class="d-block text-muted">Secure Login</small>
                </div>
                <div class="col-4">
                    <i class="fas fa-truck text-primary mb-2"></i>
                    <small class="d-block text-muted">Fast Checkout</small>
                </div>
                <div class="col-4">
                    <i class="fas fa-history text-primary mb-2"></i>
                    <small class="d-block text-muted">Order History</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>