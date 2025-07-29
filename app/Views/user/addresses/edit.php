<?php
// app/Views/user/addresses/edit.php
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
                    <h5 class="mb-0">Edit Address</h5>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('addresses/edit/' . $address['id']) ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" required value="<?= old('name', $address['name']) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" required value="<?= old('phone', $address['phone']) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="address_line1" class="form-label">Address Line 1</label>
                            <input type="text" class="form-control" id="address_line1" name="address_line1" required value="<?= old('address_line1', $address['address_line1']) ?>" placeholder="House/Flat No., Building Name, Street">
                        </div>
                        <div class="mb-3">
                            <label for="address_line2" class="form-label">Address Line 2 (Optional)</label>
                            <input type="text" class="form-control" id="address_line2" name="address_line2" value="<?= old('address_line2', $address['address_line2']) ?>" placeholder="Area, Locality">
                        </div>
                        <div class="mb-3">
                            <label for="city" class="form-label">City</label>
                            <input type="text" class="form-control" id="city" name="city" required value="<?= old('city', $address['city']) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="state" class="form-label">State</label>
                            <input type="text" class="form-control" id="state" name="state" required value="<?= old('state', $address['state']) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="pincode" class="form-label">Pincode</label>
                            <input type="text" class="form-control" id="pincode" name="pincode" required value="<?= old('pincode', $address['pincode']) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="country" class="form-label">Country</label>
                            <input type="text" class="form-control" id="country" name="country" value="<?= old('country', $address['country'] ?? 'India') ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="landmark" class="form-label">Landmark (Optional)</label>
                            <input type="text" class="form-control" id="landmark" name="landmark" value="<?= old('landmark', $address['landmark']) ?>" placeholder="Near landmark">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="is_default" name="is_default" value="1" <?= old('is_default', $address['is_default']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_default">
                                Set as default address
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Address</button>
                        <a href="<?= base_url('addresses') ?>" class="btn btn-secondary ms-2">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>