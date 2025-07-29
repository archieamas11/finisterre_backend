-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20250707.de50d366ca
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 29, 2025 at 01:48 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `finisterre_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_customers`
--

CREATE TABLE `tbl_customers` (
  `customer_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `middle_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nickname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contact_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('male','female') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `religion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `citizenship` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `occupation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_customers`
--

INSERT INTO `tbl_customers` (`customer_id`, `user_id`, `first_name`, `middle_name`, `last_name`, `email`, `nickname`, `address`, `contact_number`, `birth_date`, `gender`, `religion`, `citizenship`, `status`, `occupation`, `created_at`, `updated_at`) VALUES
(35, 3, 'archie', 'gwapo', 'albarico', 'archiealbarico69@gmail.com', 'chie', 'tunghaan, minglanilla, cebu', '09634636306', '2025-07-02', 'female', 'catholic', 'filipino', 'single', 'none', '2025-07-27 18:59:49', '2025-07-29 14:02:18'),
(39, NULL, 'lucy', 'caneteq', 'ababa', 'funihanu@mailinator.com', 'chiekay', 'Soluta voluptatem Q', '09231226478', '2006-06-02', 'female', 'Quasi dicta facere q', 'Magnam officia qui l', 'widowed', 'Quis labore error se', '2025-07-29 00:52:30', '2025-07-29 13:31:10'),
(96, NULL, 'qwe', 'qwe', 'qwe', 'voni@mailinator.com', 'qwe', 'qwe', '09231226478', '2025-07-04', 'male', 'qwe', 'asdasd', 'single', 'qwe', '2025-07-29 14:02:33', '2025-07-29 14:02:33');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_deceased`
--

CREATE TABLE `tbl_deceased` (
  `deceased_id` int NOT NULL,
  `lot_id` int NOT NULL,
  `dead_fullname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `dead_gender` enum('male','female') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `dead_citizenship` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `dead_civil_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `dead_relationship` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `dead_message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dead_bio` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dead_profile_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dead_interment` date NOT NULL,
  `dead_birth_date` date NOT NULL,
  `dead_date_death` date NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_lot`
--

CREATE TABLE `tbl_lot` (
  `lot_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `plot_id` int NOT NULL,
  `type` enum('bronze','silver','platinum','diamon') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `payment_type` enum('installment','full') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `payment_frequency` enum('monthly','annually','none') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `start_date` date NOT NULL,
  `last_payment_date` date DEFAULT NULL,
  `next_due_date` date DEFAULT NULL,
  `lot_status` enum('active','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_plots`
--

CREATE TABLE `tbl_plots` (
  `plot_id` int NOT NULL,
  `block` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `category` enum('bronze','silver','platinum','diamond') NOT NULL,
  `status` enum('available','reserved','occupied') NOT NULL,
  `label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `coordinates` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_plots`
--

INSERT INTO `tbl_plots` (`plot_id`, `block`, `category`, `status`, `label`, `coordinates`) VALUES
(1, 'A', 'diamond', 'occupied', NULL, '123.79769285129, 10.249193799482'),
(2, 'A', 'diamond', 'available', NULL, '123.79772218795, 10.249206732589'),
(3, 'A', 'silver', 'available', NULL, '123.79775692256, 10.249221975178'),
(4, 'A', 'silver', 'available', NULL, '123.7977887235, 10.249236063025'),
(5, 'A', 'diamond', 'available', NULL, '123.79773427465, 10.24917878784'),
(6, 'A', 'diamond', 'available', NULL, '123.79770376452, 10.249166316629'),
(7, 'A', 'bronze', 'available', NULL, '123.79782322341, 10.249251074665'),
(8, 'A', 'platinum', 'available', NULL, '123.79776900926, 10.249193799482'),
(9, 'A', 'platinum', 'available', NULL, '123.79780116224, 10.249206963537'),
(10, 'A', 'bronze', 'reserved', NULL, '123.79783613154, 10.249222206126');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `user_id` int NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `isAdmin` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`user_id`, `first_name`, `last_name`, `username`, `password`, `isAdmin`, `created_at`, `updated_at`) VALUES
(1, 'archie', 'albarico', 'archieamas11', '$2y$10$GMmEGnwoqH6LvEXtvJRg0uXL4AWuU0Vy8gjpjAJg/vH2EhQczroUq', 0, '2025-07-25 08:07:30', '2025-07-25 08:07:30'),
(2, 'admin', 'admin', 'admin', '$2y$10$MCHAHJhRzwkc1mcck.x5jeP9D0ny5ahQRrmbw5ToQpqWuY7lNbIKW', 1, '2025-07-25 09:00:20', '2025-07-25 09:00:20'),
(3, 'user', 'user', 'user', '$2y$10$MPkJNQDOu00BLBcgE8hXLOhze9tyJ7UvGKMZDHGUhWImfvbBgKtuS', 0, '2025-07-25 09:23:22', '2025-07-25 09:23:22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_customers`
--
ALTER TABLE `tbl_customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tbl_deceased`
--
ALTER TABLE `tbl_deceased`
  ADD PRIMARY KEY (`deceased_id`),
  ADD KEY `grave_id` (`lot_id`);

--
-- Indexes for table `tbl_lot`
--
ALTER TABLE `tbl_lot`
  ADD PRIMARY KEY (`lot_id`),
  ADD KEY `grave_id` (`plot_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `tbl_plots`
--
ALTER TABLE `tbl_plots`
  ADD PRIMARY KEY (`plot_id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_customers`
--
ALTER TABLE `tbl_customers`
  MODIFY `customer_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=97;

--
-- AUTO_INCREMENT for table `tbl_deceased`
--
ALTER TABLE `tbl_deceased`
  MODIFY `deceased_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `tbl_lot`
--
ALTER TABLE `tbl_lot`
  MODIFY `lot_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `tbl_plots`
--
ALTER TABLE `tbl_plots`
  MODIFY `plot_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbl_customers`
--
ALTER TABLE `tbl_customers`
  ADD CONSTRAINT `tbl_customers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbl_users` (`user_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `tbl_deceased`
--
ALTER TABLE `tbl_deceased`
  ADD CONSTRAINT `tbl_deceased_ibfk_1` FOREIGN KEY (`lot_id`) REFERENCES `tbl_lot` (`lot_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `tbl_lot`
--
ALTER TABLE `tbl_lot`
  ADD CONSTRAINT `tbl_lot_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `tbl_customers` (`customer_id`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `tbl_lot_ibfk_2` FOREIGN KEY (`plot_id`) REFERENCES `tbl_plots` (`plot_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
