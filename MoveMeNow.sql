-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 06, 2024 at 08:12 AM
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
-- Table structure for table `BookingMovers`
--

CREATE TABLE `BookingMovers` (
  `BookingID` int(11) NOT NULL,
  `MoverID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `BookingMovers`
--

INSERT INTO `BookingMovers` (`BookingID`, `MoverID`) VALUES
(1, 1),
(1, 4),
(2, 1),
(3, 4),
(3, 5);

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
  `BookingCompleted` tinyint(1) DEFAULT 0,
  `TimePickedUp` datetime DEFAULT NULL,
  `TimeDelivered` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Bookings`
--

INSERT INTO `Bookings` (`BookingID`, `Date`, `PickupAddress`, `Truck`, `DeliveryAddress`, `PickedUp`, `Delivered`, `BookingCompleted`, `TimePickedUp`, `TimeDelivered`) VALUES
(1, '2024-12-07', '5500 180 St, Surrey, BC V3S 6R1', 1, '12666 72 Ave Surrey, BC V3W2M8', 1, 1, 0, NULL, NULL),
(2, '2024-12-07', 'address 1', 2, 'address 2', 1, 1, 0, NULL, NULL),
(3, '2024-12-05', 'address 1', 5, 'addres2', 0, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Customers`
--

CREATE TABLE `Customers` (
  `CustomerID` int(11) NOT NULL,
  `BookingID` int(11) DEFAULT NULL,
  `FirstName` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Customers`
--

INSERT INTO `Customers` (`CustomerID`, `BookingID`, `FirstName`, `LastName`, `Email`, `Password`) VALUES
(1, 1, 'Arlo', 'Vandelay', 'Arlo@email.com', '$2y$10$aDTXVNGiUvEHGtunx5okDOxIUGQzcA.4D4SLiZ21l1gUhFh8GOBfy');

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
(2, 'Jane', 'Smith', '2021-02-20', 'Manager', 'JaneSmith', '$2y$10$hXjwyZeWLDHwVRfXryJdiu/SVntijTdxWJDPZmu/dii313P2FwcTi'),
(4, 'James', 'Smith', '2024-12-04', 'Mover', 'jamessmith', '$2y$10$tGLiogktA7vqPb6zYnO3tOC1Vt.6wNSDjTlKY3Wy3L8MHIWp12CkW'),
(5, 'Bill', 'Emerson', '2025-12-16', 'Mover', 'billemerson', '$2y$10$f7rNso1szUFjWJv479hb6.TBxnOsyDRlzs5YljS3xNEFm7IehABJe');

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
(1, 'John Doe', '778-555-5555', 'Experienced with Fragile Objects'),
(4, 'James Smith', '778-882-8888', 'Experienced with Large Furniture'),
(5, 'Bill Emerson', '778-874-3921', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `Trucks`
--

CREATE TABLE `Trucks` (
  `TruckID` int(11) NOT NULL,
  `LicensePlate` varchar(20) NOT NULL,
  `SizeInFeet` int(11) NOT NULL,
  `Make` varchar(16) NOT NULL,
  `Model` varchar(24) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Trucks`
--

INSERT INTO `Trucks` (`TruckID`, `LicensePlate`, `SizeInFeet`, `Make`, `Model`) VALUES
(1, 'AB1234', 26, 'Ford', 'F-750 Box Truck'),
(2, 'CD5678', 20, 'Ford', 'E-450 Box Truck'),
(3, 'EF9012', 26, 'Ford', 'F-750 Box Truck'),
(4, 'GH3456', 20, 'Ford', 'E-450 Box Truck'),
(5, 'IJ7890', 20, 'Ford', 'E-450 Box Truck'),
(6, 'KL1234', 20, 'Ford', 'E-450 Box Truck'),
(7, 'MN5678', 20, 'Ford', 'E-450 Box Truck'),
(8, 'OP9012', 20, 'Ford', 'E-450 Box Truck'),
(9, 'QR3456', 20, 'Ford', 'E-450 Box Truck'),
(10, 'ST7890', 20, 'Ford', 'E-450 Box Truck');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `BookingMovers`
--
ALTER TABLE `BookingMovers`
  ADD KEY `fk_booking` (`BookingID`),
  ADD KEY `fk_mover` (`MoverID`);

--
-- Indexes for table `Bookings`
--
ALTER TABLE `Bookings`
  ADD PRIMARY KEY (`BookingID`),
  ADD KEY `fk_truck` (`Truck`);

--
-- Indexes for table `Customers`
--
ALTER TABLE `Customers`
  ADD PRIMARY KEY (`CustomerID`),
  ADD KEY `fk_booking_customer` (`BookingID`);

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
  MODIFY `BookingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `Customers`
--
ALTER TABLE `Customers`
  MODIFY `CustomerID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Employees`
--
ALTER TABLE `Employees`
  MODIFY `EmployeeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Movers`
--
ALTER TABLE `Movers`
  MODIFY `MoverID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Trucks`
--
ALTER TABLE `Trucks`
  MODIFY `TruckID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `BookingMovers`
--
ALTER TABLE `BookingMovers`
  ADD CONSTRAINT `fk_booking` FOREIGN KEY (`BookingID`) REFERENCES `Bookings` (`BookingID`),
  ADD CONSTRAINT `fk_mover` FOREIGN KEY (`MoverID`) REFERENCES `Movers` (`MoverID`);

--
-- Constraints for table `Bookings`
--
ALTER TABLE `Bookings`
  ADD CONSTRAINT `fk_truck` FOREIGN KEY (`Truck`) REFERENCES `Trucks` (`TruckID`);

--
-- Constraints for table `Customers`
--
ALTER TABLE `Customers`
  ADD CONSTRAINT `fk_booking_customer` FOREIGN KEY (`BookingID`) REFERENCES `Bookings` (`BookingID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
