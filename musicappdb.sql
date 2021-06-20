-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 16, 2021 at 10:42 AM
-- Server version: 10.4.19-MariaDB
-- PHP Version: 8.0.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `musicappdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `artist`
--

CREATE TABLE `artist` (
  `artist_id` int(10) UNSIGNED NOT NULL,
  `artist_name` varchar(45) NOT NULL,
  `description` varchar(500) DEFAULT NULL,
  `user_account_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `artist_has_track`
--

CREATE TABLE `artist_has_track` (
  `artist_id` int(10) UNSIGNED NOT NULL,
  `track_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `genre`
--

CREATE TABLE `genre` (
  `genre_id` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `genre`
--

INSERT INTO `genre` (`genre_id`) VALUES
('Alternative'),
('Blues'),
('Children&#039;s Music'),
('Classical'),
('Comedy'),
('Commercial'),
('Country'),
('Dance'),
('Easy Listening'),
('Electronic'),
('Folk Music'),
('French Pop'),
('German Folk'),
('German Pop'),
('Hip&#045;Hop'),
('Holiday'),
('Indie Pop'),
('Industrial'),
('Instrumental'),
('Jazz'),
('Latin'),
('Metal'),
('New Age'),
('Opera'),
('Pop'),
('Post&#045;Disco'),
('R&#038;B'),
('Reggae'),
('Rock'),
('Soul'),
('Soundtrack'),
('Spoken Word'),
('Vocal'),
('World');

-- --------------------------------------------------------

--
-- Table structure for table `playlist`
--

CREATE TABLE `playlist` (
  `playlist_id` int(10) UNSIGNED NOT NULL,
  `playlist_name` varchar(45) NOT NULL,
  `cover_file` mediumblob NOT NULL,
  `playlist_type_id` varchar(6) NOT NULL,
  `artist_id` int(10) UNSIGNED NOT NULL,
  `user_account_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `playlist_type`
--

CREATE TABLE `playlist_type` (
  `playlist_type_id` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `playlist_type`
--

INSERT INTO `playlist_type` (`playlist_type_id`) VALUES
('ALBUM'),
('EP'),
('LP'),
('SINGLE');

-- --------------------------------------------------------

--
-- Table structure for table `track`
--

CREATE TABLE `track` (
  `track_id` int(10) UNSIGNED NOT NULL,
  `track_name` varchar(50) NOT NULL,
  `track_file` mediumblob NOT NULL,
  `order_in_playlist` int(10) UNSIGNED NOT NULL,
  `playlist_id` int(10) UNSIGNED NOT NULL,
  `genre_id` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user_account`
--

CREATE TABLE `user_account` (
  `user_account_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(45) NOT NULL,
  `surname` varchar(45) NOT NULL,
  `login` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `signUpDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `artist`
--
ALTER TABLE `artist`
  ADD PRIMARY KEY (`artist_id`,`user_account_id`),
  ADD KEY `fk_artist_user_account1_idx` (`user_account_id`);

--
-- Indexes for table `artist_has_track`
--
ALTER TABLE `artist_has_track`
  ADD PRIMARY KEY (`artist_id`,`track_id`),
  ADD KEY `fk_Artist_has_Track_Track1_idx` (`track_id`),
  ADD KEY `fk_Artist_has_Track_Artist1_idx` (`artist_id`);

--
-- Indexes for table `genre`
--
ALTER TABLE `genre`
  ADD PRIMARY KEY (`genre_id`);

--
-- Indexes for table `playlist`
--
ALTER TABLE `playlist`
  ADD PRIMARY KEY (`playlist_id`,`playlist_type_id`,`artist_id`,`user_account_id`),
  ADD UNIQUE KEY `cover_path_UNIQUE` (`cover_file`) USING HASH,
  ADD KEY `fk_Playlist_Playlist_type_idx` (`playlist_type_id`),
  ADD KEY `fk_playlist_artist1_idx` (`artist_id`),
  ADD KEY `fk_playlist_user_account1_idx` (`user_account_id`);

--
-- Indexes for table `playlist_type`
--
ALTER TABLE `playlist_type`
  ADD PRIMARY KEY (`playlist_type_id`);

--
-- Indexes for table `track`
--
ALTER TABLE `track`
  ADD PRIMARY KEY (`track_id`,`playlist_id`,`genre_id`),
  ADD UNIQUE KEY `track_path_UNIQUE` (`track_file`) USING HASH,
  ADD KEY `fk_Track_Playlist1_idx` (`playlist_id`),
  ADD KEY `fk_track_genre1_idx` (`genre_id`);

--
-- Indexes for table `user_account`
--
ALTER TABLE `user_account`
  ADD PRIMARY KEY (`user_account_id`),
  ADD UNIQUE KEY `login_UNIQUE` (`login`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `artist`
--
ALTER TABLE `artist`
  MODIFY `artist_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `playlist`
--
ALTER TABLE `playlist`
  MODIFY `playlist_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `track`
--
ALTER TABLE `track`
  MODIFY `track_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_account`
--
ALTER TABLE `user_account`
  MODIFY `user_account_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `artist`
--
ALTER TABLE `artist`
  ADD CONSTRAINT `fk_artist_user_account1` FOREIGN KEY (`user_account_id`) REFERENCES `user_account` (`user_account_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `artist_has_track`
--
ALTER TABLE `artist_has_track`
  ADD CONSTRAINT `fk_Artist_has_Track_Artist1` FOREIGN KEY (`artist_id`) REFERENCES `artist` (`artist_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_Artist_has_Track_Track1` FOREIGN KEY (`track_id`) REFERENCES `track` (`track_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `playlist`
--
ALTER TABLE `playlist`
  ADD CONSTRAINT `fk_Playlist_Playlist_type` FOREIGN KEY (`playlist_type_id`) REFERENCES `playlist_type` (`playlist_type_id`),
  ADD CONSTRAINT `fk_playlist_artist1` FOREIGN KEY (`artist_id`) REFERENCES `artist` (`artist_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_playlist_user_account1` FOREIGN KEY (`user_account_id`) REFERENCES `user_account` (`user_account_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `track`
--
ALTER TABLE `track`
  ADD CONSTRAINT `fk_Track_Playlist1` FOREIGN KEY (`playlist_id`) REFERENCES `playlist` (`playlist_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_track_genre1` FOREIGN KEY (`genre_id`) REFERENCES `genre` (`genre_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
