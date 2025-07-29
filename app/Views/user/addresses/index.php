<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-4">
    <div class="row">
        <div class="col-md-3">
            <?= $this->include('user/sidebar') ?>
        </div>
        <div class="col-md-9">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">My Delivery Addresses</h5>
                    <a href="<?= base_url('addresses/add') ?>" class="btn btn-primary btn-sm">Add New Address</a>
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
<?= $this->endSection() ?>