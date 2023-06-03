-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Jun 03, 2023 at 03:08 PM
-- Server version: 5.7.39
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library`
--

-- --------------------------------------------------------

--
-- Table structure for table `book`
--

CREATE TABLE `book` (
  `isbn` char(13) NOT NULL,
  `page_no` int(11) NOT NULL COMMENT 'Range 1 to 5000',
  `abstract` longtext,
  `available_copies` int(11) DEFAULT NULL,
  `image` blob,
  `language` varchar(45) NOT NULL,
  `title` varchar(45) NOT NULL,
  `publisher` varchar(45) NOT NULL,
  `school_id` int(11) NOT NULL,
  `reserved_copies` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `borrowing`
--

CREATE TABLE `borrowing` (
  `user_id` int(11) NOT NULL,
  `date_of_borrowing` date NOT NULL,
  `isbn` char(13) NOT NULL,
  `borrow_duration` int(11) NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` varchar(45) NOT NULL DEFAULT 'active',
  `activation` varchar(45) NOT NULL DEFAULT 'disabled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Triggers `borrowing`
--
DELIMITER $$
CREATE TRIGGER `update_borrowed_books` AFTER UPDATE ON `borrowing` FOR EACH ROW BEGIN
  UPDATE user
  SET borrowed_books = (SELECT COUNT(*) FROM borrowing WHERE user_id = NEW.user_id)
  WHERE user_id = NEW.user_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `field`
--

CREATE TABLE `field` (
  `field_name` varchar(45) NOT NULL,
  `isbn` char(13) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `key_word`
--

CREATE TABLE `key_word` (
  `word` varchar(45) NOT NULL,
  `isbn` char(13) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `reservation`
--

CREATE TABLE `reservation` (
  `user_id` int(11) NOT NULL,
  `date_of_reservation` date NOT NULL,
  `isbn` char(13) NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `expire_date` date DEFAULT NULL,
  `activation` varchar(45) NOT NULL DEFAULT 'disabled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `user_id` int(11) NOT NULL,
  `review_text` longtext NOT NULL,
  `isbn` char(13) NOT NULL,
  `likert` int(1) NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `school`
--

CREATE TABLE `school` (
  `school_id` int(11) NOT NULL,
  `school_name` varchar(45) NOT NULL,
  `mail_address` varchar(45) NOT NULL,
  `city` varchar(45) NOT NULL,
  `phone` char(10) NOT NULL,
  `email` varchar(45) NOT NULL,
  `principal_name` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `username` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  `role_id` int(11) NOT NULL,
  `controller_name` varchar(45) DEFAULT NULL,
  `borrowed_books` int(11) NOT NULL DEFAULT '0',
  `school_id` int(11) DEFAULT NULL,
  `last_login` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `reserved_copies` int(11) NOT NULL DEFAULT '0',
  `date_of_birth` date DEFAULT NULL,
  `first_name` varchar(45) DEFAULT NULL,
  `last_name` varchar(45) DEFAULT NULL,
  `status` varchar(45) NOT NULL DEFAULT 'disabled'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `writer`
--

CREATE TABLE `writer` (
  `last_name` varchar(45) NOT NULL,
  `first_name` varchar(45) NOT NULL,
  `isbn` char(13) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `book`
--
ALTER TABLE `book`
  ADD PRIMARY KEY (`isbn`,`school_id`),
  ADD KEY `fk_school_id2` (`school_id`),
  ADD KEY `isbn_idx` (`isbn`),
  ADD KEY `isbn_idx2` (`isbn`);

--
-- Indexes for table `borrowing`
--
ALTER TABLE `borrowing`
  ADD PRIMARY KEY (`user_id`,`isbn`,`date_of_borrowing`),
  ADD KEY `fk_user_id2_idx` (`user_id`),
  ADD KEY `fk_isbn_borrowing_idx` (`isbn`);

--
-- Indexes for table `field`
--
ALTER TABLE `field`
  ADD PRIMARY KEY (`field_name`,`isbn`),
  ADD KEY `fk_isbn_field_idx` (`isbn`);

--
-- Indexes for table `key_word`
--
ALTER TABLE `key_word`
  ADD PRIMARY KEY (`word`,`isbn`),
  ADD KEY `fk_isbn_word_idx` (`isbn`);

--
-- Indexes for table `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`user_id`,`isbn`),
  ADD KEY `fk_user_id3_idx` (`user_id`),
  ADD KEY `fk_isbn_reservation_idx` (`isbn`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`user_id`,`isbn`),
  ADD KEY `fk_user_id4_idx` (`user_id`),
  ADD KEY `fk_isbn_review_idx` (`isbn`);

--
-- Indexes for table `school`
--
ALTER TABLE `school`
  ADD PRIMARY KEY (`school_id`),
  ADD UNIQUE KEY `phone_UNIQUE` (`phone`),
  ADD UNIQUE KEY `email_UNIQUE` (`email`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username_UNIQUE` (`username`),
  ADD KEY `fk_school_id_idx` (`school_id`);

--
-- Indexes for table `writer`
--
ALTER TABLE `writer`
  ADD PRIMARY KEY (`first_name`,`last_name`,`isbn`),
  ADD KEY `fk_isbn_idx` (`isbn`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `school`
--
ALTER TABLE `school`
  MODIFY `school_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `book`
--
ALTER TABLE `book`
  ADD CONSTRAINT `fk_school_id2` FOREIGN KEY (`school_id`) REFERENCES `school` (`school_id`);

--
-- Constraints for table `borrowing`
--
ALTER TABLE `borrowing`
  ADD CONSTRAINT `fk_isbn_borrowing` FOREIGN KEY (`isbn`) REFERENCES `book` (`isbn`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_id2` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `field`
--
ALTER TABLE `field`
  ADD CONSTRAINT `fk_isbn_field` FOREIGN KEY (`isbn`) REFERENCES `book` (`isbn`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `key_word`
--
ALTER TABLE `key_word`
  ADD CONSTRAINT `fk_isbn_word` FOREIGN KEY (`isbn`) REFERENCES `book` (`isbn`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `fk_isbn_reservation` FOREIGN KEY (`isbn`) REFERENCES `book` (`isbn`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_reservation` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `fk_isbn_review` FOREIGN KEY (`isbn`) REFERENCES `book` (`isbn`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_user_review` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_school_id` FOREIGN KEY (`school_id`) REFERENCES `school` (`school_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `writer`
--
ALTER TABLE `writer`
  ADD CONSTRAINT `fk_isbn_writer` FOREIGN KEY (`isbn`) REFERENCES `book` (`isbn`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
