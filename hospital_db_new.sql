-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 11, 2026 at 07:18 PM
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
-- Database: `hospital_db_new`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `status` varchar(50) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `date`, `status`) VALUES
(1, 7, 2, '2026-03-24 15:30:00', 'confirmed'),
(2, 5, 4, '2026-04-10 20:00:00', 'confirmed'),
(3, 4, 2, '2026-03-31 17:26:00', 'confirmed'),
(4, 1, 2, '2026-04-01 15:30:00', 'confirmed');

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE `billing` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `status` enum('paid','unpaid') DEFAULT 'unpaid',
  `date` datetime DEFAULT NULL,
  `doctor_fee` int(11) DEFAULT 0,
  `medicine_fee` int(11) DEFAULT 0,
  `room_fee` int(11) DEFAULT 0,
  `other_fee` int(11) DEFAULT 0,
  `total_amount` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billing`
--

INSERT INTO `billing` (`id`, `patient_id`, `appointment_id`, `amount`, `status`, `date`, `doctor_fee`, `medicine_fee`, `room_fee`, `other_fee`, `total_amount`) VALUES
(1, 1, NULL, NULL, 'paid', NULL, 500, 1000, 0, 0, 1500),
(2, 1, NULL, NULL, 'unpaid', NULL, 500, 0, 0, 0, 500),
(3, 5, NULL, NULL, 'unpaid', NULL, 500, 0, 0, 0, 500),
(4, 6, NULL, NULL, 'paid', NULL, 500, 0, 0, 0, 500),
(5, 2, NULL, NULL, 'paid', NULL, 500, 0, 0, 0, 500),
(6, 4, NULL, NULL, 'unpaid', NULL, 500, 0, 0, 0, 500);

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `blood_group` varchar(10) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `weight` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'active',
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `name`, `age`, `gender`, `phone`, `address`, `blood_group`, `height`, `weight`, `date`, `time`, `status`, `is_deleted`) VALUES
(1, 'Sagar salve', 20, 'Male', '8767275674', 'ramanagar, chh sambhaji nagr', 'None', 170, 80, '2026-03-22', '15:06:00', 'active', 0),
(2, 'rohan sonwane', 20, 'Male', '7498932550', 'sindhi collony, sambhaji nagar.', NULL, 170, 100, '2026-03-22', '21:30:00', 'active', 0),
(3, 'rushikesh ambekar', 20, 'Male', '0000000000', 'osmanpura, chh sambhaji nagar.', NULL, 180, 120, '2026-04-01', '15:00:00', 'active', 0),
(4, 'shubham sonpasare ', 30, 'Male', '8956788384', 'partur, aurangabad', 'A+', 165, 80, '2026-04-09', '17:30:00', 'active', 0),
(5, 'rohan patil', 23, 'Male', '8767275674', 'balaji nagar sindhi coloni, chh sambhaji nagar', 'A+', 165, 120, '2026-03-23', '03:50:00', 'active', 0),
(6, 'gaurav kirtikar', 20, 'Male', '7498932550', 'balaji nagar', 'None', 170, 90, '2026-03-23', '03:15:00', 'active', 0),
(7, 'priyanka mali', 19, 'Female', '8956788384', 'shreyanagar, chh sambhaji nagar', 'O+', 164, 50, '2026-03-24', '15:30:00', 'active', 0),
(8, 'rohan sadavate', 51, 'Male', '8956788384', 'nagedadi, jalana', 'None', 165, 80, '2026-03-31', '12:00:00', 'active', 0);

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) DEFAULT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `doctor_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`id`, `patient_id`, `appointment_id`, `doctor_id`, `notes`, `created_at`) VALUES
(1, 1, 4, 2, 'vomiting,acidity and stomach pain', '2026-03-31 22:22:19'),
(2, 7, 1, 2, 'blood pressure , cold and flu', '2026-04-01 13:30:27'),
(3, 4, 3, 2, 'white blood , fever , vomiting , loose motion', '2026-04-01 13:38:53');

-- --------------------------------------------------------

--
-- Table structure for table `prescription_items`
--

CREATE TABLE `prescription_items` (
  `id` int(11) NOT NULL,
  `prescription_id` int(11) NOT NULL,
  `medicine_name` varchar(200) NOT NULL,
  `dosage` varchar(100) DEFAULT NULL,
  `frequency` varchar(100) DEFAULT NULL,
  `duration` varchar(100) DEFAULT NULL,
  `instructions` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescription_items`
--

INSERT INTO `prescription_items` (`id`, `prescription_id`, `medicine_name`, `dosage`, `frequency`, `duration`, `instructions`) VALUES
(2, 1, 'emeset 4 mg', '2', 'Twice daily', '5', 'before meal'),
(3, 1, 'omee-d', '2', 'Twice daily', '5', 'before meal'),
(4, 1, 'meftal spas', '2', 'Twice daily', '5', 'after meal'),
(5, 2, 'paracetemol  650 mg', '2', 'Once daily', '2', 'after meal'),
(6, 2, 'telma 80', '10', 'Once daily', '10', 'before meal'),
(7, 2, 'sinarest', '2', 'Once daily', '2', 'after meal'),
(8, 2, 'DSR', '1', 'Once daily', '1', 'before meal'),
(9, 3, 'O2 tab', '6', 'Twice daily', '3', 'after meal'),
(10, 3, 'vomiting 4 mg', '6', 'Twice daily', '3', 'after meal'),
(11, 3, 'calpol 650 mg', '10', 'Twice daily', '10', 'before meal'),
(12, 3, 'clavam 625 mg', '6', 'Twice daily', '3', 'after meal'),
(13, 3, 'omez-d', '3', 'Once daily', '3', 'before meal');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('Admin','Doctor') NOT NULL DEFAULT 'Doctor',
  `fee` int(11) DEFAULT 500,
  `consultation_fee` decimal(10,2) DEFAULT 500.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `fee`, `consultation_fee`) VALUES
(1, 'Admin', 'admin@gmail.com', '1234', 'Admin', 500, 500.00),
(2, 'Doctor', 'doctor@gmail.com', '1234', 'Doctor', 500, 500.00),
(4, 'Dr. Sharma', 'sharma@lifecare.com', 'doc123', 'Doctor', 500, 500.00);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `prescription_items`
--
ALTER TABLE `prescription_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `prescription_id` (`prescription_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `prescription_items`
--
ALTER TABLE `prescription_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `billing`
--
ALTER TABLE `billing`
  ADD CONSTRAINT `billing_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `billing_ibfk_2` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `prescriptions_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `prescription_items`
--
ALTER TABLE `prescription_items`
  ADD CONSTRAINT `pitem_ibfk_1` FOREIGN KEY (`prescription_id`) REFERENCES `prescriptions` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
