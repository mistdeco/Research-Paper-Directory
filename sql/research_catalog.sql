-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 03, 2026 at 12:44 PM
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
-- Database: `research_catalog`
--
CREATE DATABASE IF NOT EXISTS `research_catalog` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `research_catalog`;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(60) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin12345');

-- --------------------------------------------------------

--
-- Table structure for table `papers`
--

CREATE TABLE `papers` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `authors` varchar(255) NOT NULL,
  `keywords` varchar(255) NOT NULL,
  `department` varchar(100) NOT NULL,
  `year_published` smallint(5) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `abstract` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `papers`
--

INSERT INTO `papers` (`id`, `title`, `authors`, `keywords`, `department`, `year_published`, `created_at`, `abstract`) VALUES
(1, 'Keyword Extraction for Research Abstracts', 'C.B.Y., H.G. Wells', 'machine learning, AI, LLM', 'Computer Science', 2020, '2026-02-03 04:51:24', 'wwwwww'),
(2, 'd', 'd', 'sd', 'd', 2003, '2026-02-03 18:59:50', 'dwqfsd'),
(3, 'Flood Detection and Notifier', 'Colis, Fiedalan, Rabago, Mendoza', 'Arduino, Android Studio, Firebase', 'Information Techonology', 2026, '2026-02-03 19:01:26', '............'),
(4, 'Coals from Hyacinth', 'Oda', 'upcycling, greenhouse gases', 'Environmental Science', 2018, '2026-02-03 19:05:52', 'yada yada'),
(5, 'The Effects of Antioxidants in Mice', 'Raiden, Ei', 'biology, animal studies, antioxidants', 'Computer Science', 1999, '2026-02-03 19:08:24', 'q'),
(6, 'Integrating Brain Activity Monitors with Wearable Technology to Monitor Stress Level', 'Izzy', 'neuroscience, wearables, cortisol', 'Information Techonology', 2028, '2026-02-03 19:12:50', 'qwertyuiopoiuytrewertyukuytr');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_admins_username` (`username`);

--
-- Indexes for table `papers`
--
ALTER TABLE `papers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_papers_year` (`year_published`),
  ADD KEY `idx_papers_department` (`department`),
  ADD KEY `idx_papers_title` (`title`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `papers`
--
ALTER TABLE `papers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
