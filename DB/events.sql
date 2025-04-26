-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2025 at 02:28 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `juanjc5_pinn`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `event_title` varchar(255) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `price_note` text DEFAULT NULL,
  `event_picture` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `price_unit` decimal(10,2) NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `township` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `property_id` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `event_title`, `currency`, `price_note`, `event_picture`, `start_date`, `end_date`, `created_at`, `price_unit`, `user_id`, `township`, `city`, `property_id`) VALUES
(7, 'Music Concert update ', 'itm_curency19f344a0ffbd61adb63eda8a32486b4d', 'update orince note', 'uploads/events/1745626745_Hire-Freelancers-Remote-Workers-For-Free-04-23-2025_07_40_PM_(1).png', '2025-05-01', '2025-05-01', '2025-04-25 23:37:56', '10.00', 'c4ca4238a0b923820dcc509a6f75849b', 'itm_town3ee6f65c8c6972d9f5c62297b916444d', 'itm_loccae2b2eb8e9901d7e81ec8ad43c890ac', 'property5cd9a385e576decd7a42860864d8f227'),
(8, 'Music Concert', 'itm_curency19f344a0ffbd61adb63eda8a32486b4d', 'price unit note', NULL, '2025-05-01', '2025-05-01', '2025-04-26 00:27:29', '10.00', 'c4ca4238a0b923820dcc509a6f75849b', 'itm_town3ee6f65c8c6972d9f5c62297b916444d', 'itm_loccae2b2eb8e9901d7e81ec8ad43c890ac', 'property5cd9a385e576decd7a42860864d8f227');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
