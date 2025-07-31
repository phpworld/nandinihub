-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 31, 2025 at 08:53 PM
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
(9, 'Dried Mushrooms', 'dried-mushrooms', '', 'category_1753975893_f09f2136.jpg', 1, 0, '2025-07-30 05:44:56', '2025-07-31 15:31:33'),
(10, 'Microdose Mushrooms', 'microdose-mushrooms', '', 'category_1753976022_974b7818.jpg', 1, 1, '2025-07-30 05:46:04', '2025-07-31 15:33:42'),
(11, 'Mushroom Edibles', 'mushroom-edibles', '', 'category_1753976089_948cf487.jpg', 1, 2, '2025-07-30 05:46:40', '2025-07-31 15:34:49'),
(12, 'Magic Mushrooms', 'magic-mushrooms', '', 'category_1753976148_03010d53.jpg', 1, 3, '2025-07-30 05:52:35', '2025-07-31 15:35:48');

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
(29, '2024-01-01-000016', 'App\\Database\\Migrations\\AddPricingToVariationOptions', 'default', 'App', 1753781830, 17),
(30, '2025-07-31-120000', 'App\\Database\\Migrations\\CreateTestimonialsTable', 'default', 'App', 1753985677, 18);

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
(9, 9, 'Buy African Transkei Magic Mushrooms Online USA', 'buy-african-transkei-magic-mushrooms-online-usa', 'Embark on a Transformative Journey with African Transkei Magic Mushrooms\r\nUnlock the mysteries of the psychedelic realm with African Transkei Magic Mushrooms, a highly revered strain known for its profound effects and unique characteristics. Sourced from the remote Transkei region in South Africa, these mushrooms offer a powerful and enlightening psychedelic experience that has captivated users for generations. Whether you’re seeking a deep introspective journey, enhanced creativity, or spiritual insights, African Transkei Magic Mushrooms provide an extraordinary adventure into the depths of consciousness.\r\n\r\nWhat Are African Transkei Magic Mushrooms?\r\nAfrican Transkei Magic Mushrooms, also known as Psilocybe cubensis Transkei, are a potent strain of magic mushrooms renowned for their strong effects and distinctive appearance. Originating from the Transkei region of South Africa, these mushrooms are characterized by their large, golden-brown caps and thick, sturdy stems. They contain psilocybin, a naturally occurring psychedelic compound that induces altered states of consciousness, vivid visual experiences, and profound emotional and spiritual insights.\r\n\r\nKey Benefits of African Transkei Magic Mushrooms\r\nIntense Visual Experiences: Provides vivid, colorful, and intricate visual hallucinations that enhance sensory perception.\r\nDeep Emotional Insight: Facilitates deep emotional exploration and personal reflection, promoting self-discovery and healing.\r\nEnhanced Creativity: Stimulates creative thinking and innovative ideas, making it ideal for artists, writers, and creative professionals.\r\nSpiritual Awakening: Offers profound spiritual insights and a heightened sense of connection with the universe.\r\nPowerful Psychedelic Experience: Delivers a strong and impactful psychedelic journey, suitable for those seeking transformative experiences.\r\nProduct Details\r\nDosage: Start with a conservative dose (1-2 grams) to gauge your sensitivity and adjust based on your desired effects. Higher doses can be used for more intense experiences.\r\nAppearance: Features large, golden-brown caps with thick stems, characteristic of the Transkei strain.\r\nPackaging: Shipped in discreet, tamper-proof packaging to ensure freshness and maintain privacy.\r\nHow to Use African Transkei Magic Mushrooms\r\nRecommended Dosage: Begin with 1-2 grams of dried mushrooms. Adjust the dosage as needed based on your personal response and the intensity of the experience you seek.\r\n\r\nConsumption: These mushrooms can be consumed dried, ground into a powder, or brewed into tea. Consuming them in a comfortable, safe environment is essential for a positive experience.\r\n\r\nSafety and Precautions\r\nTo ensure a safe and enjoyable psychedelic experience, follow these guidelines:\r\n\r\nConsultation: If you have any pre-existing health conditions or are taking medication, consult with a healthcare professional before use.\r\nStart Low, Go Slow: Begin with a lower dose to understand your sensitivity. Increase gradually if needed.\r\nSafe Setting: Use in a controlled, comfortable environment, preferably with a trusted friend or guide.\r\nLegal Considerations: Ensure that the use and possession of psilocybin mushrooms are legal in your area before purchasing.\r\nWhy Choose African Transkei Magic Mushrooms?\r\nPremium Quality: Sourced from the Transkei region, ensuring high potency and purity.\r\nConsistent Effects: Provides reliable and profound psychedelic experiences.\r\nDiscreet and Secure: Packaged discreetly to maintain privacy and preserve product quality.\r\nCustomer Support: Our team is available to assist with any questions and provide support for your psychedelic journey.\r\nOrder Your African Transkei Magic Mushrooms Today', '', 39.00, NULL, 'BUYAFRICAN-7LGW', 100, 0.00, '', 'product_1753861288_91352ef3.jpg', NULL, 1, 1, '', '', '2025-07-30 07:41:28', '2025-07-31 14:04:03'),
(10, 9, 'Buy Blue Meanie Magic Mushrooms Online USA', 'buy-blue-meanie-magic-mushrooms-online-usa', 'Explore the Unique Effects of Blue Meanie Magic Mushrooms\r\nDiscover a distinctive psychedelic experience with Blue Meanie Magic Mushrooms, renowned for their potent effects and unique characteristics. Blue Meanies, named for their striking appearance and powerful effects, offer an intense journey into the realms of consciousness, creativity, and introspection. Ideal for both seasoned psychonauts and those seeking a profound psychedelic experience, these mushrooms provide a blend of intense visual experiences and emotional depth, making them a popular choice among enthusiasts.\r\n\r\nWhat Are Blue Meanie Magic Mushrooms?\r\nBlue Meanie Magic Mushrooms are a potent strain of psilocybin mushrooms, known for their vivid blue-staining properties and strong psychoactive effects. They are named after their blue color, which is a result of the oxidation of psilocybin, the active compound. This strain is cherished for its ability to induce intense visual hallucinations, enhanced emotional experiences, and profound introspection, making it a favorite among those seeking a transformative psychedelic journey.\r\n\r\nKey Benefits of Blue Meanie Magic Mushrooms\r\nIntense Visuals: Experience vibrant and immersive visual hallucinations, providing a rich tapestry of colors and patterns.\r\nEnhanced Creativity: Stimulates creative thinking and inspiration, ideal for artists, musicians, and innovators.\r\nEmotional Depth: Facilitates deep emotional exploration and personal insight, supporting emotional healing and growth.\r\nIncreased Awareness: Heightens sensory perception and cognitive awareness, promoting a deeper connection with your environment and inner self.\r\nPowerful Psychedelic Journey: Offers a strong and impactful psychedelic experience, suitable for those looking for profound and transformative effects.\r\nProduct Details\r\nDosage: Blue Meanie Magic Mushrooms should be consumed in measured amounts. A typical starting dose is 1-2 grams, with higher doses for more intense effects. Always start with a lower dose to gauge your sensitivity.\r\nAppearance: Features a distinctive blue-staining coloration on the caps and stems, with a potent aroma that is characteristic of this strain.\r\nPackaging: Shipped in secure, discreet packaging to ensure privacy and maintain the freshness and potency of the product.\r\nHow to Use Blue Meanie Magic Mushrooms\r\nRecommended Dosage: Begin with 1-2 grams to assess your response. Adjust the dosage based on your individual tolerance and desired intensity of the experience. Higher doses can lead to more profound and intense effects.\r\n\r\nConsumption: Blue Meanie Magic Mushrooms can be consumed dried or brewed into tea for a milder experience. Ensure to consume responsibly and in a safe, comfortable environment.\r\n\r\nSafety and Precautions\r\nFor a safe and enjoyable experience, follow these guidelines:\r\n\r\nConsultation: If you have any pre-existing health conditions or are taking medication, consult with a healthcare professional before using magic mushrooms.\r\nSafe Setting: Use in a controlled, safe environment, preferably with a trusted friend or guide, especially if you are inexperienced.\r\nStart Low, Go Slow: Begin with a lower dose to understand your sensitivity and response to the mushrooms.\r\nLegal Considerations: Ensure that the use and possession of psilocybin mushrooms are legal in your area before purchasing.\r\nWhy Choose Our Blue Meanie Magic Mushrooms?\r\nHigh-Quality Product: Sourced and cultivated with care to ensure purity and potency.\r\nConsistent Effects: Provides reliable and consistent results, enhancing your psychedelic experience.\r\nDiscreet and Secure: Packaged discreetly to maintain your privacy and ensure the freshness of the product.\r\nCustomer Support: Our team is here to assist with any questions and provide support for your psychedelic journey.\r\nOrder Your Blue Meanie Magic Mushrooms Today\r\nEmbark on a unique and powerful psychedelic adventure with Blue Meanie Magic Mushrooms. Whether you’re looking to explore new dimensions of consciousness, enhance your creativity, or seek deep emotional insight, our Blue Meanies offer a potent and transformative experience. Order now and experience the profound effects of one of the most intriguing strains of magic mushrooms available.', '', 30.00, 30.00, 'BUYBLUEMEA-OBRM', 100, 0.00, '', 'product_1753952733_1efaf7e9.jpg', NULL, 0, 1, '', '', '2025-07-31 09:05:33', '2025-07-31 09:05:57'),
(11, 9, 'Buy B+ Magic Mushrooms Spores Online USA', 'buy-b-magic-mushrooms-spores-online-usa', 'Explore the World of B+ Magic Mushrooms with Premium Spores\r\nDive into the fascinating world of psilocybin mushrooms with B+ Magic Mushrooms Spores, renowned for their exceptional growth and transformative effects. B+ is one of the most popular strains among mushroom enthusiasts and cultivators, celebrated for its robust potency and unique characteristics. Our high-quality spores are perfect for both novice and experienced growers, offering a gateway to cultivating your own magical mushrooms with ease and reliability.\r\n\r\nWhat Are B+ Magic Mushrooms Spores?\r\nB+ Magic Mushrooms Spores are the reproductive cells of the Psilocybe cubensis B+ strain, a widely acclaimed variety known for its impressive size and strength. These spores are the first step in cultivating B+ mushrooms, which are prized for their rich, golden-brown caps and potent psychedelic effects. The B+ strain is celebrated for its ease of cultivation and resilience, making it a popular choice among home growers and researchers.\r\n\r\nKey Benefits of B+ Magic Mushrooms Spores\r\nHigh-Quality Spores: Our B+ spores are sourced from premium cultures, ensuring high viability and successful cultivation.\r\nEasy to Grow: B+ mushrooms are known for their robust growth and adaptability, making them suitable for both beginners and experienced cultivators.\r\nPotent and Reliable: B+ strain offers a powerful and consistent psychedelic experience, known for its strong visual and introspective effects.\r\nVersatile Use: Ideal for personal cultivation, research, and educational purposes.\r\nSecure and Discreet: Shipped in tamper-proof, discreet packaging to ensure privacy and maintain product integrity.\r\nProduct Details\r\nSpore Type: Psilocybe cubensis B+ strain.\r\nPackaging: Each order is carefully packaged in sterile vials or syringes to maintain spore viability and prevent contamination.\r\nQuality Control: Our spores undergo rigorous quality checks to ensure high germination rates and successful cultivation.\r\nHow to Use B+ Magic Mushrooms Spores\r\nCultivation: Use the spores to inoculate a suitable substrate (such as brown rice flour or vermiculite) within a sterile environment. Follow a trusted cultivation guide or kit for best results.\r\n\r\nStorage: Keep spores in a cool, dark place to maintain their viability. Avoid exposure to heat and moisture.\r\n\r\nLegal Considerations: Ensure that the cultivation and possession of psilocybin mushrooms and spores are legal in your jurisdiction before purchasing.\r\n\r\nSafety and Precautions\r\nTo ensure a successful cultivation process and a safe experience, adhere to the following guidelines:\r\n\r\nSterile Technique: Use sterile equipment and follow hygiene practices to prevent contamination.\r\nLegal Compliance: Verify the legality of growing and possessing psilocybin mushrooms in your area.\r\nProper Storage: Store spores in a cool, dry place to maintain their viability and effectiveness.\r\nWhy Choose B+ Magic Mushrooms Spores?\r\nPremium Quality: Sourced from reputable cultures to ensure high germination rates and successful cultivation.\r\nReliable Performance: Known for robust growth and consistent psychedelic effects.\r\nConvenient and Discreet: Packaged to maintain freshness and protect your privacy.\r\nCustomer Support: Our team is available to assist with any questions and provide guidance on cultivation.\r\nOrder Your B+ Magic Mushrooms Spores Today', '', 30.00, NULL, 'BUYBMAGICM-IY80', 100, 0.00, '', 'product_1753970855_04af7b3a.jpg', NULL, 1, 1, '', '', '2025-07-31 14:07:35', '2025-07-31 14:11:24'),
(12, 9, 'Buy Cubensis Amazonian Magic Mushrooms Online', 'buy-cubensis-amazonian-magic-mushrooms-online', 'Experience the Mystical Journey with Cubensis Amazonian Magic Mushrooms\r\nImmerse yourself in a profound psychedelic adventure with Cubensis Amazonian Magic Mushrooms. Known for their robust potency and distinctive effects, these mushrooms offer an extraordinary journey into enhanced sensory perception, deep introspection, and vibrant visual experiences. Originating from the Amazon rainforest, this strain is celebrated for its powerful effects and rich cultural heritage, making it a top choice for both seasoned explorers and those new to the world of psychedelics.\r\n\r\nWhat Are Cubensis Amazonian Magic Mushrooms?\r\nCubensis Amazonian Magic Mushrooms are a prominent strain of psilocybin mushrooms renowned for their intense psychoactive effects and striking appearance. Originating from the lush Amazon basin, these mushrooms are distinguished by their large, golden-brown caps and robust growth. They contain psilocybin, a naturally occurring psychedelic compound that produces profound changes in perception, mood, and thought processes. The Amazonian strain is especially known for its potent visual and emotional experiences, making it a favorite among those seeking deep and transformative journeys.\r\n\r\nKey Benefits of Cubensis Amazonian Magic Mushrooms\r\nIntense Visuals: Offers vivid and immersive visual experiences, including intricate patterns and vibrant colors.\r\nDeep Emotional Insight: Facilitates profound emotional exploration and self-discovery, aiding in personal growth and healing.\r\nEnhanced Sensory Perception: Heightens sensory experiences, making sounds, textures, and colors more vivid and engaging.\r\nIncreased Creativity: Stimulates creative thinking and problem-solving, ideal for artists, writers, and innovators.\r\nSpiritual Connection: Provides a deeper sense of connection to oneself and the universe, often leading to meaningful spiritual insights.\r\nProduct Details\r\nDosage: Start with a lower dose (1-2 grams) to gauge your sensitivity. Higher doses can be used for more intense experiences. Always begin with a conservative amount to understand your individual response.\r\nAppearance: Features large, golden-brown caps with a robust stem, characteristic of the Amazonian strain.\r\nPackaging: Shipped in secure, discreet packaging to ensure privacy and preserve freshness and potency.\r\nHow to Use Cubensis Amazonian Magic Mushrooms\r\nRecommended Dosage: Begin with 1-2 grams of dried mushrooms for a moderate experience. Adjust the dosage based on your personal tolerance and desired effects. Higher doses can provide more profound and intense experiences.\r\n\r\nConsumption: These mushrooms can be consumed dried, ground into a powder, or brewed into tea for a milder experience. Always consume responsibly and in a comfortable, safe setting.\r\n\r\nSafety and Precautions\r\nTo ensure a safe and positive experience, follow these guidelines:\r\n\r\nConsultation: If you have any pre-existing health conditions or are taking medication, consult with a healthcare professional before using magic mushrooms.\r\nSafe Environment: Use in a controlled, comfortable environment, preferably with a trusted friend or guide, especially if you are new to psychedelics.\r\nStart Low, Go Slow: Begin with a lower dosage and adjust based on your personal sensitivity and response.\r\nLegal Considerations: Ensure the use and possession of psilocybin mushrooms are legal in your area before purchasing.\r\nWhy Choose Our Cubensis Amazonian Magic Mushrooms?\r\nPremium Quality: Sourced and cultivated with care to ensure high purity and potency.\r\nConsistent Effects: Provides reliable and consistent results for a transformative psychedelic experience.\r\nDiscreet and Secure: Packaged discreetly to maintain your privacy and ensure the product’s freshness and integrity.\r\nCustomer Support: Our team is available to assist with any questions and provide support for your psychedelic journey.\r\nOrder Your Cubensis Amazonian Magic Mushrooms Today\r\nUnlock the mysteries of the Amazon and embark on a transformative journey with Cubensis Amazonian Magic Mushrooms. Whether you’re seeking deep introspection, enhanced creativity, or vivid visual experiences, these mushrooms offer a potent and enriching psychedelic adventure. Order now to experience the unique effects of one of the most fascinating strains of magic mushrooms available.', '', 25.00, NULL, 'BUYCUBENSI-YON7', 100, 0.00, '', 'product_1753970943_11843e34.jpg', NULL, 0, 1, '', '', '2025-07-31 14:09:03', '2025-07-31 14:09:03'),
(13, 9, 'Buy Florida White Magic Mushrooms Online USA', 'buy-florida-white-magic-mushrooms-online-usa', 'Discover the Unique and Powerful Effects of Florida White Magic Mushrooms\r\nEmbark on a transformative journey with Florida White Magic Mushrooms, a distinctive strain renowned for its powerful effects and unique characteristics. These mushrooms offer an extraordinary psychedelic experience, making them a sought-after choice for both seasoned users and those new to psilocybin. Buying Florida White Magic Mushrooms online provides a convenient and secure way to access these potent mushrooms, allowing you to explore their benefits from the comfort of your home.\r\n\r\nWhat Are Florida White Magic Mushrooms?\r\nFlorida White Magic Mushrooms are a prized strain of psilocybin mushrooms, known for their striking appearance and potent psychedelic effects. Characterized by their white to pale cream-colored caps and stems, these mushrooms stand out among other strains. They are renowned for their strong, yet balanced, effects that offer both visual and introspective experiences. Florida White Mushrooms are celebrated for their ability to enhance cognitive function, creativity, and emotional insight.\r\n\r\nKey Benefits of Florida White Magic Mushrooms\r\nIntense Visual Experiences: Known for producing vivid and immersive visual effects, perfect for those seeking a profound psychedelic journey.\r\nEnhanced Cognitive Clarity: Promotes mental clarity and introspection, helping users gain new perspectives on their thoughts and emotions.\r\nEmotional Depth: Facilitates deep emotional exploration and insight, allowing for personal growth and emotional healing.\r\nCreative Stimulation: Boosts creativity and problem-solving abilities, making it a valuable tool for artists, writers, and innovators.\r\nBalanced Potency: Offers a strong yet manageable potency, suitable for both beginners and experienced users seeking a deep experience.\r\nProduct Details\r\nAppearance: Florida White Magic Mushrooms feature distinct white to pale cream-colored caps and stems. Their unique appearance adds to their appeal and sets them apart from other strains.\r\nDosage: For beginners, a starting dose of 1-2 grams is recommended to gauge sensitivity. More experienced users may use 3-5 grams for a more intense experience. Dosage should be adjusted based on individual tolerance and desired effects.\r\nPackaging: Each batch is carefully packaged to ensure maximum freshness and potency. Discreet packaging ensures privacy and security during shipping.\r\nHow to Use Florida White Magic Mushrooms\r\nRecommended Dosage: Start with a lower dose (1-2 grams) to assess your response. For a more profound experience, doses of 3-5 grams can be used. Adjust the dosage based on your experience and sensitivity.\r\n\r\nConsumption: Florida White Magic Mushrooms can be consumed dried or brewed into a tea. For the best experience, consume them on an empty stomach and in a safe, comfortable environment. A positive mindset and clear intentions can enhance the overall effects.\r\n\r\nSafety and Precautions\r\nWhile Florida White Magic Mushrooms are natural, responsible use is essential:\r\n\r\nSet and Setting: Ensure you are in a comfortable, safe environment. Having a trusted friend or “trip sitter” can enhance safety, especially if you are new to psychedelics.\r\nStart Low, Go Slow: Begin with a lower dose to understand your tolerance and gradually increase if needed.\r\nAvoid Mixing: Refrain from combining with alcohol or other substances to avoid adverse effects.\r\nMedical Consultation: If you have pre-existing health conditions or are on medication, consult with a healthcare professional before use.\r\nKeep Out of Reach of Children: Store in a secure place away from children and pets.\r\nWhy Choose Our Florida White Magic Mushrooms?\r\nPremium Quality: Sourced from reputable growers to ensure high potency and purity.\r\nConsistent Effects: Cultivated and processed to deliver reliable and consistent results.\r\nDiscreet and Secure: Discreet packaging and secure shipping methods to protect your privacy.\r\nCustomer Support: Our team is available to answer any questions and provide guidance on your psychedelic journey.\r\nOrder Your Florida White Magic Mushrooms Today\r\nUnlock the potential of Florida White Magic Mushrooms and explore their unique and powerful effects. Whether you seek deep introspection, creative inspiration, or a profound psychedelic experience, our high-quality mushrooms offer a reliable and enriching option. Order now and embark on an extraordinary journey with Florida White Magic Mushrooms from the comfort of your home.', '', 30.00, NULL, 'BUYFLORIDA-79AP', 100, 0.00, '', 'product_1753971019_a8809533.jpg', NULL, 1, 1, '', '', '2025-07-31 14:10:19', '2025-07-31 14:11:25'),
(14, 9, 'Buy Golden Teachers Magic Mushrooms Online USA', 'buy-golden-teachers-magic-mushrooms-online-usa', 'Experience the Enlightening Journey with Golden Teachers Magic Mushrooms\r\nStep into a world of profound insight and transformative experiences with Golden Teachers Magic Mushrooms. Renowned for their unique combination of potency and introspective qualities, Golden Teachers are a favorite among both seasoned psychonauts and newcomers to the realm of psychedelics. Buying Golden Teachers online offers a convenient and reliable way to access these legendary mushrooms and explore their myriad benefits from the comfort of your home.\r\n\r\nWhat Are Golden Teachers Magic Mushrooms?\r\nGolden Teachers are a popular strain of psilocybin mushrooms, highly regarded for their powerful and enlightening effects. Named for their golden-colored caps and their ability to provide insightful, teacher-like experiences, these mushrooms are known for facilitating deep introspection, spiritual growth, and enhanced self-awareness. Their moderate potency makes them suitable for both beginners and experienced users alike.\r\n\r\nKey Benefits of Golden Teachers Magic Mushrooms\r\nProfound Insight and Clarity: Promotes deep introspection and a heightened sense of self-awareness, helping users gain valuable insights into their thoughts and emotions.\r\nSpiritual Growth: Facilitates spiritual exploration and personal growth, making it a valuable tool for those seeking a deeper understanding of themselves and their place in the world.\r\nEnhanced Emotional Experience: Provides a more profound emotional experience, often leading to a sense of connectedness and empathy.\r\nCreativity Boost: Stimulates creative thinking and problem-solving, making it a favorite among artists and innovators.\r\nBalanced Potency: Offers a moderate potency that is effective yet manageable, ideal for both beginners and experienced users.\r\nProduct Details\r\nAppearance: Golden Teachers feature distinctive golden-yellow caps with a smooth texture and a white stem. The caps often have a wavy edge, which adds to their unique appearance.\r\nDosage: The recommended starting dosage is 1-2 grams for a mild experience. For more intense effects, doses of 3-5 grams can be used. Adjust the dosage according to your tolerance and desired experience.\r\nPackaging: Each batch is carefully packaged to ensure freshness and quality. Discreet packaging ensures privacy and security during shipping.\r\nHow to Use Golden Teachers Magic Mushrooms\r\nRecommended Dosage: Beginners should start with a lower dose (1-2 grams) to gauge their sensitivity and response. For more experienced users, doses of up to 5 grams can provide a more intense experience. Always start with a lower dose if you are unsure of your tolerance.\r\n\r\nConsumption: Golden Teachers can be consumed dried or prepared as a tea. For best results, consume on an empty stomach and in a comfortable, safe setting. Consuming them with a positive mindset and clear intentions can enhance the overall experience.\r\n\r\nSafety and Precautions\r\nWhile Golden Teachers are a natural substance, responsible use is crucial:\r\n\r\nSet and Setting: Ensure you are in a safe, comfortable environment and ideally have a trusted friend with you if you are new to psychedelics.\r\nStart Low, Go Slow: Begin with a lower dose to assess your reaction and gradually increase as needed.\r\nAvoid Mixing: Refrain from mixing with alcohol or other substances to avoid potential adverse effects.\r\nMedical Consultation: Consult with a healthcare professional if you have pre-existing medical conditions or are on medication.\r\nKeep Out of Reach of Children: Store in a secure place away from children and pets.\r\nWhy Choose Our Golden Teachers Magic Mushrooms?\r\nPremium Quality: We source our Golden Teachers from trusted growers to ensure high potency and purity.\r\nConsistent Effects: Our mushrooms are cultivated and processed to deliver consistent and reliable experiences.\r\nDiscreet and Secure: We use discreet packaging and secure shipping methods to protect your privacy.\r\nCustomer Support: Our team is here to assist with any questions and provide guidance on your psilocybin journey.\r\nOrder Your Golden Teachers Magic Mushrooms Today\r\nUnlock the wisdom and transformative potential of Golden Teachers Magic Mushrooms. Whether you seek personal growth, creative inspiration, or a profound psychedelic experience, our high-quality Golden Teachers offer a reliable and enriching option. Order now and embark on an enlightening journey with Golden Teachers from the comfort of your home.', '', 30.00, NULL, 'BUYGOLDENT-5U65', 100, 0.00, '', 'product_1753971072_78c81396.jpg', NULL, 0, 1, '', '', '2025-07-31 14:11:12', '2025-07-31 14:11:12'),
(15, 9, 'Buy Hillbilly Mushrooms Online USA', 'buy-hillbilly-mushrooms-online-usa', 'Discover the Unique Experience of Hillbilly Mushrooms\r\nElevate your psychedelic journey with Hillbilly Mushrooms, a distinguished strain known for its potent effects and distinctive characteristics. Whether you’re a seasoned psychonaut or a curious explorer, buying Hillbilly Mushrooms online offers a premium, reliable option for enhancing your experiences with magic mushrooms.\r\n\r\nWhat Are Hillbilly Mushrooms?\r\nHillbilly Mushrooms are a renowned strain of psilocybin mushrooms, celebrated for their robust potency and unique effects. These mushrooms are characterized by their distinctive appearance—often featuring large, light brown caps with a slightly wavy edge. They are prized for their strong visual and sensory effects, making them a favorite among those seeking profound and transformative experiences.\r\n\r\nKey Benefits of Hillbilly Mushrooms\r\nPowerful Psychedelic Effects: Known for their intense and vivid visual and sensory experiences, perfect for deep exploration and self-discovery.\r\nEnhanced Emotional Insight: Promotes introspection and emotional release, helping you gain new perspectives on personal issues and experiences.\r\nIncreased Creativity: Stimulates creative thinking and problem-solving, making it a valuable tool for artists, writers, and innovators.\r\nNatural Therapeutic Potential: Research suggests that psilocybin can aid in reducing symptoms of anxiety, depression, and PTSD, offering a natural alternative to traditional therapies.\r\nSafe and Reliable: Our Hillbilly Mushrooms are sourced from reputable growers and tested for purity, ensuring a high-quality product with consistent effects.\r\nProduct Details\r\nAppearance: Hillbilly Mushrooms typically feature large, caramel-colored caps with a distinctive wavy edge and a thick stem.\r\nDosage: Recommended starting dosage is typically 1-2 grams for a mild experience, and up to 3-5 grams for a more profound journey. Dosage should be adjusted based on individual tolerance and desired effects.\r\nPackaging: Shipped in discreet, secure packaging to ensure freshness and maintain privacy.\r\nHow to Use Hillbilly Mushrooms\r\nRecommended Dosage: For first-time users, start with a small dose (1 gram) to gauge your reaction. Gradually increase the dosage if needed, based on your experience and tolerance.\r\n\r\nConsumption: Hillbilly Mushrooms can be consumed dried or brewed into a tea. To maximize effects, it is often recommended to consume them on an empty stomach and in a comfortable, safe setting.\r\n\r\nSafety and Precautions\r\nWhile Hillbilly Mushrooms are a natural substance, it is essential to use them responsibly:\r\n\r\nSet and Setting: Ensure you are in a safe, comfortable environment and ideally with a trusted trip sitter if you are new to psychedelics.\r\nStart Low, Go Slow: Begin with a lower dose to assess your sensitivity and response.\r\nAvoid Mixing: Do not combine with alcohol or other substances to avoid potential adverse effects.\r\nMedical Consultation: If you have existing medical conditions or are on medication, consult with a healthcare professional before use.\r\nKeep Out of Reach of Children: Store in a secure place, away from children and pets.\r\nWhy Choose Our Hillbilly Mushrooms?\r\nPremium Quality: We source our Hillbilly Mushrooms from trusted growers to ensure high potency and purity.\r\nConsistent Effects: Our mushrooms are carefully cultivated and processed to deliver reliable and consistent results.\r\nDiscreet and Secure: We prioritize your privacy with discreet, secure packaging and reliable shipping methods.\r\nCustomer Support: Our dedicated team is available to assist with any questions and provide guidance on your psilocybin journey.\r\nOrder Your Hillbilly Mushrooms Today\r\nEmbark on a transformative journey with Hillbilly Mushrooms, renowned for their potent effects and unique characteristics. Whether you seek deep personal insights, creative inspiration, or a profound psychedelic experience, our high-quality mushrooms offer a reliable and enjoyable option. Order now and explore the extraordinary world of Hillbilly Mushrooms from the comfort of your home.', '', 39.00, NULL, 'BUYHILLBIL-ZH8R', 100, 0.00, '', 'product_1753971170_b4f41d22.jpg', NULL, 0, 1, '', '', '2025-07-31 14:12:50', '2025-07-31 14:12:50'),
(16, 9, 'Buy Mazatepec Mushrooms Online USA', 'buy-mazatepec-mushrooms-online-usa', 'Discover the Magic of Mazatepec Mushrooms\r\nWelcome to your premier source for Mazatepec Mushrooms, one of the most revered strains in the world of psychedelic fungi. Originating from the Mazatec region of Oaxaca, Mexico, these mushrooms have been used for centuries by indigenous tribes in sacred ceremonies. Now, you can experience their profound effects by purchasing them online in the USA.\r\n\r\nWhy Choose Mazatepec Mushrooms?\r\nMazatepec Mushrooms are celebrated for their unique blend of potency and spiritual depth. Known for their consistent and reliable effects, they offer a journey that is both enlightening and transformative. Whether you are a seasoned psychonaut or a curious newcomer, Mazatepec Mushrooms provide an ideal balance of intensity and insight.\r\n\r\nKey Benefits\r\nSpiritual Awakening: Mazatepec Mushrooms are renowned for inducing profound spiritual experiences. Users often report a heightened sense of connection to the universe, enhanced introspection, and deep emotional release.\r\nTherapeutic Potential: Emerging research suggests that psychedelic mushrooms, including Mazatepec, may have significant therapeutic benefits. They can potentially help alleviate symptoms of depression, anxiety, PTSD, and other mental health conditions.\r\nCreative Boost: Many users find that Mazatepec Mushrooms enhance creativity and problem-solving abilities, making them a favorite among artists, writers, and innovators.\r\nNatural Origin: Sourced from the pristine Mazatec region, these mushrooms are grown in their natural environment, ensuring purity and potency.\r\nProduct Details\r\nStrain: Mazatepec\r\nOrigin: Oaxaca, Mexico\r\nPotency: Medium to high\r\nAppearance: Light brown caps with slender stems\r\nForm: Dried mushrooms\r\nPackaging: Sealed, discreet packaging to ensure freshness and privacy\r\nHow to Use Mazatepec Mushrooms\r\nDosage: The optimal dosage varies depending on your experience level and desired effects. For beginners, a dose of 1-2 grams is recommended. Experienced users may opt for 3-5 grams. Always start with a lower dose to gauge your sensitivity.\r\n\r\nConsumption Methods: Mazatepec Mushrooms can be consumed in several ways:\r\n\r\nChew and Swallow: The most traditional method. Chew the dried mushrooms thoroughly and swallow.\r\nTea: Brew the mushrooms into a tea for a milder taste and quicker onset of effects.\r\nEdibles: Incorporate the mushrooms into food items such as chocolates or smoothies for a more pleasant consumption experience.\r\nSafety and Precautions\r\nWhile Mazatepec Mushrooms offer a unique and powerful experience, it is crucial to approach them with respect and caution. Ensure you are in a safe, comfortable environment, preferably with a trusted friend or sitter if it’s your first time. Avoid mixing with other substances, and do not operate heavy machinery or drive during your trip. If you have any pre-existing mental health conditions, consult with a healthcare professional before use.\r\n\r\nWhy Buy From Us?\r\nQuality Assurance: Our Mazatepec Mushrooms are sourced directly from trusted growers in the Mazatec region, ensuring the highest quality and potency.\r\nDiscreet Shipping: We prioritize your privacy with discreet packaging and secure shipping methods.\r\nCustomer Support: Our knowledgeable and friendly customer support team is here to assist you with any questions or concerns you may have.\r\nSatisfaction Guarantee: We are committed to your satisfaction and offer a money-back guarantee if you are not completely happy with your purchase.\r\nOrder Your Mazatepec Mushrooms Today\r\nEmbark on a journey of self-discovery and spiritual enlightenment with Mazatepec Mushrooms. Order online today and join countless others who have unlocked the profound benefits of this extraordinary psychedelic. Experience the magic of Mazatepec Mushrooms and transform your perspective on life.', '', 39.00, NULL, 'BUYMAZATEP-NGW4', 100, 0.00, '', 'product_1753971221_a588ebba.jpg', NULL, 0, 1, '', '', '2025-07-31 14:13:41', '2025-07-31 14:13:41'),
(17, 9, 'Buy Penis Envy Mushroom Online USA', 'buy-penis-envy-mushroom-online-usa', 'Unleash the Power of Penis Envy Mushrooms\r\nDiscover the extraordinary potency and unique characteristics of Penis Envy Mushrooms, one of the most sought-after and powerful strains of psilocybin mushrooms available today. Revered for their intense psychedelic effects and rich history, Penis Envy Mushrooms are perfect for experienced psychonauts and adventurous newcomers alike. Explore new dimensions of consciousness and unlock profound personal insights with this exceptional strain.\r\n\r\nWhat are Penis Envy Mushrooms?\r\nOrigin: Penis Envy Mushrooms are believed to have been developed by the renowned ethnobotanist and psychonaut Terence McKenna. Their origins trace back to the Amazon rainforest, where they were selectively bred for their potency.\r\nAppearance: These mushrooms are easily recognizable by their thick, dense stems and large, bulbous caps. Their unique phallic shape is where they derive their name, “Penis Envy.”\r\nPotency: Known for their exceptionally high psilocybin content, Penis Envy Mushrooms are considered one of the most potent strains available, offering an intense and immersive psychedelic experience.\r\nKey Features:\r\nIntense Psychedelic Effects: Experience powerful visual and auditory hallucinations, enhanced sensory perception, and deep introspective journeys.\r\nProfound Personal Insights: Engage in deep self-reflection and gain valuable insights into your thoughts, emotions, and behaviors.\r\nSpiritual and Emotional Growth: Unlock new dimensions of consciousness and achieve greater emotional and spiritual understanding.\r\nEnhanced Creativity: Tap into heightened creativity and improved problem-solving abilities, perfect for artistic endeavors or innovative thinking.\r\nTherapeutic Potential: Studies suggest that psilocybin mushrooms can help alleviate symptoms of depression, anxiety, and PTSD, promoting overall mental well-being.\r\nHow to Use:\r\nStart with a Low Dose: If you are new to Penis Envy Mushrooms or psilocybin in general, start with a small dose to assess your sensitivity and reaction. This approach helps you find the right dose for your experience level.\r\nSet and Setting: Consume the mushrooms in a comfortable, safe, and familiar environment. A calm and peaceful setting enhances the overall experience and allows you to fully embrace the journey.\r\nStay Hydrated: Drink plenty of water before, during, and after consumption to stay hydrated and support your overall well-being.\r\nCreate a Positive Mindset: Approach your psychedelic experience with a positive mindset and clear intentions to maximize the benefits of the journey.\r\nDosage Guidelines:\r\nMicrodose (0.1g to 0.5g): Subtle effects that enhance mood, creativity, and focus without strong psychoactive effects.\r\nLow Dose (0.5g to 1g): Mild psychedelic experience with slight visuals and enhanced sensory perception.\r\nModerate Dose (1g to 2g): More pronounced effects, including moderate visuals, emotional insights, and a sense of euphoria.\r\nHigh Dose (2g to 3.5g): Intense psychedelic experience with strong visuals, profound introspection, and significant sensory and emotional shifts.\r\nSafety and Legal Information:\r\nLegal Status: Ensure you are aware of and comply with the legal status of psilocybin mushrooms in your area before purchasing. Adherence to local laws is crucial.\r\nIntended Use: This product is intended for adult use only. Consult a healthcare professional if you have any health concerns or conditions.\r\nProper Storage: Store in a cool, dry place away from direct sunlight and moisture to maintain freshness and potency.\r\nWhy Choose Penis Envy Mushrooms?\r\nPenis Envy Mushrooms offer a unique and unparalleled psychedelic experience due to their exceptional potency and distinctive characteristics. Whether you seek profound introspection, heightened creativity, or spiritual growth, these mushrooms provide a reliable and transformative journey. With their rich history and powerful effects, Penis Envy Mushrooms are a favorite among psychonauts looking to explore the depths of their consciousness.\r\n\r\nOrder your Penis Envy Mushrooms online today and embark on an extraordinary psychedelic adventure!', '', 40.00, NULL, 'BUYPENISEN-9GM1', 100, 0.00, '', 'product_1753971262_8faebac7.jpg', NULL, 1, 1, '', '', '2025-07-31 14:14:22', '2025-07-31 14:23:09'),
(18, 9, 'Buy Puerto Rican Magic Mushrooms Online USA', 'buy-puerto-rican-magic-mushrooms-online-usa', 'Discover the Power of Puerto Rican Magic Mushrooms\r\nExperience the vibrant and powerful effects of Puerto Rican Magic Mushrooms, a unique and potent strain of psilocybin mushrooms known for its intense visuals and profound introspective journeys. Perfect for both seasoned psychonauts and adventurous newcomers, these magic mushrooms offer a transformative psychedelic experience that can lead to enhanced creativity, deep emotional insights, and spiritual growth.\r\n\r\nWhat are Puerto Rican Magic Mushrooms?\r\nOrigin: Native to the tropical climates of Puerto Rico, these magic mushrooms have been used for centuries in traditional spiritual and healing practices.\r\nAppearance: Characterized by their slender stems and small to medium-sized caps with a light golden color, these mushrooms are visually distinct and easily recognizable.\r\nPotency: Puerto Rican Magic Mushrooms are known for their high psilocybin content, making them one of the more potent strains available.\r\nKey Features:\r\nIntense Visuals: Experience vivid and colorful visuals that enhance your surroundings and open your mind to new perspectives.\r\nProfound Introspective Insights: Engage in deep self-reflection and explore the depths of your consciousness, gaining valuable personal insights and emotional clarity.\r\nEnhanced Creativity: Tap into heightened creativity and improved problem-solving abilities, perfect for artistic endeavors or innovative thinking.\r\nSpiritual Growth: Unlock new dimensions of consciousness and achieve greater emotional and spiritual understanding.\r\nHow to Use:\r\nStart with a Low Dose: If you are new to Puerto Rican Magic Mushrooms, start with a small dose to assess your sensitivity and reaction. This approach helps you find the right dose for your experience level.\r\nSet and Setting: Consume the mushrooms in a comfortable, safe, and familiar environment. A calm and peaceful setting enhances the overall experience and allows you to fully embrace the journey.\r\nStay Hydrated: Drink plenty of water before, during, and after consumption to stay hydrated and support your overall well-being.\r\nCreate a Positive Mindset: Approach your psychedelic experience with a positive mindset and clear intentions to maximize the benefits of the journey.\r\nDosage Guidelines:\r\nMicrodose: (0.1g to 0.5g) – Subtle effects that enhance mood, creativity, and focus without strong psychoactive effects.\r\nLow Dose: (0.5g to 1g) – Mild psychedelic experience with slight visuals and enhanced sensory perception.\r\nModerate Dose: (1g to 2g) – More pronounced effects, including moderate visuals, emotional insights, and a sense of euphoria.\r\nHigh Dose: (2g to 3.5g) – Intense psychedelic experience with strong visuals, profound introspection, and significant sensory and emotional shifts.\r\nSafety and Legal Information:\r\nLegal Status: Ensure you are aware of and comply with the legal status of psilocybin mushrooms in your area before purchasing. Adherence to local laws is crucial.\r\nIntended Use: This product is intended for adult use only. Consult a healthcare professional if you have any health concerns or conditions.\r\nProper Storage: Store in a cool, dry place away from direct sunlight and moisture to maintain freshness and potency.\r\nWhy Choose Puerto Rican Magic Mushrooms?\r\nPuerto Rican Magic Mushrooms offer a unique and powerful way to explore the benefits of psilocybin. Whether you seek profound introspection, heightened creativity, or spiritual growth, these mushrooms provide a reliable and transformative experience. With their intense potency and vibrant effects, Puerto Rican Magic Mushrooms are a favorite among psychonauts seeking a deep and meaningful journey.\r\n\r\nOrder your Puerto Rican Magic Mushrooms online today and embark on an extraordinary psychedelic adventure!', '', 39.00, NULL, 'BUYPUERTOR-P8KN', 100, 0.00, '', 'product_1753971326_cf45c8c7.jpg', NULL, 0, 1, '', '', '2025-07-31 14:15:26', '2025-07-31 14:15:26'),
(19, 9, 'Buy Texas Penis Envy Mushrooms Online USA', 'buy-texas-penis-envy-mushrooms-online-usa', 'Unlock the Potency of Texas Penis Envy Mushrooms\r\nDiscover the exceptional power and transformative potential of Texas Penis Envy Mushrooms, one of the most potent and sought-after psilocybin strains available online in the USA. Known for their extraordinary potency and unique characteristics, these mushrooms offer a profound journey into the depths of consciousness, making them an ideal choice for both experienced psychonauts and curious explorers.\r\n\r\nWhat Are Texas Penis Envy Mushrooms?\r\nExceptional Potency: Texas Penis Envy Mushrooms are renowned for their high psilocybin content, which results in a powerful and immersive psychedelic experience. They are considered one of the most potent strains of magic mushrooms available.\r\nDistinctive Genetics: This strain is a variant of the famous Penis Envy mushrooms, specifically adapted and cultivated to thrive in the Texas climate. They boast a unique genetic profile that contributes to their exceptional strength.\r\nUnique Appearance: Characterized by their thick, robust stems and large, bulbous caps, Texas Penis Envy Mushrooms are visually striking and immediately recognizable.\r\nKey Features:\r\nPowerful Psychedelic Effects: Experience intense visual and auditory hallucinations, enhanced sensory perception, and profound introspective insights.\r\nHigh Psilocybin and Psilocin Content: Known for their strong and consistent effects, making them ideal for deep psychedelic exploration.\r\nDistinctive Appearance: Robust, thick stems and large, bulbous caps set these mushrooms apart from other strains.\r\nRich Cultural and Historical Significance: The Penis Envy strain has a storied history in the world of psychedelics, celebrated for its powerful effects and transformative potential.\r\nHow to Use:\r\nStart with a Low Dose: Due to their high potency, it is recommended to start with a small amount to assess your sensitivity and reaction. This approach helps you find the right dose for your experience level.\r\nCreate a Safe Environment: Consume the mushrooms in a comfortable, safe, and familiar setting. A calm and peaceful environment enhances the overall experience and allows you to fully embrace the journey.\r\nStay Hydrated: Drink plenty of water before, during, and after consumption to stay hydrated and support your overall well-being.\r\nDosage and Consumption: Texas Penis Envy Mushrooms can be eaten raw, dried, or brewed into a tea. Choose the method that best suits your preference and experience level.\r\nBenefits:\r\nIntense Visual and Auditory Hallucinations: Enjoy enhanced sensory experiences with vivid, colorful visuals and heightened auditory effects.\r\nProfound Introspective Insights: Engage in deep self-reflection and explore the depths of your mind, gaining valuable insights.\r\nEmotional and Spiritual Growth: Unlock new dimensions of consciousness and achieve greater emotional and spiritual understanding.\r\nEnhanced Creativity and Problem-Solving: Tap into heightened creativity and improved problem-solving abilities during and after the experience.\r\nSafety and Legal Information:\r\nLegal Status: Ensure you are aware of and comply with the legal status of psilocybin mushrooms in your area before purchasing. Adherence to local laws is crucial.\r\nIntended Use: This product is intended for adult use only. Consult a healthcare professional if you have any health concerns or conditions.\r\nProper Storage: Store in a cool, dry place away from direct sunlight and moisture to maintain freshness and potency.\r\nWhy Choose Texas Penis Envy Mushrooms?\r\nTexas Penis Envy Mushrooms offer an unparalleled psychedelic experience, combining exceptional potency with the rich history and unique genetics of the Penis Envy strain. Their high psilocybin content ensures a powerful and consistent effect, making them an ideal choice for both personal exploration and shared experiences. Whether you seek profound introspection, heightened creativity, or spiritual growth, Texas Penis Envy Mushrooms provide a reliable and transformative journey.\r\n\r\nOrder your Texas Penis Envy Mushrooms online in the USA today and embark on an extraordinary journey into the world of psychedelics!', '', 35.00, NULL, 'BUYTEXASPE-QY2G', 100, 0.00, '', 'product_1753971374_302beed0.jpg', NULL, 0, 1, '', '', '2025-07-31 14:16:14', '2025-07-31 14:16:14');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `short_description`, `price`, `sale_price`, `sku`, `stock_quantity`, `weight`, `dimensions`, `image`, `gallery`, `is_featured`, `is_active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(20, 9, 'Buy Tidal Wave Magic Mushrooms Online USA', 'buy-tidal-wave-magic-mushrooms-online-usa', 'Dive Into the Psychedelic Power of Tidal Wave Magic Mushrooms\r\nExperience the extraordinary with Tidal Wave Magic Mushrooms, a unique and potent variety of psilocybin mushrooms available for purchase online in the USA. Renowned for their powerful effects and distinctive characteristics, Tidal Wave Magic Mushrooms promise an unforgettable journey into the depths of your consciousness. Perfect for both seasoned psychonauts and curious newcomers, these mushrooms offer a profound exploration of the mind and senses.\r\n\r\nWhat are Tidal Wave Magic Mushrooms?\r\nUnique Hybrid Strain: Tidal Wave Magic Mushrooms are a hybrid strain, created by crossing two well-known varieties, the B+ and the Penis Envy. This combination results in a mushroom with exceptional potency and a unique appearance.\r\nHigh Psilocybin Content: Known for their high psilocybin and psilocin content, Tidal Wave Magic Mushrooms deliver a powerful and consistent psychedelic experience, marked by intense visuals and deep introspection.\r\nDistinctive Appearance: These mushrooms feature a robust and thick stem with a bulbous cap, often showcasing vibrant hues that hint at their potency.\r\nKey Features:\r\nPowerful Psychedelic Effects: Expect a strong and immersive psychedelic journey, characterized by vivid visual hallucinations, heightened sensory perception, and profound introspective insights.\r\nUnique Hybrid Genetics: Enjoy the best of both worlds with this hybrid strain that combines the desirable traits of B+ and Penis Envy mushrooms.\r\nHigh-Quality Cultivation: Grown under optimal conditions to ensure maximum potency, purity, and consistency.\r\nDistinctive Visual Appeal: Recognizable by their robust stems and bulbous caps, these mushrooms are as visually striking as they are effective.\r\nHow to Use:\r\nStart with a Low Dose: Due to their high potency, it is recommended to begin with a small amount to assess your sensitivity and reaction. This approach helps in finding the right dose for your experience level.\r\nSet and Setting: Consume the mushrooms in a safe, comfortable, and familiar environment. A calm setting enhances the overall experience, allowing you to fully embrace the journey.\r\nHydration and Preparation: Stay hydrated by drinking plenty of water before, during, and after consumption. Ensure you are in a positive mental state and free from distractions.\r\nConsumption Method: Tidal Wave Magic Mushrooms can be eaten raw, dried, or brewed into a tea. Choose the method that best suits your preference and experience level.\r\nBenefits:\r\nIntense Visual and Auditory Hallucinations: Experience enhanced sensory perception with vivid, colorful visuals and heightened auditory effects.\r\nDeep Introspective Insights: Engage in profound self-reflection and explore the depths of your mind.\r\nEmotional and Spiritual Growth: Unlock new dimensions of consciousness and achieve greater emotional and spiritual understanding.\r\nEnhanced Creativity and Problem-Solving: Tap into heightened creativity and improved problem-solving abilities during and after the experience.\r\nSafety and Legal Information:\r\nLegal Status: Ensure you are aware of the legal status of psilocybin mushrooms in your area before purchasing. Adherence to local laws is crucial.\r\nIntended Use: This product is intended for adult use only. Consult a healthcare professional if you have any health concerns or conditions.\r\nProper Storage: Store in a cool, dry place away from direct sunlight and moisture to maintain freshness and potency.\r\nWhy Choose Tidal Wave Magic Mushrooms?\r\nTidal Wave Magic Mushrooms offer an unparalleled psychedelic experience, combining the best traits of two renowned strains into one powerful hybrid. Their high psilocybin content ensures a potent and consistent effect, making them an ideal choice for both personal exploration and shared experiences. Whether you seek profound introspection, heightened creativity, or spiritual growth, Tidal Wave Magic Mushrooms provide a reliable and transformative journey.\r\n\r\nOrder your Tidal Wave Magic Mushrooms online in the USA today and embark on a captivating journey into the world of psychedelics!', '', 59.00, NULL, 'BUYTIDALWA-IV0Q', 100, 0.00, '', 'product_1753971436_d032f994.jpg', NULL, 0, 1, '', '', '2025-07-31 14:17:16', '2025-07-31 14:17:16'),
(21, 9, 'Buy Vietnamese Psilocybe Cubensis Magic Mushroom', 'buy-vietnamese-psilocybe-cubensis-magic-mushroom', 'Discover the Mystical Power of Vietnamese Psilocybe Cubensis\r\nIntroducing the Vietnamese Psilocybe Cubensis Magic Mushroom, a captivating variety renowned for its unique effects and cultural significance. This strain of magic mushroom is celebrated for its potent properties and rich history, offering an exceptional journey into the realms of consciousness. Whether you are an experienced psychonaut or a curious explorer, the Vietnamese Psilocybe Cubensis is designed to provide a transformative and memorable experience.\r\n\r\nWhat is Vietnamese Psilocybe Cubensis?\r\nExotic Origins: Hailing from the lush landscapes of Vietnam, this strain of Psilocybe Cubensis is known for its distinctive appearance and powerful effects. It is a favorite among enthusiasts for its unique characteristics and historical background.\r\nPotent Psychedelic Properties: Vietnamese Psilocybe Cubensis is revered for its high psilocybin content, which contributes to a profound and immersive psychedelic experience. It is known for producing vivid visual hallucinations, enhanced sensory perception, and deep introspection.\r\nKey Features:\r\nAuthentic Vietnamese Strain: Sourced directly from Vietnam, ensuring genuine and high-quality Psilocybe Cubensis.\r\nPowerful Effects: Known for its strong potency, this strain delivers a deep and immersive psychedelic experience.\r\nUnique Appearance: Features distinct physical traits, including a medium to large size, with a caramel to golden-brown cap and white to light tan stems.\r\nRich Cultural Significance: This strain has a storied history in traditional practices and modern psychedelic exploration.\r\nHow to Use:\r\nStart with a Small Dose: Due to its potency, it is recommended to begin with a small amount to assess your sensitivity and reaction. This allows you to gauge the effects and adjust as needed.\r\nCreate a Comfortable Setting: Consume the mushrooms in a safe and comfortable environment where you can fully embrace the experience. A calm and peaceful setting enhances the overall journey.\r\nHydrate and Prepare: Drink plenty of water before, during, and after consumption to stay hydrated. Ensure you are in a positive mental state and have no distractions.\r\nDosage and Consumption: The dosage can vary based on individual sensitivity and experience level. Start with a lower dose and gradually increase as needed.\r\nBenefits:\r\nEnhanced Sensory Perception: Experience heightened senses and vivid visual and auditory effects.\r\nProfound Introspection: Engage in deep self-reflection and gain valuable insights into your inner self.\r\nEmotional and Spiritual Growth: Explore new dimensions of consciousness and achieve a greater understanding of your emotional and spiritual self.\r\nSafety and Legal Information:\r\nLegal Status: Ensure you are aware of and comply with the legal status of psilocybin mushrooms in your location before purchasing. It is important to adhere to local laws and regulations.\r\nIntended Use: This product is intended for adult use only. Consult a healthcare professional if you have any health concerns or conditions.\r\nProper Storage: Store in a cool, dry place away from direct sunlight and moisture to maintain freshness and potency.\r\nWhy Choose Vietnamese Psilocybe Cubensis?\r\nThe Vietnamese Psilocybe Cubensis Magic Mushroom offers a unique and powerful psychedelic experience, enriched by its exotic origins and potent effects. It provides an opportunity for deep introspection and sensory exploration, making it a preferred choice for those seeking both spiritual growth and sensory enhancement. Whether you are new to psychedelics or an experienced user, this strain promises a journey of discovery and transformation.\r\n\r\nOrder your Vietnamese Psilocybe Cubensis Magic Mushroom today and embark on a captivating journey into the heart of psychedelic exploration!', '', 25.00, NULL, 'BUYVIETNAM-LF4D', 100, 0.00, '', 'product_1753971625_4c0c8e90.jpg', NULL, 1, 1, '', '', '2025-07-31 14:20:25', '2025-07-31 14:23:10'),
(22, 10, 'Buy Alice Mushroom Micro Dose Capsules Cosmos (6000mg)', 'buy-alice-mushroom-micro-dose-capsules-cosmos-6000mg', 'Unlock a New Realm of Potential with Alice Mushroom Micro Dose Capsules Cosmos\r\nEmbark on a transformative journey with Alice Mushroom Micro Dose Capsules Cosmos, a premium product designed to enhance your mental clarity, creativity, and overall well-being. Each capsule contains an impressive 6000mg of psilocybin, offering a potent yet controlled dose that provides all the benefits of microdosing while supporting a balanced and productive lifestyle. Perfect for daily use, these capsules are meticulously crafted to deliver a consistent and effective microdosing experience.\r\n\r\nWhat Are Alice Mushroom Micro Dose Capsules Cosmos?\r\nAlice Mushroom Micro Dose Capsules Cosmos are carefully formulated to deliver a potent 6000mg dose of psilocybin per capsule. Psilocybin is a naturally occurring psychedelic compound found in certain mushrooms, known for its ability to boost cognitive function, inspire creativity, and promote emotional balance. These capsules are ideal for individuals seeking the subtle yet powerful benefits of psilocybin in a convenient, manageable format. Designed for those who want to explore the benefits of microdosing while maintaining control over their experience, Alice Mushroom Micro Dose Capsules Cosmos offer an exceptional solution.\r\n\r\nKey Benefits of Alice Mushroom Micro Dose Capsules Cosmos\r\nEnhanced Mental Clarity: Improves focus, concentration, and problem-solving skills, allowing you to perform at your best.\r\nStimulated Creativity: Encourages innovative thinking and creative ideas, making it ideal for artists, writers, and professionals.\r\nEmotional Stability: Supports emotional balance and resilience, helping you manage stress and maintain a positive outlook.\r\nIncreased Energy: Provides a natural boost in energy and motivation, enhancing productivity and overall well-being.\r\nPotent Microdosing: Each capsule contains 6000mg of psilocybin, offering a strong yet controlled microdosing experience.\r\nConvenient and Discreet: Capsules are easy to swallow and fit seamlessly into your daily routine with a discreet design.\r\nProduct Details\r\nDosage: Each capsule contains 6000mg of psilocybin. Start with one capsule and adjust based on your personal response and desired effects. Consistent use is key to experiencing the benefits of microdosing.\r\nAppearance: Capsules are designed for easy ingestion and discreet use, making them ideal for daily consumption.\r\nPackaging: Shipped in a secure, tamper-proof bottle to ensure the product’s freshness and potency. Discreet packaging maintains your privacy.\r\nHow to Use Alice Mushroom Micro Dose Capsules Cosmos\r\nRecommended Dosage: Begin with one 6000mg capsule and monitor your response. Adjust the dosage as needed based on your individual sensitivity and desired effects. Regular, consistent use is essential for achieving the full benefits of microdosing.\r\n\r\nConsumption: Take the capsule with water or your preferred beverage. Incorporate into your daily routine and avoid combining with alcohol or other substances for optimal results.\r\n\r\nSafety and Precautions\r\nTo ensure a safe and beneficial microdosing experience, follow these guidelines:\r\n\r\nConsultation: Consult with a healthcare professional if you have any pre-existing health conditions or are taking medication.\r\nStart Low, Go Slow: Begin with the recommended dosage and adjust based on your personal response to prevent potential overuse.\r\nAvoid Mixing: Do not combine with alcohol or other substances to avoid adverse interactions.\r\nLegal Considerations: Confirm that psilocybin use is legal in your area before purchasing.\r\nWhy Choose Alice Mushroom Micro Dose Capsules Cosmos?\r\nHigh-Quality Psilocybin: Made with premium-grade psilocybin to ensure potency and effectiveness.\r\nReliable Microdosing: Provides a consistent and dependable microdosing experience with each capsule.\r\nConvenient and Discreet: Easy-to-use capsules that are perfect for daily consumption and discreet use.\r\nCustomer Support: Our team is available to answer any questions and support you throughout your microdosing journey.\r\nOrder Your Alice Mushroom Micro Dose Capsules Cosmos Today\r\nElevate your daily routine and experience the benefits of microdosing with Alice Mushroom Micro Dose Capsules Cosmos. With a powerful 6000mg dose in each capsule, this product offers a convenient and effective solution for enhancing mental clarity, creativity, and emotional well-being. Order now to begin your journey with a meticulously crafted microdosing regimen designed to enrich every aspect of your life.', '', 80.00, NULL, 'BUYALICEMU-SLWK', 100, 0.00, '', 'product_1753972059_f5e03bc0.jpg', NULL, 0, 1, '', '', '2025-07-31 14:27:39', '2025-07-31 14:27:39'),
(23, 10, 'Buy Cubes Scooby Snacks Microdose Capsules (50x300mg)', 'buy-cubes-scooby-snacks-microdose-capsules-50x300mg', 'Elevate Your Daily Routine with Cubes Scooby Snacks Microdose Capsules\r\nDiscover a new level of mental clarity, creativity, and emotional balance with Cubes Scooby Snacks Microdose Capsules. Each capsule is carefully formulated with 300mg of psilocybin, providing a subtle yet effective dose designed to enhance your daily life. With 50 capsules per bottle, this product offers a consistent and convenient microdosing solution to support your personal and professional goals.\r\n\r\nWhat Are Cubes Scooby Snacks Microdose Capsules?\r\nCubes Scooby Snacks Microdose Capsules are a high-quality microdosing product featuring a controlled 300mg dose of psilocybin per capsule. Designed to provide a gentle yet impactful boost to your cognitive and emotional well-being, these capsules are perfect for those looking to integrate the benefits of microdosing into their daily routine. The 50-count bottle ensures a steady supply of capsules, making it easy to maintain a regular microdosing regimen.\r\n\r\nKey Benefits of Cubes Scooby Snacks Microdose Capsules\r\nEnhanced Cognitive Function: Improves focus, mental clarity, and problem-solving skills for optimal daily performance.\r\nBoosted Creativity: Encourages creative thinking and innovative ideas, beneficial for artists, writers, and entrepreneurs.\r\nEmotional Balance: Supports emotional stability and resilience, helping you manage stress and maintain a positive outlook.\r\nIncreased Energy: Provides a natural boost in energy and motivation, enhancing productivity and overall well-being.\r\nPrecise Dosage: Each capsule contains a consistent 300mg of psilocybin for a reliable microdosing experience.\r\nConvenient Supply: 50 capsules per bottle offer a long-lasting supply for sustained microdosing benefits.\r\nProduct Details\r\nDosage: Each capsule contains 300mg of psilocybin, designed for precise microdosing. Start with one capsule and adjust as needed based on your personal response and desired effects.\r\nAppearance: Capsules are easy to swallow and ideal for discreet use, fitting seamlessly into your daily routine.\r\nPackaging: Shipped in a secure, tamper-proof bottle to ensure the freshness and potency of the product. Discreet packaging maintains your privacy.\r\nHow to Use Cubes Scooby Snacks Microdose Capsules\r\nRecommended Dosage: Begin with one 300mg capsule and observe your response. You may adjust the dosage based on individual sensitivity and desired effects. For best results, incorporate into your daily routine and avoid combining with alcohol or other substances.\r\n\r\nConsumption: Take the capsule with water or your preferred beverage. Consistent use is key to achieving the benefits of microdosing, so consider incorporating it into your regular schedule.\r\n\r\nSafety and Precautions\r\nFor a safe and effective microdosing experience, follow these guidelines:\r\n\r\nConsultation: Consult with a healthcare professional if you have any pre-existing health conditions or are taking medication.\r\nStart Low, Go Slow: Begin with the recommended dosage and adjust based on your individual response.\r\nAvoid Mixing: Do not combine with alcohol or other substances to prevent potential interactions.\r\nLegal Considerations: Ensure that psilocybin use is legal in your area before purchasing.\r\nWhy Choose Cubes Scooby Snacks Microdose Capsules?\r\nHigh-Quality Psilocybin: Made with premium-grade psilocybin to ensure purity and consistent effects.\r\nReliable Results: Provides a dependable microdosing experience with each capsule.\r\nConvenient and Discreet: Easy-to-use capsules that fit effortlessly into your daily routine.\r\nCustomer Support: Our team is dedicated to assisting you with any questions and providing support for your microdosing journey.\r\nOrder Your Cubes Scooby Snacks Microdose Capsules Today\r\nEnhance your daily performance and well-being with Cubes Scooby Snacks Microdose Capsules. With a precise 300mg dose in each of the 50 capsules, this product offers a powerful and convenient microdosing solution. Order now to experience the benefits of a well-crafted microdosing regimen designed to elevate every aspect of your life.', '', 160.00, NULL, 'BUYCUBESSC-479B', 100, 0.00, '', 'product_1753972134_865ca839.jpg', NULL, 1, 1, '', '', '2025-07-31 14:28:54', '2025-07-31 14:36:51'),
(24, 10, 'Buy Euphoria Psychedelics Micro Boost Capsules (2000mg)', 'buy-euphoria-psychedelics-micro-boost-capsules-2000mg', 'Unleash Your Potential with Euphoria Psychedelics Micro Boost Capsules\r\nElevate your daily performance and mental acuity with Euphoria Psychedelics Micro Boost Capsules. Formulated with a precise 2000mg dose of psilocybin, these capsules are designed to enhance cognitive function, increase motivation, and support overall mental well-being. Perfect for those seeking a subtle yet impactful boost, Euphoria Psychedelics Micro Boost Capsules offer a convenient and controlled way to experience the benefits of microdosing without the intensity of a full psychedelic trip.\r\n\r\nWhat Are Euphoria Psychedelics Micro Boost Capsules?\r\nEuphoria Psychedelics Micro Boost Capsules are a specially crafted microdosing supplement containing 2000mg of psilocybin. This carefully measured dosage is intended to provide a gentle enhancement to your cognitive and emotional state, helping you achieve peak performance and maintain a positive outlook. Each capsule is designed to deliver consistent and reliable results, making it an ideal choice for those who want to integrate microdosing into their routine for a boost in productivity and mental clarity.\r\n\r\nKey Benefits of Euphoria Psychedelics Micro Boost Capsules\r\nIncreased Motivation: Helps boost your drive and enthusiasm for daily tasks, improving productivity and goal achievement.\r\nEnhanced Cognitive Function: Supports mental clarity, focus, and problem-solving abilities for optimal performance.\r\nImproved Mood: Promotes a positive and uplifted mood, contributing to overall mental well-being and resilience.\r\nBoosted Creativity: Stimulates creative thinking and innovation, making it valuable for creative professionals and entrepreneurs.\r\nBalanced Energy: Provides a natural boost in energy levels without the jitteriness or crash associated with stimulants.\r\nProduct Details\r\nDosage: Each capsule contains 2000mg of psilocybin, offering a precise microdose for effective and subtle enhancements to your mental and emotional state.\r\nAppearance: Capsules are discreet and easy to swallow, designed for convenience and daily use.\r\nPackaging: Packaged in secure, tamper-proof containers to ensure freshness and potency. Discreet packaging ensures privacy and protection during shipping.\r\nHow to Use Euphoria Psychedelics Micro Boost Capsules\r\nRecommended Dosage: Start with one capsule per day to assess your response. Adjust the dosage based on personal tolerance and desired effects. For optimal results, take the capsule in the morning or at a time that aligns with your daily routine.\r\n\r\nConsumption: Take the capsule with water or your preferred beverage. To maximize benefits, avoid combining with alcohol or other substances.\r\n\r\nSafety and Precautions\r\nFor a safe and effective experience, follow these guidelines:\r\n\r\nConsultation: If you have existing health conditions or are on medication, consult a healthcare professional before starting microdosing.\r\nStart Low, Go Slow: Begin with the recommended dosage and adjust as needed based on your individual response.\r\nAvoid Mixing: Do not combine with alcohol or other substances to prevent adverse interactions.\r\nKeep Out of Reach of Children: Store in a secure place away from children and pets.\r\nWhy Choose Our Euphoria Psychedelics Micro Boost Capsules?\r\nPremium Quality: Made with high-quality psilocybin to ensure purity and effectiveness.\r\nConsistent Results: Each capsule delivers a precise microdose for reliable and consistent benefits.\r\nConvenient and Discreet: Easy-to-use capsules are perfect for incorporating microdosing into your lifestyle discreetly.\r\nCustomer Support: Our team is dedicated to providing assistance and guidance to enhance your microdosing experience.\r\nOrder Your Euphoria Psychedelics Micro Boost Capsules Today\r\nUnlock your full potential and enhance your daily performance with Euphoria Psychedelics Micro Boost Capsules. Whether you’re seeking increased motivation, mental clarity, or a boost in creativity, our 2000mg capsules offer a reliable and effective solution. Order now to experience the subtle, yet impactful benefits of microdosing and take your productivity and well-being to the next level.', '', 45.00, NULL, 'BUYEUPHORI-6SMG', 100, 0.00, '', 'product_1753972194_1600e2b3.jpg', NULL, 0, 1, '', '', '2025-07-31 14:29:54', '2025-07-31 14:29:54'),
(25, 10, 'Buy Euphoria Psychedelics Micro Calm Capsules (2000mg)', 'buy-euphoria-psychedelics-micro-calm-capsules-2000mg', 'Embrace Serenity with Euphoria Psychedelics Micro Calm Capsules\r\nDiscover a new level of tranquility and mental clarity with Euphoria Psychedelics Micro Calm Capsules. Formulated to offer a gentle yet effective microdosing experience, these capsules are designed to help you achieve a state of calm and balance in your daily life. With a carefully measured 2000mg of psilocybin per capsule, Euphoria Psychedelics Micro Calm Capsules are perfect for those seeking subtle, ongoing benefits from microdosing, including stress relief, emotional stability, and enhanced focus.\r\n\r\nWhat Are Euphoria Psychedelics Micro Calm Capsules?\r\nEuphoria Psychedelics Micro Calm Capsules contain a precise microdose of psilocybin, the active compound in magic mushrooms, aimed at promoting relaxation and emotional balance without the intensity of a full psychedelic experience. Each 2000mg capsule is expertly crafted to provide a controlled dose that supports mental well-being and helps maintain a serene state of mind throughout your day. Ideal for anyone looking to integrate microdosing into their routine for a more peaceful and focused life.\r\n\r\nKey Benefits of Euphoria Psychedelics Micro Calm Capsules\r\nEnhanced Calmness: Promotes relaxation and reduces anxiety, helping you stay calm and composed in stressful situations.\r\nImproved Emotional Stability: Supports emotional balance and resilience, aiding in mood regulation and mental well-being.\r\nIncreased Focus: Enhances concentration and mental clarity, making it easier to stay on task and maintain productivity.\r\nNatural Stress Relief: Provides a natural way to manage stress and promote a sense of overall well-being.\r\nGentle Microdosing: Offers a subtle, controlled dosage that provides benefits without overwhelming effects.\r\nProduct Details\r\nDosage: Each capsule contains 2000mg of psilocybin, providing a controlled microdose that supports calmness and focus. The dosage is carefully formulated to deliver effective results while maintaining subtlety.\r\nAppearance: Capsules are easy to swallow and feature a discreet design for convenient daily use.\r\nPackaging: Comes in secure, tamper-proof containers to ensure product freshness and integrity. Discreet packaging ensures privacy during shipping.\r\nHow to Use Euphoria Psychedelics Micro Calm Capsules\r\nRecommended Dosage: Start with one capsule daily to gauge your response. Adjust dosage as needed based on individual tolerance and desired effects. For optimal results, take the capsule in the morning or at a time that suits your daily routine.\r\n\r\nConsumption: Take the capsule with water or your preferred beverage. It is advisable to avoid consuming with alcohol or other substances to ensure the best possible effects.\r\n\r\nSafety and Precautions\r\nWhile Euphoria Psychedelics Micro Calm Capsules are designed for safe and effective use, please follow these guidelines:\r\n\r\nConsultation: If you have any pre-existing health conditions or are on medication, consult with a healthcare professional before starting microdosing.\r\nStart Low, Go Slow: Begin with the recommended dosage and adjust according to your personal experience and tolerance.\r\nAvoid Mixing: Refrain from combining with alcohol or other substances to avoid potential interactions.\r\nKeep Out of Reach of Children: Store in a secure place away from children and pets.\r\nWhy Choose Our Euphoria Psychedelics Micro Calm Capsules?\r\nPremium Quality: Made with high-quality psilocybin to ensure purity and effectiveness.\r\nConsistent Results: Each capsule delivers a precise microdose for reliable and consistent results.\r\nDiscreet and Convenient: Easy-to-use capsules are perfect for incorporating microdosing into your daily routine discreetly.\r\nCustomer Support: Our dedicated team is here to assist with any questions and provide guidance on your microdosing journey.\r\nOrder Your Euphoria Psychedelics Micro Calm Capsules Today\r\nEnhance your mental well-being and embrace a state of calm with Euphoria Psychedelics Micro Calm Capsules. Whether you seek relief from stress, improved focus, or emotional balance, our 2000mg capsules offer a reliable and gentle solution. Order now to experience the subtle benefits of microdosing and enjoy a more serene and focused life', '', 45.00, NULL, 'BUYEUPHORI-J8K2', 100, 0.00, '', 'product_1753972275_752f7e24.jpg', NULL, 0, 1, '', '', '2025-07-31 14:31:15', '2025-07-31 14:31:15'),
(26, 10, 'Buy Euphoria Psychedelics Microdose Capsules (3000mg)', 'buy-euphoria-psychedelics-microdose-capsules-3000mg', 'Elevate Your Mind with Euphoria Psychedelics Microdose Capsules\r\nExperience a new level of mental clarity, creativity, and emotional balance with Euphoria Psychedelics Microdose Capsules. Designed for those seeking the benefits of psychedelics without the intensity of a full trip, these capsules offer a controlled and convenient way to integrate microdosing into your daily routine. With a potent 3000mg formulation, Euphoria Psychedelics Microdose Capsules are perfect for enhancing productivity, boosting mood, and fostering a deeper sense of well-being.\r\n\r\nWhat Are Euphoria Psychedelics Microdose Capsules?\r\nEuphoria Psychedelics Microdose Capsules contain a precise dose of psilocybin, the active compound in magic mushrooms, formulated to provide subtle yet effective benefits. Each capsule is meticulously crafted to deliver a consistent microdose, ensuring that you receive a balanced and controlled experience. The 3000mg dosage is designed to offer significant effects without overwhelming the user, making it an ideal choice for those looking to enhance their mental performance and emotional health.\r\n\r\nKey Benefits of Euphoria Psychedelics Microdose Capsules\r\nEnhanced Cognitive Function: Supports improved focus, mental clarity, and problem-solving abilities for increased productivity and performance.\r\nElevated Mood: Helps uplift your mood and reduce symptoms of anxiety and depression, promoting a more positive outlook on life.\r\nIncreased Creativity: Stimulates creative thinking and innovation, making it a valuable tool for artists, writers, and entrepreneurs.\r\nBalanced Energy Levels: Provides a natural boost in energy without the jitteriness associated with stimulants, helping you stay motivated throughout the day.\r\nEmotional Stability: Promotes emotional balance and resilience, aiding in stress management and overall mental well-being.\r\nProduct Details\r\nDosage: Each capsule contains 3000mg of psilocybin, providing a controlled microdose for daily use. The dosage is carefully calibrated to deliver subtle effects that enhance mental and emotional health.\r\nAppearance: Capsules are discreet and easy to swallow, making them a convenient option for on-the-go use.\r\nPackaging: Shipped in secure, tamper-proof containers to ensure freshness and potency. Discreet packaging protects your privacy and maintains product integrity.\r\nHow to Use Euphoria Psychedelics Microdose Capsules\r\nRecommended Dosage: Start with one capsule per day to assess your response. Adjust dosage based on individual tolerance and desired effects. For best results, take the capsule in the morning to experience the full benefits throughout the day.\r\n\r\nConsumption: Take the capsule with water or your preferred beverage. Avoid taking with other substances or alcohol to ensure optimal effects.\r\n\r\nSafety and Precautions\r\nWhile Euphoria Psychedelics Microdose Capsules are formulated for safe and effective use, it is important to follow these guidelines:\r\n\r\nConsultation: If you have pre-existing health conditions or are taking medication, consult with a healthcare professional before use.\r\nStart Low, Go Slow: Begin with the recommended dosage and adjust as needed based on your response.\r\nAvoid Mixing: Do not combine with alcohol or other substances to prevent potential interactions.\r\nKeep Out of Reach of Children: Store in a secure location away from children and pets.\r\nWhy Choose Our Euphoria Psychedelics Microdose Capsules?\r\nHigh-Quality Ingredients: Crafted with premium psilocybin and other supportive ingredients to ensure purity and efficacy.\r\nConsistent Effects: Each capsule provides a precise and reliable microdose for a consistent experience.\r\nDiscreet and Convenient: Capsules are easy to take and carry, offering a discreet option for incorporating microdosing into your lifestyle.\r\nCustomer Support: Our team is available to answer any questions and provide guidance on your microdosing journey.\r\nOrder Your Euphoria Psychedelics Microdose Capsules Today\r\nUnlock the benefits of microdosing with Euphoria Psychedelics Microdose Capsules. Whether you seek enhanced cognitive function, mood elevation, or creative inspiration, our 3000mg capsules offer a reliable and effective solution. Order now and experience the subtle, yet powerful effects of microdosing from the comfort of your home.', '', 55.00, NULL, 'BUYEUPHORI-LSBS', 100, 0.00, '', 'product_1753972322_13dc44f8.jpg', NULL, 0, 1, '', '', '2025-07-31 14:32:02', '2025-07-31 14:32:02'),
(27, 10, 'Buy Kind Stranger Holiday Capsules (125mg)', 'buy-kind-stranger-holiday-capsules-125mg', 'Celebrate the Season with Kind Stranger Holiday Capsules\r\nElevate your holiday experience with Kind Stranger Holiday Capsules, expertly crafted to offer a festive boost to your seasonal celebrations. Each capsule contains a precisely measured 125mg dose of high-quality psilocybin, designed to enhance your holiday mood, creativity, and overall well-being. Perfect for those looking to infuse their holiday season with a touch of magic, these capsules provide a unique and uplifting way to enjoy the festivities.\r\n\r\nWhat Are Kind Stranger Holiday Capsules?\r\nKind Stranger Holiday Capsules are specially formulated to deliver a controlled and effective dose of psilocybin, a naturally occurring compound found in magic mushrooms known for its mood-enhancing properties. Each capsule contains 125mg of psilocybin, making it an ideal choice for those looking to experience the positive effects of microdosing during the holiday season. The capsules are designed to enhance your festive spirit, boost creativity, and promote a sense of joy and well-being.\r\n\r\nKey Benefits of Kind Stranger Holiday Capsules\r\nFestive Mood Enhancement: Elevates your holiday spirit and enhances your enjoyment of seasonal celebrations.\r\nBoosted Creativity: Stimulates creative thinking and helps you come up with unique ideas for holiday activities and gifts.\r\nImproved Emotional Well-Being: Supports a positive mood and emotional balance, helping you navigate the holiday season with ease.\r\nControlled Dosage: Each capsule contains a precise 125mg dose of psilocybin, providing a consistent and manageable experience.\r\nConvenient and Discreet: Easy-to-use capsules that fit seamlessly into your holiday routine, with a discreet design for privacy.\r\nProduct Details\r\nDosage: Each capsule contains 125mg of high-quality psilocybin. Begin with the recommended dosage and adjust as needed based on your personal response.\r\nAppearance: Capsules are designed for easy ingestion and discreet use, making them suitable for holiday celebrations.\r\nPackaging: Shipped in a secure, tamper-proof bottle to maintain freshness and product integrity. Discreet packaging ensures your privacy.\r\nHow to Use Kind Stranger Holiday Capsules\r\nRecommended Dosage: Start with one capsule to gauge your response. Adjust the dosage as needed to achieve the desired effects. Regular use during the holiday season can help you maintain a joyful and creative mindset.\r\n\r\nConsumption: Take the capsule with water or your preferred beverage. Integrate into your holiday routine and avoid combining with alcohol or other substances for optimal results.\r\n\r\nSafety and Precautions\r\nTo ensure a safe and enjoyable experience, follow these guidelines:\r\n\r\nConsultation: Consult with a healthcare professional if you have any pre-existing health conditions or are taking medication.\r\nStart Low, Go Slow: Begin with the recommended dosage and adjust based on your personal response to avoid overuse.\r\nAvoid Mixing: Do not combine with alcohol or other substances to prevent adverse interactions.\r\nLegal Considerations: Verify the legality of psilocybin use in your area before purchasing.\r\nWhy Choose Kind Stranger Holiday Capsules?\r\nPremium Quality: Made with high-quality psilocybin to ensure potency and effectiveness.\r\nReliable Microdosing: Provides a consistent and dependable experience with each capsule.\r\nConvenient and Discreet: Designed for easy daily use and discreet consumption.\r\nCustomer Support: Our team is available to assist with any questions and provide support throughout your holiday experience.\r\nOrder Your Kind Stranger Holiday Capsules Today\r\nAdd a touch of magic to your holiday season with Kind Stranger Holiday Capsules. With a precisely measured 125mg dose in each capsule, this product offers a practical and festive way to enhance your mood, creativity, and overall well-being. Order now and experience the joy and positive effects of a well-crafted microdosing regimen designed to make your holidays truly special.', '', 82.00, NULL, 'BUYKINDSTR-AZW7', 100, 0.00, '', 'product_1753972377_88730a0a.jpg', NULL, 1, 1, '', '', '2025-07-31 14:32:57', '2025-07-31 14:36:56'),
(28, 10, 'Buy Mastermind Magic Brain Tech Capsules For Sale USA', 'buy-mastermind-magic-brain-tech-capsules-for-sale-usa', 'Unlock the Power of Cognitive Enhancement with Mastermind Magic Brain Tech Capsules\r\nExperience a new frontier of mental clarity, focus, and cognitive enhancement with Mastermind Magic Brain Tech Capsules. Formulated with cutting-edge ingredients, these capsules are designed to support optimal brain function and mental performance. Whether you’re looking to improve your cognitive abilities, enhance your focus, or simply boost your overall mental well-being, Mastermind Magic Brain Tech Capsules offer a potent and effective solution.\r\n\r\nWhat Are Mastermind Magic Brain Tech Capsules?\r\nMastermind Magic Brain Tech Capsules are expertly crafted supplements designed to promote cognitive health and mental performance. Each capsule contains a blend of high-quality nootropic compounds and natural ingredients known for their brain-boosting properties. These capsules are formulated to support memory, concentration, mental clarity, and overall cognitive function, making them ideal for students, professionals, and anyone seeking to enhance their mental capabilities.\r\n\r\nKey Benefits\r\nEnhanced Cognitive Function: Supports improved memory, learning, and mental clarity to help you perform at your best.\r\nIncreased Focus and Concentration: Helps you stay focused and attentive, making it easier to tackle complex tasks and projects.\r\nBoosted Mental Energy: Provides a natural energy boost to keep your mind sharp and alert throughout the day.\r\nStress and Anxiety Reduction: Includes ingredients that help manage stress and reduce anxiety, promoting a balanced and calm state of mind.\r\nSupport for Overall Brain Health: Contains antioxidants and nutrients that support brain health and protect against cognitive decline.\r\nProduct Details\r\nIngredients: A potent blend of nootropic compounds, vitamins, minerals, and natural extracts known for their cognitive-enhancing properties. Specific ingredients may include Bacopa Monnieri, Rhodiola Rosea, Ginkgo Biloba, Lion’s Mane Mushroom, and other brain-boosting nutrients.\r\nDosage: Each bottle contains 60 capsules. The recommended dosage is typically 1-2 capsules per day, preferably with a meal. Adjustments can be made based on individual needs and responses.\r\nPackaging: Secure and discreet packaging to ensure the freshness and integrity of the product.\r\nHow to Use Mastermind Magic Brain Tech Capsules\r\nRecommended Dosage: Start with 1 capsule daily and assess your response. For enhanced effects, you may gradually increase to 2 capsules per day. It’s advisable to take the capsules with a meal to aid in absorption.\r\n\r\nConsumption: Simply swallow the capsule with a glass of water. Consistent daily use is recommended for optimal results.\r\n\r\nSafety and Precautions\r\nWhile Mastermind Magic Brain Tech Capsules are formulated for safety and effectiveness, it’s essential to use them responsibly:\r\n\r\nConsult a Healthcare Professional: If you have any pre-existing medical conditions or are taking other medications, consult with a healthcare professional before starting any new supplement.\r\nFollow Dosage Instructions: Do not exceed the recommended dosage to avoid potential side effects.\r\nMonitor Your Response: Pay attention to how your body responds to the capsules and adjust your dosage as needed.\r\nKeep Out of Reach of Children: Store in a safe place, away from children and pets.\r\nWhy Choose Mastermind?\r\nPremium Quality: We use only the highest quality ingredients to ensure a potent and effective product.\r\nScientific Formulation: Our capsules are formulated based on the latest research and scientific advancements in cognitive enhancement.\r\nCustomer Support: Our dedicated team is available to answer any questions and provide guidance on using Mastermind Magic Brain Tech Capsules.\r\nDiscreet Shipping: We prioritize your privacy with secure, discreet packaging and reliable shipping methods.\r\nOrder Your Mastermind Magic Brain Tech Capsules Today\r\nUnlock your full cognitive potential with Mastermind Magic Brain Tech Capsules. Whether you aim to enhance your mental performance, improve focus, or support overall brain health, our capsules provide a powerful and effective solution. Order now and experience the benefits of cutting-edge brain technology in every capsule.', '', 85.00, NULL, 'BUYMASTERM-OTVG', 100, 0.00, '', 'product_1753972447_b33d0813.jpg', NULL, 0, 1, '', '', '2025-07-31 14:34:07', '2025-07-31 14:34:07'),
(29, 10, 'Buy Mastermind New Mood Microdose Magic Mushrooms Capsules', 'buy-mastermind-new-mood-microdose-magic-mushrooms-capsules', 'Elevate Your Mood with Mastermind New Mood Microdose Capsules\r\nWelcome to the future of mental wellness with Mastermind New Mood Microdose Magic Mushroom Capsules. Designed to enhance your day-to-day life subtly and effectively, these capsules offer a unique way to experience the benefits of psilocybin in a controlled and convenient form. Whether you are looking to boost your creativity, alleviate anxiety, or improve your overall mood, Mastermind New Mood Microdose Capsules are your go-to solution.\r\n\r\nWhat are Mastermind New Mood Microdose Capsules?\r\nMastermind New Mood Microdose Capsules are carefully formulated with a precise amount of psilocybin, the active compound found in magic mushrooms. Each capsule contains a low, non-psychedelic dose designed to deliver the therapeutic benefits of psilocybin without the intense hallucinogenic effects. This allows you to integrate the power of magic mushrooms into your daily routine seamlessly and responsibly.\r\n\r\nKey Benefits\r\nMood Enhancement: These capsules are specifically designed to promote a positive mood and emotional balance, helping you navigate daily stresses with ease.\r\nIncreased Focus and Productivity: Experience heightened mental clarity and enhanced focus, making it easier to tackle tasks and achieve your goals.\r\nReduced Anxiety and Depression: Emerging studies suggest that psilocybin can help reduce symptoms of anxiety and depression, providing a natural alternative to traditional medications.\r\nBoosted Creativity: Unlock new levels of creativity and problem-solving abilities, perfect for artists, writers, and professionals seeking innovative solutions.\r\nConvenient and Discreet: The capsule form makes it easy to incorporate microdosing into your lifestyle without the need for preparation or the risk of unwanted attention.\r\nProduct Details\r\nFormulation: Each capsule contains a precise microdose of psilocybin.\r\nDosage: Typically, users start with one capsule every two to three days. Adjustments can be made based on individual response and desired effects.\r\nIngredients: 100% pure psilocybin extract, combined with natural excipients for optimal absorption.\r\nPackaging: Secure, discreet packaging ensures your privacy and the freshness of the product.\r\nHow to Use Mastermind New Mood Microdose Capsules\r\nRecommended Dosage: Begin with one capsule every two to three days. Gradually adjust the frequency and dosage based on your body’s response and your personal goals. It is recommended to follow a microdosing schedule, taking breaks to avoid building tolerance.\r\n\r\nConsumption: Simply swallow the capsule with water, just like any other supplement. There is no need for preparation, making it easy to incorporate into your morning or evening routine.\r\n\r\nSafety and Precautions\r\nWhile Mastermind New Mood Microdose Capsules are designed to be safe and effective, it is essential to approach microdosing with mindfulness and caution. Keep the following points in mind:\r\n\r\nConsult a Professional: If you have any pre-existing medical conditions or are taking other medications, consult with a healthcare professional before starting microdosing.\r\nStart Low and Go Slow: Begin with the lowest effective dose to gauge your sensitivity and response to psilocybin.\r\nAvoid Mixing with Substances: Do not mix with alcohol or other recreational drugs to prevent adverse interactions.\r\nKeep Out of Reach of Children: Store in a safe place, away from children and pets.\r\nWhy Choose Mastermind?\r\nQuality Assurance: Our products are made with the highest quality psilocybin extracts, ensuring purity, potency, and consistency.\r\nResearch-Backed: We base our formulations on the latest scientific research to provide you with safe and effective microdosing solutions.\r\nCustomer Support: Our dedicated customer service team is available to answer any questions and provide guidance on your microdosing journey.\r\nDiscreet Shipping: We prioritize your privacy with secure, discreet packaging and reliable shipping methods.\r\nOrder Your Mastermind New Mood Microdose Capsules Today\r\nUnlock the potential of microdosing with Mastermind New Mood Microdose Magic Mushroom Capsules. Enhance your mood, boost your creativity, and improve your overall well-being with the power of psilocybin. Order now and take the first step towards a more balanced, productive, and joyful life.', '', 85.00, NULL, 'BUYMASTERM-L4RA', 100, 0.00, '', 'product_1753972517_2dbb8ed3.jpg', NULL, 1, 1, '', '', '2025-07-31 14:35:17', '2025-07-31 14:36:58');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `short_description`, `price`, `sale_price`, `sku`, `stock_quantity`, `weight`, `dimensions`, `image`, `gallery`, `is_featured`, `is_active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(30, 10, 'Buy Neuro Botanicals Brain Formula Microdose Capsules', 'buy-neuro-botanicals-brain-formula-microdose-capsules', 'Unlock Peak Cognitive Performance with Neuro Botanicals Brain Formula Microdose Capsules\r\nElevate your cognitive abilities and mental performance with Neuro Botanicals Brain Formula Microdose Capsules. Expertly crafted to support optimal brain function, these capsules offer a carefully measured dose of psilocybin to enhance focus, mental clarity, and overall cognitive health. Ideal for professionals, students, and anyone seeking to improve their mental agility, Neuro Botanicals Brain Formula provides a potent, yet subtle, microdosing solution for achieving peak cognitive performance.\r\n\r\nWhat Are Neuro Botanicals Brain Formula Microdose Capsules?\r\nNeuro Botanicals Brain Formula Microdose Capsules are designed to deliver a precise and controlled amount of psilocybin, a naturally occurring compound in magic mushrooms known for its cognitive-enhancing properties. Each capsule is formulated to support brain function, including improved focus, memory, and creative thinking, without the overwhelming effects of full doses. This makes them a perfect choice for those looking to incorporate microdosing into their daily routine for enhanced mental capabilities.\r\n\r\nKey Benefits of Neuro Botanicals Brain Formula Microdose Capsules\r\nEnhanced Focus: Improves concentration and attention, allowing you to stay engaged and productive throughout the day.\r\nImproved Mental Clarity: Supports sharper thinking and clearer decision-making, boosting overall cognitive function.\r\nBoosted Memory: Aids in memory retention and recall, enhancing learning and information processing.\r\nIncreased Creativity: Stimulates creative thinking and problem-solving skills, making it ideal for innovative tasks and projects.\r\nBalanced Microdosing: Provides a consistent and reliable dose of psilocybin for effective cognitive enhancement.\r\nConvenient and Discreet: Easy-to-use capsules that integrate seamlessly into your daily routine, with a discreet design for privacy.\r\nProduct Details\r\nDosage: Each capsule contains a precise dose of psilocybin, carefully measured to ensure consistent effects. Start with the recommended dosage and adjust based on your personal response.\r\nAppearance: Capsules are designed for easy ingestion and discreet use, suitable for daily consumption.\r\nPackaging: Shipped in a secure, tamper-proof bottle to maintain product integrity and freshness. Discreet packaging ensures privacy.\r\nHow to Use Neuro Botanicals Brain Formula Microdose Capsules\r\nRecommended Dosage: Begin with one capsule and monitor your response. Adjust the dosage as needed to achieve the desired cognitive benefits. Consistent use is key to experiencing the full advantages of microdosing.\r\n\r\nConsumption: Take the capsule with water or your preferred beverage. Incorporate into your daily routine and avoid combining with alcohol or other substances for optimal results.\r\n\r\nSafety and Precautions\r\nTo ensure a safe and beneficial microdosing experience, follow these guidelines:\r\n\r\nConsultation: Consult with a healthcare professional if you have any pre-existing health conditions or are taking medication.\r\nStart Low, Go Slow: Begin with the recommended dosage and adjust based on your personal response to prevent potential overuse.\r\nAvoid Mixing: Do not combine with alcohol or other substances to avoid adverse interactions.\r\nLegal Considerations: Verify the legality of psilocybin use in your area before purchasing.\r\nWhy Choose Neuro Botanicals Brain Formula Microdose Capsules?\r\nPremium Quality: Made with high-quality psilocybin to ensure potency and effectiveness.\r\nReliable Microdosing: Provides a consistent and dependable microdosing experience with each capsule.\r\nConvenient and Discreet: Designed for easy daily use and discreet consumption.\r\nCustomer Support: Our team is available to assist with any questions and provide support throughout your microdosing journey.\r\nOrder Your Neuro Botanicals Brain Formula Microdose Capsules Today\r\nExperience the transformative effects of Neuro Botanicals Brain Formula Microdose Capsules and unlock your cognitive potential. With a meticulously measured dose in each capsule, this product offers a practical and effective solution for enhancing focus, mental clarity, and overall brain function. Order now to start benefiting from a microdosing regimen designed to elevate every aspect of your cognitive performance.', '', 25.00, NULL, 'BUYNEUROBO-6VW8', 100, 0.00, '', 'product_1753972591_9665f029.jpg', NULL, 0, 1, '', '', '2025-07-31 14:36:31', '2025-07-31 14:36:31'),
(31, 10, 'Buy Neuro Botanicals Focus Magic Mushroom Microdose Capsules', 'buy-neuro-botanicals-focus-magic-mushroom-microdose-capsules', 'Enhance Your Cognitive Performance with Neuro Botanicals Focus Magic Mushroom Microdose Capsules\r\nUnlock the power of microdosing with Neuro Botanicals Focus Magic Mushroom Microdose Capsules, specially formulated to boost your mental clarity, focus, and overall cognitive performance. Each capsule contains a carefully measured dose of psilocybin, designed to enhance your ability to concentrate and improve your daily productivity without overwhelming effects. Ideal for professionals, students, and anyone looking to optimize their mental function, Neuro Botanicals Focus provides a potent yet subtle microdosing solution.\r\n\r\nWhat Are Neuro Botanicals Focus Magic Mushroom Microdose Capsules?\r\nNeuro Botanicals Focus Magic Mushroom Microdose Capsules are crafted to deliver a precise and effective dose of psilocybin, a naturally occurring compound found in magic mushrooms. Known for its cognitive-enhancing properties, psilocybin has been shown to support mental clarity, improve focus, and foster creativity. These capsules are designed for individuals seeking to harness these benefits in a manageable and controlled format, making microdosing accessible and practical for daily use.\r\n\r\nKey Benefits of Neuro Botanicals Focus Magic Mushroom Microdose Capsules\r\nImproved Focus: Enhances concentration and attention, helping you stay on task and perform better in both personal and professional settings.\r\nEnhanced Mental Clarity: Boosts cognitive function and problem-solving abilities, allowing for sharper thinking and more efficient decision-making.\r\nIncreased Creativity: Stimulates innovative thinking and creative ideas, making it ideal for creative professionals and those in need of inspiration.\r\nEmotional Balance: Supports emotional well-being and resilience, contributing to a more positive and stable mood.\r\nControlled Microdosing: Each capsule contains a precise dose of psilocybin, ensuring a consistent and reliable microdosing experience.\r\nConvenient and Discreet: Easy-to-use capsules that seamlessly fit into your daily routine, with a discreet design for privacy.\r\nProduct Details\r\nDosage: Each capsule contains a carefully measured dose of psilocybin. Start with one capsule to gauge your sensitivity and adjust as needed.\r\nAppearance: Capsules are designed for easy ingestion and discreet use, making them suitable for daily consumption.\r\nPackaging: Shipped in a secure, tamper-proof bottle to maintain product integrity and freshness. Discreet packaging ensures privacy.\r\nHow to Use Neuro Botanicals Focus Magic Mushroom Microdose Capsules\r\nRecommended Dosage: Begin with one capsule and monitor your response. Adjust the dosage based on your individual sensitivity and desired effects. Regular, consistent use is key to experiencing the full benefits of microdosing.\r\n\r\nConsumption: Take the capsule with water or your preferred beverage. Integrate into your daily routine and avoid combining with alcohol or other substances for optimal results.\r\n\r\nSafety and Precautions\r\nTo ensure a safe and beneficial microdosing experience, adhere to the following guidelines:\r\n\r\nConsultation: Consult with a healthcare professional if you have any pre-existing health conditions or are taking medication.\r\nStart Low, Go Slow: Begin with the recommended dosage and adjust based on your personal response to prevent potential overuse.\r\nAvoid Mixing: Do not combine with alcohol or other substances to avoid adverse interactions.\r\nLegal Considerations: Verify the legality of psilocybin use in your area before purchasing.\r\nWhy Choose Neuro Botanicals Focus Magic Mushroom Microdose Capsules?\r\nPremium Quality: Made with high-quality psilocybin to ensure potency and effectiveness.\r\nReliable Microdosing: Provides a consistent and dependable microdosing experience with each capsule.\r\nConvenient and Discreet: Capsules are designed for easy daily use and discreet consumption.\r\nCustomer Support: Our team is available to assist with any questions and provide support throughout your microdosing journey.\r\nOrder Your Neuro Botanicals Focus Magic Mushroom Microdose Capsules Today\r\nElevate your cognitive function and enhance your daily productivity with Neuro Botanicals Focus Magic Mushroom Microdose Capsules. With a carefully measured dose in each capsule, this product offers a practical and effective solution for improving mental clarity, focus, and overall well-being. Order now to experience the benefits of a meticulously crafted microdosing regimen designed to optimize every aspect of your life.', '', 25.00, NULL, 'BUYNEUROBO-72VZ', 100, 0.00, '', 'product_1753972715_901db31b.jpg', NULL, 0, 1, '', '', '2025-07-31 14:38:35', '2025-07-31 14:38:35'),
(32, 10, 'Buy Psilocybin Microdose Capsules (4500mg) – Mikro Dose', 'buy-psilocybin-microdose-capsules-4500mg-mikro-dose', 'Unlock Your Potential with Mikro Dose Psilocybin Microdose Capsules (4500mg)\r\nDiscover the benefits of microdosing with Mikro Dose Psilocybin Microdose Capsules. Expertly formulated to provide a consistent and precise dose, these capsules are designed to help you unlock your full potential. With a total of 4500mg of high-quality psilocybin, Mikro Dose capsules offer a convenient, effective, and discreet way to enhance your daily life, creativity, and mental well-being.\r\n\r\nWhat are Mikro Dose Psilocybin Microdose Capsules?\r\nFormulation: Each capsule contains a carefully measured amount of psilocybin, ensuring a consistent microdosing experience.\r\nQuality: Made with premium, lab-tested psilocybin extract to guarantee purity and potency.\r\nConvenience: Capsules provide an easy and discreet way to incorporate microdosing into your routine without the taste or preparation of traditional mushrooms.\r\nKey Features:\r\nPrecise Dosage: Each capsule contains an exact dose of psilocybin, allowing for accurate and consistent microdosing.\r\nEnhanced Creativity and Focus: Microdosing with psilocybin can boost creativity, improve focus, and increase productivity, making it ideal for work or artistic endeavors.\r\nImproved Mood and Emotional Balance: Psilocybin microdosing has been reported to enhance mood, reduce symptoms of anxiety and depression, and promote emotional stability.\r\nCognitive Benefits: Experience improved problem-solving abilities, mental clarity, and overall cognitive function.\r\nMinimal Psychoactive Effects: Microdosing provides subtle benefits without the intense psychoactive effects associated with higher doses of psilocybin.\r\nDiscreet and Convenient: Capsules are easy to take and can be integrated seamlessly into your daily routine, providing a discreet way to microdose.\r\nHow to Use:\r\nStart with a Low Dose: Begin with one capsule to assess your sensitivity and response. Gradually adjust the dosage to find what works best for you.\r\nConsistent Schedule: Follow a consistent microdosing schedule, such as taking a capsule every other day, to maximize benefits.\r\nStay Hydrated: Drink plenty of water throughout the day to support your overall well-being.\r\nTrack Your Progress: Keep a journal to note the effects and benefits you experience, helping you fine-tune your microdosing routine.\r\nDosage Guidelines:\r\nMicrodose (0.1g to 0.5g psilocybin per capsule): Subtle effects that enhance mood, creativity, focus, and emotional balance without strong psychoactive effects.\r\nModerate Dose: If needed, increase the number of capsules gradually to achieve desired benefits while maintaining minimal psychoactive effects.\r\nSafety and Legal Information:\r\nLegal Status: Ensure you are aware of and comply with the legal status of psilocybin in your area before purchasing. Adherence to local laws is crucial.\r\nIntended Use: This product is intended for adult use only. Consult a healthcare professional if you have any health concerns or conditions.\r\nProper Storage: Store in a cool, dry place away from direct sunlight and moisture to maintain freshness and potency.\r\nWhy Choose Mikro Dose Psilocybin Microdose Capsules?\r\nMikro Dose Psilocybin Microdose Capsules (4500mg) offer a practical and effective way to experience the numerous benefits of psilocybin microdosing. Whether you seek enhanced creativity, improved mood, increased focus, or overall cognitive improvement, these capsules provide a reliable and consistent solution. Their precise dosing, high-quality ingredients, and convenient form make them an excellent choice for anyone looking to integrate microdosing into their lifestyle.\r\n\r\nOrder your Mikro Dose Psilocybin Microdose Capsules (4500mg) online today and start your journey towards enhanced well-being and peak performance!', '', 40.00, NULL, 'BUYPSILOCY-2LI0', 100, 0.00, '', 'product_1753972764_e7ae23b6.jpg', NULL, 0, 1, '', '', '2025-07-31 14:39:24', '2025-07-31 14:39:24'),
(33, 10, 'Buy Shroomies Microdose Psilocybin Capsules (3000mg)', 'buy-shroomies-microdose-psilocybin-capsules-3000mg', 'Unlock Your Potential with Shroomies Microdose Psilocybin Capsules\r\nDiscover the benefits of microdosing with Shroomies Microdose Psilocybin Capsules, designed to enhance your mental clarity, creativity, and emotional well-being. Each capsule contains a potent 3000mg of psilocybin, offering a carefully measured dose to help you unlock your full potential without the overwhelming effects of a full psychedelic experience. Perfect for daily use, Shroomies provides a convenient and consistent microdosing solution to support a balanced and productive lifestyle.\r\n\r\nWhat Are Shroomies Microdose Psilocybin Capsules?\r\nShroomies Microdose Psilocybin Capsules are specially formulated to deliver a precise 3000mg dose of psilocybin per capsule. Psilocybin is a naturally occurring psychedelic compound found in certain mushrooms, known for its ability to enhance cognitive function, stimulate creativity, and support emotional balance. These capsules are designed for those seeking the subtle benefits of psilocybin in a controlled and manageable format, making microdosing accessible and easy to incorporate into your daily routine.\r\n\r\nKey Benefits of Shroomies Microdose Psilocybin Capsules\r\nImproved Mental Clarity: Enhances focus, concentration, and problem-solving abilities, helping you perform at your best.\r\nBoosted Creativity: Stimulates creative thinking and innovative ideas, ideal for artists, writers, and professionals.\r\nEmotional Balance: Supports emotional stability and resilience, promoting a positive outlook and effective stress management.\r\nIncreased Energy: Provides a natural boost in energy and motivation, improving productivity and overall well-being.\r\nControlled Microdosing: Each capsule contains 3000mg of psilocybin, offering a precise and consistent microdosing experience.\r\nConvenient and Discreet: Easy-to-use capsules that fit seamlessly into your daily routine, with a discreet design for privacy.\r\nProduct Details\r\nDosage: Each capsule contains 3000mg of psilocybin. Start with one capsule and adjust the dosage based on personal response and desired effects.\r\nAppearance: Capsules are designed for easy ingestion and discreet use, making them ideal for daily consumption.\r\nPackaging: Shipped in a secure, tamper-proof bottle to ensure freshness and potency. Packaging is discreet to maintain privacy.\r\nHow to Use Shroomies Microdose Psilocybin Capsules\r\nRecommended Dosage: Begin with one 3000mg capsule to gauge your sensitivity. Adjust the dosage as needed based on your response and the effects you desire. Consistent use is key to achieving the benefits of microdosing.\r\n\r\nConsumption: Take the capsule with water or your preferred beverage. For best results, incorporate into your daily routine and avoid combining with alcohol or other substances.\r\n\r\nSafety and Precautions\r\nFor a safe and effective microdosing experience, adhere to the following guidelines:\r\n\r\nConsultation: If you have any health conditions or are taking medication, consult with a healthcare professional before starting microdosing.\r\nStart Low, Go Slow: Begin with the recommended dosage and adjust based on your personal response to avoid potential overuse.\r\nAvoid Mixing: Do not combine with alcohol or other substances to prevent adverse interactions.\r\nLegal Considerations: Ensure that psilocybin use is legal in your area before purchasing.\r\nWhy Choose Shroomies Microdose Psilocybin Capsules?\r\nHigh-Quality Psilocybin: Made with premium-grade psilocybin to ensure potency and consistency.\r\nReliable Microdosing: Provides a dependable and controlled microdosing experience with each capsule.\r\nConvenient and Discreet: Easy-to-use capsules designed for daily consumption and discreet use.\r\nCustomer Support: Our team is available to assist with any questions and provide support for your microdosing journey.\r\nOrder Your Shroomies Microdose Psilocybin Capsules Today\r\nEnhance your daily life and performance with Shroomies Microdose Psilocybin Capsules. With a precise 3000mg dosage in each capsule, this product offers a powerful and convenient microdosing solution to support mental clarity, creativity, and emotional well-being. Order now to experience the benefits of a well-crafted microdosing regimen designed to elevate your everyday life.', '', 45.00, NULL, 'BUYSHROOMI-2G89', 100, 0.00, '', 'product_1753972839_ba4c039b.jpg', NULL, 1, 1, '', '', '2025-07-31 14:40:39', '2025-07-31 14:45:48'),
(34, 10, 'Shop Alice Micro Dose Capsules Scooby Snacks (6000mg)', 'shop-alice-micro-dose-capsules-scooby-snacks-6000mg', 'Elevate Your Everyday with Alice Micro Dose Capsules Scooby Snacks\r\nIntroducing Alice Micro Dose Capsules Scooby Snacks—an advanced microdosing solution designed to enhance your daily life with precision and ease. Each capsule is meticulously formulated with a potent 6000mg dosage of psilocybin, providing a powerful and controlled experience that helps you unlock new levels of mental clarity, creativity, and emotional balance. Perfect for both seasoned users and newcomers, these capsules offer a convenient way to integrate the benefits of microdosing into your routine.\r\n\r\nWhat Are Alice Micro Dose Capsules Scooby Snacks?\r\nAlice Micro Dose Capsules Scooby Snacks are a premium-grade microdosing product featuring a substantial 6000mg of psilocybin per dose. This high-potency formulation is designed to deliver a subtle yet effective boost to your cognitive and emotional state, facilitating enhanced focus, creativity, and overall well-being. Each capsule is carefully crafted to ensure consistent potency and a reliable microdosing experience, making it an ideal choice for those looking to optimize their mental and emotional performance.\r\n\r\nKey Benefits of Alice Micro Dose Capsules Scooby Snacks\r\nEnhanced Cognitive Function: Improves mental clarity, focus, and problem-solving skills for better daily performance.\r\nBoosted Creativity: Stimulates innovative thinking and creative expression, ideal for artists, writers, and entrepreneurs.\r\nEmotional Balance: Supports emotional stability and resilience, helping to manage stress and maintain a positive outlook.\r\nIncreased Energy: Provides a natural boost in energy and motivation, enhancing productivity and overall well-being.\r\nControlled Microdosing: Offers a precise 6000mg dosage for a consistent and effective microdosing experience.\r\nProduct Details\r\nDosage: Each capsule contains 6000mg of psilocybin, providing a potent microdose for enhanced cognitive and emotional effects. Adjust your intake based on personal tolerance and desired results.\r\nAppearance: Capsules are designed for easy consumption and discreet use, making them a convenient addition to your daily routine.\r\nPackaging: Shipped in secure, tamper-proof containers to ensure the freshness and potency of the product. Discreet packaging maintains privacy and protection.\r\nHow to Use Alice Micro Dose Capsules Scooby Snacks\r\nRecommended Dosage: Start with one capsule to gauge your response. Adjust the dosage as needed based on individual sensitivity and the effects experienced. Higher doses can be used for more pronounced results.\r\n\r\nConsumption: Take the capsule with water or your preferred beverage. For best results, incorporate into your daily routine and avoid combining with alcohol or other substances.\r\n\r\nSafety and Precautions\r\nFor a safe and enjoyable experience, adhere to the following guidelines:\r\n\r\nConsultation: If you have any health conditions or are on medication, consult with a healthcare professional before starting microdosing.\r\nStart Low, Go Slow: Begin with the recommended dosage and adjust according to your personal response.\r\nAvoid Mixing: Do not combine with alcohol or other substances to prevent potential interactions.\r\nLegal Considerations: Ensure that psilocybin use is legal in your area before purchasing.\r\nWhy Choose Alice Micro Dose Capsules Scooby Snacks?\r\nHigh-Quality Ingredients: Made with premium psilocybin to ensure purity and effectiveness.\r\nConsistent Results: Each capsule provides a reliable and consistent microdosing experience.\r\nConvenient and Discreet: Easy-to-use capsules that fit seamlessly into your daily routine.\r\nCustomer Support: Our team is dedicated to providing assistance and guidance to enhance your microdosing journey.\r\nShop Alice Micro Dose Capsules Scooby Snacks Today\r\nEnhance your daily performance and well-being with Alice Micro Dose Capsules Scooby Snacks. Whether you’re looking to boost creativity, improve focus, or achieve emotional balance, our 6000mg capsules offer a potent and controlled solution for your microdosing needs. Order now and experience the benefits of a finely-tuned microdosing regimen designed to elevate every aspect of your life.', '', 80.00, NULL, 'SHOPALICEM-Z1OC', 100, 0.00, '', 'product_1753972915_b984903c.jpg', NULL, 0, 1, '', '', '2025-07-31 14:41:55', '2025-07-31 14:41:55'),
(35, 10, 'Shop Neuro Botanicals Calm Microdose Mushroom Capsules', 'shop-neuro-botanicals-calm-microdose-mushroom-capsules', 'Discover Tranquility with Neuro Botanicals Calm Microdose Mushroom Capsules\r\nEmbrace a new level of relaxation and emotional stability with Neuro Botanicals Calm Microdose Mushroom Capsules. Specially formulated to provide a gentle, controlled dose of psilocybin, these capsules are designed to help you achieve a state of calm and balance in your daily life. Ideal for those seeking to manage stress, reduce anxiety, and foster emotional well-being, Neuro Botanicals Calm offers a convenient and effective solution for achieving inner peace and stability.\r\n\r\nWhat Are Neuro Botanicals Calm Microdose Mushroom Capsules?\r\nNeuro Botanicals Calm Microdose Mushroom Capsules contain a precisely measured amount of psilocybin, the active compound found in magic mushrooms known for its calming and mood-enhancing effects. Each capsule is crafted to deliver a consistent microdosing experience, promoting relaxation and emotional balance without the intense effects of full doses. Whether you’re looking to reduce stress, improve your mood, or simply maintain a sense of calm throughout the day, these capsules offer a practical and effective way to integrate microdosing into your routine.\r\n\r\nKey Benefits of Neuro Botanicals Calm Microdose Mushroom Capsules\r\nStress Reduction: Helps alleviate stress and promote a sense of relaxation, making it easier to navigate daily challenges.\r\nEmotional Stability: Supports emotional balance and resilience, contributing to a more positive and stable mood.\r\nAnxiety Management: Assists in managing anxiety and creating a calm, centered mental state.\r\nEnhanced Well-Being: Fosters a general sense of well-being and contentment.\r\nControlled Microdosing: Provides a precise and reliable dose of psilocybin for consistent results.\r\nConvenient and Discreet: Easy-to-use capsules that fit seamlessly into your daily routine, with a discreet design for privacy.\r\nProduct Details\r\nDosage: Each capsule contains a carefully measured dose of psilocybin. Start with the recommended dosage and adjust based on your personal response.\r\nAppearance: Capsules are designed for easy ingestion and discreet use, ideal for daily consumption.\r\nPackaging: Shipped in a secure, tamper-proof bottle to ensure freshness and maintain product integrity. Discreet packaging ensures your privacy.\r\nHow to Use Neuro Botanicals Calm Microdose Mushroom Capsules\r\nRecommended Dosage: Begin with one capsule and observe your response. Adjust the dosage as needed to achieve the desired effects. Consistent use is key to experiencing the full benefits of microdosing.\r\n\r\nConsumption: Take the capsule with water or your preferred beverage. Integrate into your daily routine and avoid combining with alcohol or other substances for optimal results.\r\n\r\nSafety and Precautions\r\nTo ensure a safe and beneficial microdosing experience, follow these guidelines:\r\n\r\nConsultation: Consult with a healthcare professional if you have any pre-existing health conditions or are taking medication.\r\nStart Low, Go Slow: Begin with the recommended dosage and adjust based on your personal response to prevent potential overuse.\r\nAvoid Mixing: Do not combine with alcohol or other substances to avoid adverse interactions.\r\nLegal Considerations: Verify the legality of psilocybin use in your area before purchasing.\r\nWhy Choose Neuro Botanicals Calm Microdose Mushroom Capsules?\r\nPremium Quality: Made with high-quality psilocybin to ensure potency and effectiveness.\r\nReliable Microdosing: Offers a consistent and dependable microdosing experience with each capsule.\r\nConvenient and Discreet: Designed for easy daily use and discreet consumption.\r\nCustomer Support: Our team is available to assist with any questions and provide guidance throughout your microdosing journey.\r\nShop Your Neuro Botanicals Calm Microdose Mushroom Capsules Today\r\nAchieve a greater sense of tranquility and emotional balance with Neuro Botanicals Calm Microdose Mushroom Capsules. With a carefully measured dose in each capsule, this product provides a practical and effective solution for managing stress, anxiety, and overall well-being. Order now and experience the calming benefits of a meticulously crafted microdosing regimen designed to enhance every aspect of your life.', '', 25.00, NULL, 'SHOPNEUROB-SEWZ', 100, 0.00, '', 'product_1753972989_4c81688d.jpg', NULL, 0, 1, '', '', '2025-07-31 14:43:09', '2025-07-31 14:43:09'),
(36, 11, 'Buy Mastermind Dark Chocolate Bar (1500mg)', 'buy-mastermind-dark-chocolate-bar-1500mg', 'Indulge in a Luxurious Psychedelic Experience with Mastermind Dark Chocolate Bars\r\nDiscover the perfect blend of indulgence and enlightenment with Mastermind Dark Chocolate Bars. Each bar is infused with 1500mg of premium psilocybin mushrooms, providing a delightful and potent experience wrapped in the rich, deep flavors of dark chocolate. Whether you’re seeking a profound psychedelic journey or a gentle introduction to magic mushrooms, our dark chocolate bars offer a delicious and sophisticated way to enjoy the benefits of psilocybin.\r\n\r\nWhy Choose Mastermind Dark Chocolate Bars?\r\nMastermind Dark Chocolate Bars are meticulously crafted to deliver a consistent, enjoyable, and potent experience. The combination of high-quality dark chocolate and precisely measured psilocybin mushrooms ensures you get the best of both worlds – a treat for your taste buds and a journey for your mind.\r\n\r\nKey Benefits\r\nRich, Decadent Flavor: Our dark chocolate is crafted from the finest cocoa beans, offering a luxurious, bittersweet taste that perfectly complements the psilocybin infusion.\r\nPrecise Dosage: Each bar contains 1500mg of psilocybin, segmented into easily breakable squares, allowing for accurate dosing and a controlled experience.\r\nEnhanced Mood and Creativity: Psilocybin is known to elevate mood, boost creativity, and promote a sense of well-being, making it a favorite among artists, writers, and professionals.\r\nTherapeutic Potential: Emerging research highlights the potential of psilocybin to alleviate symptoms of depression, anxiety, PTSD, and other mental health conditions.\r\nConvenient and Discreet: The bar form makes it easy to carry and consume discreetly, whether at home or on the go.\r\nProduct Details\r\nIngredients: Premium dark chocolate, psilocybin mushrooms (1500mg per bar), natural flavorings, and emulsifiers for a smooth texture.\r\nDosage: Each bar is divided into segments for precise dosing. Beginners are advised to start with one square (approximately 150mg of psilocybin) and adjust based on their experience and tolerance.\r\nPackaging: Secure, discreet packaging ensures your privacy and the freshness of the product.\r\nHow to Use Mastermind Dark Chocolate Bars\r\nRecommended Dosage: Start with a low dose, such as one square, and wait 1-2 hours to gauge the effects before consuming more. Adjust the dosage based on your desired experience and tolerance level.\r\n\r\nConsumption: Simply break off a square and savor the rich, dark chocolate. It can be enjoyed as is or melted into a warm drink for a soothing experience.\r\n\r\nSafety and Precautions\r\nWhile Mastermind Dark Chocolate Bars are designed to offer a safe and enjoyable experience, it’s important to use them responsibly:\r\n\r\nStart Low, Go Slow: Begin with a low dose to assess your tolerance and sensitivity to psilocybin.\r\nSafe Environment: Ensure you are in a comfortable, safe environment, especially if you are new to psilocybin.\r\nAvoid Mixing: Do not mix with alcohol or other substances to prevent adverse reactions.\r\nMedical Consultation: If you have pre-existing medical conditions or are taking other medications, consult a healthcare professional before use.\r\nKeep Out of Reach of Children: Store in a safe place, away from children and pets.\r\nWhy Choose Mastermind?\r\nPremium Quality: We use only the finest ingredients to ensure a safe, potent, and enjoyable product.\r\nConsistency: Each bar is carefully crafted to provide a consistent dosage and reliable experience.\r\nCustomer Support: Our dedicated team is here to answer any questions and provide guidance on your psilocybin journey.\r\nDiscreet Shipping: We prioritize your privacy with secure, discreet packaging and reliable shipping methods.\r\nOrder Your Mastermind Dark Chocolate Bars Today\r\nExperience the ultimate combination of luxury and enlightenment with Mastermind Dark Chocolate Bars. Whether you’re looking to explore the benefits of psilocybin for personal growth, therapeutic purposes, or simply to enhance your mood and creativity, our dark chocolate bars offer a delicious and effective solution. Order now and indulge in a rich, decadent treat that also nourishes your mind and spirit.', '', 25.00, NULL, 'BUYMASTERM-UHD7', 100, 0.00, '', 'product_1753973134_d23f1d7f.jpg', NULL, 1, 1, '', '', '2025-07-31 14:45:34', '2025-07-31 14:45:51'),
(37, 11, 'Buy Mastermind Milk Chocolate Funghi Bars (1500mg)', 'buy-mastermind-milk-chocolate-funghi-bars-1500mg', 'Indulge in a Magical Experience with Mastermind Milk Chocolate Funghi Bars\r\nElevate your senses and embark on a delightful journey with Mastermind Milk Chocolate Funghi Bars. Each bar is infused with 1500mg of premium psilocybin mushrooms, artfully blended with rich, creamy milk chocolate. Whether you’re seeking a profound psychedelic experience or a gentle introduction to magic mushrooms, our Funghi Bars offer a delicious and accessible way to enjoy the benefits of psilocybin.\r\n\r\nWhat Makes Mastermind Milk Chocolate Funghi Bars Special?\r\nMastermind Milk Chocolate Funghi Bars are crafted with the finest ingredients to ensure a pleasurable and potent experience. The combination of high-quality milk chocolate and carefully measured psilocybin mushrooms delivers a consistent dose and an enjoyable taste, making it an ideal choice for both experienced users and newcomers.\r\n\r\nKey Benefits\r\nDelicious Taste: Enjoy the rich, creamy flavor of premium milk chocolate, making the experience of consuming psilocybin mushrooms more enjoyable and palatable.\r\nPrecise Dosage: Each bar contains 1500mg of psilocybin, divided into easily breakable squares, allowing you to control your dosage with precision.\r\nEnhanced Mood and Creativity: Psilocybin is known to boost mood, enhance creativity, and promote a sense of well-being, making it a popular choice for those seeking personal growth and mental clarity.\r\nTherapeutic Effects: Emerging research suggests that psilocybin can help alleviate symptoms of depression, anxiety, and PTSD, offering a natural alternative to traditional treatments.\r\nConvenient and Discreet: Our Funghi Bars are easy to carry and consume discreetly, whether you’re at home or on the go.\r\nProduct Details\r\nIngredients: Premium milk chocolate, psilocybin mushrooms (1500mg per bar), natural flavorings, and emulsifiers for a smooth texture.\r\nDosage: Each bar is segmented into squares for easy dosing. Beginners are advised to start with one square (approximately 150mg of psilocybin) and gradually increase the dosage based on their experience and tolerance.\r\nPackaging: Secure, discreet packaging to ensure freshness and privacy.\r\nHow to Use Mastermind Milk Chocolate Funghi Bars\r\nRecommended Dosage: Start with a low dose, such as one square, and wait for 1-2 hours to gauge the effects before consuming more. Adjust the dosage based on your desired experience and tolerance level.\r\n\r\nConsumption: Simply break off a square and enjoy the delicious milk chocolate. It can be consumed as is or melted into a warm drink for a soothing experience.\r\n\r\nSafety and Precautions\r\nWhile Mastermind Milk Chocolate Funghi Bars are designed for a safe and enjoyable experience, it’s important to use them responsibly:\r\n\r\nStart Low, Go Slow: Begin with a low dose to assess your tolerance and sensitivity to psilocybin.\r\nSafe Environment: Ensure you are in a comfortable, safe environment, especially if you are new to psilocybin.\r\nAvoid Mixing: Do not mix with alcohol or other substances to prevent adverse reactions.\r\nMedical Consultation: If you have pre-existing medical conditions or are taking other medications, consult a healthcare professional before use.\r\nKeep Out of Reach of Children: Store in a safe place, away from children and pets.\r\nWhy Choose Mastermind?\r\nPremium Quality: We use only the highest quality ingredients to ensure a safe, potent, and enjoyable product.\r\nConsistency: Each bar is carefully crafted to provide a consistent dosage and reliable experience.\r\nCustomer Support: Our dedicated team is here to answer any questions and provide guidance on your psilocybin journey.\r\nDiscreet Shipping: We prioritize your privacy with secure, discreet packaging and reliable shipping methods.\r\nOrder Your Mastermind Milk Chocolate Funghi Bars Today\r\nDiscover the perfect blend of indulgence and enlightenment with Mastermind Milk Chocolate Funghi Bars. Whether you’re looking to explore the benefits of psilocybin for personal growth, therapeutic purposes, or simply to enhance your mood and creativity, our Funghi Bars offer a delicious and effective solution. Order now and experience the magic of psilocybin in a delectable chocolate treat.', '', 25.00, NULL, 'BUYMASTERM-SJMX', 100, 0.00, '', 'product_1753973295_ac5e9a50.jpg', NULL, 0, 1, '', '', '2025-07-31 14:48:15', '2025-07-31 14:48:15'),
(38, 11, 'Buy Moons Psilocybin Gummies Blackberry (3000mg)', 'buy-moons-psilocybin-gummies-blackberry-3000mg', 'Elevate Your Psychedelic Experience with Moons Psilocybin Gummies Blackberry (3000mg)\r\nIndulge in the delicious and potent Moons Psilocybin Gummies Blackberry (3000mg), expertly crafted to provide a seamless and enjoyable psychedelic experience. These gummies are perfect for both beginners and experienced users seeking a convenient, tasty, and effective way to consume psilocybin.\r\n\r\nWhat are Moons Psilocybin Gummies Blackberry?\r\nFlavor: Bursting with the rich, sweet, and tangy taste of ripe blackberries, these gummies offer a delightful flavor that masks the natural taste of psilocybin.\r\nPsilocybin Content: Each gummy is precisely infused with 3000mg of high-quality psilocybin extract, ensuring consistent potency and effects.\r\nForm: Gummies provide a discreet, easy-to-dose, and convenient alternative to traditional psilocybin mushrooms.\r\nKey Features:\r\nDelicious Blackberry Flavor: Enjoy the irresistible taste of blackberries while experiencing the powerful effects of psilocybin.\r\nConvenient and Discreet: Perfect for on-the-go use, these gummies are easy to carry and consume discreetly.\r\nPrecise Dosage: Each gummy is infused with an exact amount of psilocybin, ensuring a consistent and controlled experience.\r\nHigh-Quality Ingredients: Made with premium ingredients and high-quality psilocybin extract for a safe and effective journey.\r\nPotent Effects: Provides vivid visuals, enhanced sensory perception, and deep introspective insights.\r\nEmotional and Spiritual Growth: Facilitates greater emotional and spiritual understanding, promoting overall well-being.\r\nEnhanced Creativity: Boosts creativity and problem-solving abilities, making it perfect for artistic and innovative endeavors.\r\nHow to Use:\r\nStart with a Low Dose: If you are new to psilocybin or gummies, start with a small portion to assess your sensitivity and reaction.\r\nSet and Setting: Consume the gummies in a comfortable, safe, and familiar environment to enhance your experience.\r\nStay Hydrated: Drink plenty of water before, during, and after consumption to stay hydrated and support your overall well-being.\r\nPositive Mindset: Approach your psychedelic experience with a positive mindset and clear intentions for a more fulfilling journey.\r\nDosage Guidelines:\r\nMicrodose (0.1g to 0.5g psilocybin): Subtle effects that enhance mood, creativity, and focus without strong psychoactive effects.\r\nLow Dose (0.5g to 1g psilocybin): Mild psychedelic experience with slight visuals and enhanced sensory perception.\r\nModerate Dose (1g to 2g psilocybin): More pronounced effects, including moderate visuals, emotional insights, and a sense of euphoria.\r\nHigh Dose (2g to 3g psilocybin): Intense psychedelic experience with strong visuals, profound introspection, and significant sensory and emotional shifts.\r\nSafety and Legal Information:\r\nLegal Status: Ensure you are aware of and comply with the legal status of psilocybin in your area before purchasing. Adherence to local laws is crucial.\r\nIntended Use: This product is intended for adult use only. Consult a healthcare professional if you have any health concerns or conditions.\r\nProper Storage: Store in a cool, dry place away from direct sunlight and moisture to maintain freshness and potency.\r\nWhy Choose Moons Psilocybin Gummies Blackberry?\r\nMoons Psilocybin Gummies Blackberry (3000mg) offer a unique and enjoyable way to experience the benefits of psilocybin. Whether you are seeking profound introspection, heightened creativity, or emotional and spiritual growth, these gummies provide a reliable and transformative journey. Their delicious flavor, precise dosing, and convenient form make them a top choice for anyone looking to explore the world of psilocybin.\r\n\r\nOrder your Moons Psilocybin Gummies Blackberry (3000mg) online today and embark on a delightful and extraordinary psychedelic adventure!', '', 45.00, NULL, 'BUYMOONSPS-4B0V', 100, 0.00, '', 'product_1753973344_9bc8f8b0.jpg', NULL, 0, 1, '', '', '2025-07-31 14:49:04', '2025-07-31 14:49:04'),
(39, 11, 'Buy Shroomies Cookies and Cream Chocolate Bar (3000mg)', 'buy-shroomies-cookies-and-cream-chocolate-bar-3000mg', 'Delight in a Delectable Psychedelic Experience\r\nDiscover a delicious and transformative journey with the Shroomies Cookies and Cream Chocolate Bar, featuring 3000mg of premium psilocybin mushrooms. This exquisite chocolate bar combines the creamy richness of white chocolate with crunchy cookie bits, offering both a delightful taste and a powerful psychedelic experience. Perfect for experienced psychonauts and curious explorers alike, the Shroomies Cookies and Cream Chocolate Bar provides a convenient and enjoyable way to explore the benefits of psilocybin.\r\n\r\nWhat is the Shroomies Cookies and Cream Chocolate Bar?\r\nPremium Psilocybin Content: Each bar contains 3000mg of high-quality psilocybin mushrooms, ensuring a potent and consistent psychedelic experience.\r\nCreamy Cookies and Cream Flavor: Made with smooth white chocolate and crunchy cookie bits for a delightful taste sensation.\r\nConvenient and Discreet: Easily portable and perfect for discreet consumption, making it ideal for both personal use and sharing with friends.\r\nKey Features:\r\nPotent Psychedelic Effects: Experience vivid visual and auditory hallucinations, enhanced sensory perception, and deep introspective insights.\r\nDelicious Flavor: Enjoy the creamy, rich taste of white chocolate combined with crunchy cookie bits for an irresistible treat.\r\nPrecise Dosage: Each bar is infused with 3000mg of psilocybin, allowing for accurate and controlled dosing.\r\nHigh-Quality Ingredients: Made with premium white chocolate and carefully sourced psilocybin mushrooms to ensure purity and potency.\r\nHow to Use:\r\nStart with a Small Piece: Due to the high psilocybin content, it is recommended to start with a small piece to assess your sensitivity and reaction. This approach helps you find the right dose for your experience level.\r\nCreate a Safe Environment: Consume the chocolate bar in a comfortable, safe, and familiar setting. A calm and peaceful environment enhances the overall experience and allows you to fully embrace the journey.\r\nStay Hydrated: Drink plenty of water before, during, and after consumption to stay hydrated and support your overall well-being.\r\nEnjoy the Flavor: Savor the delicious taste of cookies and cream while experiencing the transformative effects of psilocybin.\r\nBenefits:\r\nIntense Visual and Auditory Hallucinations: Enjoy enhanced sensory experiences with vivid, colorful visuals and heightened auditory effects.\r\nProfound Introspective Insights: Engage in deep self-reflection and explore the depths of your mind, gaining valuable insights.\r\nEmotional and Spiritual Growth: Unlock new dimensions of consciousness and achieve greater emotional and spiritual understanding.\r\nEnhanced Creativity and Problem-Solving: Tap into heightened creativity and improved problem-solving abilities during and after the experience.\r\nSafety and Legal Information:\r\nLegal Status: Ensure you are aware of and comply with the legal status of psilocybin mushrooms in your area before purchasing. Adherence to local laws is crucial.\r\nIntended Use: This product is intended for adult use only. Consult a healthcare professional if you have any health concerns or conditions.\r\nProper Storage: Store in a cool, dry place away from direct sunlight and moisture to maintain freshness and potency.\r\nWhy Choose the Shroomies Cookies and Cream Chocolate Bar?\r\nThe Shroomies Cookies and Cream Chocolate Bar offers a unique and enjoyable way to experience the powerful effects of psilocybin mushrooms. Combining the creamy, rich flavor of white chocolate with crunchy cookie bits and the transformative potential of psilocybin, this chocolate bar is perfect for both personal exploration and shared experiences. Whether you seek profound introspection, heightened creativity, or spiritual growth, the Shroomies Cookies and Cream Chocolate Bar provides a reliable and delightful journey.\r\n\r\nOrder your Shroomies Cookies and Cream Chocolate Bar (3000mg) online today and embark on an extraordinary psychedelic adventure!', '', 40.00, NULL, 'BUYSHROOMI-HLAG', 100, 0.00, '', 'product_1753973398_012bfaf9.jpg', NULL, 0, 1, '', '', '2025-07-31 14:49:58', '2025-07-31 14:49:58'),
(40, 11, 'Buy Shroomies Dark Chocolate Sea Salt Chocolate Bar (3000mg)', 'buy-shroomies-dark-chocolate-sea-salt-chocolate-bar-3000mg', 'Indulge in an Exquisite Psychedelic Experience\r\nIntroducing the Shroomies Dark Chocolate Sea Salt Chocolate Bar, a luxurious treat that combines the rich, bold flavor of dark chocolate with a touch of sea salt and the powerful effects of psilocybin. Each bar is carefully crafted with 3000mg of premium psilocybin mushrooms, offering an unparalleled journey into the realms of consciousness. Perfect for both experienced psychonauts and curious newcomers, this chocolate bar provides a delectable and convenient way to explore the benefits of psilocybin.\r\n\r\nWhat is the Shroomies Dark Chocolate Sea Salt Chocolate Bar?\r\nPremium Psilocybin Content: Infused with 3000mg of high-quality psilocybin mushrooms to ensure a potent and consistent psychedelic experience.\r\nExquisite Dark Chocolate: Made with rich, smooth dark chocolate and a hint of sea salt for a sophisticated flavor profile.\r\nConvenient and Discreet: This chocolate bar is easily portable and perfect for discreet consumption, making it ideal for both personal use and sharing with friends.\r\nKey Features:\r\nPotent Psychedelic Effects: Experience intense visual and auditory hallucinations, heightened sensory perception, and profound introspective insights.\r\nSophisticated Flavor: Enjoy the bold taste of dark chocolate balanced with a hint of sea salt for an indulgent treat.\r\nPrecise Dosage: Each bar contains 3000mg of psilocybin, allowing for accurate and controlled dosing.\r\nHigh-Quality Ingredients: Crafted with premium dark chocolate and carefully sourced psilocybin mushrooms to ensure purity and potency.\r\nHow to Use:\r\nStart with a Small Piece: Given the high psilocybin content, it is recommended to start with a small piece to gauge your sensitivity and reaction. This approach helps you find the right dose for your experience level.\r\nCreate a Safe Environment: Consume the chocolate bar in a comfortable, safe, and familiar setting. A calm and peaceful environment enhances the overall experience and allows you to fully embrace the journey.\r\nStay Hydrated: Drink plenty of water before, during, and after consumption to stay hydrated and support your overall well-being.\r\nSavor the Flavor: Enjoy the rich, bold taste of dark chocolate with a hint of sea salt while experiencing the transformative effects of psilocybin.\r\nBenefits:\r\nIntense Visual and Auditory Hallucinations: Enjoy enhanced sensory experiences with vivid, colorful visuals and heightened auditory effects.\r\nProfound Introspective Insights: Engage in deep self-reflection and explore the depths of your mind, gaining valuable insights.\r\nEmotional and Spiritual Growth: Unlock new dimensions of consciousness and achieve greater emotional and spiritual understanding.\r\nEnhanced Creativity and Problem-Solving: Tap into heightened creativity and improved problem-solving abilities during and after the experience.\r\nSafety and Legal Information:\r\nLegal Status: Ensure you are aware of and comply with the legal status of psilocybin mushrooms in your area before purchasing. Adherence to local laws is crucial.\r\nIntended Use: This product is intended for adult use only. Consult a healthcare professional if you have any health concerns or conditions.\r\nProper Storage: Store in a cool, dry place away from direct sunlight and moisture to maintain freshness and potency.\r\nWhy Choose the Shroomies Dark Chocolate Sea Salt Chocolate Bar?\r\nThe Shroomies Dark Chocolate Sea Salt Chocolate Bar offers a unique and enjoyable way to experience the powerful effects of psilocybin mushrooms. Combining the rich, sophisticated flavor of dark chocolate with the transformative potential of psilocybin, this chocolate bar is perfect for both personal exploration and shared experiences. Whether you seek profound introspection, heightened creativity, or spiritual growth, the Shroomies Dark Chocolate Sea Salt Chocolate Bar provides a reliable and delightful journey.\r\n\r\nOrder your Shroomies Dark Chocolate Sea Salt Chocolate Bar (3000mg) online today and embark on an extraordinary psychedelic adventure!', '', 40.00, NULL, 'BUYSHROOMI-ZXEY', 100, 0.00, '', 'product_1753973444_636916b7.jpg', NULL, 1, 1, '', '', '2025-07-31 14:50:44', '2025-07-31 14:55:00');
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `description`, `short_description`, `price`, `sale_price`, `sku`, `stock_quantity`, `weight`, `dimensions`, `image`, `gallery`, `is_featured`, `is_active`, `meta_title`, `meta_description`, `created_at`, `updated_at`) VALUES
(41, 11, 'Buy Shroomies Milk Crunch Chocolate Bar (3000mg)', 'buy-shroomies-milk-crunch-chocolate-bar-3000mg', 'Description\r\nIndulge in the Perfect Blend of Flavor and Psychedelic Experience\r\nDiscover the ultimate fusion of taste and transcendence with the Shroomies Milk Crunch Chocolate Bar, featuring 3000mg of premium psilocybin. This delectable treat combines the rich, creamy flavor of milk chocolate with the powerful effects of psilocybin mushrooms, offering a delightful and transformative journey. Whether you’re a seasoned psychonaut or a curious explorer, the Shroomies Milk Crunch Chocolate Bar provides a convenient and enjoyable way to experience the benefits of psilocybin.\r\n\r\nWhat is the Shroomies Milk Crunch Chocolate Bar?\r\nPremium Psilocybin Content: Each bar contains 3000mg of high-quality psilocybin mushrooms, ensuring a potent and consistent psychedelic experience.\r\nDelicious Milk Chocolate: Made with rich, creamy milk chocolate and crunchy bits for an indulgent taste sensation.\r\nConvenient and Discreet: Easily portable and perfect for discreet consumption, making it ideal for both personal use and sharing with friends.\r\nKey Features:\r\nPotent Psychedelic Effects: Experience vivid visual and auditory hallucinations, enhanced sensory perception, and deep introspective insights.\r\nDelicious Flavor: Enjoy the creamy, rich taste of milk chocolate combined with crunchy bits for an irresistible treat.\r\nPrecise Dosage: Each bar is infused with 3000mg of psilocybin, allowing for accurate and controlled dosing.\r\nHigh-Quality Ingredients: Made with premium chocolate and carefully sourced psilocybin mushrooms to ensure purity and potency.\r\nHow to Use:\r\nStart with a Small Piece: Due to the high psilocybin content, it is recommended to start with a small piece to assess your sensitivity and reaction. This approach helps you find the right dose for your experience level.\r\nCreate a Safe Environment: Consume the chocolate bar in a comfortable, safe, and familiar setting. A calm and peaceful environment enhances the overall experience and allows you to fully embrace the journey.\r\nStay Hydrated: Drink plenty of water before, during, and after consumption to stay hydrated and support your overall well-being.\r\nEnjoy the Flavor: Savor the delicious taste of milk chocolate and crunchy bits while experiencing the transformative effects of psilocybin.\r\nBenefits:\r\nIntense Visual and Auditory Hallucinations: Enjoy enhanced sensory experiences with vivid, colorful visuals and heightened auditory effects.\r\nProfound Introspective Insights: Engage in deep self-reflection and explore the depths of your mind, gaining valuable insights.\r\nEmotional and Spiritual Growth: Unlock new dimensions of consciousness and achieve greater emotional and spiritual understanding.\r\nEnhanced Creativity and Problem-Solving: Tap into heightened creativity and improved problem-solving abilities during and after the experience.\r\nSafety and Legal Information:\r\nLegal Status: Ensure you are aware of and comply with the legal status of psilocybin mushrooms in your area before purchasing. Adherence to local laws is crucial.\r\nIntended Use: This product is intended for adult use only. Consult a healthcare professional if you have any health concerns or conditions.\r\nProper Storage: Store in a cool, dry place away from direct sunlight and moisture to maintain freshness and potency.\r\nWhy Choose the Shroomies Milk Crunch Chocolate Bar?\r\nThe Shroomies Milk Crunch Chocolate Bar offers a unique and enjoyable way to experience the powerful effects of psilocybin mushrooms. Combining the delicious taste of milk chocolate with the transformative potential of psilocybin, this chocolate bar is perfect for both personal exploration and shared experiences. Whether you seek profound introspection, heightened creativity, or spiritual growth, the Shroomies Milk Crunch Chocolate Bar provides a reliable and delightful journey.\r\n\r\nOrder your Shroomies Milk Crunch Chocolate Bar (3000mg) online today and embark on an extraordinary psychedelic adventure!', '', 40.00, NULL, 'BUYSHROOMI-P6VW', 100, 0.00, '', 'product_1753973538_5c734607.jpg', NULL, 0, 1, '', '', '2025-07-31 14:52:18', '2025-07-31 14:52:18'),
(42, 11, 'Buy Wonder Psilocybin Chocolate Bar Cookies & Creme (6000mg)', 'buy-wonder-psilocybin-chocolate-bar-cookies-creme-6000mg', 'Description\r\nExperience a Decadent Fusion of Flavors and Effects\r\nIntroducing the Wonder Psilocybin Chocolate Bar Cookies & Creme (6000mg)—a luxurious treat that marries the indulgent taste of cookies and creme with the intriguing effects of psilocybin mushrooms. This innovative chocolate bar is meticulously crafted to offer a delightful combination of gourmet flavors and transformative experiences, making it the perfect choice for those seeking both pleasure and introspection.\r\n\r\nWhat Makes It Special?\r\nGourmet Cookies & Creme Flavor: Enjoy the rich, creamy taste of cookies and creme, a beloved classic with a twist. Each bar is infused with real cookie bits and a velvety creme filling, creating a mouthwatering flavor profile that complements the high-quality chocolate.\r\nPotent Psilocybin Infusion: With 6000mg of premium psilocybin mushrooms per bar, you can expect a powerful and consistent experience. The psilocybin is carefully sourced and blended to ensure a potent yet enjoyable effect.\r\nExpert Craftsmanship: Our chocolate bar is crafted with precision and care, ensuring that each piece offers the perfect balance of flavor and potency. The result is a luxurious product that stands out in both taste and quality.\r\nHow to Use:\r\nStart Small: For those new to psilocybin, begin by consuming a small portion of the chocolate bar. This allows you to gauge your sensitivity and adjust your intake accordingly.\r\nMindful Consumption: Enjoy the chocolate bar in a comfortable and safe environment. Savor the rich flavors and allow yourself to fully experience the effects of the psilocybin.\r\nDosage Adjustment: Based on your personal experience, you can gradually adjust the amount consumed. The 6000mg dosage is potent, so it’s important to be mindful of your own tolerance and experience level.\r\nStay Hydrated: Drink plenty of water to stay hydrated before, during, and after consumption.\r\nBenefits:\r\nEnhanced Sensory Perception: Experience a heightened sense of awareness and a deeper connection with your surroundings.\r\nIntrospective Journey: Gain valuable insights and a greater understanding of yourself as you explore your consciousness.\r\nElevated Mood: Enjoy a positive mood boost and a sense of well-being as you indulge in this luxurious treat.\r\nSafety and Legal Information:\r\nLegal Status: Ensure that you are aware of the legal status of psilocybin mushrooms in your area before purchasing. Compliance with local laws is crucial.\r\nIntended Use: The Wonder Psilocybin Chocolate Bar Cookies & Creme is intended for adult use only. Consult with a healthcare professional if you have any health concerns or conditions.\r\nProper Storage: Store the chocolate bar in a cool, dry place away from direct sunlight and heat to maintain its quality and freshness.\r\nWhy Choose the Wonder Psilocybin Chocolate Bar Cookies & Creme?\r\nThe Wonder Psilocybin Chocolate Bar Cookies & Creme (6000mg) offers a unique and luxurious way to experience the effects of psilocybin mushrooms. Combining the rich, indulgent flavors of cookies and creme with a potent dose of psilocybin, this bar provides a sophisticated and enjoyable journey into self-discovery and sensory exploration. Perfect for personal use or sharing with friends, it’s an ideal choice for those seeking both a premium treat and a transformative experience.\r\n\r\nOrder your Wonder Psilocybin Chocolate Bar Cookies & Creme (6000mg) today and elevate your indulgence to new heights!', '', 40.00, NULL, 'BUYWONDERP-X9AE', 100, 0.00, '', 'product_1753973590_ff14a822.jpg', NULL, 0, 1, '', '', '2025-07-31 14:53:10', '2025-07-31 14:53:10'),
(43, 11, 'Buy Wonder Psilocybin Chocolate Bars S’mores (6000mg)', 'buy-wonder-psilocybin-chocolate-bars-smores-6000mg', 'Description\r\nIndulge in a Magical Experience with Wonder Psilocybin Chocolate Bars\r\nWelcome to a new dimension of taste and sensation with our Wonder Psilocybin Chocolate Bars S’mores. Each bar is infused with a potent dose of 6000mg of high-quality psilocybin, designed to take you on a journey of mind and body like never before. Whether you’re looking to enhance your creativity, explore your consciousness, or simply enjoy a delicious treat, our S’mores-flavored psilocybin chocolate bars are the perfect choice.\r\n\r\nProduct Features\r\nHigh Potency: Each bar contains a total of 6000mg of psilocybin, providing a powerful and consistent experience.\r\nDelicious Flavor: Enjoy the classic taste of S’mores with rich chocolate, graham crackers, and marshmallows, expertly blended to perfection.\r\nConvenient Dosing: The bar is easily divisible into smaller pieces, allowing for precise dosing to suit your needs and experience level.\r\nPremium Ingredients: Made with high-quality ingredients to ensure a delicious taste and a smooth, enjoyable texture.\r\nWhy Choose Wonder Psilocybin Chocolate Bars?\r\nEnhanced Experience: Combining psilocybin with chocolate not only masks the natural taste of mushrooms but also provides a pleasant, enjoyable consumption method.\r\nConsistent Dosing: Each piece of the bar offers a precise amount of psilocybin, ensuring a reliable and predictable experience every time.\r\nConvenient and Discreet: Our chocolate bars are easy to carry and consume discreetly, making them perfect for on-the-go use or a quiet evening at home.\r\nHow to Use\r\nStart with a Low Dose: If you are new to psilocybin or have a low tolerance, start with a small piece of the chocolate bar to gauge your reaction.\r\nWait for the Effects: It may take 60-90 minutes for the effects to kick in. Be patient and avoid consuming more until you feel the initial effects.\r\nEnjoy Responsibly: Ensure you are in a safe and comfortable environment, preferably with a trusted friend or sitter if you are new to psilocybin.\r\nImportant Information\r\nIngredients: Milk chocolate, graham cracker crumbs, marshmallow pieces, psilocybin extract.\r\nStorage: Store in a cool, dry place away from direct sunlight to maintain freshness and potency.\r\nCaution: Keep out of reach of children and pets. Do not consume if you are pregnant, nursing, or have a medical condition without consulting your healthcare provider.\r\nPurchase with Confidence\r\nAt Shroom Wonders, we prioritize quality and customer satisfaction. When you buy Wonder Psilocybin Chocolate Bars S’mores (6000mg) from us, you can trust that you are getting a premium product that meets the highest standards of safety and effectiveness. Enjoy the delicious taste and powerful effects of our psilocybin chocolate bars and embark on a magical journey today.', '', 80.00, NULL, 'BUYWONDERP-U13M', 100, 0.00, '', 'product_1753973643_4c500f4b.jpg', NULL, 0, 1, '', '', '2025-07-31 14:54:03', '2025-07-31 14:54:03'),
(44, 11, 'Buy Wonder Psilocybin Mushroom Cherry Cola Gummies (3000mg)', 'buy-wonder-psilocybin-mushroom-cherry-cola-gummies-3000mg', 'Description\r\nA Delicious Twist on Psilocybin Experiences\r\nIntroducing the Wonder Psilocybin Mushroom Cherry Cola Gummies (3000mg)—a delightful fusion of nostalgic cherry cola flavor and the transformative effects of psilocybin mushrooms. These innovative gummies offer a unique and flavorful way to enjoy the benefits of psilocybin, combining the fun of a classic candy with a potent dose of mind-expanding ingredients.\r\n\r\nWhat Makes These Gummies Special?\r\nIrresistible Cherry Cola Flavor: Enjoy the sweet and tangy taste of cherry cola, expertly captured in every gummy. Each piece is designed to deliver a burst of classic soda flavor while providing the benefits of psilocybin.\r\nHigh Potency: Each pack of gummies contains a total of 3000mg of premium psilocybin mushrooms, ensuring a strong and consistent experience with each serving.\r\nPremium Ingredients: Made with high-quality psilocybin mushrooms and natural flavors to ensure a delicious and effective product.\r\nConvenient and Discreet: The gummy form provides a convenient and discreet way to consume psilocybin, making it easy to integrate into your routine or take on the go.\r\nHow to Use:\r\nStart Small: Begin with a small number of gummies to gauge your sensitivity to psilocybin. Since each pack contains 3000mg, it’s important to start with a lower dose to assess how you respond.\r\nMindful Enjoyment: Consume the gummies in a comfortable and safe environment where you can fully embrace the experience. The cherry cola flavor enhances the enjoyment, making each bite a treat.\r\nDosage Guidance: Adjust the number of gummies based on your personal experience and desired effect. The potency of 3000mg allows for flexible dosing to suit your needs.\r\nHydration: Drink plenty of water to stay hydrated and support your overall experience.\r\nBenefits:\r\nEnhanced Sensory Experience: Expect heightened sensory perception and a deeper connection with your surroundings.\r\nIntrospective Insights: Explore self-awareness and gain valuable insights as you engage with the effects of psilocybin.\r\nMood Enhancement: Experience a positive mood boost and a sense of well-being with each gummy.\r\nSafety and Legal Information:\r\nLegal Compliance: Verify the legal status of psilocybin mushrooms in your area before purchasing. Ensure that you are in compliance with local laws.\r\nIntended Use: These gummies are intended for adult use only. Consult a healthcare professional if you have any health concerns or conditions.\r\nStorage: Store the gummies in a cool, dry place away from sunlight and heat to maintain freshness and potency.\r\nWhy Choose Wonder Psilocybin Mushroom Cherry Cola Gummies?\r\nThe Wonder Psilocybin Mushroom Cherry Cola Gummies (3000mg) offer a delicious and effective way to experience the effects of psilocybin mushrooms. Combining the fun and flavor of cherry cola with a potent dose of psilocybin, these gummies provide a sophisticated yet enjoyable way to explore your consciousness. Perfect for personal use or sharing with friends, they offer both a tasty treat and a transformative experience.\r\n\r\nOrder your Wonder Psilocybin Mushroom Cherry Cola Gummies (3000mg) today and savor the unique combination of flavor and adventure!', '', 45.00, NULL, 'BUYWONDERP-VT23', 100, 0.00, '', 'product_1753973692_e4bcf15f.jpg', NULL, 1, 1, '', '', '2025-07-31 14:54:52', '2025-07-31 14:55:01'),
(45, 11, 'Buy Wonder Psilocybin Mushroom Dark Chocolate Bar (6000mg)', 'buy-wonder-psilocybin-mushroom-dark-chocolate-bar-6000mg', 'Description\r\nWhat is the Wonder Psilocybin Mushroom Dark Chocolate Bar?\r\nThe Wonder Psilocybin Mushroom Dark Chocolate Bar (6000mg) is an exquisite confection that combines the indulgent richness of premium dark chocolate with the unique properties of psilocybin mushrooms. Each bar is meticulously crafted to deliver a potent dose of psilocybin—6000mg—ensuring a transformative experience with every bite. Perfect for those seeking both a gourmet treat and a journey into the realms of consciousness.\r\n\r\nKey Features:\r\nPremium Ingredients: Made with high-quality dark chocolate and carefully selected psilocybin mushrooms.\r\nPotent Dosage: Each bar contains 6000mg of psilocybin for a powerful experience.\r\nRich Flavor: Enjoy the deep, smooth taste of dark chocolate with subtle mushroom notes.\r\nCraftsmanship: Expertly crafted to ensure consistent potency and exceptional taste.\r\nHow to Use:\r\nStart Small: Begin by consuming a small portion of the chocolate bar to assess your sensitivity to psilocybin. Each bar is potent, so starting with a smaller piece helps gauge your personal response.\r\nEnjoy Mindfully: Consume the chocolate in a comfortable and safe environment. Allow yourself to fully experience the effects of the psilocybin as you savor the rich flavors of the dark chocolate.\r\nDosage Guidance: For those new to psilocybin, it’s advisable to consume a minimal amount initially. As you become more familiar with its effects, you can adjust your intake according to your preferences and experience level.\r\nStay Hydrated: Drink plenty of water and stay hydrated before, during, and after your experience.\r\nBenefits:\r\nEnhanced Sensory Perception: Enjoy heightened senses and a more profound connection with your surroundings.\r\nIntrospective Insights: Gain deeper insights and introspection as you explore your consciousness.\r\nElevated Mood: Experience improved mood and a sense of well-being.\r\nSafety and Legal Information:\r\nLegal Status: Ensure you are aware of the legal status of psilocybin mushrooms in your area before purchasing. Compliance with local laws is essential.\r\nUsage: Intended for adult use only. Use responsibly and consult with a healthcare professional if you have any underlying health conditions or concerns.\r\nStorage: Keep the chocolate bar in a cool, dry place away from direct sunlight and heat to preserve its quality.\r\nWhy Choose Wonder Psilocybin Mushroom Dark Chocolate Bar?\r\nThe Wonder Psilocybin Mushroom Dark Chocolate Bar is more than just a treat—it’s a gateway to a unique and enriching experience. Combining the best of gourmet chocolate with the intriguing effects of psilocybin mushrooms, this bar offers a luxurious and transformative journey. Perfect for personal exploration or sharing with like-minded friends, it’s an ideal choice for those looking to elevate their experience with a touch of elegance.\r\n\r\nOrder your Wonder Psilocybin Mushroom Dark Chocolate Bar (6000mg) today and embark on a sensory adventure like no other!', '', 80.00, NULL, 'BUYWONDERP-DK3J', 100, 0.00, '', 'product_1753973739_45549788.jpg', NULL, 0, 1, '', '', '2025-07-31 14:55:39', '2025-07-31 14:55:39'),
(46, 11, 'Shop Wonder Psilocybin Milk Chocolate Bar (6000mg)', 'shop-wonder-psilocybin-milk-chocolate-bar-6000mg', 'Description\r\nIndulge in a Premium Psilocybin Experience\r\nDiscover the perfect blend of taste and therapeutic benefits with our Wonder Psilocybin Milk Chocolate Bar. Each bar is infused with a potent dose of 6000mg of high-quality psilocybin, designed to provide a powerful and transformative experience. Whether you’re looking to explore new dimensions of your mind or simply enjoy a delicious treat, our milk chocolate psilocybin bar is the ideal choice.\r\n\r\nProduct Highlights\r\nHigh Potency: Each bar contains 6000mg of high-quality psilocybin for a powerful experience.\r\nSmooth Milk Chocolate: Enjoy the rich and creamy taste of premium milk chocolate infused with psilocybin.\r\nConvenient Dosing: The bar is easily breakable into smaller pieces, allowing for precise dosing to suit your needs.\r\nQuality Ingredients: Made with the finest ingredients to ensure a delicious and enjoyable treat.\r\nEnhanced Experience: Combines the therapeutic benefits of psilocybin with the delightful taste of milk chocolate.\r\nConsistent Dosage: Each piece offers a reliable and predictable psilocybin dose for a consistent experience.\r\nDiscreet and Portable: Easy to carry and consume discreetly, making it perfect for on-the-go use or a quiet evening at home.\r\nSafe Storage: Store in a cool, dry place away from direct sunlight to maintain freshness and potency.\r\nResponsible Consumption: Start with a low dose, wait 60-90 minutes for effects, and ensure you’re in a safe environment.\r\nQuality Assurance: Rigorously tested to meet the highest standards of safety and effectiveness, ensuring a premium psilocybin experience.\r\nWhy Choose Wonder Psilocybin Milk Chocolate Bar?\r\nOur Wonder Psilocybin Milk Chocolate Bar is crafted with care and precision to provide a superior psilocybin experience. The smooth and creamy milk chocolate not only masks the natural taste of mushrooms but also enhances the overall experience. Whether you’re a seasoned psychonaut or new to psilocybin, our chocolate bar offers a delightful and effective way to enjoy the benefits of psilocybin.\r\n\r\nHow to Use\r\nStart Low and Go Slow: If you are new to psilocybin or have a low tolerance, start with a small piece of the chocolate bar to gauge your reaction.\r\nWait for the Effects: It may take 60-90 minutes for the effects to kick in. Be patient and avoid consuming more until you feel the initial effects.\r\nEnjoy Responsibly: Ensure you are in a safe and comfortable environment, preferably with a trusted friend or sitter if you are new to psilocybin.\r\nImportant Information\r\nIngredients: Milk chocolate, sugar, cocoa butter, whole milk powder, lecithin, natural vanilla flavor, psilocybin extract.\r\nStorage: Store in a cool, dry place away from direct sunlight to maintain freshness and potency.\r\nCaution: Keep out of reach of children and pets. Do not consume if you are pregnant, nursing, or have a medical condition without consulting your healthcare provider.\r\nPurchase with Confidence\r\nAt Shroom Wonders Psilocybin Chocolate Bar, we prioritize quality and customer satisfaction. When you shop Wonder Psilocybin Milk Chocolate Bar (6000mg) from us, you can trust that you are getting a premium product that meets the highest standards of safety and effectiveness. Indulge in the delicious taste and powerful effects of our psilocybin chocolate bar and embark on a magical journey today.', '', 80.00, NULL, 'SHOPWONDER-CWRJ', 100, 0.00, '', 'product_1753973784_01dcbfc6.jpg', NULL, 0, 1, '', '', '2025-07-31 14:56:24', '2025-07-31 15:41:48');

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
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `position` varchar(255) DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `testimonial` text NOT NULL,
  `rating` tinyint(1) NOT NULL DEFAULT 5 COMMENT 'Rating from 1 to 5 stars',
  `image` varchar(255) DEFAULT NULL COMMENT 'Profile image filename',
  `location` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 for featured testimonials',
  `is_active` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 for active, 0 for inactive',
  `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT 'Display order',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `name`, `email`, `position`, `company`, `testimonial`, `rating`, `image`, `location`, `is_featured`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'Sarah Johnson', '', 'Wellness Coach', 'Mindful Living Center', 'The microdose mushrooms from this store have been a game-changer for my mental clarity and creativity. The quality is exceptional and the effects are exactly what I was looking for.', 5, 'testimonial_1753986771_9bda724a.jpg', 'California, USA', 1, 1, 1, '2025-07-31 18:14:37', '2025-07-31 18:32:51'),
(2, 'Michael Chen', NULL, 'Artist', 'Creative Studios', 'As an artist, I was looking for something to enhance my creative process. These magic mushrooms have opened up new dimensions in my work. Highly recommended!', 5, NULL, 'New York, USA', 1, 1, 2, '2025-07-31 18:14:37', '2025-07-31 18:33:28'),
(3, 'Emma Rodriguez', NULL, 'Therapist', 'Healing Minds Clinic', 'I have been recommending these products to my clients for therapeutic purposes. The consistent quality and positive results speak for themselves.', 5, NULL, 'Colorado, USA', 1, 1, 3, '2025-07-31 18:14:37', '2025-07-31 18:14:37');

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
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `is_featured` (`is_featured`),
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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

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
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
