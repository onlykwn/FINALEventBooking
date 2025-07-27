-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 27, 2025 at 05:13 PM
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
-- Database: `ebookingsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `facility_id` int(11) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `time_in` time DEFAULT NULL,
  `time_out` time DEFAULT NULL,
  `booked_by` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'Booked',
  `group_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `facility_id`, `start_date`, `end_date`, `time_in`, `time_out`, `booked_by`, `description`, `status`, `group_id`, `user_id`) VALUES
(2, 12, '2025-07-28', '2025-07-28', '17:52:00', '18:52:00', 'FDSFD', 'SFSFDSF', 'Booked', NULL, 0),
(5, 13, '2025-07-29', '2025-07-29', '20:53:00', '21:53:00', 'GFH', 'HGFHFGH', 'Available', NULL, 0),
(10, 19, '2025-07-28', '2025-07-28', '08:00:00', '10:00:00', 'LLKK', 'IKIJL', 'Booked', NULL, 0),
(12, 17, '2025-08-01', '2025-08-01', '10:30:00', '12:30:00', 'EDRFHD', 'HDRFHDHFDH', 'Booked', NULL, 0),
(13, 17, '2025-08-02', '2025-08-02', '10:30:00', '12:30:00', 'EDRFHD', 'HDRFHDHFDH', 'Booked', NULL, 0),
(14, 17, '2025-08-03', '2025-08-03', '10:30:00', '12:30:00', 'EDRFHD', 'HDRFHDHFDH', 'Booked', NULL, 0),
(15, 17, '2025-08-04', '2025-08-04', '10:30:00', '12:30:00', 'EDRFHD', 'HDRFHDHFDH', 'Booked', NULL, 0),
(16, 17, '2025-08-05', '2025-08-05', '10:30:00', '12:30:00', 'EDRFHD', 'HDRFHDHFDH', 'Booked', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `facilities`
--

CREATE TABLE `facilities` (
  `facility_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `facilities`
--

INSERT INTO `facilities` (`facility_id`, `name`, `location`) VALUES
(11, 'Multi Media Room', ''),
(12, 'Pavilion', ''),
(13, 'Mansaka Lobby', ''),
(14, 'Mandaya Lobby', ''),
(15, 'TED Library', 'Conference Room'),
(16, 'TED Library', 'Fourth Floor'),
(17, 'MAC Lab/MT201', 'T Boli Second Floor'),
(18, 'COM Lab/TL204', 'T Boli Second Floor'),
(19, 'RSM Events Center', 'Gate 1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','staff','student') NOT NULL,
  `status` varchar(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `status`) VALUES
(1, 'Admin', '$2y$10$WqYY4b9UYo9U9M0sCdfI2uDJ6KZPZrIXuTh8feyk9LKt0W4kSmSva', 'admin', 'approved'),
(8, 'EsoStaff', '$2y$10$ilHcsKKGAzsy.FjEuJmj8u4Wb/9dzxf9.Boykhoa.HvCyWiLwjKXy', 'staff', 'approved'),
(13, 'QueenStaff', '$2y$10$PkIV5UXU.dtJXjFhPIiTcuDHgYa4D9.xySRm4SGB/gvQ5rX7GWjbG', 'staff', 'pending'),
(14, 'Queenie', '$2y$10$Ibqe4URLrTn9yCCCs4ZZ8uFtWvnPaPQrOpmKtS2GE1hGay2g6vPZK', 'student', 'approved');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `facility_id` (`facility_id`);

--
-- Indexes for table `facilities`
--
ALTER TABLE `facilities`
  ADD PRIMARY KEY (`facility_id`);

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
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `facilities`
--
ALTER TABLE `facilities`
  MODIFY `facility_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`facility_id`) REFERENCES `facilities` (`facility_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
