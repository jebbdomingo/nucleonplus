-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 31, 2016 at 07:41 AM
-- Server version: 5.5.46-0ubuntu0.14.04.2
-- PHP Version: 5.6.15-1+deb.sury.org~trusty+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sites_nucleonplus`
--

-- --------------------------------------------------------

--
-- Table structure for table `j_qbsync_customers`
--

CREATE TABLE IF NOT EXISTS `j_qbsync_customers` (
  `qbsync_customer_id` int(11) NOT NULL AUTO_INCREMENT,
  `action` varchar(50) NOT NULL,
  `account_id` int(11) NOT NULL,
  `CustomerRef` int(11) NOT NULL,
  `DisplayName` varchar(255) NOT NULL,
  `PrimaryPhone` varchar(255) NOT NULL,
  `Mobile` varchar(255) NOT NULL,
  `PrimaryEmailAddr` varchar(255) NOT NULL,
  `PrintOnCheckName` varchar(255) NOT NULL,
  `Line1` varchar(255) NOT NULL,
  `City` varchar(255) NOT NULL,
  `State` varchar(255) NOT NULL,
  `PostalCode` varchar(50) NOT NULL,
  `Country` varchar(255) NOT NULL,
  `synced` varchar(3) NOT NULL DEFAULT 'no',
  PRIMARY KEY (`qbsync_customer_id`),
  KEY `name` (`Mobile`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `j_qbsync_salesreceiptlines`
--

CREATE TABLE IF NOT EXISTS `j_qbsync_salesreceiptlines` (
  `qbsync_salesreceiptline_id` int(11) NOT NULL AUTO_INCREMENT,
  `SalesReceipt` int(11) NOT NULL,
  `Description` varchar(255) NOT NULL,
  `ItemRef` int(11) NOT NULL,
  `Qty` int(11) NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  PRIMARY KEY (`qbsync_salesreceiptline_id`),
  KEY `name` (`ItemRef`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `j_qbsync_salesreceipts`
--

CREATE TABLE IF NOT EXISTS `j_qbsync_salesreceipts` (
  `qbsync_salesreceipt_id` int(11) NOT NULL AUTO_INCREMENT,
  `DepositToAccountRef` int(11) NOT NULL,
  `DocNumber` int(11) NOT NULL,
  `TxnDate` datetime NOT NULL,
  `CustomerRef` int(11) NOT NULL,
  `synced` varchar(3) NOT NULL DEFAULT 'no',
  PRIMARY KEY (`qbsync_salesreceipt_id`),
  KEY `name` (`TxnDate`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `j_qbsync_transfers`
--

CREATE TABLE IF NOT EXISTS `j_qbsync_transfers` (
  `qbsync_transfer_id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `FromAccountRef` int(11) NOT NULL,
  `ToAccountRef` int(11) NOT NULL,
  `Amount` decimal(10,2) NOT NULL,
  `PrivateNote` varchar(255) NOT NULL,
  `synced` varchar(3) NOT NULL DEFAULT 'no',
  PRIMARY KEY (`qbsync_transfer_id`),
  KEY `name` (`PrivateNote`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
