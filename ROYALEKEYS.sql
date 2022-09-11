-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Aug 31, 2022 at 09:55 AM
-- Server version: 10.3.34-MariaDB-0ubuntu0.20.04.1
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `envolabs`
--

-- --------------------------------------------------------

--
-- Table structure for table `ROYALEKEYS`
--

DROP TABLE IF EXISTS `ROYALEKEYS`;
CREATE TABLE IF NOT EXISTS `ROYALEKEYS` (
  `EID` bigint(20) NOT NULL AUTO_INCREMENT,
  `ELASTDATE` datetime NOT NULL DEFAULT current_timestamp(),
  `EGROUP` varchar(64) NOT NULL DEFAULT '',
  `EKEY` varchar(64) NOT NULL DEFAULT '',
  `EVALUE` varchar(92) NOT NULL DEFAULT '',
  PRIMARY KEY (`EID`),
  KEY `EGROUP` (`EGROUP`),
  KEY `EKEY` (`EKEY`),
  KEY `MYTIME` (`ELASTDATE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;