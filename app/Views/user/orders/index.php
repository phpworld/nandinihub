<?php
// app/Views/user/orders/index.php
// Profile-style layout for orders page
?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="container py-4">
    <div class="row">
        <div class="col-md-3">
            <?= $this->include('user/sidebar') ?>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">My Orders</h5>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    <?php if (empty($orders)): ?>
                        <div class="alert alert-info mb-0">You have not placed any orders yet.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td><?= esc($order['order_number']) ?></td>
                                            <td><?= date('d M Y', strtotime($order['created_at'])) ?></td>
                                            <td><span class="badge bg-<?= $order['status'] === 'completed' ? 'success' : ($order['status'] === 'pending' ? 'warning' : 'secondary') ?>"><?= ucfirst($order['status']) ?></span></td>
                                            <td>₹<?= number_format($order['total'], 2) ?></td>
                                            <td><a href="<?= base_url('orders/view/' . $order['id']) ?>" class="btn btn-sm btn-outline-primary">View</a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>