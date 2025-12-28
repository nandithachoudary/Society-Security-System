-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Dec 28, 2025 at 12:56 PM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `society`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance_log`
--

DROP TABLE IF EXISTS `attendance_log`;
CREATE TABLE IF NOT EXISTS `attendance_log` (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `regular_visitor_id` int NOT NULL,
  `check_in_time` datetime NOT NULL,
  `check_out_time` datetime DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `regular_visitor_id` (`regular_visitor_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `attendance_log`
--

INSERT INTO `attendance_log` (`log_id`, `regular_visitor_id`, `check_in_time`, `check_out_time`) VALUES
(1, 1, '2025-10-20 21:18:18', '2025-10-20 21:19:36'),
(2, 5, '2025-10-27 23:07:25', '2025-10-27 23:33:38');

-- --------------------------------------------------------

--
-- Table structure for table `buildings`
--

DROP TABLE IF EXISTS `buildings`;
CREATE TABLE IF NOT EXISTS `buildings` (
  `building_id` int NOT NULL AUTO_INCREMENT,
  `building_name` varchar(100) NOT NULL,
  PRIMARY KEY (`building_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `buildings`
--

INSERT INTO `buildings` (`building_id`, `building_name`) VALUES
(1, 'A-Wing'),
(2, 'B-Wing'),
(3, 'C Wing'),
(4, 'D Wing'),
(5, 'E wing'),
(6, 'F wing');

-- --------------------------------------------------------

--
-- Table structure for table `flats`
--

DROP TABLE IF EXISTS `flats`;
CREATE TABLE IF NOT EXISTS `flats` (
  `flat_id` int NOT NULL AUTO_INCREMENT,
  `building_id` int NOT NULL,
  `flat_number` varchar(10) NOT NULL,
  PRIMARY KEY (`flat_id`),
  KEY `building_id` (`building_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `flats`
--

INSERT INTO `flats` (`flat_id`, `building_id`, `flat_number`) VALUES
(1, 1, '101'),
(2, 1, '102'),
(3, 2, '201'),
(4, 3, '301'),
(5, 4, '405'),
(6, 5, '405');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance`
--

DROP TABLE IF EXISTS `maintenance`;
CREATE TABLE IF NOT EXISTS `maintenance` (
  `maintenance_id` int NOT NULL AUTO_INCREMENT,
  `flat_id` int NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `month` int NOT NULL,
  `year` int NOT NULL,
  `due_date` date NOT NULL,
  `status` enum('paid','due') NOT NULL DEFAULT 'due',
  `payment_date` date DEFAULT NULL,
  `payment_mode` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`maintenance_id`),
  KEY `flat_id` (`flat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `maintenance`
--

INSERT INTO `maintenance` (`maintenance_id`, `flat_id`, `amount`, `month`, `year`, `due_date`, `status`, `payment_date`, `payment_mode`) VALUES
(1, 1, 2500.00, 10, 2025, '2025-10-20', 'paid', '2025-10-20', 'Online'),
(2, 2, 2500.00, 10, 2025, '2025-10-20', 'due', NULL, NULL),
(3, 3, 2500.00, 10, 2025, '2025-10-20', 'due', NULL, NULL),
(4, 4, 2500.00, 10, 2025, '2025-10-20', 'due', NULL, NULL),
(5, 1, 3000.00, 11, 2025, '2025-10-27', 'due', NULL, NULL),
(6, 2, 3000.00, 11, 2025, '2025-10-27', 'due', NULL, NULL),
(7, 3, 3000.00, 11, 2025, '2025-10-27', 'due', NULL, NULL),
(8, 4, 3000.00, 11, 2025, '2025-10-27', 'due', NULL, NULL),
(9, 5, 3000.00, 11, 2025, '2025-10-27', 'due', NULL, NULL),
(10, 1, 2500.00, 12, 2025, '2025-10-27', 'due', NULL, NULL),
(11, 2, 2500.00, 12, 2025, '2025-10-27', 'due', NULL, NULL),
(12, 3, 2500.00, 12, 2025, '2025-10-27', 'paid', '2025-10-27', 'Online'),
(13, 5, 2500.00, 12, 2025, '2025-10-27', 'due', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `regular_visitors`
--

DROP TABLE IF EXISTS `regular_visitors`;
CREATE TABLE IF NOT EXISTS `regular_visitors` (
  `regular_visitor_id` int NOT NULL AUTO_INCREMENT,
  `flat_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` varchar(50) DEFAULT 'Vendor',
  `security_code` varchar(10) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  PRIMARY KEY (`regular_visitor_id`),
  UNIQUE KEY `security_code` (`security_code`),
  KEY `flat_id` (`flat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `regular_visitors`
--

INSERT INTO `regular_visitors` (`regular_visitor_id`, `flat_id`, `name`, `role`, `security_code`, `status`) VALUES
(1, 1, 'Geeta bai', 'House Help', '6E11FA', 'active'),
(5, 3, 'Maya Devi', 'House Help', '3D2474', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `residents`
--

DROP TABLE IF EXISTS `residents`;
CREATE TABLE IF NOT EXISTS `residents` (
  `resident_id` int NOT NULL AUTO_INCREMENT,
  `flat_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`resident_id`),
  UNIQUE KEY `flat_id` (`flat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `residents`
--

INSERT INTO `residents` (`resident_id`, `flat_id`, `name`) VALUES
(1, 1, 'Amit Sharma'),
(2, 3, 'Priya Singh'),
(3, 2, 'Sunil Rao'),
(4, 5, 'Pranav Prerepa'),
(5, 4, 'Sanjay'),
(6, 6, 'Lasya');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
CREATE TABLE IF NOT EXISTS `staff` (
  `staff_id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `joining_date` date DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`staff_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `name`, `role`, `contact_number`, `joining_date`, `status`) VALUES
(1, 'Ramesh Singh', 'Security Guard', '9876556789', '2025-10-09', 'active'),
(3, 'Gowri', 'Gardener', '9876543201', '2025-09-11', 'active'),
(5, 'Gagan', 'Sweeper', '8492349836', '2025-10-07', 'inactive');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','supervisor','resident') NOT NULL,
  `resident_id` int DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  KEY `resident_id` (`resident_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password_hash`, `role`, `resident_id`) VALUES
(1, 'amit.sharma', '$2y$10$G.t4Jz3q7oasbs6kjUxIH.NGY.8hsaL8VajGAEYjtYLaUm9TzOl0K', 'resident', 1),
(2, 'priya.singh', '$2y$10$ijolU8ZSplF6OYxoGiHPuuI9MS4gwXVC81B46lKtkLnc7/KHqPOuq', 'resident', 2),
(3, 'supervisor1', '$2y$10$eYKs5gDIsubuInRj1vJkiOoUOE3mrvnhkJkvhOPvdeIJilOeF4d9.', 'supervisor', NULL),
(5, 'admin', '$2y$10$bjYPU1WT/W/iCyH5XlaUUeKmTmYsf.RGd0KQMmgBkhNDRHlQL75KG', 'admin', NULL),
(6, 'sunil.rao', '$2y$10$KSHxA/CiJItoPCBJTWEdYOYb8mvWM17gJlgrIMIRHZQa1KIXSNSqe', 'resident', 3),
(7, 'supervisor2', '$2y$10$VRXZ6BdZ6cFeKMB8GmsF/uQvOC3WzNsTCQSXSTjqzlZqQfWzSwoHy', 'supervisor', NULL),
(8, 'pranav.p', '$2y$10$yzVLhRJQI/GaIzR7BDs3AOrEtGOr8jxXCPpQFtELCh8x6VEwW.D5O', 'resident', 4),
(9, 'sanjay', '$2y$10$upQGQdKFH1t..1h5bCKEx..7i5iH.TK9oCEabiSf08Oz.mqjJXsky', 'resident', 5),
(10, 'lasya', '$2y$10$OxBM/2htNgOzc0TOFLUfyOWTFc7HfKeXA5c6/NjtW/Tk5rL0dsZLq', 'resident', 6);

-- --------------------------------------------------------

--
-- Table structure for table `visitors`
--

DROP TABLE IF EXISTS `visitors`;
CREATE TABLE IF NOT EXISTS `visitors` (
  `visitor_id` int NOT NULL AUTO_INCREMENT,
  `flat_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `request_time` datetime DEFAULT CURRENT_TIMESTAMP,
  `check_in_time` datetime DEFAULT NULL,
  `check_out_time` datetime DEFAULT NULL,
  `status` enum('pending','approved','denied','checked_out') NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`visitor_id`),
  KEY `flat_id` (`flat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `visitors`
--

INSERT INTO `visitors` (`visitor_id`, `flat_id`, `name`, `contact_number`, `request_time`, `check_in_time`, `check_out_time`, `status`) VALUES
(1, 1, 'Ravi Kumar', '8887776665', '2025-11-04 14:52:30', '2025-10-20 20:21:49', '2025-11-04 14:57:15', 'checked_out'),
(2, 3, 'Sunita Devi', '8887776660', '2025-11-04 14:52:30', '2025-10-20 18:00:00', '2025-10-20 21:08:50', 'checked_out'),
(3, 3, 'Priya\'s Cousin', '9999988888', '2025-11-04 14:52:30', '2025-10-20 20:30:23', '2025-11-04 14:57:12', 'checked_out'),
(4, 1, 'Radha', '8882838382', '2025-11-04 14:52:30', '2025-10-27 23:24:41', '2025-10-27 23:27:33', 'checked_out'),
(5, 1, 'Tapasya', '2349849817', '2025-11-04 14:52:30', NULL, NULL, 'denied'),
(6, 1, 'Tapasya', '6349849817', '2025-11-04 14:52:30', '2025-11-04 14:48:13', '2025-11-04 14:48:26', 'checked_out'),
(7, 1, 'Tapasya', '6749849817', '2025-11-04 14:57:01', NULL, NULL, 'denied'),
(8, 1, 'rambabu', '9482948484', '2025-11-04 15:00:20', '2025-11-04 15:00:47', '2025-11-04 15:01:03', 'checked_out'),
(9, 4, 'Kartik', '9891999191', '2025-11-04 15:44:25', NULL, NULL, 'pending');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
