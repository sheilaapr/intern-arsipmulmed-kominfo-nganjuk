-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 20, 2026 at 03:31 AM
-- Server version: 9.0.1
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `omahtandang`
--

-- --------------------------------------------------------

--
-- Table structure for table `app_meta`
--

CREATE TABLE `app_meta` (
  `meta_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meta_value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `app_meta`
--

INSERT INTO `app_meta` (`meta_key`, `meta_value`) VALUES
('schema_version', '2.0');

-- --------------------------------------------------------

--
-- Table structure for table `arsip`
--

CREATE TABLE `arsip` (
  `id_arsip` int NOT NULL,
  `judul` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipe_file` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_kategori` int NOT NULL,
  `tanggal_acara` date NOT NULL,
  `uploaded_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `arsip`
--

INSERT INTO `arsip` (`id_arsip`, `judul`, `deskripsi`, `filename`, `tipe_file`, `id_kategori`, `tanggal_acara`, `uploaded_by`, `created_at`) VALUES
(1, 'Pembinaan Lansia', 'coba', 'pembinaan_lansia/VBG_PANITIA_WEBINAR_1781851848_0.png', 'PNG', 1, '2026-06-19', 3, '2026-06-19 06:50:48'),
(3, 'p', 'p', 'p/split_2_1_1781856660_0.jpg', 'JPG', 2, '2026-06-19', 4, '2026-06-19 08:11:00');

-- --------------------------------------------------------

--
-- Table structure for table `arsip_file`
--

CREATE TABLE `arsip_file` (
  `id_file` int NOT NULL,
  `id_arsip` int NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipe_file` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `arsip_file`
--

INSERT INTO `arsip_file` (`id_file`, `id_arsip`, `filename`, `tipe_file`, `created_at`) VALUES
(1, 1, 'pembinaan_lansia/VBG_PANITIA_WEBINAR_1781851848_0.png', 'PNG', '2026-06-19 06:50:48'),
(8, 3, 'p/split_2_1_1781856660_0.jpg', 'JPG', '2026-06-19 08:11:00');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int NOT NULL,
  `nama_kategori` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`, `deskripsi`) VALUES
(1, 'KADER', 'Pembinaan Kader 1'),
(2, 'KOPERASI', 'Perayaan Hari Koperasi ke-79'),
(4, 'EVENT', 'anu'),
(5, 'KOMINFO', 'Event rutinan dinas');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `nama` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('kabag','staff') COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `foto_profile` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `role`, `username`, `password`, `created`, `foto_profile`) VALUES
(1, 'omah tandang', 'omahtandang@gmail.com', 'kabag', 'admin1', '$2a$12$TYXjapSlTmfICCuuNtieEuUyT1MgxK8CX6gHGRC1GZi5DYxhjmBs.', '2025-07-03 20:36:18', NULL),
(2, 'vv', 'sivanafirdausy@gmail.com', 'staff', 'vava', '$2y$10$C.PzTII6CVEBb8vDrVU9cOEPowSd1JuOg0wyrm/.YVNADx1I3hAQe', '2025-07-27 14:43:10', NULL),
(3, 'SHEILA APRILIANI PUTRI', 'sheilaapr24@gmail.com', 'staff', 'sheilaapr_', '$2y$10$r4YwL9DuUWJeboM4woHM8e1DeepEIJ1msP/Qx60z2CRKQLfN9uwva', '2026-06-19 06:14:45', NULL),
(4, 'Azzahva Qesmea Dany', 'azzahva@gmail.com', 'staff', 'zhv', '$2y$10$5Ia/cBHHlPiqT1KL..tiUe3CP9iQqRT0ZBziIawqDBiU//McJdNs6', '2026-06-19 07:12:03', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `app_meta`
--
ALTER TABLE `app_meta`
  ADD PRIMARY KEY (`meta_key`);

--
-- Indexes for table `arsip`
--
ALTER TABLE `arsip`
  ADD PRIMARY KEY (`id_arsip`),
  ADD KEY `FK_arsip_kategori` (`id_kategori`),
  ADD KEY `FK_arsip_users` (`uploaded_by`);

--
-- Indexes for table `arsip_file`
--
ALTER TABLE `arsip_file`
  ADD PRIMARY KEY (`id_file`),
  ADD KEY `idx_arsip_file_id_arsip` (`id_arsip`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `arsip`
--
ALTER TABLE `arsip`
  MODIFY `id_arsip` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `arsip_file`
--
ALTER TABLE `arsip_file`
  MODIFY `id_file` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `arsip`
--
ALTER TABLE `arsip`
  ADD CONSTRAINT `FK_arsip_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE,
  ADD CONSTRAINT `FK_arsip_users` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
