-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 09, 2024 at 02:38 AM
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
-- Table structure for table `ArchivedBookings`
--

CREATE TABLE `ArchivedBookings` (
  `BookingID` int(11) NOT NULL,
  `DateArchived` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookingmovers`
--

CREATE TABLE `bookingmovers` (
  `BookingID` int(11) NOT NULL,
  `MoverID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookingmovers`
--

INSERT INTO `bookingmovers` (`BookingID`, `MoverID`) VALUES
(6, 1),
(6, 4),
(6, 5),
(7, 1),
(7, 4),
(7, 5),
(7, 6),
(1, 1),
(1, 11);

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
  `TimeDelivered` datetime DEFAULT NULL,
  `Instructions` text NOT NULL,
  `MoveSize` varchar(32) NOT NULL,
  `MoveWeight` int(11) NOT NULL,
  `Paid` tinyint(1) NOT NULL,
  `TotalCost` decimal(10,0) NOT NULL,
  `CreatedDate` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Bookings`
--

INSERT INTO `Bookings` (`BookingID`, `Date`, `PickupAddress`, `Truck`, `DeliveryAddress`, `PickedUp`, `Delivered`, `BookingCompleted`, `TimePickedUp`, `TimeDelivered`, `Instructions`, `MoveSize`, `MoveWeight`, `Paid`, `TotalCost`, `CreatedDate`) VALUES
(1, '2024-12-08', '5500 180 St, Surrey, BC V3S 6R1', 1, '12666 72 Ave Surrey, BC V3W2M8', 0, 0, 0, NULL, NULL, '', '4-bedroom', 3000, 0, 0, '2024-12-08 15:10:34'),
(2, '2024-12-10', 'address 1', 2, 'address 2', 0, 0, 0, NULL, NULL, '', '', 0, 0, 0, '2024-12-08 12:48:40'),
(3, '2024-12-05', 'address 1', 5, 'addres2', 0, 0, 0, NULL, NULL, 'none', '', 0, 0, 0, '0000-00-00 00:00:00'),
(6, '2024-12-14', '816 Peace Portal Dr, Blaine, wa, 98230', 1, '15263, 85 ave, Surrey, BC, V3S2P5', 0, 0, 0, NULL, NULL, 'hi there', '2-Bed Apartment', 2500, 0, 2415, '2024-12-08 12:48:44'),
(7, '2024-12-18', '15263, 85 ave, Surrey, bc, V3S2P5', 1, '816 Peace Portal Dr, Blaine, wa, 98230', 1, 0, 0, NULL, NULL, 'hi', '3-Bed House', 3500, 0, 3385, '2024-12-08 17:10:12');

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
  `Password` varchar(255) DEFAULT NULL,
  `PhoneNumber` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Customers`
--

INSERT INTO `Customers` (`CustomerID`, `BookingID`, `FirstName`, `LastName`, `Email`, `Password`, `PhoneNumber`) VALUES
(1, 7, 'Arlo', 'Vandelay', 'robert.cot800@gmail.com', '$2y$10$aDTXVNGiUvEHGtunx5okDOxIUGQzcA.4D4SLiZ21l1gUhFh8GOBfy', '');

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
(5, 'Bill', 'Emerson', '2025-12-16', 'Mover', 'billemerson', '$2y$10$f7rNso1szUFjWJv479hb6.TBxnOsyDRlzs5YljS3xNEFm7IehABJe'),
(6, 'Robert', 'Cioata', '2023-11-07', 'Mover', 'robertcioata', '$2y$10$Ue.nozWyvIRMcwjBXKq12OIUlGLsnKZO9K2qVRhb8beIzGC1FgiKC'),
(11, 'Robert', 'Smith', '2024-11-12', 'Mover', 'robertsmith', '$2y$10$YFi83hR5S/0LhDzBum/SmOWheiz7SavNvRhayqzvkHx8ZE0D8rAU2'),
(12, 'yica', 'smith', '2024-12-03', 'Mover', 'yicasmith', '$2y$10$tXBWf4FrT3DpEsQDPFEzTek2W48nD6a2pke4TRVvsm2Bh.rLBHdG2'),
(14, 'Bill', 'Nye', '2011-01-03', 'Mover', 'billnye', '$2y$10$v2mxFwwS//Cbk5/O9/dT6.0dkHK5.qarV9.mFoH.QXiUtR3h.VYIK'),
(15, 'Martha', 'Stewart', '2024-11-25', 'Dispatcher', 'marthastewart', '$2y$10$VQkrJv26ap8hkqOqsrXrpON2UgZjl8U8.l2E2TGMgzw.Vj1JsWm16');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `MessageID` int(11) NOT NULL,
  `CustomerID` int(11) NOT NULL,
  `Message` text NOT NULL,
  `SentAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `IsRead` tinyint(1) DEFAULT 0,
  `Sender` varchar(24) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`MessageID`, `CustomerID`, `Message`, `SentAt`, `IsRead`, `Sender`) VALUES
(4, 1, 'Hi I have a question.', '2024-12-08 19:07:01', 1, 'Customer'),
(5, 1, 'What\'s your question?', '2024-12-09 00:27:55', 1, 'Employee'),
(6, 1, 'How do I select a move?', '2024-12-09 00:32:37', 1, 'Customer'),
(7, 1, 'You just have to go to the Submit a move section and submit a new form.', '2024-12-09 00:34:16', 1, 'Employee'),
(8, 1, 'Then you\'re all good!', '2024-12-09 00:51:12', 1, 'Employee'),
(9, 1, 'Cool, thanks.', '2024-12-09 00:53:16', 1, 'Customer'),
(10, 1, 'Happy to Help!', '2024-12-09 00:56:01', 1, 'Employee');

-- --------------------------------------------------------

--
-- Table structure for table `MoveRequests`
--

CREATE TABLE `MoveRequests` (
  `CustomerID` int(11) NOT NULL,
  `Message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(5, 'Bill Emerson', '778-874-3921', NULL),
(6, 'Robert Cioata', '778-874-3926', NULL),
(11, 'Robert Smith', '778-999-9871', NULL),
(12, 'yica smith', '778-872-8878', 'Very strong'),
(14, 'Bill Nye', '221-765-8921', 'Good at problem solving');

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
-- Indexes for table `ArchivedBookings`
--
ALTER TABLE `ArchivedBookings`
  ADD KEY `fk_archivedbookings_bookingid` (`BookingID`);

--
-- Indexes for table `bookingmovers`
--
ALTER TABLE `bookingmovers`
  ADD KEY `fk_mover` (`MoverID`),
  ADD KEY `fk_booking` (`BookingID`);

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
  ADD KEY `fk__bookingid` (`BookingID`);

--
-- Indexes for table `Employees`
--
ALTER TABLE `Employees`
  ADD PRIMARY KEY (`EmployeeID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`MessageID`),
  ADD KEY `messages_ibfk_1` (`CustomerID`);

--
-- Indexes for table `MoveRequests`
--
ALTER TABLE `MoveRequests`
  ADD KEY `fk_move_requests_customer` (`CustomerID`);

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
  MODIFY `BookingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `Employees`
--
ALTER TABLE `Employees`
  MODIFY `EmployeeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `MessageID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `Movers`
--
ALTER TABLE `Movers`
  MODIFY `MoverID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `Trucks`
--
ALTER TABLE `Trucks`
  MODIFY `TruckID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ArchivedBookings`
--
ALTER TABLE `ArchivedBookings`
  ADD CONSTRAINT `fk_archivedbookings_bookingid` FOREIGN KEY (`BookingID`) REFERENCES `Bookings` (`BookingID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bookingmovers`
--
ALTER TABLE `bookingmovers`
  ADD CONSTRAINT `fk_booking` FOREIGN KEY (`BookingID`) REFERENCES `bookings` (`BookingID`) ON DELETE CASCADE ON UPDATE CASCADE,
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
  ADD CONSTRAINT `fk__bookingid` FOREIGN KEY (`BookingID`) REFERENCES `Bookings` (`BookingID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_customer_booking` FOREIGN KEY (`BookingID`) REFERENCES `Bookings` (`BookingID`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`CustomerID`) REFERENCES `customers` (`CustomerID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `MoveRequests`
--
ALTER TABLE `MoveRequests`
  ADD CONSTRAINT `fk_move_requests_customer` FOREIGN KEY (`CustomerID`) REFERENCES `Customers` (`CustomerID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
