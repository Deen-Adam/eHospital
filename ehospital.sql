-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Oct 24, 2016 at 03:02 PM
-- Server version: 10.1.10-MariaDB
-- PHP Version: 5.6.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ehospital`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(10) NOT NULL,
  `admin_fullname` varchar(50) NOT NULL,
  `admin_email` varchar(50) NOT NULL,
  `admin_password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `admin_fullname`, `admin_email`, `admin_password`) VALUES
(1, 'Ameen', 'amin@gmail.com', '123456'),
(2, 'faisal', 'faisal@gmail.com', '123456'),
(3, 'deen', 'deen@gmail.com', '123456');

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `appoint_id` int(10) NOT NULL,
  `appoint_date` date NOT NULL,
  `appoint_time` time NOT NULL,
  `patient_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`appoint_id`, `appoint_date`, `appoint_time`, `patient_id`) VALUES
(231, '2016-11-17', '11:33:00', 459),
(232, '2016-10-28', '20:15:00', 460),
(233, '2016-11-15', '06:30:00', 461),
(234, '2016-11-07', '16:00:00', 462),
(235, '2016-11-08', '07:30:00', 463);

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

CREATE TABLE `employee` (
  `employee_id` int(20) NOT NULL,
  `employee_fname` varchar(20) NOT NULL,
  `employee_lname` varchar(20) NOT NULL,
  `employee_email` varchar(50) NOT NULL,
  `employee_password` varchar(20) NOT NULL,
  `employee_type` varchar(20) NOT NULL,
  `employee_address` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`employee_id`, `employee_fname`, `employee_lname`, `employee_email`, `employee_password`, `employee_type`, `employee_address`) VALUES
(1, 'Deen', 'Adam', 'deen@gmail.com', '12345', 'lab scientist', 'KL'),
(2, 'Dr. Imam', 'Muhammad', 'imam@yahoo.com', '123456', 'physician', 'Pennsylvania US'),
(3, 'Husna', 'Muhammad', 'husna@gmail.com', '12345', 'pharmacy', 'Kuala Lumpur'),
(4, 'Hibbatullah', 'Nuraddeen', 'hibat@yahoo.com', '123456', 'Phycisian', 'KSA'),
(5, 'Fulani', 'Abdul', 'fulaninnmu@yahoo.com', '123456', 'lab scientist', 'Abuja Nigeria');

-- --------------------------------------------------------

--
-- Table structure for table `medical_result`
--

CREATE TABLE `medical_result` (
  `result_id` int(10) NOT NULL,
  `result_title` varchar(50) NOT NULL,
  `result_date` date NOT NULL,
  `patient_id` int(10) NOT NULL,
  `employee_id` int(10) NOT NULL,
  `prescription_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `medical_result`
--

INSERT INTO `medical_result` (`result_id`, `result_title`, `result_date`, `patient_id`, `employee_id`, `prescription_id`) VALUES
(1, 'negative', '2016-11-04', 459, 1, 1),
(2, 'positive', '2016-11-15', 460, 5, 2),
(3, '45/60', '2016-11-10', 461, 5, 3),
(4, 'O', '2016-10-31', 462, 1, 4),
(6, 'AA+', '2016-10-27', 459, 4, 6),
(8, 'negative', '2016-11-26', 459, 1, 8),
(457, 'No Test Required', '2016-10-30', 463, 2, 5);

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `patient_id` int(10) NOT NULL,
  `name` varchar(20) NOT NULL,
  `lastname` varchar(20) NOT NULL,
  `gender` varchar(20) NOT NULL,
  `password` varchar(80) NOT NULL,
  `email` varchar(50) NOT NULL,
  `address` varchar(50) NOT NULL,
  `birthday` date NOT NULL,
  `confirmcode` varchar(100) NOT NULL,
  `joined_date` date NOT NULL,
  `imageUrl` varchar(200) NOT NULL,
  `salt` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`patient_id`, `name`, `lastname`, `gender`, `password`, `email`, `address`, `birthday`, `confirmcode`, `joined_date`, `imageUrl`, `salt`) VALUES
(459, 'Faisal', 'Muhammad', 'Male', 'XQUKOm0AKI/PFRHqs4gJRseI9+A5OGNiMzRkNDI1', 'faisal_muhammad@gmail.com', '106, Cyberjaya', '1994-09-27', 'f877052b3dded36a6fbee88cd17e3cc1', '2016-10-24', '', '98cb34d425'),
(460, 'Kalala', 'Kalalatu', 'Female', 'cuMxa+M3Afz8gqbftdE4d6yZT8M5OTViNjYyMTY4', 'kalala@yahoo.com', '11 Kwantagora, USA', '1988-10-24', '6d5a776cb3f65423c6f34f974f71de17', '2016-10-24', '', '995b662168'),
(461, 'Amina', 'Umaru', 'Female', 'g2dWj1QJDenHuOWUzFlK/irPWHo5ZWIzNGI2ODZm', 'meenana@yahoo.com', 'Lamba 1, Kasar Bauchi', '1996-07-22', '10c4b148de945ce4fb0738fd3ca1fc84', '2016-10-24', '', '9eb34b686f'),
(462, 'Asma''u', 'Abba', 'Female', '72yS9zeMcoYTxrE6p2TuKJsSip5iNzE4YjgxNTRj', 'aagaladanchi@hotmail.com', '12 Lautai House, Kano', '2000-10-22', 'c21191d82f9da5f260fbc18ad1e56e3a', '2016-10-24', '', 'b718b8154c'),
(463, 'Imadeeddeen', 'Ahmad', 'Male', 'bcL9GfR/RzdlqNZ9hjfej9yqgMNhMDFhZjY1NzA5', 'imad@yahoo.com', 'Nicosia Cyprus', '1999-02-09', '20d8688d6124b40960e5965dc0e29144', '2016-10-24', '', 'a01af65709');

-- --------------------------------------------------------

--
-- Table structure for table `prescription`
--

CREATE TABLE `prescription` (
  `prescription_id` int(10) NOT NULL,
  `prescription_type` varchar(20) NOT NULL,
  `prescription_title` varchar(50) NOT NULL,
  `prescription_date` date NOT NULL,
  `employee_id` int(10) NOT NULL,
  `patient_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `prescription`
--

INSERT INTO `prescription` (`prescription_id`, `prescription_type`, `prescription_title`, `prescription_date`, `employee_id`, `patient_id`) VALUES
(1, 'Test', 'Malaria', '2016-10-14', 2, 459),
(2, 'Test', 'Typhoid', '2016-10-12', 4, 460),
(3, 'Test', 'Blood count', '2016-10-03', 2, 461),
(4, 'Test', 'Blood type', '2016-10-03', 4, 462),
(5, 'Drug', 'Cafenol', '2016-10-29', 2, 463),
(6, 'Test', 'Genotype', '2016-10-28', 2, 459),
(7, 'Drug', 'Pakalyn with Codeine', '2016-11-22', 2, 459),
(8, 'Test', 'HIV', '2016-10-12', 4, 459),
(9, 'Drug', 'Tramol', '2016-11-06', 2, 459);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`appoint_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `appoint_id` (`appoint_id`);

--
-- Indexes for table `employee`
--
ALTER TABLE `employee`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `employee_email` (`employee_email`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `medical_result`
--
ALTER TABLE `medical_result`
  ADD PRIMARY KEY (`result_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `prescription_id` (`prescription_id`),
  ADD KEY `test_id` (`result_id`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`patient_id`),
  ADD UNIQUE KEY `patient_email` (`email`),
  ADD KEY `patiend_id` (`patient_id`);

--
-- Indexes for table `prescription`
--
ALTER TABLE `prescription`
  ADD PRIMARY KEY (`prescription_id`),
  ADD KEY `prescription_id` (`prescription_id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `appoint_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=236;
--
-- AUTO_INCREMENT for table `employee`
--
ALTER TABLE `employee`
  MODIFY `employee_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT for table `medical_result`
--
ALTER TABLE `medical_result`
  MODIFY `result_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=458;
--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `patient_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=464;
--
-- AUTO_INCREMENT for table `prescription`
--
ALTER TABLE `prescription`
  MODIFY `prescription_id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12346;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `p_key` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`patient_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `medical_result`
--
ALTER TABLE `medical_result`
  ADD CONSTRAINT `epl_fk` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `prescrip_fk` FOREIGN KEY (`prescription_id`) REFERENCES `prescription` (`prescription_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `pt_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`patient_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `prescription`
--
ALTER TABLE `prescription`
  ADD CONSTRAINT `emp_fk` FOREIGN KEY (`employee_id`) REFERENCES `employee` (`employee_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `patient_fk` FOREIGN KEY (`patient_id`) REFERENCES `patient` (`patient_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
