-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 21 Sep 2024 pada 05.38
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `novelwebsite`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `bans`
--

CREATE TABLE `bans` (
  `id` int(11) NOT NULL,
  `banned_user_id` int(11) NOT NULL,
  `banned_until` datetime NOT NULL,
  `reason` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `bans`
--

INSERT INTO `bans` (`id`, `banned_user_id`, `banned_until`, `reason`, `created_at`) VALUES
(1, 1, '2024-10-04 09:27:45', 'Violation of rules', '2024-09-04 14:27:45'),
(2, 1, '2024-10-04 09:50:54', 'Violation of rules', '2024-09-04 14:50:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `name`, `created_at`) VALUES
(1, 'fantasy', '2024-09-17 01:06:45');

-- --------------------------------------------------------

--
-- Struktur dari tabel `chaptermarks`
--

CREATE TABLE `chaptermarks` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `marked_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `chapters`
--

CREATE TABLE `chapters` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `work_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `chapters`
--

INSERT INTO `chapters` (`id`, `name`, `work_id`, `user_id`, `content`, `created_at`) VALUES
(5, 'Chapter 1', 3, 1, 'zxxxxxxxxxxxxx', '2024-08-16 08:41:58'),
(6, 's', 5, 2, 'ssssssssssss', '2024-08-16 09:09:20'),
(9, 'Artha Ardiansyah', 6, 8, 'aaaaaaaaaa', '2024-09-06 15:09:53'),
(11, 'ddf', 5, 2, 'df', '2024-09-13 13:25:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `work_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `parent_comment_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `comments`
--

INSERT INTO `comments` (`id`, `chapter_id`, `user_id`, `work_id`, `content`, `created_at`, `parent_comment_id`) VALUES
(4, 6, 1, 5, 'a', '2024-08-18 18:53:30', NULL),
(5, 6, 1, 5, 'a', '2024-08-18 18:53:32', NULL),
(6, 6, 1, 5, 'a', '2024-08-18 19:23:34', NULL),
(7, 6, 1, 5, 'a', '2024-08-18 19:23:38', NULL),
(8, 6, 1, 5, 'asdasd', '2024-08-18 19:23:41', NULL),
(9, 6, 1, 5, 'a', '2024-08-18 19:25:15', NULL),
(10, 6, 1, 5, 'asdasdas', '2024-08-18 19:25:17', NULL),
(11, 6, 1, 5, 'a', '2024-08-18 19:27:23', 10),
(12, 6, 1, 5, 'a', '2024-08-18 19:28:58', NULL),
(13, 6, 1, 5, 'a', '2024-08-18 19:30:25', 10),
(14, 6, 1, 5, 'sdasd', '2024-08-18 19:30:30', 10),
(15, 6, 1, 5, 'g', '2024-08-18 19:30:35', 10),
(16, 6, 1, 5, 'assd', '2024-08-18 19:30:42', 9),
(17, 6, 1, 5, 'asdas', '2024-08-18 19:30:51', 8),
(18, 6, 1, 5, 'd', '2024-08-18 19:32:23', 9),
(21, 6, 1, 5, 'a', '2024-08-18 19:33:40', 12),
(22, 6, 1, 5, 'aa', '2024-08-18 19:33:44', 12),
(23, 6, 1, 5, 'aaaa', '2024-08-18 19:33:52', 12),
(24, 6, 1, 5, 'aaaa', '2024-08-18 19:34:01', 12),
(25, 6, 1, 5, 'a', '2024-08-18 19:35:23', NULL),
(26, 6, 1, 5, 'a', '2024-08-18 19:35:26', NULL),
(27, 6, 1, 5, 'aaa', '2024-08-18 19:35:33', NULL),
(28, 6, 1, 5, 'aaa', '2024-08-18 19:35:36', NULL),
(33, 6, 1, 5, 'a', '2024-08-18 19:42:40', NULL),
(34, 6, 1, 5, 'aa', '2024-08-18 19:42:43', NULL),
(35, 6, 1, 5, 'aadsaerfsdfsdgdsghftjujfyjtyyi', '2024-08-18 19:42:46', NULL),
(36, 6, 1, 5, 'sgfsdf', '2024-08-18 19:42:49', NULL),
(37, 6, 1, 5, 'asdasd', '2024-08-18 19:43:09', NULL),
(93, 5, 2, 3, 'zzzz', '2024-08-29 08:17:30', NULL),
(94, 5, 2, 3, 'bjirrr', '2024-08-29 08:18:07', NULL),
(95, 5, 1, 3, 'lawak', '2024-08-29 08:18:24', 94),
(100, 6, 1, 5, 'sad', '2024-09-03 13:57:17', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `work_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` between 1 and 5),
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `readinghistory`
--

CREATE TABLE `readinghistory` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `chapter_id` int(11) NOT NULL,
  `read_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `reported_id` int(11) DEFAULT NULL,
  `report_type` enum('work','comment') NOT NULL,
  `reported_user_id` int(11) DEFAULT NULL,
  `reporter_id` int(11) DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `reported_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','resolved','banned') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `reports`
--

INSERT INTO `reports` (`id`, `reported_id`, `report_type`, `reported_user_id`, `reporter_id`, `reason`, `reported_at`, `status`) VALUES
(1, 3, 'work', 1, 2, 's', '2024-09-06 01:45:31', 'pending'),
(2, 3, 'work', 1, 2, 'aaaaaaaaaaaaaaaa', '2024-09-06 02:04:47', 'pending'),
(3, 3, 'work', 1, 7, 'sdd', '2024-09-06 06:59:27', 'pending'),
(4, 94, 'comment', 2, 7, 'ass', '2024-09-06 07:00:19', 'pending'),
(5, 2, 'work', 1, 8, 'aa', '2024-09-06 08:08:09', 'pending'),
(6, 7, 'work', 9, 7, 'tes', '2024-09-12 04:05:47', 'pending');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `banned_until` datetime DEFAULT NULL,
  `ban_reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `profile_picture`, `bio`, `role`, `created_at`, `updated_at`, `banned_until`, `ban_reason`) VALUES
(1, 'yoho1234', '$2y$10$W11U3enY99JPAEYvi9z2BuYqtxqT01g6Y9FeRD3Dw68XyGk0P0/Wu', 'arthaardiansyah2018@gmail.com', 'Screenshot (7).png', '', 'user', '2024-08-11 10:57:20', '2024-09-06 08:48:14', '2024-09-13 08:48:14', NULL),
(2, 'hai', '$2y$10$Av3emWPBwe8QyQQuBqwbIOtHN1I3Ly3FKLOMz3CwMX4TU.XecQsrq', 'nafal@wan.coaq', 'Screenshot 2024-02-01 184022.png', '', 'admin', '2024-08-16 08:52:28', '2024-09-05 10:19:30', '2024-09-04 00:00:00', NULL),
(3, 'hadmin123', '$2y$10$uMN0eMtlXyAzGEDGbInqMeWQPkaVy/HnJfTQEhM19.iHWD./pPR3O', 'admin@gmail.com', NULL, NULL, 'admin', '2024-09-04 14:45:05', '2024-09-04 14:47:32', NULL, NULL),
(5, 'admin', '$2y$10$6INOLPZlmx/AC8fF5iRdiO4xPt7LMZjo09VYe7KTtFpg3V1XjOHeK', 'admin@gmail.com', NULL, NULL, 'admin', '2024-09-04 14:49:14', '2024-09-05 10:11:14', '2024-09-14 00:00:00', 'k'),
(6, 'hasya', '$2y$10$9apYVN55rLei18xPuDNDJuyAEyDbygiRndpD6ruks3001chTFJKPO', 'hasya@gmail.com', NULL, NULL, 'user', '2024-09-06 12:42:51', '2024-09-06 12:42:51', NULL, NULL),
(7, 'akuaa', '$2y$10$XILJIv2iEaTub.kJRawDmesTruL4q1wmjatHX9BWpfg1g0pdtbV42', 'aku@gmail.com', 'Screenshot 2024-02-08 195107.png', '', 'admin', '2024-09-06 13:59:03', '2024-09-12 09:27:52', NULL, NULL),
(8, 'ak', '$2y$10$gvKITXSKqv7Gz7IdH8OMi.MdHBPE6vkIrI4FdHbrnb8n4lk8Uymm.', 'aku@gmail.com', NULL, NULL, 'user', '2024-09-06 15:07:42', '2024-09-06 15:07:42', NULL, NULL),
(9, 'tes', '$2y$10$a.eYKcY1CwEZLRkyhnnYd.9DczJ4LLL3W3Zzci.BK1DeM9QKX.Auq', 'tes@GMAIL.COM', NULL, NULL, 'user', '2024-09-12 10:06:52', '2024-09-12 11:05:55', '2024-09-19 11:05:55', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `works`
--

CREATE TABLE `works` (
  `id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `author_id` int(11) NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `category_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `works`
--

INSERT INTO `works` (`id`, `title`, `author_id`, `thumbnail`, `description`, `created_at`, `updated_at`, `category_id`) VALUES
(3, 'aaa', 1, '2024-07-05_12.48.53_2.png', 'aaaaaaa', '2024-08-16 08:41:58', '2024-08-16 09:12:44', NULL),
(5, 'aa', 2, '', NULL, '2024-08-16 08:53:36', '2024-08-16 08:53:36', NULL),
(6, 'aaa', 8, '2024-01-25.png', NULL, '2024-09-06 15:09:27', '2024-09-06 15:09:27', NULL),
(8, 'aaa', 2, '2024-07-05_12.48.53_2.png', 'as', '2024-09-13 12:50:16', '2024-09-13 12:50:16', NULL),
(9, 'aaa', 2, 'Screenshot (4).png', 'asdasdsadsa', '2024-09-17 08:28:35', '2024-09-17 08:28:35', 1);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `bans`
--
ALTER TABLE `bans`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `chaptermarks`
--
ALTER TABLE `chaptermarks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `chapter_id` (`chapter_id`);

--
-- Indeks untuk tabel `chapters`
--
ALTER TABLE `chapters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `work_id` (`work_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chapter_id` (`chapter_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `work_id` (`work_id`),
  ADD KEY `parent_comment_id` (`parent_comment_id`);

--
-- Indeks untuk tabel `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `work_id` (`work_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `readinghistory`
--
ALTER TABLE `readinghistory`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `chapter_id` (`chapter_id`);

--
-- Indeks untuk tabel `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reported_user_id` (`reported_user_id`),
  ADD KEY `reporter_id` (`reporter_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `username` (`username`),
  ADD KEY `email` (`email`);

--
-- Indeks untuk tabel `works`
--
ALTER TABLE `works`
  ADD PRIMARY KEY (`id`),
  ADD KEY `author_id` (`author_id`),
  ADD KEY `fk_category` (`category_id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `bans`
--
ALTER TABLE `bans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `chaptermarks`
--
ALTER TABLE `chaptermarks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `chapters`
--
ALTER TABLE `chapters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- AUTO_INCREMENT untuk tabel `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `readinghistory`
--
ALTER TABLE `readinghistory`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `works`
--
ALTER TABLE `works`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `chaptermarks`
--
ALTER TABLE `chaptermarks`
  ADD CONSTRAINT `chaptermarks_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `chaptermarks_ibfk_2` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`);

--
-- Ketidakleluasaan untuk tabel `chapters`
--
ALTER TABLE `chapters`
  ADD CONSTRAINT `chapters_ibfk_1` FOREIGN KEY (`work_id`) REFERENCES `works` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `chapters_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`work_id`) REFERENCES `works` (`id`),
  ADD CONSTRAINT `comments_ibfk_4` FOREIGN KEY (`parent_comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`work_id`) REFERENCES `works` (`id`),
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `readinghistory`
--
ALTER TABLE `readinghistory`
  ADD CONSTRAINT `readinghistory_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `readinghistory_ibfk_2` FOREIGN KEY (`chapter_id`) REFERENCES `chapters` (`id`);

--
-- Ketidakleluasaan untuk tabel `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`reported_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `works`
--
ALTER TABLE `works`
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `works_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
