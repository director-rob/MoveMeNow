-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 03, 2024 at 09:27 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `MoveMeNow`
--

-- --------------------------------------------------------

--
-- Table structure for table `Bookings`
--

CREATE TABLE `Bookings` (
  `BookingID` int(11) NOT NULL,
  `Date` date NOT NULL,
  `PickupAddress` varchar(255) NOT NULL,
  `Truck` int(11) NOT NULL,
  `DeliveryAddress` varchar(255) NOT NULL,
  `PickedUp` tinyint(1) DEFAULT 0,
  `Delivered` tinyint(1) DEFAULT 0,
  `BookingCompleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Bookings`
--

INSERT INTO `Bookings` (`BookingID`, `Date`, `PickupAddress`, `Truck`, `DeliveryAddress`, `PickedUp`, `Delivered`, `BookingCompleted`) VALUES
(1, '2024-12-07', '5500 180 St, Surrey, BC V3S 6R1', 1, '12666 72 Ave Surrey, BC V3W2M8', 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `Employees`
--

CREATE TABLE `Employees` (
  `EmployeeID` int(11) NOT NULL,
  `FirstName` varchar(100) NOT NULL,
  `LastName` varchar(100) NOT NULL,
  `DateJoined` date NOT NULL,
  `Role` varchar(50) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Employees`
--

INSERT INTO `Employees` (`EmployeeID`, `FirstName`, `LastName`, `DateJoined`, `Role`, `Username`, `Password`) VALUES
(1, 'John', 'Doe', '2023-06-15', 'Mover', 'JohnDoe', '$2y$10$sokiU9ihGyIkpHaNUGjtSOexHZvvYcNsECCcyooKCvRPmD/cktbN6'),
(2, 'Jane', 'Smith', '2021-02-20', 'Manager', 'JaneSmith', '$2y$10$fZWIWbhj2G/6kSqxzaOwoumD60l.Ra/rkTCyDExSENFhh8z6KZaui');

-- --------------------------------------------------------

--
-- Table structure for table `Movers`
--

CREATE TABLE `Movers` (
  `MoverID` int(11) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `ContactInfo` varchar(255) DEFAULT NULL,
  `OtherDetails` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Movers`
--

INSERT INTO `Movers` (`MoverID`, `Name`, `ContactInfo`, `OtherDetails`) VALUES
(1, 'John Doe', '778-555-5555', 'Experienced with Fragile Objects');

-- --------------------------------------------------------

--
-- Table structure for table `Trucks`
--

CREATE TABLE `Trucks` (
  `TruckID` int(11) NOT NULL,
  `LicensePlate` varchar(20) NOT NULL,
  `SizeInFeet` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Bookings`
--
ALTER TABLE `Bookings`
  ADD PRIMARY KEY (`BookingID`);

--
-- Indexes for table `Employees`
--
ALTER TABLE `Employees`
  ADD PRIMARY KEY (`EmployeeID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- Indexes for table `Movers`
--
ALTER TABLE `Movers`
  ADD PRIMARY KEY (`MoverID`);

--
-- Indexes for table `Trucks`
--
ALTER TABLE `Trucks`
  ADD PRIMARY KEY (`TruckID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Bookings`
--
ALTER TABLE `Bookings`
  MODIFY `BookingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Employees`
--
ALTER TABLE `Employees`
  MODIFY `EmployeeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Movers`
--
ALTER TABLE `Movers`
  MODIFY `MoverID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Trucks`
--
ALTER TABLE `Trucks`
  MODIFY `TruckID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
