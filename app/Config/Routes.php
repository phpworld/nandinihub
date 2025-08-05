<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Home routes
$routes->get('/', 'Home::index');

// Product routes
$routes->get('/products', 'ProductController::index');
$routes->get('/products/search', 'ProductController::search');
$routes->get('/category/(:segment)', 'ProductController::category/$1');
$routes->get('/product/(:segment)', 'ProductController::show/$1');

// Cart routes
$routes->get('/cart', 'CartController::index');
$routes->post('/cart/add', 'CartController::add');
$routes->post('/cart/update', 'CartController::update');
$routes->post('/cart/remove', 'CartController::remove');
$routes->get('/cart/clear', 'CartController::clear');
$routes->post('/cart/clear', 'CartController::clear');
$routes->get('/cart/count', 'CartController::getCartCount');

// Wishlist routes
$routes->get('/wishlist', 'WishlistController::index');
$routes->post('/wishlist/add', 'WishlistController::add');
$routes->post('/wishlist/remove', 'WishlistController::remove');
$routes->post('/wishlist/toggle', 'WishlistController::toggle');
$routes->get('/wishlist/clear', 'WishlistController::clear');
$routes->post('/wishlist/clear', 'WishlistController::clear');
$routes->get('/wishlist/count', 'WishlistController::getCount');
$routes->post('/wishlist/check', 'WishlistController::check');

// Authentication routes
$routes->get('/login', 'AuthController::login');
$routes->post('/login', 'AuthController::processLogin');
$routes->get('/register', 'AuthController::register');
$routes->post('/register', 'AuthController::processRegister');
$routes->get('/logout', 'AuthController::logout');
$routes->get('/profile', 'AuthController::profile');
$routes->post('/profile', 'AuthController::updateProfile');
$routes->get('/change-password', 'AuthController::changePassword');
$routes->post('/change-password', 'AuthController::updatePassword');

// Order routes
$routes->get('/orders', 'OrderController::index');
$routes->get('/orders/(:segment)', 'OrderController::show/$1');
$routes->get('/orders/(:segment)/payment', 'OrderController::showPaymentDetails/$1');
$routes->post('/orders/upload-payment-screenshot', 'OrderController::uploadPaymentScreenshot');
$routes->get('/checkout', 'OrderController::checkout');
$routes->post('/checkout', 'OrderController::processCheckout');
$routes->post('/orders/cancel/(:segment)', 'OrderController::cancelOrder/$1');



// Coupon routes
$routes->group('coupon', function ($routes) {
    $routes->post('apply', 'CouponController::apply');
    $routes->post('remove', 'CouponController::remove');
    $routes->post('validate', 'CouponController::validateCoupon');
    $routes->get('available', 'CouponController::getAvailable');
    $routes->get('check/(:segment)', 'CouponController::check/$1');
});

// Review routes
$routes->get('/product/(:segment)/review', 'ReviewController::create/$1');
$routes->post('/reviews', 'ReviewController::store');
$routes->post('/reviews/(:num)/helpful', 'ReviewController::helpful/$1');
$routes->get('/api/products/(:num)/reviews', 'ReviewController::getProductReviews/$1');

// Admin routes
$routes->group('admin', function ($routes) {
    $routes->get('/', 'AdminController::index');
    $routes->get('dashboard', 'AdminController::index');

    // Product management
    $routes->get('products', 'AdminController::products');
    $routes->get('products/create', 'AdminController::createProduct');
    $routes->post('products', 'AdminController::storeProduct');
    $routes->get('products/(:num)/edit', 'AdminController::editProduct/$1');
    $routes->post('products/(:num)', 'AdminController::updateProduct/$1');
    $routes->delete('products/(:num)', 'AdminController::deleteProduct/$1');

    // Product import
    $routes->get('products/import', 'AdminController::importProducts');
    $routes->post('products/import', 'AdminController::processImport');
    $routes->get('products/import/preview', 'AdminController::importPreview');
    $routes->post('products/import/execute', 'AdminController::executeImport');
    $routes->get('products/import/sample', 'AdminController::downloadSampleCsv');

    // AJAX Product endpoints
    $routes->post('products/(:num)/toggle-status', 'AdminController::toggleProductStatus/$1');
    $routes->post('products/(:num)/toggle-featured', 'AdminController::toggleProductFeatured/$1');
    $routes->post('products/bulk-action', 'AdminController::bulkProductAction');
    $routes->get('products/export', 'AdminController::exportProducts');

    // Product Variations
    $routes->get('product-variations', 'AdminController::productVariations');
    $routes->get('variation-types/create', 'AdminController::createVariationType');
    $routes->post('variation-types', 'AdminController::storeVariationType');
    $routes->get('variation-options/create/(:num)', 'AdminController::createVariationOption/$1');
    $routes->get('variation-options/create', 'AdminController::createVariationOption');
    $routes->post('variation-options', 'AdminController::storeVariationOption');
    $routes->get('products/(:num)/variants', 'AdminController::manageProductVariants/$1');
    $routes->post('products/generate-variants', 'AdminController::generateProductVariants');

    // AJAX Variation Management
    $routes->get('variation-types/(:num)/edit', 'AdminController::editVariationType/$1');
    $routes->post('variation-types/(:num)/update', 'AdminController::updateVariationType/$1');
    $routes->delete('variation-types/(:num)', 'AdminController::deleteVariationType/$1');
    $routes->get('variation-options/(:num)/edit', 'AdminController::editVariationOption/$1');
    $routes->post('variation-options/(:num)/update', 'AdminController::updateVariationOption/$1');
    $routes->delete('variation-options/(:num)', 'AdminController::deleteVariationOption/$1');
    $routes->get('product-variants/(:num)/edit', 'AdminController::editProductVariant/$1');
    $routes->post('product-variants/(:num)/update', 'AdminController::updateProductVariant/$1');
    $routes->delete('product-variants/(:num)', 'AdminController::deleteProductVariant/$1');
    $routes->post('product-variants/(:num)/duplicate', 'AdminController::duplicateProductVariant/$1');
    $routes->post('product-variants/(:num)/set-default', 'AdminController::setDefaultVariant/$1');
    $routes->post('product-variants/(:num)/merge-stock', 'AdminController::mergeVariantStock/$1');
    $routes->post('product-variants/(:num)/quick-stock', 'AdminController::quickStockUpdate/$1');

    // Category management
    $routes->get('categories', 'AdminController::categories');
    $routes->get('categories/create', 'AdminController::createCategory');
    $routes->post('categories', 'AdminController::storeCategory');
    $routes->get('categories/(:num)/edit', 'AdminController::editCategory/$1');
    $routes->post('categories/(:num)', 'AdminController::updateCategory/$1');
    $routes->delete('categories/(:num)', 'AdminController::deleteCategory/$1');

    // AJAX Category endpoints
    $routes->post('categories/(:num)/toggle-status', 'AdminController::toggleCategoryStatus/$1');
    $routes->get('categories/(:num)/product-count', 'AdminController::getCategoryProductCount/$1');

    // User management
    $routes->get('users', 'AdminController::users');
    $routes->get('users/(:num)', 'AdminController::viewUser/$1');
    $routes->post('users/(:num)/toggle-status', 'AdminController::toggleUserStatus/$1');

    // Order management
    $routes->get('orders', 'AdminController::orders');
    $routes->get('orders/(:num)', 'AdminController::viewOrder/$1');
    $routes->get('orders/(:num)/print', 'AdminController::printOrder/$1');
    $routes->get('orders/(:num)/pdf', 'AdminController::downloadOrderPdf/$1');
    $routes->post('orders/(:num)/status', 'AdminController::updateOrderStatus/$1');
    $routes->post('orders/(:num)/payment-status', 'AdminController::updatePaymentStatus/$1');
    $routes->post('orders/(:num)/verify-payment', 'AdminController::verifyPayment/$1');
    $routes->post('orders/(:num)/reject-payment', 'AdminController::rejectPayment/$1');


    // Review management
    $routes->get('reviews', 'AdminController::reviews');
    $routes->post('reviews/(:num)/approve', 'AdminController::approveReview/$1');
    $routes->post('reviews/(:num)/reject', 'AdminController::rejectReview/$1');
    $routes->delete('reviews/(:num)', 'AdminController::deleteReview/$1');
    $routes->post('reviews/(:num)/delete', 'AdminController::deleteReview/$1');

    // Banner management
    $routes->get('banners', 'AdminController::banners');
    $routes->get('banners/create', 'AdminController::createBanner');
    $routes->post('banners', 'AdminController::storeBanner');
    $routes->get('banners/(:num)/edit', 'AdminController::editBanner/$1');
    $routes->post('banners/(:num)', 'AdminController::updateBanner/$1');
    $routes->delete('banners/(:num)', 'AdminController::deleteBanner/$1');
    $routes->post('banners/(:num)/toggle-status', 'AdminController::toggleBannerStatus/$1');

    // Coupon management
    $routes->get('coupons', 'Admin\CouponController::index');
    $routes->get('coupons/create', 'Admin\CouponController::create');
    $routes->post('coupons/store', 'Admin\CouponController::store');
    $routes->get('coupons/(:num)/edit', 'Admin\CouponController::edit/$1');
    $routes->post('coupons/(:num)/update', 'Admin\CouponController::update/$1');
    $routes->post('coupons/(:num)/delete', 'Admin\CouponController::delete/$1');
    $routes->post('coupons/(:num)/toggle', 'Admin\CouponController::toggle/$1');
    $routes->get('coupons/(:num)/stats', 'Admin\CouponController::stats/$1');

    // Shipping management
    $routes->get('shipping', 'Admin\ShippingController::index');
    $routes->get('shipping/create', 'Admin\ShippingController::create');
    $routes->post('shipping/store', 'Admin\ShippingController::store');
    $routes->get('shipping/(:num)/edit', 'Admin\ShippingController::edit/$1');
    $routes->post('shipping/(:num)/update', 'Admin\ShippingController::update/$1');
    $routes->post('shipping/(:num)/delete', 'Admin\ShippingController::delete/$1');

    // Payment Methods management
    $routes->get('payment-methods', 'Admin\PaymentMethodController::index');
    $routes->get('payment-methods/create', 'Admin\PaymentMethodController::create');
    $routes->post('payment-methods/store', 'Admin\PaymentMethodController::store');
    $routes->get('payment-methods/(:num)/edit', 'Admin\PaymentMethodController::edit/$1');
    $routes->post('payment-methods/(:num)/update', 'Admin\PaymentMethodController::update/$1');
    $routes->get('payment-methods/(:num)/delete', 'Admin\PaymentMethodController::delete/$1');
    $routes->get('payment-methods/(:num)/toggle', 'Admin\PaymentMethodController::toggleStatus/$1');
    $routes->post('shipping/(:num)/toggle', 'Admin\ShippingController::toggle/$1');
    $routes->post('shipping/update-sort-order', 'Admin\ShippingController::updateSortOrder');

    // Page management
    $routes->get('pages', 'Admin\PagesController::index');
    $routes->get('pages/create', 'Admin\PagesController::create');
    $routes->post('pages/store', 'Admin\PagesController::store');
    $routes->get('pages/(:num)/edit', 'Admin\PagesController::edit/$1');
    $routes->post('pages/(:num)/update', 'Admin\PagesController::update/$1');
    $routes->delete('pages/(:num)/delete', 'Admin\PagesController::delete/$1');
    $routes->post('pages/(:num)/toggle-status', 'Admin\PagesController::toggleStatus/$1');
    $routes->post('pages/update-header-order', 'Admin\PagesController::updateHeaderOrder');
    $routes->post('pages/update-footer-order', 'Admin\PagesController::updateFooterOrder');

    // Testimonial management
    $routes->get('testimonials', 'AdminController::testimonials');
    $routes->get('testimonials/create', 'AdminController::createTestimonial');
    $routes->post('testimonials/store', 'AdminController::storeTestimonial');
    $routes->get('testimonials/edit/(:num)', 'AdminController::editTestimonial/$1');
    $routes->post('testimonials/update/(:num)', 'AdminController::updateTestimonial/$1');
    $routes->get('testimonials/delete/(:num)', 'AdminController::deleteTestimonial/$1');
    $routes->get('testimonials/toggle-featured/(:num)', 'AdminController::toggleTestimonialFeatured/$1');
    $routes->get('testimonials/toggle-active/(:num)', 'AdminController::toggleTestimonialActive/$1');

    // Settings
    $routes->get('settings', 'AdminController::settings');
    $routes->post('settings', 'AdminController::updateSettings');
});

// Page routes (public)
$routes->get('pages/(:segment)', 'PagesController::show/$1');
$routes->post('pages/contact/submit', 'PagesController::submitContact');

// API Routes
$routes->group('api/v1', function ($routes) {
    // Public API routes (no authentication required)

    // Authentication routes
    $routes->post('auth/register', 'Api\AuthApiController::register');
    $routes->post('auth/login', 'Api\AuthApiController::login');
    $routes->post('auth/refresh', 'Api\AuthApiController::refresh');

    // Public product routes
    $routes->get('products', 'Api\ProductApiController::index');
    $routes->get('products/featured', 'Api\ProductApiController::featured');
    $routes->get('products/search', 'Api\ProductApiController::search');
    $routes->get('products/(:num)/variant', 'Api\ProductApiController::getVariantByOptions/$1');
    $routes->get('products/(:segment)', 'Api\ProductApiController::show/$1');
    $routes->get('products/category/(:num)', 'Api\ProductApiController::byCategory/$1');

    // Public category routes
    $routes->get('categories', 'Api\CategoryApiController::index');
    $routes->get('categories/tree', 'Api\CategoryApiController::tree');
    $routes->get('categories/popular', 'Api\CategoryApiController::popular');
    $routes->get('categories/search', 'Api\CategoryApiController::search');
    $routes->get('categories/(:segment)', 'Api\CategoryApiController::show/$1');
});

// Protected API routes (require JWT authentication)
$routes->group('api/v1', ['filter' => 'jwtauth'], function ($routes) {
    // User profile routes
    $routes->get('auth/profile', 'Api\AuthApiController::profile');
    $routes->put('auth/profile', 'Api\AuthApiController::updateProfile');
    $routes->post('auth/change-password', 'Api\AuthApiController::changePassword');
    $routes->post('auth/logout', 'Api\AuthApiController::logout');

    // Cart routes
    $routes->get('cart', 'Api\CartApiController::index');
    $routes->post('cart/add', 'Api\CartApiController::add');
    $routes->put('cart/(:num)', 'Api\CartApiController::update/$1');
    $routes->delete('cart/(:num)', 'Api\CartApiController::remove/$1');
    $routes->delete('cart', 'Api\CartApiController::clear');
    $routes->get('cart/count', 'Api\CartApiController::count');

    // Order routes
    $routes->get('orders', 'Api\OrderApiController::index');
    $routes->get('orders/(:num)', 'Api\OrderApiController::show/$1');
    $routes->post('orders', 'Api\OrderApiController::create');
    $routes->put('orders/(:num)/cancel', 'Api\OrderApiController::cancel/$1');

    // Address routes
    $routes->get('addresses', 'Api\AddressApiController::index');
    $routes->get('addresses/(:num)', 'Api\AddressApiController::show/$1');
    $routes->post('addresses', 'Api\AddressApiController::create');
    $routes->put('addresses/(:num)', 'Api\AddressApiController::update/$1');
    $routes->delete('addresses/(:num)', 'Api\AddressApiController::delete/$1');
    $routes->put('addresses/(:num)/default', 'Api\AddressApiController::setDefault/$1');



    // Notification routes
    $routes->post('notifications/register-token', 'Api\NotificationApiController::registerToken');
    $routes->get('notifications', 'Api\NotificationApiController::getNotifications');
    $routes->get('notifications/unread-count', 'Api\NotificationApiController::getUnreadCount');
    $routes->put('notifications/(:num)/read', 'Api\NotificationApiController::markAsRead/$1');
    $routes->put('notifications/mark-all-read', 'Api\NotificationApiController::markAllAsRead');
    $routes->delete('notifications/(:num)', 'Api\NotificationApiController::deleteNotification/$1');
    $routes->get('notifications/preferences', 'Api\NotificationApiController::getPreferences');
    $routes->put('notifications/preferences', 'Api\NotificationApiController::updatePreferences');
});

$routes->group('', ['filter' => 'auth'], function ($routes) {
    $routes->get('addresses', 'UserAddressController::index');
    $routes->get('addresses/add', 'UserAddressController::add');
    $routes->post('addresses/add', 'UserAddressController::add');
    $routes->get('addresses/edit/(:num)', 'UserAddressController::edit/$1');
    $routes->post('addresses/edit/(:num)', 'UserAddressController::edit/$1');
    $routes->get('addresses/delete/(:num)', 'UserAddressController::delete/$1');
});
