-- phpMyAdmin SQL Dump
-- version 4.5.3.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jan 18, 2016 at 02:21 PM
-- Server version: 5.5.46-0ubuntu0.14.04.2
-- PHP Version: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `stk`
--

-- --------------------------------------------------------

--
-- Table structure for table `stk_city`
--

CREATE TABLE `stk_city` (
  `cityId` int(11) NOT NULL,
  `cityName` varchar(200) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0=disable, 1= enable'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `stk_city`
--

INSERT INTO `stk_city` (`cityId`, `cityName`, `status`) VALUES
(1, 'Boston', 1),
(2, 'NewYork', 1);

-- --------------------------------------------------------

--
-- Table structure for table `stk_venue`
--

CREATE TABLE `stk_venue` (
  `venueId` int(11) NOT NULL,
  `venueCityId` int(11) NOT NULL,
  `venueName` varchar(200) NOT NULL,
  `venueContact` varchar(200) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '0=disable, 1= enable'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `stk_venue`
--

INSERT INTO `stk_venue` (`venueId`, `venueCityId`, `venueName`, `venueContact`, `status`) VALUES
(1, 1, 'Night club', '+12541541243', 1),
(2, 1, 'Dance club', '+12541541245', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `stk_city`
--
ALTER TABLE `stk_city`
  ADD PRIMARY KEY (`cityId`);

--
-- Indexes for table `stk_venue`
--
ALTER TABLE `stk_venue`
  ADD PRIMARY KEY (`venueId`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `stk_city`
--
ALTER TABLE `stk_city`
  MODIFY `cityId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `stk_venue`
--
ALTER TABLE `stk_venue`
  MODIFY `venueId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
