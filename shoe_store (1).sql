-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 15, 2025 at 09:05 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shoe_store`
--

-- --------------------------------------------------------

--
-- Table structure for table `attributes`
--

CREATE TABLE `attributes` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attributes`
--

INSERT INTO `attributes` (`id`, `name`, `category_id`) VALUES
(1, 'Size', 1),
(2, 'Màu sắc', 1),
(3, 'Thương hiệu', 1),
(4, 'Size', 2),
(5, 'Màu sắc', 2),
(6, 'Chất liệu', 2),
(7, 'Size', 3),
(8, 'Màu sắc', 3),
(9, 'Kiểu dáng', 3);

-- --------------------------------------------------------

--
-- Table structure for table `attribute_values`
--

CREATE TABLE `attribute_values` (
  `id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `value` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attribute_values`
--

INSERT INTO `attribute_values` (`id`, `attribute_id`, `value`) VALUES
(1, 1, '36'),
(2, 1, '37'),
(3, 1, '38'),
(4, 1, '39'),
(5, 1, '40'),
(6, 1, '41'),
(7, 1, '42'),
(8, 1, '43'),
(9, 1, '44'),
(10, 2, 'Đen'),
(11, 2, 'Trắng'),
(12, 2, 'Xanh'),
(13, 2, 'Đỏ'),
(14, 2, 'Nâu'),
(15, 3, 'Nike'),
(16, 3, 'Adidas'),
(17, 3, 'Puma'),
(18, 3, 'Converse'),
(19, 4, 'S'),
(20, 4, 'M'),
(21, 4, 'L'),
(22, 4, 'XL'),
(23, 4, 'XXL'),
(24, 5, 'Đen'),
(25, 5, 'Trắng'),
(26, 5, 'Xanh'),
(27, 5, 'Đỏ'),
(28, 5, 'Vàng'),
(29, 6, 'Cotton'),
(30, 6, 'Polyester'),
(31, 6, 'Linen'),
(32, 6, 'Silk'),
(33, 7, '28'),
(34, 7, '29'),
(35, 7, '30'),
(36, 7, '31'),
(37, 7, '32'),
(38, 7, '33'),
(39, 7, '34'),
(40, 7, '36'),
(41, 8, 'Đen'),
(42, 8, 'Xanh navy'),
(43, 8, 'Xanh jean'),
(44, 8, 'Nâu'),
(45, 8, 'Xám'),
(46, 9, 'Slim fit'),
(47, 9, 'Regular fit'),
(48, 9, 'Straight'),
(49, 9, 'Skinny');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`) VALUES
(1, 'Giày', 'Các loại giày thể thao, giày công sở, giày dép'),
(2, 'Áo', 'Áo thun, áo sơ mi, áo khoác các loại'),
(3, 'Quần', 'Quần jean, quần tây, quần short, quần thể thao');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_address` text NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `total_amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT 'COD',
  `payment_status` varchar(50) DEFAULT 'pending',
  `transaction_id` varchar(100) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_name`, `customer_email`, `customer_phone`, `customer_address`, `order_date`, `total_amount`, `payment_method`, `payment_status`, `transaction_id`, `note`, `user_id`) VALUES
(1, 'Lưu Hoàng Linh', 'linkvjp.pro@gmail.com', '0393024703', 'Học viện Kỹ thuật Mật mã', '2025-06-13 11:28:32', 600000.00, 'COD', 'pending', NULL, NULL, 2),
(2, 'Lưu Hoàng Linh', 'linkvjp.pro@gmail.com', '0393024703', 'Học viện Kỹ thuật Mật mã', '2025-06-13 11:40:50', 950000.00, 'COD', 'pending', NULL, NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `variant_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`, `variant_id`) VALUES
(1, 1, 8, 1, 600000.00, NULL),
(2, 2, 9, 1, 350000.00, NULL),
(3, 2, 8, 1, 600000.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `category`, `stock`, `created_at`, `category_id`) VALUES
(1, 'Giày thể thao mẫu 1', 'Mẫu giày thể thao chất lượng cao, đệm khí êm ái', 1500000.00, 'https://bizweb.dktcdn.net/thumb/1024x1024/100/449/458/products/anh-ghep-logo-web-3-a78626d5-8b39-4b29-9321-2b26224129a5.png', 'Running', 20, '2025-06-13 09:43:06', 1),
(2, 'Giày chạy bộ năng động', 'Giày chạy bộ siêu nhẹ, đế bám tốt', 1200000.00, 'https://product.hstatic.net/1000312752/product/arpv003-3_1_7260e97a734848b596a118464e6c1313.jpg', 'Running', 15, '2025-06-13 09:43:06', 1),
(3, 'Giày da công sở', 'Giày da cao cấp, sang trọng cho dân văn phòng', 2300000.00, 'https://giaycaosmartmen.com/wp-content/uploads/2025/06/GC2.jpeg', 'Formal', 10, '2025-06-13 09:43:06', 1),
(4, 'Áo thun mẫu 1', 'Áo thun cotton thoáng mát, phù hợp mọi hoạt động', 300000.00, 'https://www.hpshop1.vn/uploads/Mau%20dong%20phuc%20cong%20ty%20(17).jpg', 'Casual', 50, '2025-06-13 09:43:06', 2),
(5, 'Áo sơ mi công sở', 'Áo sơ mi trắng lịch sự, phù hợp môi trường làm việc', 450000.00, 'https://product.hstatic.net/200000588671/product/ao-so-mi-nam-tay-dai-cong-so-bamboo-mau-xanh-den-1_15a13335f0b74f48a7e2f4d50aed2e01_master.jpg', 'Formal', 30, '2025-06-13 09:43:06', 2),
(6, 'Áo khoác mùa đông', 'Áo khoác giữ nhiệt, phong cách hiện đại', 850000.00, 'https://product.hstatic.net/200000690725/product/fwcl002_54050573537_o_e651d1526c9b4889821c5b5afa443476_master.jpg', 'Outerwear', 25, '2025-06-13 09:43:06', 2),
(7, 'Quần jean mẫu 1', 'Quần jean thời trang, co giãn tốt, dễ phối đồ', 500000.00, 'https://product.hstatic.net/200000690725/product/qu_n_web_2f11da571d5e4e40b476755c66a3cb23_master.png', 'Jeans', 30, '2025-06-13 09:43:06', 3),
(8, 'Quần tây nam', 'Quần tây nam kiểu dáng chuẩn công sở', 600000.00, 'https://product.hstatic.net/1000360022/product/quan-tay-icondenim-classic-black-form-slim_8e2171d63ffd493fa3794baf50b7dbac_1024x1024.jpg', 'Formal', 38, '2025-06-13 09:43:06', 3),
(9, 'Quần short hè', 'Quần short nhẹ, thoáng mát cho mùa hè', 350000.00, 'https://product.hstatic.net/1000360022/product/id-2005a_6c93e71ec322499e853ae9533bad3e50_1024x1024.jpg', 'Casual', 34, '2025-06-13 09:43:06', 3),
(13, 'Giày sneaker trắng basic', 'Thiết kế đơn giản, trẻ trung, phù hợp đi học đi chơi.', 420000.00, 'https://cdn.sablanca.vn/ImageProducts/se0010/wht/se0010_wht_1000x1000_2800007633.jpg', 'Sneaker', 20, '2025-06-15 13:50:24', 1),
(14, 'Giày chạy bộ nam Xspeed', 'Đệm siêu nhẹ, đế bám tốt, phù hợp chạy bộ và gym.', 580000.00, 'https://assets.adidas.com/images/h_840,f_auto,q_auto,fl_lossy,c_fill,g_auto/163c91e1518646bea1e0a6edc29098d7_9366/DJoi_giay_Adizero_Boston_13_trang_JS4932_02_standard_hover.jpg', 'Running', 18, '2025-06-15 13:50:24', 1),
(15, 'Giày boot da cổ lửng', 'Phong cách mạnh mẽ, chất liệu giả da cao cấp.', 990000.00, 'https://giayhuyhoang.vn/wp-content/uploads/2020/12/dat-dong-giay-da-nam-thu-cong-chukka-boot-cnes68-005.jpg', 'Boots', 10, '2025-06-15 13:50:24', 1),
(16, 'Áo polo nam basic', 'Chất liệu cotton thoáng mát, thiết kế đơn giản trẻ trung.', 390000.00, 'https://4menshop.com/images/thumbs/2020/12/ao-polo-tron-basic-po016-mau-hong-15750-slide-products-5fd440be80ab4.jpg', 'Polo', 30, '2025-06-15 13:56:13', 2),
(17, 'Áo khoác bomber nam', 'Phong cách hiện đại, phù hợp thu đông, vải chống gió.', 750000.00, 'https://product.hstatic.net/1000378223/product/chinhdien_copy_7c89e64288be40eb8bc0f21068c6e15a.jpg', 'Bomber', 20, '2025-06-15 13:56:13', 2),
(18, 'Áo sơ mi denim nam', 'Chất liệu denim dày dặn, cổ điển và nam tính.', 520000.00, 'https://product.hstatic.net/1000360022/product/id-2205_7d77095bb16643bd87dab04cf0dfac36_master.jpg', 'Denim', 25, '2025-06-15 13:56:13', 2),
(19, 'Quần short kaki nam', 'Chất liệu kaki mềm nhẹ, thoáng mát, lý tưởng cho mùa hè.', 320000.00, 'https://product.hstatic.net/200000690725/product/fsbk015-6_53853471065_o_165fb1931821413a861ef92333a03604_master.jpg', 'Short', 28, '2025-06-15 13:58:51', 3),
(20, 'Quần tây nam form slim', 'Thiết kế lịch sự, chất vải co giãn nhẹ, phù hợp đi làm.', 540000.00, 'https://product.hstatic.net/1000253775/product/quan-tay-nam-icondenim-classic-black-form-slim_85094b74406a45e8b5aca0b62579a065_1024x1024.jpg', 'Formal', 22, '2025-06-15 13:58:51', 3),
(21, 'Quần jogger thể thao nam', 'Thiết kế năng động, phù hợp luyện tập thể thao và mặc hàng ngày.', 460000.00, 'https://file.hstatic.net/1000253775/collection/quan_jooger_308a031f60794d42b5a56454614a2035_master.jpg', 'Sport', 24, '2025-06-15 13:58:51', 3);

-- --------------------------------------------------------

--
-- Table structure for table `product_variants`
--

CREATE TABLE `product_variants` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `sku` varchar(100) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `sku`, `price`, `stock`) VALUES
(1, 1, 'GIAYTS36DEN', 1500000.00, 10),
(2, 1, 'GIAYTS37DEN', 1500000.00, 5),
(3, 1, 'GIAYTS38DEN', 1500000.00, 5),
(4, 2, 'GIAYCB39TRANG', 1200000.00, 7),
(5, 2, 'GIAYCB40TRANG', 1200000.00, 8),
(6, 3, 'GIAYDC41DA', 2300000.00, 10);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `isAdmin` tinyint(4) NOT NULL,
  `email` varchar(255) NOT NULL,
  `full_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`, `isAdmin`, `email`, `full_name`) VALUES
(1, 'admin', '123456', '2025-06-13 09:43:06', 1, '', 'Administrator'),
(2, '1', '1', '2025-06-13 09:43:06', 0, 'dm.phuong@hehe.com', 'Đòn Minh Phương'),
(4, 'phuong123', 'phuong123', '2025-06-14 17:15:42', 0, 'phuong123@gmail.com', 'Đoàn Minh Phương');

-- --------------------------------------------------------

--
-- Table structure for table `variant_attribute_values`
--

CREATE TABLE `variant_attribute_values` (
  `variant_id` int(11) NOT NULL,
  `attribute_value_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `variant_attribute_values`
--

INSERT INTO `variant_attribute_values` (`variant_id`, `attribute_value_id`) VALUES
(1, 1),
(1, 10),
(1, 15),
(2, 2),
(2, 10),
(2, 15),
(3, 3),
(3, 10),
(3, 15),
(4, 4),
(4, 11),
(4, 16),
(5, 5),
(5, 11),
(5, 16),
(6, 6),
(6, 13),
(6, 17);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attributes`
--
ALTER TABLE `attributes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `attribute_values`
--
ALTER TABLE `attribute_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attribute_id` (`attribute_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_orders_user` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_variant` (`variant_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_category` (`category_id`);

--
-- Indexes for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `variant_attribute_values`
--
ALTER TABLE `variant_attribute_values`
  ADD PRIMARY KEY (`variant_id`,`attribute_value_id`),
  ADD KEY `attribute_value_id` (`attribute_value_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attributes`
--
ALTER TABLE `attributes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `attribute_values`
--
ALTER TABLE `attribute_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attributes`
--
ALTER TABLE `attributes`
  ADD CONSTRAINT `attributes_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `attribute_values`
--
ALTER TABLE `attribute_values`
  ADD CONSTRAINT `attribute_values_ibfk_1` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_variant` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`),
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `variant_attribute_values`
--
ALTER TABLE `variant_attribute_values`
  ADD CONSTRAINT `variant_attribute_values_ibfk_1` FOREIGN KEY (`variant_id`) REFERENCES `product_variants` (`id`),
  ADD CONSTRAINT `variant_attribute_values_ibfk_2` FOREIGN KEY (`attribute_value_id`) REFERENCES `attribute_values` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
