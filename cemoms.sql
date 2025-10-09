-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2025 at 02:23 PM
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
-- Database: `cemoms`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `email`, `username`, `password`) VALUES
(1, 'admin@gmail.com', 'admin', '$2y$10$e7adDD/6LKNoAjrCInxVC.WyR.1dUP9LUGmP6tjbnSoyBPlm6RV3O');

-- --------------------------------------------------------

--
-- Table structure for table `foreman`
--

CREATE TABLE `foreman` (
  `foreman_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `foreman_role_id` int(11) DEFAULT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `foreman`
--

INSERT INTO `foreman` (`foreman_id`, `email`, `username`, `foreman_role_id`, `password`) VALUES
(1, 'john.doe@example.com', 'John Doe', 1, '$2y$10$1K2GQfK4IZwKYsqSyq3JTepgiOCkr8tfaZwDPASG8RrD75wx6vONa'),
(2, 'maria.santos@example.com', 'Maria Santos', 2, '$2y$10$zcjY6m434E3uwMfW36rsuOMz4dZjBTQwnyAOPlf834i0uRGzo/xhi'),
(3, 'carlos.reyes@example.com', 'Carlos Reyes', 3, '$2y$10$QW1SssQW2BGrwRBijFjIaeyyw/j6TzXErlq5Q5KdyKa08wPcKgK1q'),
(4, 'anna.delacruz@example.com', 'Anna Dela Cruz', 4, '$2y$10$Y9Ci/5elthh5gMsLMtkG5OziE5lK28Zrc6D/Dy3XwMkLwUxQ5/Iw6'),
(5, 'james.will@example.com', 'James Will', 5, '$2y$10$EEDeABvh46Z7t1E4/oLod.AFGmHTdJ7hfLma/VX/FLVI6kRHHUvc.'),
(6, 'william.smith@gmail.com', 'William Smith', 1, '$2y$10$Pj4chaPBCwCy.Kzw78DLUuCahjANMxnnq6fq2duNxG3v4ye.PcYbO');

-- --------------------------------------------------------

--
-- Table structure for table `foreman_role`
--

CREATE TABLE `foreman_role` (
  `foreman_role_id` int(11) NOT NULL,
  `role_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `foreman_role`
--

INSERT INTO `foreman_role` (`foreman_role_id`, `role_name`) VALUES
(1, 'Garbage Collection'),
(2, 'Street Sweeping'),
(3, 'Flushing'),
(4, 'De-clogging'),
(5, 'Cleanup Drives');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `foreman`
--
ALTER TABLE `foreman`
  ADD PRIMARY KEY (`foreman_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `foreman_role_id` (`foreman_role_id`);

--
-- Indexes for table `foreman_role`
--
ALTER TABLE `foreman_role`
  ADD PRIMARY KEY (`foreman_role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `foreman`
--
ALTER TABLE `foreman`
  MODIFY `foreman_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `foreman_role`
--
ALTER TABLE `foreman_role`
  MODIFY `foreman_role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `foreman`
--
ALTER TABLE `foreman`
  ADD CONSTRAINT `foreman_ibfk_1` FOREIGN KEY (`foreman_role_id`) REFERENCES `foreman_role` (`foreman_role_id`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
