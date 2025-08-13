-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20250707.de50d366ca
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 13, 2025 at 04:57 PM
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
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `middle_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contact_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `gender` enum('Male','Female') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `religion` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `citizenship` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `occupation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `isArchive` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_customers`
--

INSERT INTO `tbl_customers` (`customer_id`, `first_name`, `middle_name`, `last_name`, `email`, `address`, `contact_number`, `birth_date`, `gender`, `religion`, `citizenship`, `status`, `occupation`, `isArchive`, `created_at`, `updated_at`) VALUES
(118, 'Archie', 'Amas', 'Albarico', 'archiealbarico69@gmail.com', 'Tunghaan, Minglanilla, Cebu', '09231226478', '2011-08-11', 'Female', 'Catholic', 'Filipino', 'Single', 'Student', 0, '2025-08-13 20:38:37', '2025-08-13 23:24:43'),
(119, 'Lucy', 'Caneta', 'Ababa', 'archiealbarico69@gmail.com', 'Tunghaan, Minglanilla, Cebu', '09231226478', '2025-08-27', 'Female', 'Catholic', 'Filipino', 'Married', 'Student', 0, '2025-08-13 20:43:36', '2025-08-13 20:43:36'),
(120, 'Lebron', 'King', 'James', 'archiealbarico69@gmail.com', 'Tunghaan, Minglanilla, Cebu', '09231226478', '1990-08-01', 'Male', 'Catholic', 'Filipino', 'Single', 'Student', 0, '2025-08-13 22:06:47', '2025-08-13 23:31:59');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_deceased`
--

CREATE TABLE `tbl_deceased` (
  `deceased_id` int NOT NULL,
  `lot_id` int NOT NULL,
  `dead_fullname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `dead_gender` enum('Male','Female') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `dead_citizenship` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dead_civil_status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dead_relationship` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dead_message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dead_bio` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dead_profile_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `dead_interment` date NOT NULL,
  `dead_birth_date` date NOT NULL,
  `dead_date_death` date NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_deceased`
--

INSERT INTO `tbl_deceased` (`deceased_id`, `lot_id`, `dead_fullname`, `dead_gender`, `dead_citizenship`, `dead_civil_status`, `dead_relationship`, `dead_message`, `dead_bio`, `dead_profile_link`, `dead_interment`, `dead_birth_date`, `dead_date_death`, `created_at`, `updated_at`) VALUES
(48, 57, 'Kyrie Irving', 'Male', NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-28', '2025-08-21', '2025-08-21', '2025-08-14 00:03:46', '2025-08-14 00:03:46'),
(49, 58, 'Luka Doncic', 'Male', NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-28', '2025-08-30', '2025-08-29', '2025-08-14 00:05:11', '2025-08-14 00:05:11');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_lot`
--

CREATE TABLE `tbl_lot` (
  `lot_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `plot_id` int NOT NULL,
  `niche_number` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `niche_status` enum('available','reserved','occupied') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lot_status` enum('active','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_lot`
--

INSERT INTO `tbl_lot` (`lot_id`, `customer_id`, `plot_id`, `niche_number`, `niche_status`, `lot_status`, `created_at`, `updated_at`) VALUES
(55, 119, 1, NULL, NULL, 'active', '2025-08-13 13:28:48', '2025-08-13 13:28:48'),
(56, 118, 2, NULL, NULL, 'active', '2025-08-13 13:46:06', '2025-08-13 13:46:06'),
(57, 120, 15, '45', 'occupied', 'active', '2025-08-13 23:41:53', '2025-08-13 23:41:53'),
(58, 118, 15, '44', 'occupied', 'active', '2025-08-14 00:04:58', '2025-08-14 00:04:58'),
(59, 119, 15, '43', 'reserved', 'active', '2025-08-14 00:38:20', '2025-08-14 00:38:20'),
(60, 118, 15, '42', 'reserved', 'active', '2025-08-14 00:41:48', '2025-08-14 00:41:48'),
(61, 120, 15, '41', 'reserved', 'active', '2025-08-14 00:42:09', '2025-08-14 00:42:09'),
(62, 118, 15, '40', 'reserved', 'active', '2025-08-14 00:42:50', '2025-08-14 00:42:50');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_media`
--

CREATE TABLE `tbl_media` (
  `media_id` int NOT NULL,
  `plot_id` int NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_media`
--

INSERT INTO `tbl_media` (`media_id`, `plot_id`, `file_name`, `created_at`, `updated_at`) VALUES
(1, 1, 'https://res.cloudinary.com/djrkvgfvo/image/upload/v1752756875/Grave_Maintenance_-_Standard_copy_mzxqpt.jpg', '2025-08-02 04:17:40', '2025-08-02 04:17:40'),
(2, 1, 'https://res.cloudinary.com/djrkvgfvo/image/upload/v1752756582/9457a7ca-fa2f-4331-b32e-d0223db1fd8a_-_Edited_xkre2p.png', '2025-08-02 04:17:40', '2025-08-02 04:17:40'),
(3, 15, 'https://res.cloudinary.com/djrkvgfvo/image/upload/v1754204958/d7c71ee6-552f-44dc-911b-b713023e4d03_jkgwnp.png', '2025-08-03 07:02:58', '2025-08-03 07:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_plots`
--

CREATE TABLE `tbl_plots` (
  `plot_id` int NOT NULL,
  `block` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `category` enum('bronze','silver','platinum','diamond','columbarium','chambers') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `length` varchar(255) DEFAULT NULL,
  `width` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `rows` varchar(255) DEFAULT NULL,
  `columns` varchar(255) DEFAULT NULL,
  `status` enum('available','reserved','occupied') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `coordinates` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_plots`
--

INSERT INTO `tbl_plots` (`plot_id`, `block`, `category`, `length`, `width`, `area`, `rows`, `columns`, `status`, `label`, `coordinates`) VALUES
(1, 'A', 'diamond', '2.5', '1.2', '3.0', NULL, NULL, 'reserved', NULL, '123.79769285129, 10.249193799482'),
(2, 'A', 'diamond', NULL, NULL, NULL, NULL, NULL, 'reserved', NULL, '123.79772218795, 10.249206732589'),
(3, 'A', 'silver', NULL, NULL, NULL, NULL, NULL, 'available', NULL, '123.79775692256, 10.249221975178'),
(4, 'A', 'silver', NULL, NULL, NULL, NULL, NULL, 'available', NULL, '123.7977887235, 10.249236063025'),
(5, 'A', 'diamond', NULL, NULL, NULL, NULL, NULL, 'available', NULL, '123.79773427465, 10.24917878784'),
(6, 'A', 'diamond', NULL, NULL, NULL, NULL, NULL, 'available', NULL, '123.79770376452, 10.249166316629'),
(7, 'A', 'bronze', NULL, NULL, NULL, NULL, NULL, 'available', NULL, '123.79782322341, 10.249251074665'),
(8, 'A', 'platinum', NULL, NULL, NULL, NULL, NULL, 'available', NULL, '123.79776900926, 10.249193799482'),
(9, 'A', 'platinum', NULL, NULL, NULL, NULL, NULL, 'available', NULL, '123.79780116224, 10.249206963537'),
(10, 'A', 'bronze', NULL, NULL, NULL, NULL, NULL, 'available', NULL, '123.79783613154, 10.249222206126'),
(11, NULL, 'chambers', NULL, NULL, NULL, '5', '9', NULL, NULL, '123.79734521528, 10.24940426143'),
(12, NULL, 'chambers', NULL, NULL, NULL, '5', '9', NULL, NULL, '123.79737958462, 10.249367524939'),
(13, NULL, 'chambers', NULL, NULL, NULL, '5', '9', NULL, NULL, '123.79741810198, 10.249323791016'),
(14, NULL, 'chambers', NULL, NULL, NULL, '5', '9', NULL, NULL, '123.79745958222, 10.249275975252'),
(15, NULL, 'chambers', NULL, NULL, NULL, '5', '9', NULL, NULL, '123.79750046989, 10.249232824435');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

CREATE TABLE `tbl_users` (
  `user_id` int NOT NULL,
  `customer_id` int DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `isAdmin` tinyint NOT NULL DEFAULT '0',
  `isArchive` tinyint NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`user_id`, `customer_id`, `username`, `password`, `isAdmin`, `isArchive`, `created_at`, `updated_at`) VALUES
(2, NULL, 'admin', '$2y$10$MCHAHJhRzwkc1mcck.x5jeP9D0ny5ahQRrmbw5ToQpqWuY7lNbIKW', 1, 0, '2025-07-25 09:00:20', '2025-07-25 09:00:20'),
(3, NULL, 'user', '$2y$10$MPkJNQDOu00BLBcgE8hXLOhze9tyJ7UvGKMZDHGUhWImfvbBgKtuS', 0, 0, '2025-07-25 09:23:22', '2025-07-25 09:23:22'),
(8, 118, '54', '$2y$10$zxOl7hHjmoFBbiWBa4KfS.6Zvb13VXtIJA3t6U28apFW22DXUuF2C', 0, 0, '2025-08-13 21:08:56', '2025-08-13 21:08:56'),
(9, 120, '57', '$2y$10$T3W0rn9k9JoSEwrUVKXtlOIGjZXfa2Y50piY5AacmbBDrL.XAKKiG', 0, 0, '2025-08-13 23:41:53', '2025-08-13 23:41:53'),
(10, 119, '59', '$2y$10$LYFkkDoPlEHYgSh/.K1yEOevPVz7EICmSDqIKb4pkeo8HmvYbRtZ6', 0, 0, '2025-08-14 00:38:20', '2025-08-14 00:38:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_customers`
--
ALTER TABLE `tbl_customers`
  ADD PRIMARY KEY (`customer_id`);

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
-- Indexes for table `tbl_media`
--
ALTER TABLE `tbl_media`
  ADD PRIMARY KEY (`media_id`),
  ADD KEY `plot_id` (`plot_id`);

--
-- Indexes for table `tbl_plots`
--
ALTER TABLE `tbl_plots`
  ADD PRIMARY KEY (`plot_id`);

--
-- Indexes for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_customers`
--
ALTER TABLE `tbl_customers`
  MODIFY `customer_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `tbl_deceased`
--
ALTER TABLE `tbl_deceased`
  MODIFY `deceased_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `tbl_lot`
--
ALTER TABLE `tbl_lot`
  MODIFY `lot_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `tbl_media`
--
ALTER TABLE `tbl_media`
  MODIFY `media_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbl_plots`
--
ALTER TABLE `tbl_plots`
  MODIFY `plot_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tbl_users`
--
ALTER TABLE `tbl_users`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

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

--
-- Constraints for table `tbl_media`
--
ALTER TABLE `tbl_media`
  ADD CONSTRAINT `tbl_media_ibfk_1` FOREIGN KEY (`plot_id`) REFERENCES `tbl_plots` (`plot_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `tbl_users`
--
ALTER TABLE `tbl_users`
  ADD CONSTRAINT `tbl_users_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `tbl_customers` (`customer_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
