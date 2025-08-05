-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20250707.de50d366ca
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 05, 2025 at 05:52 PM
-- Server version: 8.4.3
-- PHP Version: 8.3.16

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

DROP TABLE IF EXISTS `tbl_customers`;
CREATE TABLE IF NOT EXISTS `tbl_customers` (
  `customer_id` int NOT NULL AUTO_INCREMENT,
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
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_customers`
--

INSERT INTO `tbl_customers` (`customer_id`, `first_name`, `middle_name`, `last_name`, `email`, `nickname`, `address`, `contact_number`, `birth_date`, `gender`, `religion`, `citizenship`, `status`, `occupation`, `created_at`, `updated_at`) VALUES
(99, 'archie', 'amas', 'albarico', 'archiealbarico69@gmail.com', 'chie', 'tunghaan, minglanilla, cebu', '09231226478', '2000-10-24', 'male', 'catholic', 'filipino', 'divorced', 'freelancing', '2025-08-01 14:37:57', '2025-08-01 21:01:55');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_deceased`
--

DROP TABLE IF EXISTS `tbl_deceased`;
CREATE TABLE IF NOT EXISTS `tbl_deceased` (
  `deceased_id` int NOT NULL AUTO_INCREMENT,
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
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`deceased_id`),
  KEY `grave_id` (`lot_id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_deceased`
--

INSERT INTO `tbl_deceased` (`deceased_id`, `lot_id`, `dead_fullname`, `dead_gender`, `dead_citizenship`, `dead_civil_status`, `dead_relationship`, `dead_message`, `dead_bio`, `dead_profile_link`, `dead_interment`, `dead_birth_date`, `dead_date_death`, `created_at`, `updated_at`) VALUES
(37, 37, 'lebron james', 'male', 'sdf', 'sdf', 'sdf', 'sdf', 'sdf', 'sdf', '2025-08-12', '2025-08-27', '2025-08-06', '2025-08-03 08:50:54', '2025-08-03 08:50:54');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_lot`
--

DROP TABLE IF EXISTS `tbl_lot`;
CREATE TABLE IF NOT EXISTS `tbl_lot` (
  `lot_id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `plot_id` int NOT NULL,
  `niche_number` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `niche_status` enum('available','reserved','occupied') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lot_status` enum('active','completed','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`lot_id`),
  KEY `grave_id` (`plot_id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_lot`
--

INSERT INTO `tbl_lot` (`lot_id`, `customer_id`, `plot_id`, `niche_number`, `niche_status`, `lot_status`, `created_at`, `updated_at`) VALUES
(37, 99, 15, '23', 'occupied', 'active', '2025-08-03 07:16:33', '2025-08-03 07:16:33');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_media`
--

DROP TABLE IF EXISTS `tbl_media`;
CREATE TABLE IF NOT EXISTS `tbl_media` (
  `media_id` int NOT NULL AUTO_INCREMENT,
  `plot_id` int NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`media_id`),
  KEY `plot_id` (`plot_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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

DROP TABLE IF EXISTS `tbl_plots`;
CREATE TABLE IF NOT EXISTS `tbl_plots` (
  `plot_id` int NOT NULL AUTO_INCREMENT,
  `block` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `category` enum('bronze','silver','platinum','diamond','columbarium','chambers') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `length` varchar(255) DEFAULT NULL,
  `width` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `area` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `rows` varchar(255) DEFAULT NULL,
  `columns` varchar(255) DEFAULT NULL,
  `status` enum('available','reserved','occupied') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `coordinates` varchar(255) NOT NULL,
  PRIMARY KEY (`plot_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_plots`
--

INSERT INTO `tbl_plots` (`plot_id`, `block`, `category`, `length`, `width`, `area`, `rows`, `columns`, `status`, `label`, `coordinates`) VALUES
(1, 'A', 'diamond', '2.5', '1.2', '3.0', NULL, NULL, 'available', NULL, '123.79769285129, 10.249193799482'),
(2, 'A', 'diamond', NULL, NULL, NULL, NULL, NULL, 'available', NULL, '123.79772218795, 10.249206732589'),
(3, 'A', 'silver', NULL, NULL, NULL, NULL, NULL, 'available', NULL, '123.79775692256, 10.249221975178'),
(4, 'A', 'silver', NULL, NULL, NULL, NULL, NULL, 'available', NULL, '123.7977887235, 10.249236063025'),
(5, 'A', 'diamond', NULL, NULL, NULL, NULL, NULL, 'available', NULL, '123.79773427465, 10.24917878784'),
(6, 'A', 'diamond', NULL, NULL, NULL, NULL, NULL, 'available', NULL, '123.79770376452, 10.249166316629'),
(7, 'A', 'bronze', NULL, NULL, NULL, NULL, NULL, 'available', NULL, '123.79782322341, 10.249251074665'),
(8, 'A', 'platinum', NULL, NULL, NULL, NULL, NULL, 'available', NULL, '123.79776900926, 10.249193799482'),
(9, 'A', 'platinum', NULL, NULL, NULL, NULL, NULL, 'available', NULL, '123.79780116224, 10.249206963537'),
(10, 'A', 'bronze', NULL, NULL, NULL, NULL, NULL, 'available', NULL, '123.79783613154, 10.249222206126'),
(11, NULL, 'chambers', NULL, NULL, NULL, '5', '5', NULL, NULL, '123.79734521528, 10.24940426143'),
(12, NULL, 'chambers', NULL, NULL, NULL, '5', '9', NULL, NULL, '123.79737958462, 10.249367524939'),
(13, NULL, 'chambers', NULL, NULL, NULL, '5', '9', NULL, NULL, '123.79741810198, 10.249323791016'),
(14, NULL, 'chambers', NULL, NULL, NULL, '5', '9', NULL, NULL, '123.79745958222, 10.249275975252'),
(15, NULL, 'chambers', NULL, NULL, NULL, '5', '9', NULL, NULL, '123.79750046989, 10.249232824435');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_users`
--

DROP TABLE IF EXISTS `tbl_users`;
CREATE TABLE IF NOT EXISTS `tbl_users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int DEFAULT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `isAdmin` tinyint NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`user_id`),
  KEY `customer_id` (`customer_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_users`
--

INSERT INTO `tbl_users` (`user_id`, `customer_id`, `username`, `password`, `isAdmin`, `created_at`, `updated_at`) VALUES
(1, NULL, 'archieamas11', '$2y$10$GMmEGnwoqH6LvEXtvJRg0uXL4AWuU0Vy8gjpjAJg/vH2EhQczroUq', 0, '2025-07-25 08:07:30', '2025-07-25 08:07:30'),
(2, NULL, 'admin', '$2y$10$MCHAHJhRzwkc1mcck.x5jeP9D0ny5ahQRrmbw5ToQpqWuY7lNbIKW', 1, '2025-07-25 09:00:20', '2025-07-25 09:00:20'),
(3, NULL, 'user', '$2y$10$MPkJNQDOu00BLBcgE8hXLOhze9tyJ7UvGKMZDHGUhWImfvbBgKtuS', 0, '2025-07-25 09:23:22', '2025-07-25 09:23:22');

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
