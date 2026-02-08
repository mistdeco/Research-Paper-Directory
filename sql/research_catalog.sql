SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `admins` (
  `id` bigint(15) UNSIGNED NOT NULL,
  `username` varchar(60) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin12345');

CREATE TABLE `authors` (
  `id` bigint(15) UNSIGNED NOT NULL,
  `fName` varchar(80) NOT NULL,
  `MI` char(1) DEFAULT NULL,
  `lName` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `authors` (`id`, `fName`, `MI`, `lName`) VALUES
(31, 'Egyptian', NULL, 'Mau'),
(1, 'Ei', NULL, 'Makoto'),
(10, 'Ei', NULL, 'Makoto'),
(11, 'Ei', NULL, 'Makoto'),
(30, 'Ei', NULL, 'Makoto'),
(34, 'Ei', NULL, 'Makoto'),
(36, 'Ei', NULL, 'Makoto'),
(12, 'ER', NULL, 'F'),
(35, 'Ewi', 'K', 'Nine'),
(13, 'EWRG', NULL, 'ERGF'),
(15, 'FIRST', 'M', 'LAST'),
(3, 'Frane', 'T', 'Romanova'),
(2, 'Lemuel', 'I', 'Koppel'),
(4, 'Maurice', 'Ä', 'AdomaitienÄ—'),
(20, 'New', NULL, 'Author'),
(28, 'New', NULL, 'author'),
(14, 'Q', 'R', 'WEWR'),
(5, 'Rheinallt', NULL, 'Sasaki'),
(7, 'Rheinallt', NULL, 'Sasaki'),
(18, 'Sample', 'E', 'Author'),
(32, 'Si', NULL, 'maica');

CREATE TABLE `departments` (
  `id` bigint(15) UNSIGNED NOT NULL,
  `department` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `departments` (`id`, `department`) VALUES
(1, 'Computer Science'),
(20, 'Cooking'),
(22, 'Culture'),
(2, 'Environmental Science'),
(25, 'Food Department'),
(3, 'Information Technology'),
(4, 'Information Techonology'),
(39, 'IT247'),
(24, 'Social Sciences'),
(35, 'Technology'),
(14, 'Test Department'),
(40, 'yelo');

CREATE TABLE `papers` (
  `id` bigint(15) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `keywords` varchar(255) NOT NULL,
  `departmentId` bigint(15) UNSIGNED NOT NULL,
  `yearPublished` smallint(5) UNSIGNED NOT NULL,
  `createdAt` datetime NOT NULL DEFAULT current_timestamp(),
  `abstract` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `papers` (`id`, `title`, `keywords`, `departmentId`, `yearPublished`, `createdAt`, `abstract`) VALUES
(1, 'Keyword Extraction for Research Abstracts', 'machine learning, AI, LLM', 1, 2020, '2026-02-03 04:51:24', 'wwwwww'),
(2, 'd', 'sd', 1, 2003, '2026-02-03 18:59:50', 'dwqfsd'),
(3, 'Flood Detection and Notifier', 'Arduino, Android Studio, Firebase', 4, 2026, '2026-02-03 19:01:26', '............'),
(4, 'Coals from Hyacinth', 'upcycling, greenhouse gases', 2, 2018, '2026-02-03 19:05:52', 'yada yada'),
(5, 'The Effects of Antioxidants in Mice', 'biology, animal studies, antioxidants', 1, 1999, '2026-02-03 19:08:24', 'q'),
(6, 'Integrating Brain Activity Monitors with Wearable Technology to Monitor Stress Level', 'neuroscience, wearables, cortisol', 3, 2028, '2026-02-03 19:12:50', 'qwertyuiopoiuytrewertyukuytr'),
(9, 'Food', 'Food', 20, 1900, '2026-02-07 21:49:35', 'Cook'),
(10, 'aaa', 'sd', 22, 2003, '2026-02-07 21:50:42', 'a'),
(11, 'A', 'culture, humms,', 24, 2020, '2026-02-08 03:15:57', 'a'),
(12, '1', 'Arduino, Android Studio, Firebase', 25, 2008, '2026-02-08 04:25:01', 'ww'),
(13, 'Utilizing blah blah blah', 'a', 1, 1967, '2026-02-08 04:36:00', 'q'),
(14, '.....', 'Artificial Intelligence', 35, 2006, '2026-02-08 04:37:46', 'Wjisndjdiiw'),
(15, 'Hanlooo sana Quatro', 'hewow', 39, 2005, '2026-02-08 04:45:00', 'hehehehee'),
(16, 'hello pooo', 'woo', 40, 2007, '2026-02-08 04:48:11', 'HAHAHAHAH');

CREATE TABLE `paper_authors` (
  `paperId` bigint(15) UNSIGNED NOT NULL,
  `authorId` bigint(15) UNSIGNED NOT NULL,
  `authorOrder` smallint(5) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `paper_authors` (`paperId`, `authorId`, `authorOrder`) VALUES
(2, 11, 1),
(3, 12, 1),
(3, 13, 2),
(4, 4, 1),
(4, 7, 2),
(5, 3, 1),
(6, 2, 1),
(9, 18, 1),
(10, 15, 1),
(11, 15, 1),
(11, 20, 2),
(12, 4, 1),
(13, 35, 2),
(13, 36, 1),
(14, 32, 1);


ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_admins_username` (`username`);

ALTER TABLE `authors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_authors_fullname` (`fName`,`MI`,`lName`);

ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_departments_name` (`department`);

ALTER TABLE `papers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_papers_year` (`yearPublished`),
  ADD KEY `idx_papers_title` (`title`),
  ADD KEY `idx_papers_department_id` (`departmentId`);

ALTER TABLE `paper_authors`
  ADD PRIMARY KEY (`paperId`,`authorId`),
  ADD KEY `idx_paper_authors_author` (`authorId`);


ALTER TABLE `admins`
  MODIFY `id` bigint(15) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

ALTER TABLE `authors`
  MODIFY `id` bigint(15) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

ALTER TABLE `departments`
  MODIFY `id` bigint(15) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

ALTER TABLE `papers`
  MODIFY `id` bigint(15) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;


ALTER TABLE `papers`
  ADD CONSTRAINT `fk_papers_department` FOREIGN KEY (`departmentId`) REFERENCES `departments` (`id`) ON UPDATE CASCADE;

ALTER TABLE `paper_authors`
  ADD CONSTRAINT `fk_paper_authors_author` FOREIGN KEY (`authorId`) REFERENCES `authors` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_paper_authors_paper` FOREIGN KEY (`paperId`) REFERENCES `papers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
