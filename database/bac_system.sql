-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 09, 2026 at 09:04 AM
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
(51, 1, 'User logged out', 'Authentication', '::1', '2026-01-09 08:28:31'),
(52, 1, 'User logged in', 'Authentication', '::1', '2026-01-09 08:31:12'),
(53, 1, 'User logged out', 'Authentication', '::1', '2026-01-09 08:31:16'),
(54, 1, 'User logged in', 'Authentication', '::1', '2026-01-09 08:31:45'),
(55, 1, 'User logged out', 'Authentication', '::1', '2026-01-09 08:31:49'),
(56, 1, 'User logged in', 'Authentication', '::1', '2026-01-09 08:35:26'),
(57, 1, 'User logged out', 'Authentication', '::1', '2026-01-09 08:35:28'),
(58, 1, 'User logged in', 'Authentication', '::1', '2026-01-09 08:35:39'),
(59, 1, 'User logged out', 'Authentication', '::1', '2026-01-09 08:36:30'),
(60, 1, 'User logged in', 'Authentication', '::1', '2026-01-09 08:36:42'),
(61, 1, 'User logged out', 'Authentication', '::1', '2026-01-09 08:39:11'),
(62, 1, 'User logged in', 'Authentication', '::1', '2026-01-09 09:45:59'),
(63, 1, 'Added new record: PE 25-032', 'Records', '::1', '2026-01-09 09:56:07'),
(64, 1, 'Uploaded BAC document for PE 25-001', 'BAC Documents', '::1', '2026-01-09 10:05:44'),
(65, 1, 'Updated BAC document for PE 25-001', 'BAC Documents', '::1', '2026-01-09 10:07:00'),
(66, 1, 'User logged out', 'Authentication', '::1', '2026-01-09 10:09:31'),
(67, 1, 'User logged in', 'Authentication', '::1', '2026-01-09 10:09:49'),
(68, 1, 'User logged out', 'Authentication', '::1', '2026-01-09 10:09:53'),
(69, 1, 'User logged in', 'Authentication', '::1', '2026-01-09 10:10:50'),
(70, 1, 'User logged in', 'Authentication', '::1', '2026-02-23 09:43:33'),
(71, 1, 'User logged out', 'Authentication', '::1', '2026-02-23 09:44:03'),
(72, 1, 'User logged in', 'Authentication', '::1', '2026-02-23 10:19:51'),
(73, 1, 'User logged in', 'Authentication', '::1', '2026-02-25 14:42:58'),
(74, 1, 'User logged out', 'Authentication', '::1', '2026-02-25 14:43:10'),
(75, 1, 'User logged in', 'Authentication', '::1', '2026-02-25 14:43:16'),
(76, 1, 'User logged in', 'Authentication', '::1', '2026-03-09 06:58:36'),
(77, 1, 'Updated record: PE 25-032', 'Records', '::1', '2026-03-09 06:59:04'),
(78, 1, 'Uploaded BAC document for PE 25-032', 'BAC Documents', '::1', '2026-03-09 07:23:05'),
(79, 1, 'Deleted record: PE 25-032', 'Records', '::1', '2026-03-09 07:23:41'),
(80, 1, 'Deleted record: PE 25-001', 'Records', '::1', '2026-03-09 07:23:43'),
(81, 1, 'Added new record: PE 25-001', 'Records', '::1', '2026-03-09 07:24:09'),
(82, 1, 'Uploaded BAC document for PE 25-001', 'BAC Documents', '::1', '2026-03-09 07:25:27'),
(83, 1, 'Uploaded BAC document for PE 25-001', 'BAC Documents', '::1', '2026-03-09 07:33:48'),
(84, 1, 'Uploaded BAC document for PE 25-001', 'BAC Documents', '::1', '2026-03-09 07:36:07'),
(85, 1, 'Updated BAC document for PE 25-001', 'BAC Documents', '::1', '2026-03-09 07:37:54'),
(86, 1, 'Uploaded BAC document for PE 25-001', 'BAC Documents', '::1', '2026-03-09 07:39:42'),
(87, 1, 'Uploaded BAC document for PE 25-001', 'BAC Documents', '::1', '2026-03-09 07:40:41'),
(88, 1, 'Uploaded BAC document for PE 25-001', 'BAC Documents', '::1', '2026-03-09 07:40:59'),
(89, 1, 'Uploaded BAC document for PE 25-001', 'BAC Documents', '::1', '2026-03-09 07:41:13'),
(90, 1, 'Uploaded BAC document for PE 25-001', 'BAC Documents', '::1', '2026-03-09 07:41:27'),
(91, 1, 'Uploaded BAC document for PE 25-001', 'BAC Documents', '::1', '2026-03-09 07:41:40'),
(92, 1, 'Uploaded BAC document for PE 25-001', 'BAC Documents', '::1', '2026-03-09 07:41:55'),
(93, 1, 'Uploaded BAC document for PE 25-001', 'BAC Documents', '::1', '2026-03-09 07:42:18'),
(94, 1, 'Added new record: PE 26-003', 'Records', '::1', '2026-03-09 07:49:21'),
(95, 1, 'Uploaded BAC document for PE 26-003', 'BAC Documents', '::1', '2026-03-09 07:54:01');

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
(8, 5, 14, '1269f566-7d00-4116-a3b6-3268d58957d6.jpg', 'uploads/bac_doc_5_14_1773041127.jpg', '2026-03-09', '2027-05-09', 'Valid', 1, '2026-03-09 07:25:27', '2026-03-09 07:25:27'),
(9, 5, 15, '1269f566-7d00-4116-a3b6-3268d58957d6.jpg', 'uploads/bac_doc_5_15_1773041628.jpg', '2026-03-09', '2027-03-09', 'Valid', 1, '2026-03-09 07:33:48', '2026-03-09 07:33:48'),
(10, 5, 16, '1269f566-7d00-4116-a3b6-3268d58957d6.jpg', 'uploads/bac_doc_5_16_1773041874.jpg', '2026-03-09', '2027-03-09', 'Valid', 1, '2026-03-09 07:36:07', '2026-03-09 07:37:54'),
(11, 5, 17, 'e1d8aaee-3325-4f9e-84c6-e98c7478285e.jpg', 'uploads/bac_doc_5_17_1773041982.jpg', '2026-03-09', '2027-03-09', 'Valid', 1, '2026-03-09 07:39:42', '2026-03-09 07:39:42'),
(12, 5, 18, '4b15b849-ddc7-4141-a640-26bcd1970ca4.jpg', 'uploads/bac_doc_5_18_1773042041.jpg', '2026-03-09', '2027-03-09', 'Valid', 1, '2026-03-09 07:40:41', '2026-03-09 07:40:41'),
(13, 5, 19, '4b15b849-ddc7-4141-a640-26bcd1970ca4.jpg', 'uploads/bac_doc_5_19_1773042059.jpg', '2026-03-09', '2027-03-09', 'Valid', 1, '2026-03-09 07:40:59', '2026-03-09 07:40:59'),
(14, 5, 20, '4b15b849-ddc7-4141-a640-26bcd1970ca4.jpg', 'uploads/bac_doc_5_20_1773042073.jpg', '2026-03-09', '2027-03-09', 'Valid', 1, '2026-03-09 07:41:13', '2026-03-09 07:41:13'),
(15, 5, 21, 'e1d8aaee-3325-4f9e-84c6-e98c7478285e.jpg', 'uploads/bac_doc_5_21_1773042087.jpg', '2026-03-09', '2027-03-09', 'Valid', 1, '2026-03-09 07:41:27', '2026-03-09 07:41:27'),
(16, 5, 22, 'e1d8aaee-3325-4f9e-84c6-e98c7478285e.jpg', 'uploads/bac_doc_5_22_1773042100.jpg', '2026-03-09', '2027-03-09', 'Valid', 1, '2026-03-09 07:41:40', '2026-03-09 07:41:40'),
(17, 5, 23, 'e1d8aaee-3325-4f9e-84c6-e98c7478285e.jpg', 'uploads/bac_doc_5_23_1773042115.jpg', '2026-03-09', '2027-03-09', 'Valid', 1, '2026-03-09 07:41:55', '2026-03-09 07:41:55'),
(18, 5, 24, '4b15b849-ddc7-4141-a640-26bcd1970ca4.jpg', 'uploads/bac_doc_5_24_1773042138.jpg', '2026-03-09', '2027-03-09', 'Valid', 1, '2026-03-09 07:42:18', '2026-03-09 07:42:18'),
(19, 6, 14, '4b15b849-ddc7-4141-a640-26bcd1970ca4.jpg', 'uploads/bac_doc_6_14_1773042841.jpg', '2026-03-09', '2027-03-09', 'Valid', 1, '2026-03-09 07:54:01', '2026-03-09 07:54:01');

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
(5, 'PE 25-001', 1, '2026-03-09 07:24:09', '2026-03-09 07:24:09'),
(6, 'PE 26-003', 1, '2026-03-09 07:49:21', '2026-03-09 07:49:21');

-- --------------------------------------------------------

--
-- Table structure for table `doc_types`
--

CREATE TABLE `doc_types` (
  `id` int(11) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 99,
  `description` text DEFAULT NULL,
  `is_required` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doc_types`
--

INSERT INTO `doc_types` (`id`, `document_name`, `category`, `sort_order`, `description`, `is_required`, `created_at`) VALUES
(1, 'PhilGEPS Registration', NULL, 99, 'Philippine Government Electronic Procurement System Registration', 1, '2025-12-04 13:19:24'),
(2, 'Registration Certificate', NULL, 99, 'Business Registration Certificate', 1, '2025-12-04 13:19:24'),
(3, 'Mayor\'s Permit', NULL, 99, 'Valid Mayor\'s Permit issued by the city/municipality', 1, '2025-12-04 13:19:24'),
(4, 'Tax Clearance', NULL, 99, 'BIR Tax Clearance Certificate', 1, '2025-12-04 13:19:24'),
(5, 'Bid Security', NULL, 99, 'Bid security document (bond/check)', 1, '2025-12-04 13:19:24'),
(6, 'Omnibus Sworn Statement', NULL, 99, 'Sworn statement on eligibility requirements', 1, '2025-12-04 13:19:24'),
(7, 'Audited Financial Statement', NULL, 99, 'Latest Audited Financial Statement', 1, '2025-12-04 13:19:24'),
(8, 'Net Statement Contracting Capacity', NULL, 99, 'NFCC - Net Financial Contracting Capacity', 1, '2025-12-04 13:19:24'),
(9, 'Resolution', NULL, 99, 'Board/Corporate Resolution authorizing signatory', 1, '2025-12-04 13:19:24'),
(10, 'Notice of Award', NULL, 99, 'Official Notice of Award document', 1, '2025-12-04 13:19:24'),
(11, 'Performance Bond', NULL, 99, 'Performance security bond', 1, '2025-12-04 13:19:24'),
(12, 'Purchase Order/Contract', NULL, 99, 'Purchase Order or Contract Agreement', 1, '2025-12-04 13:19:24'),
(13, 'Notice to Proceed', NULL, 99, 'Official Notice to Proceed', 1, '2025-12-04 13:19:24'),
(14, 'Valid PHILGEPS Registration Certificate (Platinum Membership)', 'II. Eligibility and Technical Documents', 1, 'Philippine Government Electronic Procurement System Registration Certificate - Platinum Membership', 1, '2026-03-09 07:17:54'),
(15, 'DTI Registration Certification & Certificate of Accreditation', 'II. Eligibility and Technical Documents', 2, 'DTI Business Name Registration and Certificate of Accreditation', 1, '2026-03-09 07:17:54'),
(16, 'Mayor\'s Permit', 'II. Eligibility and Technical Documents', 3, 'Valid Mayor\'s Permit issued by the city/municipality', 1, '2026-03-09 07:17:54'),
(17, 'Tax Clearance', 'II. Eligibility and Technical Documents', 4, 'BIR Tax Clearance Certificate', 1, '2026-03-09 07:17:54'),
(18, 'Statement of Ongoing Government and Private Contracts', 'II. Eligibility and Technical Documents', 5, 'Statement of the prospective bidder of all its ongoing government and private contracts, including contracts awarded but not yet started', 1, '2026-03-09 07:17:54'),
(19, 'Statement of Bidder\'s Single Largest Completed Contract (SLCC)', 'II. Eligibility and Technical Documents', 6, 'Statement of the bidder\'s Single Largest Completed Contract (SLCC)', 1, '2026-03-09 07:17:54'),
(20, 'Bid Security', 'II. Eligibility and Technical Documents', 7, 'Bid security document (bond/check)', 1, '2026-03-09 07:17:54'),
(21, 'Conformity with the Technical Specification', 'II. Eligibility and Technical Documents', 8, 'Conformity with the Technical Specification (Production/Delivery Schedule / Manpower Requirement / Warranty Certificate and After Sales/Parts, if applicable)', 1, '2026-03-09 07:17:54'),
(22, 'Omnibus Sworn Statement (OSS)', 'II. Eligibility and Technical Documents', 9, 'Sworn statement on eligibility requirements', 1, '2026-03-09 07:17:54'),
(23, 'Financial Bid Form', 'III. Financial Documents', 1, 'Original of duly signed and accomplished Financial Bid Form', 1, '2026-03-09 07:17:54'),
(24, 'Price Schedule(s)', 'III. Financial Documents', 2, 'Original of duly signed and accomplished Price Schedule(s)', 1, '2026-03-09 07:17:54');

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
(1, 'admin', '$2y$10$ou6AxSEMBDOBqTSMyThHSulMQFyxDZYxtET62O3vHnaA7pPuPIkOK', 'System Administrator', 'admin@bac.gov.ph', 'Admin', 'Active', '2025-12-04 13:19:24', '2025-12-04 13:19:32');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT for table `bac_documents`
--
ALTER TABLE `bac_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `bac_records`
--
ALTER TABLE `bac_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `doc_types`
--
ALTER TABLE `doc_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `eligibility_docs`
--
ALTER TABLE `eligibility_docs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
