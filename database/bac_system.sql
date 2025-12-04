-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 04, 2025 at 04:01 PM
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
-- Database: `bac_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `module` varchar(50) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `module`, `ip_address`, `created_at`) VALUES
(1, 1, 'Added supplier: Sillicon Valley', 'Suppliers', '::1', '2025-12-04 14:02:40'),
(2, 1, 'Uploaded document for Sillicon Valley', 'Documents', '::1', '2025-12-04 14:03:05'),
(3, 1, 'Added new record: PE 23-423', 'Records', '::1', '2025-12-04 14:18:25'),
(4, 1, 'Deleted supplier: ', 'Suppliers', '::1', '2025-12-04 14:21:54'),
(5, 1, 'Added new record: PE 23-423', 'Records', '::1', '2025-12-04 14:23:01'),
(6, 1, 'Added new record: PE 23-023', 'Records', '::1', '2025-12-04 14:34:22'),
(7, 1, 'Deleted supplier: ', 'Suppliers', '::1', '2025-12-04 14:34:29'),
(8, 1, 'Uploaded BAC document for PE 23-023', 'BAC Documents', '::1', '2025-12-04 14:38:24'),
(9, 1, 'Updated BAC document for PE 23-023', 'BAC Documents', '::1', '2025-12-04 14:38:48'),
(10, 1, 'Deleted BAC document for PE 23-023', 'BAC Documents', '::1', '2025-12-04 14:39:12'),
(11, 1, 'Uploaded BAC document for PE 23-023', 'BAC Documents', '::1', '2025-12-04 14:39:39'),
(12, 1, 'Uploaded BAC document for PE 23-023', 'BAC Documents', '::1', '2025-12-04 14:42:02'),
(13, 1, 'Deleted BAC document for PE 23-023', 'BAC Documents', '::1', '2025-12-04 14:42:23'),
(14, 1, 'Deleted BAC document for PE 23-023', 'BAC Documents', '::1', '2025-12-04 14:42:25'),
(15, 1, 'Uploaded BAC document for PE 23-023', 'BAC Documents', '::1', '2025-12-04 14:42:44'),
(16, 1, 'Added new record: PE 23-093', 'Records', '::1', '2025-12-04 14:51:52');

-- --------------------------------------------------------

--
-- Table structure for table `bac_documents`
--

CREATE TABLE `bac_documents` (
  `id` int(11) NOT NULL,
  `bac_record_id` int(11) NOT NULL,
  `doc_type_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `issued_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `status` enum('Valid','Expired','For Renewal','Missing') DEFAULT 'Missing',
  `uploaded_by` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bac_documents`
--

INSERT INTO `bac_documents` (`id`, `bac_record_id`, `doc_type_id`, `file_name`, `file_path`, `issued_date`, `expiry_date`, `status`, `uploaded_by`, `uploaded_at`, `updated_at`) VALUES
(4, 1, 7, 'HAXM error.png', 'uploads/bac_doc_1_7_1764859364.png', '2025-12-04', '2026-12-04', 'Valid', 1, '2025-12-04 14:42:44', '2025-12-04 14:42:44');

-- --------------------------------------------------------

--
-- Table structure for table `bac_records`
--

CREATE TABLE `bac_records` (
  `id` int(11) NOT NULL,
  `bac_cod` varchar(50) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bac_records`
--

INSERT INTO `bac_records` (`id`, `bac_cod`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'PE 23-023', 1, '2025-12-04 14:34:22', '2025-12-04 14:34:22'),
(2, 'PE 23-093', 1, '2025-12-04 14:51:52', '2025-12-04 14:51:52');

-- --------------------------------------------------------

--
-- Table structure for table `doc_types`
--

CREATE TABLE `doc_types` (
  `id` int(11) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_required` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doc_types`
--

INSERT INTO `doc_types` (`id`, `document_name`, `description`, `is_required`, `created_at`) VALUES
(1, 'PhilGEPS Registration', 'Philippine Government Electronic Procurement System Registration', 1, '2025-12-04 13:19:24'),
(2, 'Registration Certificate', 'Business Registration Certificate', 1, '2025-12-04 13:19:24'),
(3, 'Mayor\'s Permit', 'Valid Mayor\'s Permit issued by the city/municipality', 1, '2025-12-04 13:19:24'),
(4, 'Tax Clearance', 'BIR Tax Clearance Certificate', 1, '2025-12-04 13:19:24'),
(5, 'Bid Security', 'Bid security document (bond/check)', 1, '2025-12-04 13:19:24'),
(6, 'Omnibus Sworn Statement', 'Sworn statement on eligibility requirements', 1, '2025-12-04 13:19:24'),
(7, 'Audited Financial Statement', 'Latest Audited Financial Statement', 1, '2025-12-04 13:19:24'),
(8, 'Net Statement Contracting Capacity', 'NFCC - Net Financial Contracting Capacity', 1, '2025-12-04 13:19:24'),
(9, 'Resolution', 'Board/Corporate Resolution authorizing signatory', 1, '2025-12-04 13:19:24'),
(10, 'Notice of Award', 'Official Notice of Award document', 1, '2025-12-04 13:19:24'),
(11, 'Performance Bond', 'Performance security bond', 1, '2025-12-04 13:19:24'),
(12, 'Purchase Order/Contract', 'Purchase Order or Contract Agreement', 1, '2025-12-04 13:19:24'),
(13, 'Notice to Proceed', 'Official Notice to Proceed', 1, '2025-12-04 13:19:24');

-- --------------------------------------------------------

--
-- Table structure for table `eligibility_docs`
--

CREATE TABLE `eligibility_docs` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `doc_type_id` int(11) NOT NULL,
  `issued_date` date DEFAULT NULL,
  `expiration_date` date DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `status` enum('Valid','Expired','Missing','For Renewal') DEFAULT 'Missing',
  `remarks` text DEFAULT NULL,
  `uploaded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eligibility_docs`
--

INSERT INTO `eligibility_docs` (`id`, `supplier_id`, `doc_type_id`, `issued_date`, `expiration_date`, `file_path`, `status`, `remarks`, `uploaded_by`, `created_at`, `updated_at`) VALUES
(1, 1, 7, '2025-12-04', '2026-12-18', 'uploads/doc_1_7_1764856985.png', 'Valid', 'Sample', 1, '2025-12-04 14:03:05', '2025-12-04 14:03:05');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `bac_cod` varchar(50) DEFAULT NULL,
  `company_name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `tin` varchar(50) NOT NULL,
  `philgeps_number` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `contact_no` varchar(20) DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `bac_cod`, `company_name`, `address`, `tin`, `philgeps_number`, `email`, `contact_person`, `contact_no`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Sillicon Valley', 'Bishan St. 23\r\n4-232', '123-4123-423123', 'wqe241-qwe9230', 'glenard2308@gmail.com', 'Glenard U Pagurayan', '86193011', 'Active', 1, '2025-12-04 14:02:40', '2025-12-04 14:02:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('Admin','BAC Secretariat Staff','BAC Committee Member','Auditor/COA') NOT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `role`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$ou6AxSEMBDOBqTSMyThHSulMQFyxDZYxtET62O3vHnaA7pPuPIkOK', 'System Administrator', 'admin@bac.gov.ph', 'Admin', 'Active', '2025-12-04 13:19:24', '2025-12-04 13:19:32'),
(2, 'secretariat', '$2y$10$rvW5ETfPX5/obnbagbSxYONDSuN1m90D3m1Pj/lScLLWEI97t7Ypy', 'BAC Secretary', 'secretary@bac.gov.ph', 'BAC Secretariat Staff', 'Active', '2025-12-04 13:19:24', '2025-12-04 13:19:32'),
(3, 'member', '$2y$10$ddUxK0K5q9BU1wZn6Trdf.z9qSl3BK6UyaR1bqLtw8MmZoc0PHPUG', 'BAC Committee Member', 'member@bac.gov.ph', 'BAC Committee Member', 'Active', '2025-12-04 13:19:24', '2025-12-04 13:19:32'),
(4, 'auditor', '$2y$10$KLOPHQcUJ2G/Y/6LBpjaDuKS8AnM8MWNlZtUW7BBpQDSPLcUEQRdq', 'COA Auditor', 'auditor@coa.gov.ph', 'Auditor/COA', 'Active', '2025-12-04 13:19:24', '2025-12-04 13:19:32');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `bac_documents`
--
ALTER TABLE `bac_documents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_bac_doc` (`bac_record_id`,`doc_type_id`),
  ADD KEY `doc_type_id` (`doc_type_id`),
  ADD KEY `uploaded_by` (`uploaded_by`),
  ADD KEY `idx_bac_record` (`bac_record_id`),
  ADD KEY `idx_status_bac` (`status`);

--
-- Indexes for table `bac_records`
--
ALTER TABLE `bac_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bac_cod` (`bac_cod`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_bac_cod` (`bac_cod`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `doc_types`
--
ALTER TABLE `doc_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `eligibility_docs`
--
ALTER TABLE `eligibility_docs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `doc_type_id` (`doc_type_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_bac_cod` (`bac_cod`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_bac_cod` (`bac_cod`);

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
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `bac_documents`
--
ALTER TABLE `bac_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `bac_records`
--
ALTER TABLE `bac_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `doc_types`
--
ALTER TABLE `doc_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `eligibility_docs`
--
ALTER TABLE `eligibility_docs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `bac_documents`
--
ALTER TABLE `bac_documents`
  ADD CONSTRAINT `bac_documents_ibfk_1` FOREIGN KEY (`bac_record_id`) REFERENCES `bac_records` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bac_documents_ibfk_2` FOREIGN KEY (`doc_type_id`) REFERENCES `doc_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bac_documents_ibfk_3` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `bac_records`
--
ALTER TABLE `bac_records`
  ADD CONSTRAINT `bac_records_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `eligibility_docs`
--
ALTER TABLE `eligibility_docs`
  ADD CONSTRAINT `eligibility_docs_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `eligibility_docs_ibfk_2` FOREIGN KEY (`doc_type_id`) REFERENCES `doc_types` (`id`),
  ADD CONSTRAINT `eligibility_docs_ibfk_3` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD CONSTRAINT `suppliers_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
