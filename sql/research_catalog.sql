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
(94, ' ', NULL, 'Bulalayao'),
(90, ' ', NULL, 'Colis'),
(91, ' ', NULL, 'Fiedalan'),
(98, ' ', NULL, 'Freyden'),
(95, ' ', NULL, 'Hya'),
(101, ' ', NULL, 'Maica'),
(92, ' ', NULL, 'Mendoza'),
(93, ' ', NULL, 'Rabago'),
(104, ' ', NULL, 'Raia'),
(99, ' ', NULL, 'Rain'),
(100, ' ', NULL, 'Raina'),
(86, ' ', NULL, 'Reena'),
(97, ' ', NULL, 'Remy'),
(96, ' ', NULL, 'Rodney'),
(56, 'Ei', NULL, 'Makoto'),
(89, 'Eric', 'C', 'Cartman'),
(54, 'Frane', 'T', 'Roman'),
(3, 'Frane', 'T', 'Romanova'),
(47, 'Frauke', 'B', 'Payton'),
(88, 'Kyle', 'V', 'Brovloski'),
(105, 'Lana', 'D', 'Rey'),
(2, 'Lemuel', 'I', 'Koppel'),
(61, 'Prof', NULL, 'oak'),
(62, 'Prof', 'A', 'Ketchum'),
(87, 'Randy', 'M', 'Marsh'),
(5, 'Rheinallt', NULL, 'Sasaki'),
(106, 'Rhya', NULL, 'Copy'),
(64, 'Soraya', 'V', 'Kelly'),
(103, 'William', 'B', 'Afton');

CREATE TABLE `departments` (
  `id` bigint(15) UNSIGNED NOT NULL,
  `department` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `departments` (`id`, `department`) VALUES
(1, 'Computer Science'),
(51, 'Engineering'),
(2, 'Environmental Science'),
(3, 'Information Technology'),
(4, 'Information Techonology'),
(39, 'IT247'),
(62, 'Pokemans'),
(96, 'Psychology'),
(83, 'Science'),
(49, 'Zoology');

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
(3, 'Flood Detection and Notifier', 'Arduino, Android Studio, Firebase', 4, 2026, '2026-02-03 19:01:26', '............'),
(4, 'Coals from Hyacinth', 'upcycling, greenhouse gases', 2, 2018, '2026-02-03 19:05:52', 'yada yada'),
(5, 'The Effects of Antioxidants in Mice', 'biology, animal studies, antioxidants', 1, 1999, '2026-02-03 19:08:24', 'q'),
(6, 'Integrating Brain Activity Monitors with Wearable Technology to Monitor Stress Level', 'neuroscience, wearables, cortisol', 3, 2028, '2026-02-03 19:12:50', 'qwertyuiopoiuytrewertyukuytr'),
(22, 'Life of Yuan', 'furry', 49, 2024, '2026-02-08 05:33:39', 'meowmeowwoofwoofbaarkbark'),
(24, 'Fluid Gears', 'biology, animal studies, antioxidants', 51, 1800, '2026-02-08 05:50:54', 'q'),
(27, 'How Friendship Affects Pokemon Evolution', 'pokeball, pokemon, poke centers, gym badges, levels, friendship', 62, 1992, '2026-02-08 08:12:43', 'Gotta catchem all'),
(31, 'Clean Energy', 'environment, windmills', 2, 1799, '2026-02-08 08:37:56', 'aaa'),
(34, 'ACTUAL BOOK??', 'southpark', 83, 1992, '2026-02-08 09:58:13', 'South park'),
(35, 'ShelfStudy: Digital Catalog for Student Research Papers', 'Shelfstudy', 39, 2026, '2026-02-08 09:59:04', 'EYYYYY'),
(38, 'Night Owl & Lock In: A Correlational Study Between Motivation and Sleep', 'sleep', 96, 2026, '2026-02-08 12:43:09', 'na oc sa mga smol errors');

CREATE TABLE `paper_authors` (
  `paperId` bigint(15) UNSIGNED NOT NULL,
  `authorId` bigint(15) UNSIGNED NOT NULL,
  `authorOrder` smallint(5) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `paper_authors` (`paperId`, `authorId`, `authorOrder`) VALUES
(1, 96, 1),
(1, 97, 2),
(3, 86, 2),
(3, 99, 1),
(3, 100, 3),
(3, 101, 4),
(4, 5, 2),
(4, 95, 1),
(5, 3, 1),
(6, 2, 1),
(22, 94, 1),
(22, 98, 2),
(24, 47, 2),
(24, 54, 1),
(24, 56, 3),
(27, 61, 1),
(27, 62, 2),
(31, 64, 1),
(34, 87, 1),
(34, 88, 2),
(34, 89, 3),
(35, 90, 1),
(35, 91, 2),
(35, 92, 3),
(35, 93, 4),
(35, 94, 5),
(38, 105, 1),
(38, 106, 2);


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
  MODIFY `id` bigint(15) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

ALTER TABLE `departments`
  MODIFY `id` bigint(15) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

ALTER TABLE `papers`
  MODIFY `id` bigint(15) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;


ALTER TABLE `papers`
  ADD CONSTRAINT `fk_papers_department` FOREIGN KEY (`departmentId`) REFERENCES `departments` (`id`) ON UPDATE CASCADE;

ALTER TABLE `paper_authors`
  ADD CONSTRAINT `fk_paper_authors_author` FOREIGN KEY (`authorId`) REFERENCES `authors` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_paper_authors_paper` FOREIGN KEY (`paperId`) REFERENCES `papers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
