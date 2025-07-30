-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 30, 2025 at 10:57 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nandini`
--

-- --------------------------------------------------------

--
-- Table structure for table `banners`
--

CREATE TABLE `banners` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(500) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `button_text` varchar(100) DEFAULT NULL,
  `button_link` varchar(255) DEFAULT NULL,
  `button_text_2` varchar(100) DEFAULT NULL,
  `button_link_2` varchar(255) DEFAULT NULL,
  `background_color` varchar(7) DEFAULT '#ff6b35',
  `text_color` varchar(7) DEFAULT '#ffffff',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `variant_id` int(11) UNSIGNED DEFAULT NULL,
  `variant_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Selected variation options for this cart item' CHECK (json_valid(`variant_options`)),
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(9, 'Dried Mushrooms', 'dried-mushrooms', '', 'category_1753854329_01642503.jpg', 1, 0, '2025-07-30 05:44:56', '2025-07-30 05:45:29'),
(10, 'Microdose Mushrooms', 'microdose-mushrooms', '', 'category_1753854364_e3506463.jpg', 1, 1, '2025-07-30 05:46:04', '2025-07-30 05:46:11'),
(11, 'Mushroom Edibles', 'mushroom-edibles', '', 'category_1753854400_8489c85e.jpg', 1, 2, '2025-07-30 05:46:40', '2025-07-30 05:46:40'),
(12, 'Magic Mushrooms', 'magic-mushrooms', '', 'category_1753854818_a6403d8e.jpg', 1, 3, '2025-07-30 05:52:35', '2025-07-30 05:53:38');

-- --------------------------------------------------------

--
-- Table structure for table `coupons`
--

CREATE TABLE `coupons` (
  `id` int(11) UNSIGNED NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `type` enum('percentage','fixed_amount','free_shipping') NOT NULL DEFAULT 'percentage',
  `value` decimal(10,2) NOT NULL DEFAULT 0.00,
  `minimum_order_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `maximum_discount_amount` decimal(10,2) DEFAULT NULL,
  `usage_limit` int(11) DEFAULT NULL,
  `usage_limit_per_customer` int(11) NOT NULL DEFAULT 1,
  `used_count` int(11) NOT NULL DEFAULT 0,
  `valid_from` datetime DEFAULT NULL,
  `valid_until` datetime DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coupons`
--

INSERT INTO `coupons` (`id`, `code`, `name`, `description`, `type`, `value`, `minimum_order_amount`, `maximum_discount_amount`, `usage_limit`, `usage_limit_per_customer`, `used_count`, `valid_from`, `valid_until`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'FIRST01', '10% OFF', '10% OFF', 'percentage', 10.00, 1.00, NULL, 5, 5, 0, NULL, NULL, 1, '2025-05-27 11:57:15', '2025-06-29 06:16:38');

-- --------------------------------------------------------

--
-- Table structure for table `coupon_usage`
--

CREATE TABLE `coupon_usage` (
  `id` int(11) UNSIGNED NOT NULL,
  `coupon_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `order_id` int(11) UNSIGNED NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `order_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2024-01-01-000001', 'App\\Database\\Migrations\\CreateUsersTable', 'default', 'App', 1748253181, 1),
(2, '2024-01-01-000002', 'App\\Database\\Migrations\\CreateCategoriesTable', 'default', 'App', 1748253306, 2),
(3, '2024-01-01-000003', 'App\\Database\\Migrations\\CreateProductsTable', 'default', 'App', 1748253306, 2),
(4, '2024-01-01-000004', 'App\\Database\\Migrations\\CreateCartTable', 'default', 'App', 1748253306, 2),
(5, '2024-01-01-000005', 'App\\Database\\Migrations\\CreateOrdersTable', 'default', 'App', 1748253371, 3),
(6, '2024-01-01-000006', 'App\\Database\\Migrations\\CreateOrderItemsTable', 'default', 'App', 1748253371, 3),
(7, '2024-01-01-000007', 'App\\Database\\Migrations\\CreateReviewsTable', 'default', 'App', 1748254392, 4),
(8, '2024-01-01-000008', 'App\\Database\\Migrations\\AddAdminRoleToUsers', 'default', 'App', 1748254392, 4),
(9, '2024-01-01-000009', 'App\\Database\\Migrations\\CreateBannersTable', 'default', 'App', 1748325034, 5),
(10, '2024-01-01-000009', 'App\\Database\\Migrations\\CreatePaymentTransactionsTable', 'default', 'App', 1748335705, 6),
(11, '2024-01-01-000010', 'App\\Database\\Migrations\\CreateCouponsTable', 'default', 'App', 1748346893, 7),
(12, '2024-01-01-000011', 'App\\Database\\Migrations\\CreateCouponUsageTable', 'default', 'App', 1748346893, 7),
(13, '2024-01-01-000012', 'App\\Database\\Migrations\\AddCouponFieldsToOrders', 'default', 'App', 1748346893, 7),
(14, '2025-01-28-120000', 'App\\Database\\Migrations\\CreateSettingsTable', 'default', 'App', 1748433752, 8),
(15, '2025-06-27-000001', 'App\\Database\\Migrations\\CreateUserAddresses', 'default', 'App', 1750999008, 9),
(16, '2025-06-29-000001', 'App\\Database\\Migrations\\CreateWishlistTable', 'default', 'App', 1751175248, 10),
(17, '2025-06-29-000002', 'App\\Database\\Migrations\\UpdateUserAddressesTable', 'default', 'App', 1751175888, 11),
(18, '2025-06-29-070000', 'App\\Database\\Migrations\\CreateShippingMethodsTable', 'default', 'App', 1751179966, 12),
(19, '2025-06-29-080000', 'App\\Database\\Migrations\\AddShippingMethodToOrders', 'default', 'App', 1751181485, 13),
(20, '2025-06-29-120000', 'App\\Database\\Migrations\\CreatePagesTable', 'default', 'App', 1751185495, 14),
(21, '2024-01-15-000001', 'App\\Database\\Migrations\\CreateUserDevicesTable', 'default', 'App', 1752039768, 15),
(22, '2024-01-15-000002', 'App\\Database\\Migrations\\CreateNotificationsTable', 'default', 'App', 1752039768, 15),
(23, '2024-01-01-000010', 'App\\Database\\Migrations\\CreateProductVariationTypesTable', 'default', 'App', 1753775589, 16),
(24, '2024-01-01-000011', 'App\\Database\\Migrations\\CreateProductVariationOptionsTable', 'default', 'App', 1753775589, 16),
(25, '2024-01-01-000012', 'App\\Database\\Migrations\\CreateProductVariantsTable', 'default', 'App', 1753775589, 16),
(26, '2024-01-01-000013', 'App\\Database\\Migrations\\CreateProductVariantOptionsTable', 'default', 'App', 1753775589, 16),
(27, '2024-01-01-000014', 'App\\Database\\Migrations\\AddVariantSupportToCart', 'default', 'App', 1753775589, 16),
(28, '2024-01-01-000015', 'App\\Database\\Migrations\\AddVariantSupportToOrderItems', 'default', 'App', 1753775589, 16),
(29, '2024-01-01-000016', 'App\\Database\\Migrations\\AddPricingToVariationOptions', 'default', 'App', 1753781830, 17);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('order_update','promotion','general','system') NOT NULL DEFAULT 'general',
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `order_number` varchar(50) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `total_amount` decimal(10,2) NOT NULL,
  `coupon_id` int(11) UNSIGNED DEFAULT NULL,
  `coupon_code` varchar(50) DEFAULT NULL,
  `shipping_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `shipping_method_id` int(11) UNSIGNED DEFAULT NULL,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `subtotal_amount` decimal(10,2) DEFAULT 0.00,
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `shipping_address` text NOT NULL,
  `billing_address` text NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `variant_id` int(11) UNSIGNED DEFAULT NULL,
  `variant_sku` varchar(100) DEFAULT NULL,
  `variant_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Selected variation options for this order item' CHECK (json_valid(`variant_options`)),
  `product_name` varchar(255) NOT NULL,
  `product_sku` varchar(100) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pages`
--

CREATE TABLE `pages` (
  `id` int(11) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_keywords` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `show_in_header` tinyint(1) NOT NULL DEFAULT 0,
  `show_in_footer` tinyint(1) NOT NULL DEFAULT 0,
  `header_order` int(11) NOT NULL DEFAULT 0,
  `footer_order` int(11) NOT NULL DEFAULT 0,
  `template` varchar(50) NOT NULL DEFAULT 'default',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pages`
--

INSERT INTO `pages` (`id`, `title`, `slug`, `content`, `meta_title`, `meta_description`, `meta_keywords`, `is_active`, `show_in_header`, `show_in_footer`, `header_order`, `footer_order`, `template`, `created_at`, `updated_at`) VALUES
(1, 'About Us', 'about-us', '<h2>About Microdose Mushroom</h2><p>Welcome to Microdose Mushroom, your trusted microdose destination. We are committed to providing you with the best microdose products and exceptional customer service.</p><p>Our mission is to make microdosing convenient, reliable, and accessible for everyone.</p>', 'About Us - Microdose Mushroom', 'Learn more about Microdose Mushroom, your trusted online microdose destination.', '', 1, 0, 0, 0, 0, 'default', '2025-06-29 08:24:55', '2025-06-30 06:10:55'),
(2, 'Contact Us', 'contact-us', '<h2>Contact Us</h2><p>We\'d love to hear from you! Get in touch with us for any questions, concerns, or feedback.</p><h3>Contact Information</h3><p><strong>Email:</strong> info@microdosemushroom.com<br><strong>Phone:</strong> +1 (xxx)xxx xxxx<br><strong>Address:</strong> 123 Business Street, City, State - 123456</p><h3>Business Hours</h3><p>Monday - Friday: 9:00 AM - 6:00 PM<br>Saturday: 10:00 AM - 4:00 PM<br>Sunday: Closed</p>', 'Contact Us - Microdose Mushroom', 'Get in touch with Microdose Mushroom for any questions or support.', NULL, 1, 1, 1, 2, 2, 'default', '2025-06-29 08:24:55', '2025-06-29 08:24:55'),
(3, 'Privacy Policy', 'privacy-policy', '<h2>Privacy Policy</h2><p>This Privacy Policy describes how Microdose Mushroom collects, uses, and protects your personal information.</p><h3>Information We Collect</h3><p>We collect information you provide directly to us, such as when you create an account, make a purchase, or contact us.</p><h3>How We Use Your Information</h3><p>We use the information we collect to provide, maintain, and improve our services, process transactions, and communicate with you.</p><h3>Information Security</h3><p>We implement appropriate security measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction.</p>', 'Privacy Policy - Microdose Mushroom', 'Read our privacy policy to understand how we protect your personal information.', NULL, 1, 0, 1, 0, 3, 'default', '2025-06-29 08:24:55', '2025-06-29 08:24:55'),
(4, 'Shipping Policy', 'shipping-policy', '<h2>Shipping Policy</h2><p>We offer various shipping options to meet your needs.</p><h3>Shipping Methods</h3><ul><li><strong>Standard Shipping:</strong> 3-5 business days</li><li><strong>Express Shipping:</strong> 1-2 business days</li><li><strong>Free Shipping:</strong> 5-7 business days (orders over ₹500)</li><li><strong>Local Pickup:</strong> Same day</li></ul><h3>Shipping Costs</h3><p>Shipping costs are calculated based on the weight and destination of your order. Free shipping is available for orders over ₹500.</p><h3>Processing Time</h3><p>Orders are typically processed within 1-2 business days before shipping.</p>', 'Shipping Policy - Microdose Mushroom', 'Learn about our shipping options, costs, and delivery times.', NULL, 1, 0, 1, 0, 4, 'default', '2025-06-29 08:24:55', '2025-06-29 08:24:55'),
(5, 'Terms and Conditions', 'terms-and-conditions', '<h2>Terms and Conditions</h2><p>By using our website and services, you agree to these terms and conditions.</p><h3>Use of Our Service</h3><p>You may use our service for lawful purposes only. You agree not to use the service in any way that could damage, disable, or impair the service.</p><h3>Account Responsibility</h3><p>You are responsible for maintaining the confidentiality of your account and password and for restricting access to your account.</p><h3>Product Information</h3><p>We strive to provide accurate product information, but we do not warrant that product descriptions or other content is accurate, complete, reliable, or error-free.</p><h3>Limitation of Liability</h3><p>In no event shall Nandini Hub be liable for any indirect, incidental, special, or consequential damages.</p>', 'Terms and Conditions - Nandini Hub', 'Read our terms and conditions for using Nandini Hub services.', NULL, 1, 0, 1, 0, 5, 'default', '2025-06-29 08:24:55', '2025-06-29 08:24:55');

-- --------------------------------------------------------

--
-- Table structure for table `payment_transactions`
--

CREATE TABLE `payment_transactions` (
  `id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `gateway_transaction_id` varchar(100) DEFAULT NULL,
  `payment_gateway` varchar(50) NOT NULL DEFAULT 'hdfc',
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) NOT NULL DEFAULT 'INR',
  `status` enum('pending','processing','success','failed','cancelled','refunded') NOT NULL DEFAULT 'pending',
  `gateway_status` varchar(50) DEFAULT NULL,
  `gateway_response` text DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `bank_ref_no` varchar(100) DEFAULT NULL,
  `failure_reason` varchar(255) DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `short_description` varchar(500) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_price` decimal(10,2) DEFAULT NULL,
  `sku` varchar(100) NOT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `weight` decimal(8,2) DEFAULT NULL,
  `dimensions` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `gallery` text DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_description` varchar(500) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `short_description`, `price`, `sale_price`, `sku`, `stock_quantity`, `weight`, `dimensions`, `image`, `gallery`, `is_featured`, `is_active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(9, 9, 'Buy African Transkei Magic Mushrooms Online USA', 'buy-african-transkei-magic-mushrooms-online-usa', 'Embark on a Transformative Journey with African Transkei Magic Mushrooms\r\nUnlock the mysteries of the psychedelic realm with African Transkei Magic Mushrooms, a highly revered strain known for its profound effects and unique characteristics. Sourced from the remote Transkei region in South Africa, these mushrooms offer a powerful and enlightening psychedelic experience that has captivated users for generations. Whether you’re seeking a deep introspective journey, enhanced creativity, or spiritual insights, African Transkei Magic Mushrooms provide an extraordinary adventure into the depths of consciousness.\r\n\r\nWhat Are African Transkei Magic Mushrooms?\r\nAfrican Transkei Magic Mushrooms, also known as Psilocybe cubensis Transkei, are a potent strain of magic mushrooms renowned for their strong effects and distinctive appearance. Originating from the Transkei region of South Africa, these mushrooms are characterized by their large, golden-brown caps and thick, sturdy stems. They contain psilocybin, a naturally occurring psychedelic compound that induces altered states of consciousness, vivid visual experiences, and profound emotional and spiritual insights.\r\n\r\nKey Benefits of African Transkei Magic Mushrooms\r\nIntense Visual Experiences: Provides vivid, colorful, and intricate visual hallucinations that enhance sensory perception.\r\nDeep Emotional Insight: Facilitates deep emotional exploration and personal reflection, promoting self-discovery and healing.\r\nEnhanced Creativity: Stimulates creative thinking and innovative ideas, making it ideal for artists, writers, and creative professionals.\r\nSpiritual Awakening: Offers profound spiritual insights and a heightened sense of connection with the universe.\r\nPowerful Psychedelic Experience: Delivers a strong and impactful psychedelic journey, suitable for those seeking transformative experiences.\r\nProduct Details\r\nDosage: Start with a conservative dose (1-2 grams) to gauge your sensitivity and adjust based on your desired effects. Higher doses can be used for more intense experiences.\r\nAppearance: Features large, golden-brown caps with thick stems, characteristic of the Transkei strain.\r\nPackaging: Shipped in discreet, tamper-proof packaging to ensure freshness and maintain privacy.\r\nHow to Use African Transkei Magic Mushrooms\r\nRecommended Dosage: Begin with 1-2 grams of dried mushrooms. Adjust the dosage as needed based on your personal response and the intensity of the experience you seek.\r\n\r\nConsumption: These mushrooms can be consumed dried, ground into a powder, or brewed into tea. Consuming them in a comfortable, safe environment is essential for a positive experience.\r\n\r\nSafety and Precautions\r\nTo ensure a safe and enjoyable psychedelic experience, follow these guidelines:\r\n\r\nConsultation: If you have any pre-existing health conditions or are taking medication, consult with a healthcare professional before use.\r\nStart Low, Go Slow: Begin with a lower dose to understand your sensitivity. Increase gradually if needed.\r\nSafe Setting: Use in a controlled, comfortable environment, preferably with a trusted friend or guide.\r\nLegal Considerations: Ensure that the use and possession of psilocybin mushrooms are legal in your area before purchasing.\r\nWhy Choose African Transkei Magic Mushrooms?\r\nPremium Quality: Sourced from the Transkei region, ensuring high potency and purity.\r\nConsistent Effects: Provides reliable and profound psychedelic experiences.\r\nDiscreet and Secure: Packaged discreetly to maintain privacy and preserve product quality.\r\nCustomer Support: Our team is available to assist with any questions and provide support for your psychedelic journey.\r\nOrder Your African Transkei Magic Mushrooms Today', 'Intense Visuals: Experience vivid, colorful, and intricate visual hallucinations with enhanced sensory perception.\r\nDeep Emotional Insight: Facilitate profound personal reflection and self-discovery for emotional healing and growth.\r\nBoosted Creativity: Stimulate creative thinking and innovative ideas, perfect for artists and creative professionals.\r\nSpiritual Awakening: Gain profound spiritual insights and a heightened sense of connection with the universe.\r\nPowerful Psychedelic Experience: Enj', 39.00, 38.00, 'BUYAFRICAN-7LGW', 100, 0.00, '', 'product_1753861288_91352ef3.jpg', NULL, 1, 1, '', '', '2025-07-30 07:41:28', '2025-07-30 07:42:10');

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `sku` varchar(100) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL COMMENT 'Override price, null uses parent product price',
  `sale_price` decimal(10,2) DEFAULT NULL,
  `stock_quantity` int(11) NOT NULL DEFAULT 0,
  `weight` decimal(8,2) DEFAULT NULL,
  `dimensions` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL COMMENT 'Variant-specific image',
  `is_default` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Default variant for the product',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `sku`, `price`, `sale_price`, `stock_quantity`, `weight`, `dimensions`, `image`, `is_default`, `is_active`, `created_at`, `updated_at`) VALUES
(16, 9, 'BUYAFRICAN-7LGW-35G', NULL, NULL, 100, NULL, NULL, NULL, 1, 1, '2025-07-30 07:45:28', '2025-07-30 07:45:34'),
(17, 9, 'BUYAFRICAN-7LGW-7G', NULL, NULL, 100, NULL, NULL, NULL, 0, 1, '2025-07-30 07:45:28', '2025-07-30 07:45:37'),
(18, 9, 'BUYAFRICAN-7LGW-14G', NULL, NULL, 100, NULL, NULL, NULL, 0, 1, '2025-07-30 07:45:28', '2025-07-30 07:45:40'),
(19, 9, 'BUYAFRICAN-7LGW-28G', NULL, NULL, 100, NULL, NULL, NULL, 0, 1, '2025-07-30 07:45:28', '2025-07-30 07:45:43');

-- --------------------------------------------------------

--
-- Table structure for table `product_variant_options`
--

CREATE TABLE `product_variant_options` (
  `id` int(11) UNSIGNED NOT NULL,
  `variant_id` int(11) UNSIGNED NOT NULL,
  `variation_option_id` int(11) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_variant_options`
--

INSERT INTO `product_variant_options` (`id`, `variant_id`, `variation_option_id`, `created_at`, `updated_at`) VALUES
(23, 16, 10, '2025-07-30 07:45:28', '2025-07-30 07:45:28'),
(24, 17, 11, '2025-07-30 07:45:28', '2025-07-30 07:45:28'),
(25, 18, 12, '2025-07-30 07:45:28', '2025-07-30 07:45:28'),
(26, 19, 13, '2025-07-30 07:45:28', '2025-07-30 07:45:28');

-- --------------------------------------------------------

--
-- Table structure for table `product_variation_options`
--

CREATE TABLE `product_variation_options` (
  `id` int(11) UNSIGNED NOT NULL,
  `variation_type_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` varchar(255) NOT NULL,
  `color_code` varchar(7) DEFAULT NULL COMMENT 'Hex color code for color variations',
  `image` varchar(255) DEFAULT NULL COMMENT 'Image for image-based variations',
  `price_modifier` decimal(10,2) DEFAULT 0.00 COMMENT 'Price modifier for this option (can be positive or negative)',
  `price_type` enum('fixed','percentage') DEFAULT 'fixed' COMMENT 'Whether price_modifier is a fixed amount or percentage',
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_variation_options`
--

INSERT INTO `product_variation_options` (`id`, `variation_type_id`, `name`, `value`, `color_code`, `image`, `price_modifier`, `price_type`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(10, 6, '3.5g', '35g', '#000000', NULL, 0.00, 'fixed', 0, 1, '2025-07-30 07:43:42', '2025-07-30 07:43:42'),
(11, 6, '7g', '7g', '#000000', NULL, 20.00, 'fixed', 1, 1, '2025-07-30 07:44:17', '2025-07-30 07:44:17'),
(12, 6, '14g', '14g', '#000000', NULL, 50.00, 'fixed', 2, 1, '2025-07-30 07:44:48', '2025-07-30 07:44:48'),
(13, 6, '28g', '28g', '#000000', NULL, 120.00, 'fixed', 3, 1, '2025-07-30 07:45:18', '2025-07-30 07:45:18');

-- --------------------------------------------------------

--
-- Table structure for table `product_variation_types`
--

CREATE TABLE `product_variation_types` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `type` enum('text','color','image','button') NOT NULL DEFAULT 'text',
  `is_required` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_variation_types`
--

INSERT INTO `product_variation_types` (`id`, `name`, `slug`, `display_name`, `type`, `is_required`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(6, 'Qty', 'qty', 'Qty', 'text', 1, 0, 1, '2025-07-30 07:43:16', '2025-07-30 08:05:38');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(10) UNSIGNED NOT NULL,
  `product_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(10) UNSIGNED DEFAULT NULL,
  `rating` tinyint(3) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `review` text DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `is_approved` tinyint(1) NOT NULL DEFAULT 1,
  `helpful_count` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `setting_key` varchar(255) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','text','number','boolean','json') NOT NULL DEFAULT 'string',
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'Microdose Mushroom', 'string', 'Website name displayed in header and title', '2025-05-28 12:02:32', '2025-07-30 07:15:49'),
(2, 'site_tagline', 'Your Trusted Microdose Destination', 'string', 'Website tagline or slogan', '2025-05-28 12:02:32', '2025-07-30 07:15:49'),
(3, 'site_description', 'Microdose Mushroom is your one-stop destination for quality microdose products at affordable prices.', 'string', 'Website description for SEO', '2025-05-28 12:02:32', '2025-07-30 07:15:49'),
(4, 'contact_email', 'info@microdosemushroom.com', 'string', 'Primary contact email address', '2025-05-28 12:02:32', '2025-07-30 07:15:49'),
(5, 'contact_phone', '+1 (xxx) xxx xxxx', 'string', 'Primary contact phone number', '2025-05-28 12:02:32', '2025-07-30 07:15:49'),
(6, 'google_analytics_id', '', 'string', 'Google Analytics Measurement ID (e.g., G-XXXXXXXXXX)', '2025-05-28 12:02:32', '2025-07-30 07:15:49'),
(7, 'google_analytics_enabled', '0', 'boolean', 'Enable or disable Google Analytics tracking', '2025-05-28 12:02:32', '2025-07-30 07:15:49'),
(8, 'site_logo', 'uploads/logo/logo_1753857143.png', 'string', NULL, '2025-06-26 11:08:47', '2025-07-30 06:32:23'),
(9, 'site_favicon', 'uploads/favicon/favicon_1753859593.png', 'string', NULL, '2025-06-26 11:20:16', '2025-07-30 07:13:13'),
(10, 'business_address', '123 Business Street, City, State - 123456', 'string', NULL, '2025-07-30 06:32:23', '2025-07-30 07:15:49'),
(11, 'currency', 'USD', 'string', NULL, '2025-07-30 14:24:58', '2025-07-30 14:24:58'),
(12, 'country', 'US', 'string', NULL, '2025-07-30 14:24:58', '2025-07-30 14:24:58'),
(13, 'timezone', 'America/New_York', 'string', NULL, '2025-07-30 14:24:58', '2025-07-30 14:24:58');

-- --------------------------------------------------------

--
-- Table structure for table `shipping_methods`
--

CREATE TABLE `shipping_methods` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `delivery_time` varchar(100) NOT NULL,
  `cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `minimum_order_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `maximum_order_amount` decimal(10,2) DEFAULT NULL,
  `is_free_shipping` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping_methods`
--

INSERT INTO `shipping_methods` (`id`, `name`, `description`, `delivery_time`, `cost`, `minimum_order_amount`, `maximum_order_amount`, `is_free_shipping`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Standard Shipping', 'Regular delivery service with tracking', '5–7 Business Days', 50.00, 0.00, NULL, 0, 0, 3, '2025-06-29 06:52:46', '2025-07-29 10:44:14'),
(2, 'Express Shipping', 'Fast delivery with priority handling', '1–2 Business Days', 150.00, 0.00, NULL, 0, 1, 2, '2025-06-29 06:52:46', '2025-07-29 10:45:01'),
(3, 'Free Shipping', 'Free delivery for orders above ₹500', '5–7 Business Days', 0.00, 500.00, NULL, 1, 1, 1, '2025-06-29 06:52:46', '2025-07-29 10:45:01'),
(4, 'Local Pickup', 'Pick up from our store location', 'Same Day', 0.00, 0.00, NULL, 1, 0, 4, '2025-06-29 06:52:46', '2025-06-29 07:14:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `pincode` varchar(10) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `role` enum('customer','admin') DEFAULT 'customer',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `phone`, `address`, `city`, `state`, `pincode`, `is_active`, `role`, `created_at`, `updated_at`) VALUES
(3, 'Vinay', 'Singh', 'vinaysingh43@gmail.com', '$2y$10$MvxwBn4m4ZBWJdDd7Dh1hOuDaAPdaUaEKP1RisQVYFhkLlxrlSfj6', '919457790679', 'LGF 10 Anora kalan papnamow Road', 'Lucknow', 'Utter Pradesh', '226028', 1, 'customer', '2025-05-26 11:41:02', '2025-05-26 12:57:36'),
(13, 'Admin', '', 'admin@nandinihub.com', '$2y$10$s5UQhZnvq8wyiXNL2m3KoOTTVjZASWTa9yjXKjZhbT0BVAgEQIEES', '', NULL, NULL, NULL, NULL, 1, 'admin', '2025-07-30 05:40:03', '2025-07-30 05:40:03');

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address_line1` varchar(255) DEFAULT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `pincode` varchar(10) NOT NULL,
  `country` varchar(100) DEFAULT 'India',
  `is_default` tinyint(1) DEFAULT 0,
  `landmark` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_addresses`
--

INSERT INTO `user_addresses` (`id`, `user_id`, `name`, `phone`, `address_line1`, `address_line2`, `address`, `city`, `state`, `pincode`, `country`, `is_default`, `landmark`, `created_at`, `updated_at`) VALUES
(1, 3, 'Vinay Singh', '9457790679', 'LGF 10 ANORA KALAN', '', '', 'Lucknow', 'Uttar Pradesh', '226028', 'India', 1, 'TECHNO COLLAGE', '2025-06-29 05:48:55', '2025-06-29 05:49:08');

-- --------------------------------------------------------

--
-- Table structure for table `user_devices`
--

CREATE TABLE `user_devices` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `device_token` varchar(500) NOT NULL,
  `platform` enum('ios','android','web') NOT NULL DEFAULT 'android',
  `device_info` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`device_info`)),
  `notification_preferences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`notification_preferences`)),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `last_used_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `product_id` int(11) UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `banners`
--
ALTER TABLE `banners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_active` (`is_active`),
  ADD KEY `sort_order` (`sort_order`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `is_active` (`is_active`);

--
-- Indexes for table `coupons`
--
ALTER TABLE `coupons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `is_active` (`is_active`),
  ADD KEY `valid_from` (`valid_from`),
  ADD KEY `valid_until` (`valid_until`);

--
-- Indexes for table `coupon_usage`
--
ALTER TABLE `coupon_usage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `coupon_id` (`coupon_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `used_at` (`used_at`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `user_id_is_read` (`user_id`,`is_read`),
  ADD KEY `type` (`type`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pages_slug_unique` (`slug`),
  ADD KEY `is_active` (`is_active`);

--
-- Indexes for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `transaction_id` (`transaction_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `gateway_transaction_id` (`gateway_transaction_id`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `is_featured` (`is_featured`),
  ADD KEY `is_active` (`is_active`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `is_default` (`is_default`),
  ADD KEY `is_active` (`is_active`);

--
-- Indexes for table `product_variant_options`
--
ALTER TABLE `product_variant_options`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `variant_id_variation_option_id` (`variant_id`,`variation_option_id`),
  ADD KEY `variant_id` (`variant_id`),
  ADD KEY `variation_option_id` (`variation_option_id`);

--
-- Indexes for table `product_variation_options`
--
ALTER TABLE `product_variation_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `variation_type_id` (`variation_type_id`),
  ADD KEY `is_active` (`is_active`),
  ADD KEY `sort_order` (`sort_order`);

--
-- Indexes for table `product_variation_types`
--
ALTER TABLE `product_variation_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `is_active` (`is_active`),
  ADD KEY `sort_order` (`sort_order`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviews_order_id_foreign` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `rating` (`rating`),
  ADD KEY `is_approved` (`is_approved`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Indexes for table `shipping_methods`
--
ALTER TABLE `shipping_methods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_active` (`is_active`),
  ADD KEY `sort_order` (`sort_order`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_devices`
--
ALTER TABLE `user_devices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `user_id_device_token` (`user_id`,`device_token`),
  ADD KEY `is_active` (`is_active`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id_product_id` (`user_id`,`product_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banners`
--
ALTER TABLE `banners`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `coupons`
--
ALTER TABLE `coupons`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `coupon_usage`
--
ALTER TABLE `coupon_usage`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `product_variant_options`
--
ALTER TABLE `product_variant_options`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `product_variation_options`
--
ALTER TABLE `product_variation_options`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `product_variation_types`
--
ALTER TABLE `product_variation_types`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `shipping_methods`
--
ALTER TABLE `shipping_methods`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user_devices`
--
ALTER TABLE `user_devices`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `cart_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `coupon_usage`
--
ALTER TABLE `coupon_usage`
  ADD CONSTRAINT `coupon_usage_coupon_id_foreign` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `coupon_usage_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `coupon_usage_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `payment_transactions`
--
ALTER TABLE `payment_transactions`
  ADD CONSTRAINT `payment_transactions_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_variant_options`
--
ALTER TABLE `product_variant_options`
  ADD CONSTRAINT `product_variant_options_variant_id_foreign` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `product_variant_options_variation_option_id_foreign` FOREIGN KEY (`variation_option_id`) REFERENCES `product_variation_options` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `product_variation_options`
--
ALTER TABLE `product_variation_options`
  ADD CONSTRAINT `product_variation_options_variation_type_id_foreign` FOREIGN KEY (`variation_type_id`) REFERENCES `product_variation_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `reviews_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reviews_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_devices`
--
ALTER TABLE `user_devices`
  ADD CONSTRAINT `user_devices_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `wishlist_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
