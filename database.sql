-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 26, 2026 at 05:08 PM
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
-- Database: `cash_khata`
--

-- --------------------------------------------------------

--
-- Table structure for table `cash_transactions`
--

CREATE TABLE `cash_transactions` (
  `id` int(11) NOT NULL,
  `type` enum('add','withdraw') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `note` varchar(255) DEFAULT '',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cash_transactions`
--

INSERT INTO `cash_transactions` (`id`, `type`, `amount`, `note`, `created_by`, `created_at`) VALUES
(1, 'add', 200000.00, 'Mot moldon', 1, '2026-08-19 13:04:13');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `mobile` varchar(30) NOT NULL,
  `address` varchar(255) DEFAULT '',
  `due` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `mobile`, `address`, `due`, `created_at`) VALUES
(1, 'Rejaol vai gm', '01846478470', 'Gazipur', 0.00, '2026-08-19 15:55:13'),
(2, 'Rejaol vai gm', '01846478470', 'Gazipur', 1600.00, '2026-08-19 15:55:13');

-- --------------------------------------------------------

--
-- Table structure for table `customer_payments`
--

CREATE TABLE `customer_payments` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `name`, `amount`, `created_at`) VALUES
(1, 'Fresh tisho box croy', 80.00, '2026-08-20 11:27:34');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` varchar(255) DEFAULT '',
  `category` enum('mobile','accessory','part') DEFAULT NULL,
  `purchase_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `sale_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `stock` int(11) NOT NULL DEFAULT 0,
  `low_stock_alert` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `category`, `purchase_price`, `sale_price`, `stock`, `low_stock_alert`, `supplier_id`, `created_at`) VALUES
(1, 'Y20 Lcd', 'Crown', 'part', 850.00, 950.00, 3, 1, 4, '2026-08-19 13:09:55'),
(2, 'Hot 9 play Lcd', '', 'part', 900.00, 1000.00, 0, 1, NULL, '2026-08-19 15:53:23'),
(3, 'Excel Charger 18w T/B', '', 'accessory', 240.00, 450.00, 5, 2, 3, '2026-08-20 06:12:57'),
(4, 'Nokia 1.7 Lcd 12 pin', '', 'part', 100.00, 120.00, 1, 1, 4, '2026-08-20 13:18:29'),
(5, 'Oppo a18 lcd', '', 'part', 900.00, 1000.00, 1, 1, 4, '2026-08-20 15:55:02'),
(6, 'Samsung j6+ lcd', 'Crown', 'part', 850.00, 950.00, 1, 1, 4, '2026-08-20 16:01:24'),
(7, 'samsung s20 backcover', '', 'accessory', 50.00, 100.00, 5, 5, 4, '2026-08-23 14:51:27');

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `purchase_price` decimal(15,2) NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `payment_type` enum('cash','due') NOT NULL,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `due_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `product_id`, `supplier_id`, `quantity`, `purchase_price`, `total_amount`, `payment_type`, `paid_amount`, `due_amount`, `created_at`) VALUES
(1, 1, 4, 3, 850.00, 2550.00, 'cash', 2550.00, 0.00, '2026-08-19 13:09:55'),
(2, 2, NULL, 1, 900.00, 900.00, 'cash', 900.00, 0.00, '2026-08-19 15:53:23'),
(3, 3, 3, 7, 240.00, 1680.00, 'cash', 1680.00, 0.00, '2026-08-20 06:12:57'),
(4, 4, 4, 1, 100.00, 100.00, 'cash', 100.00, 0.00, '2026-08-20 13:18:29'),
(5, 5, 4, 1, 900.00, 900.00, 'cash', 900.00, 0.00, '2026-08-20 15:55:02'),
(6, 6, 4, 1, 850.00, 850.00, 'cash', 850.00, 0.00, '2026-08-20 16:01:24'),
(7, 7, 4, 6, 50.00, 300.00, 'cash', 300.00, 0.00, '2026-08-23 14:51:27');

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `sale_price` decimal(15,2) NOT NULL,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL,
  `payment_type` enum('cash','due') NOT NULL,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `due_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `product_id`, `customer_id`, `quantity`, `sale_price`, `discount_amount`, `total_amount`, `payment_type`, `paid_amount`, `due_amount`, `created_at`) VALUES
(1, 2, 2, 1, 1600.00, 0.00, 1600.00, 'due', 0.00, 1600.00, '2026-08-19 15:56:45'),
(2, 3, NULL, 1, 450.00, 0.00, 450.00, 'cash', 450.00, 0.00, '2026-08-23 13:54:32'),
(3, 3, NULL, 1, 450.00, 0.00, 450.00, 'cash', 450.00, 0.00, '2026-08-23 13:55:20'),
(4, 7, NULL, 1, 100.00, 0.00, 100.00, 'cash', 100.00, 0.00, '2026-08-26 01:40:50');

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `customer_name` varchar(150) NOT NULL,
  `customer_mobile` varchar(30) NOT NULL,
  `mobile_brand` varchar(100) NOT NULL,
  `mobile_model` varchar(100) NOT NULL,
  `problem_description` text NOT NULL,
  `service_charge` decimal(15,2) NOT NULL DEFAULT 0.00,
  `parts_total` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_type` enum('cash','due') NOT NULL DEFAULT 'cash',
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `due_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','in_progress','completed','delivered') NOT NULL DEFAULT 'pending',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `customer_id`, `customer_name`, `customer_mobile`, `mobile_brand`, `mobile_model`, `problem_description`, `service_charge`, `parts_total`, `discount_amount`, `total_amount`, `payment_type`, `paid_amount`, `due_amount`, `status`, `created_by`, `created_at`) VALUES
(3, NULL, 'Walk-in Customer', '-', 'Button', '', 'headphon logo', 100.00, 0.00, 0.00, 100.00, 'cash', 100.00, 0.00, 'delivered', 1, '2026-08-19 16:11:21'),
(4, NULL, 'Walk-in Customer', '-', 'Infinix', '', 'Headphone logo', 200.00, 0.00, 0.00, 200.00, 'cash', 200.00, 0.00, 'delivered', 1, '2026-08-19 16:46:10'),
(5, NULL, 'Walk-in Customer', '-', 'Button', '', 'Charging pin', 100.00, 0.00, 0.00, 100.00, 'cash', 100.00, 0.00, 'delivered', 1, '2026-08-20 05:59:25'),
(6, NULL, 'Walk-in Customer', '-', 'Button  wash', '', 'Button', 80.00, 0.00, 0.00, 80.00, 'cash', 80.00, 0.00, 'delivered', 1, '2026-08-20 11:18:08'),
(7, NULL, 'Walk-in Customer', '-', 'Button  wash', '', '100', 100.00, 0.00, 0.00, 100.00, 'cash', 100.00, 0.00, 'delivered', 1, '2026-08-20 11:19:48'),
(8, NULL, 'Walk-in Customer', '-', 'Button', '', 'Water damoj', 80.00, 0.00, 0.00, 80.00, 'cash', 80.00, 0.00, 'delivered', 1, '2026-08-20 11:26:39'),
(9, NULL, 'Walk-in Customer', '-', 'Nokia Ta1010', '', 'Lcd reples', 80.00, 0.00, 0.00, 80.00, 'cash', 80.00, 0.00, 'delivered', 1, '2026-08-20 13:23:04'),
(10, NULL, 'Walk-in Customer', '-', 'Oppo a18 lcd', '', 'Lcd', 500.00, 0.00, 0.00, 500.00, 'cash', 500.00, 0.00, 'delivered', 1, '2026-08-20 15:58:43'),
(11, NULL, 'Walk-in Customer', '-', 'Samsung j6+', '', 'Lcd reples', 550.00, 0.00, 0.00, 550.00, 'cash', 550.00, 0.00, 'delivered', 1, '2026-08-20 16:03:01'),
(12, NULL, 'Walk-in Customer', '-', 'redmi 6', '', 'wash', 200.00, 0.00, 0.00, 200.00, 'cash', 200.00, 0.00, 'pending', 1, '2026-08-26 14:44:22'),
(13, NULL, 'Walk-in Customer', '-', 'redmi 6', '', 'disply', 100.00, 950.00, 0.00, 1050.00, 'cash', 1050.00, 0.00, 'pending', 1, '2026-08-26 14:45:28');

-- --------------------------------------------------------

--
-- Table structure for table `service_parts`
--

CREATE TABLE `service_parts` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(15,2) NOT NULL,
  `total` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `service_parts`
--

INSERT INTO `service_parts` (`id`, `service_id`, `product_id`, `quantity`, `price`, `total`) VALUES
(2, 13, 1, 1, 950.00, 950.00);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `business_name` varchar(150) DEFAULT 'My Business',
  `business_address` varchar(255) DEFAULT '',
  `business_phone` varchar(50) DEFAULT '',
  `cash_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `opening_cash_set` tinyint(1) NOT NULL DEFAULT 0,
  `language` varchar(5) NOT NULL DEFAULT 'en',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `business_name`, `business_address`, `business_phone`, `cash_balance`, `opening_cash_set`, `language`, `updated_at`) VALUES
(1, 'my Business', '', '', 195080.00, 1, 'en', '2026-08-26 14:45:28');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `mobile` varchar(30) NOT NULL,
  `address` varchar(255) DEFAULT '',
  `due` decimal(15,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `mobile`, `address`, `due`, `created_at`) VALUES
(3, 'AK Telecom Riaz', '01818530247', '', 0.00, '2026-08-20 06:16:14'),
(4, 'AK Telecom Hanif', '01929207428', '', 0.00, '2026-08-20 06:16:59');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_payments`
--

CREATE TABLE `supplier_payments` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','staff') NOT NULL DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `role`, `created_at`) VALUES
(1, 'admin', 'admin', 'Administrator', 'admin', '2026-07-11 17:19:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cash_transactions`
--
ALTER TABLE `cash_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `service_parts`
--
ALTER TABLE `service_parts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `service_id` (`service_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cash_transactions`
--
ALTER TABLE `cash_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `customer_payments`
--
ALTER TABLE `customer_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `service_parts`
--
ALTER TABLE `service_parts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cash_transactions`
--
ALTER TABLE `cash_transactions`
  ADD CONSTRAINT `cash_transactions_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD CONSTRAINT `customer_payments_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `services_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `services_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `service_parts`
--
ALTER TABLE `service_parts`
  ADD CONSTRAINT `service_parts_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `service_parts_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD CONSTRAINT `supplier_payments_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
