<div class="card mb-3">
    <div class="card-body text-center">
        <i class="fas fa-user-circle fa-3x text-primary mb-2"></i>
        <h6><?= esc(session('user_name')) ?></h6>
    </div>
</div>
<div class="list-group">
    <a href="<?= base_url('profile') ?>" class="list-group-item list-group-item-action<?= service('uri')->getSegment(1) === 'profile' ? ' active' : '' ?>">
        <i class="fas fa-user me-2"></i>Profile
    </a>
    <a href="<?= base_url('orders') ?>" class="list-group-item list-group-item-action<?= service('uri')->getSegment(1) === 'orders' ? ' active' : '' ?>">
        <i class="fas fa-shopping-bag me-2"></i>My Orders
    </a>
    <a href="<?= base_url('addresses') ?>" class="list-group-item list-group-item-action<?= service('uri')->getSegment(1) === 'addresses' ? ' active' : '' ?>">
        <i class="fas fa-map-marker-alt me-2"></i>Addresses
    </a>
    <a href="<?= base_url('wishlist') ?>" class="list-group-item list-group-item-action<?= service('uri')->getSegment(1) === 'wishlist' ? ' active' : '' ?>">
        <i class="fas fa-heart me-2"></i>My Wishlist
    </a>
</div>