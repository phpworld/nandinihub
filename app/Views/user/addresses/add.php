<?php
// app/Views/user/addresses/add.php
$this->extend('layouts/main');
$this->section('content');
?>
<div class="container py-4">
    <div class="row">
        <div class="col-md-3">
            <?= $this->include('user/addresses/sidebar') ?>
        </div>
        <div class="col-md-9">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Add New Address</h5>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('addresses/add') ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required value="<?= old('name') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" required value="<?= old('phone') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="address_line1" class="form-label">Address Line 1</label>
                            <input type="text" class="form-control" id="address_line1" name="address_line1" required value="<?= old('address_line1') ?>" placeholder="House/Flat No., Building Name, Street">
                        </div>
                        <div class="mb-3">
                            <label for="address_line2" class="form-label">Address Line 2 (Optional)</label>
                            <input type="text" class="form-control" id="address_line2" name="address_line2" value="<?= old('address_line2') ?>" placeholder="Area, Locality">
                        </div>
                        <div class="mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control" id="city" name="city" required value="<?= old('city') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="state" class="form-label">State</label>
                            <input type="text" class="form-control" id="state" name="state" required value="<?= old('state') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="pincode" class="form-label">Pincode</label>
                            <input type="text" class="form-control" id="pincode" name="pincode" required value="<?= old('pincode') ?>">
                        </div>
                        <div class="mb-3">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" class="form-control" id="country" name="country" value="<?= old('country', 'India') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="landmark" class="form-label">Landmark (Optional)</label>
                            <input type="text" class="form-control" id="landmark" name="landmark" value="<?= old('landmark') ?>" placeholder="Near landmark">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_default" name="is_default" value="1" <?= old('is_default') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_default">
                                Set as default address
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Address</button>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Saved Addresses</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($addresses)): ?>
                        <div class="alert alert-info mb-0">No addresses found. Add your first delivery address.</div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($addresses as $address): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        <div class="card-body">
                                            <h6 class="card-title mb-1"><?= esc($address['name']) ?> <?= !empty($address['is_default']) ? '<span class=\'badge bg-success\'>Default</span>' : '' ?></h6>
                                            <p class="mb-1"><strong>Phone:</strong> <?= esc($address['phone']) ?></p>
                                            <p class="mb-1"><strong>Address:</strong>
                                                <?= esc($address['address_line1']) ?>
                                                <?php if (!empty($address['address_line2'])): ?>, <?= esc($address['address_line2']) ?><?php endif; ?>
                                                <?php if (!empty($address['landmark'])): ?>, Near <?= esc($address['landmark']) ?><?php endif; ?>
                                            </p>
                                            <p class="mb-1"><strong>City:</strong> <?= esc($address['city']) ?>, <strong>State:</strong> <?= esc($address['state']) ?></p>
                                            <p class="mb-1"><strong>Pincode:</strong> <?= esc($address['pincode']) ?></p>
                                            <?php if (!empty($address['country']) && $address['country'] !== 'India'): ?>
                                                <p class="mb-1"><strong>Country:</strong> <?= esc($address['country']) ?></p>
                                            <?php endif; ?>
                                            <div class="mt-2">
                                                <a href="<?= base_url('addresses/edit/' . $address['id']) ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                                <a href="<?= base_url('addresses/delete/' . $address['id']) ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this address?')">Delete</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>