-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 31, 2016 at 02:37 AM
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
-- Table structure for table `#__nucleonplus_accounts`
--

CREATE TABLE IF NOT EXISTS `#__nucleonplus_accounts` (
  `nucleonplus_account_id` int(11) NOT NULL,
  `account_number` varchar(50) DEFAULT NULL,
  `sponsor_id` varchar(50) DEFAULT NULL COMMENT 'Sponsor''s account_number',
  `user_id` int(11) NOT NULL,
  `CustomerRef` int(11) NOT NULL COMMENT 'Customer reference in QBO',
  `note` varchar(255) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'new',
  `bank_account_number` varchar(50) NOT NULL,
  `bank_account_name` varchar(50) NOT NULL,
  `bank_account_type` varchar(50) NOT NULL,
  `bank_account_branch` varchar(50) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `mobile` varchar(255) NOT NULL,
  `street` varchar(255) NOT NULL,
  `city` varchar(255) NOT NULL,
  `state` varchar(255) NOT NULL,
  `postal_code` varchar(255) NOT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `modified_on` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  PRIMARY KEY (`nucleonplus_account_id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `nucleonplus_account_id` (`nucleonplus_account_id`),
  KEY `account_number` (`account_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `#__nucleonplus_items`
--

CREATE TABLE IF NOT EXISTS `#__nucleonplus_items` (
  `nucleonplus_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `inventory_item_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,0) NOT NULL,
  PRIMARY KEY (`nucleonplus_item_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

--
-- Dumping data for table `#__nucleonplus_items`
--

INSERT INTO `#__nucleonplus_items` (`nucleonplus_item_id`, `inventory_item_id`, `name`, `price`) VALUES
(1, 35, 'TUM Chocolate Drink', 140);

-- --------------------------------------------------------

--
-- Table structure for table `#__nucleonplus_orders`
--

CREATE TABLE IF NOT EXISTS `#__nucleonplus_orders` (
  `nucleonplus_order_id` int(11) NOT NULL AUTO_INCREMENT,
  `package_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `package_name` varchar(255) NOT NULL,
  `package_price` decimal(10,0) NOT NULL,
  `order_status` varchar(50) NOT NULL,
  `invoice_status` varchar(50) NOT NULL,
  `payment_reference` varchar(255) NOT NULL COMMENT 'e.g. deposit reference number',
  `payment_method` varchar(50) NOT NULL,
  `shipping_method` varchar(50) NOT NULL,
  `tracking_reference` text NOT NULL,
  `note` text NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_on` datetime NOT NULL,
  PRIMARY KEY (`nucleonplus_order_id`),
  KEY `account_number` (`account_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1001 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__nucleonplus_packageitems`
--

CREATE TABLE IF NOT EXISTS `#__nucleonplus_packageitems` (
  `nucleonplus_packageitem_id` int(11) NOT NULL AUTO_INCREMENT,
  `package_id` int(11) NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  PRIMARY KEY (`nucleonplus_packageitem_id`),
  KEY `name` (`package_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

--
-- Dumping data for table `#__nucleonplus_packageitems`
--

INSERT INTO `#__nucleonplus_packageitems` (`nucleonplus_packageitem_id`, `package_id`, `item_id`, `quantity`) VALUES
(1, 1, 1, 1),
(2, 2, 1, 2),
(3, 3, 1, 3),
(4, 4, 1, 4),
(5, 5, 1, 5),
(6, 6, 1, 6),
(7, 7, 1, 7),
(8, 8, 1, 8),
(9, 9, 1, 9),
(10, 10, 1, 10);

-- --------------------------------------------------------

--
-- Table structure for table `#__nucleonplus_packages`
--

CREATE TABLE IF NOT EXISTS `#__nucleonplus_packages` (
  `nucleonplus_package_id` int(11) NOT NULL AUTO_INCREMENT,
  `rewardpackage_id` int(11) NOT NULL,
  `acctg_item_id` int(11) NOT NULL COMMENT 'Item ID in the accounting system',
  `acctg_item_delivery_id` int(11) NOT NULL COMMENT 'Item ID with delivery charge in the accounting system',
  `name` varchar(255) NOT NULL,
  `price` decimal(10,0) NOT NULL,
  `delivery_charge` decimal(10,2) NOT NULL,
  PRIMARY KEY (`nucleonplus_package_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

--
-- Dumping data for table `#__nucleonplus_packages`
--

INSERT INTO `#__nucleonplus_packages` (`nucleonplus_package_id`, `rewardpackage_id`, `acctg_item_id`, `acctg_item_delivery_id`, `name`, `price`, `delivery_charge`) VALUES
(1, 1, 36, 46, 'Package 1', 1500, 99.00),
(2, 2, 37, 47, 'Package 2', 3000, 198.00),
(3, 3, 38, 48, 'Package 3', 4500, 297.00),
(4, 4, 39, 49, 'Package 4', 6000, 396.00),
(5, 5, 40, 50, 'Package 5', 7500, 495.00),
(6, 6, 41, 51, 'Package 6', 9000, 594.00),
(7, 7, 42, 52, 'Package 7', 10500, 693.00),
(8, 8, 43, 53, 'Package 8', 12000, 792.00),
(9, 9, 44, 54, 'Package 9', 13500, 891.00),
(10, 10, 45, 55, 'Package 10', 15000, 990.00);

-- --------------------------------------------------------

--
-- Table structure for table `#__nucleonplus_payouts`
--

CREATE TABLE IF NOT EXISTS `#__nucleonplus_payouts` (
  `nucleonplus_payout_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL COMMENT 'Total amount of referral bonus and rebates',
  `status` varchar(50) DEFAULT NULL,
  `void` smallint(1) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_on` datetime NOT NULL,
  PRIMARY KEY (`nucleonplus_payout_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1001 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__nucleonplus_rebates`
--

CREATE TABLE IF NOT EXISTS `#__nucleonplus_rebates` (
  `nucleonplus_rebate_id` int(11) NOT NULL AUTO_INCREMENT,
  `reward_id_from` int(11) NOT NULL COMMENT 'The Order''s Reward of other Member that pays the reward_id_to',
  `reward_id_to` int(11) NOT NULL COMMENT 'The reward that is paid by the reward_id_from',
  `points` decimal(10,2) NOT NULL,
  `void` smallint(1) NOT NULL DEFAULT '0',
  `created_by` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_on` datetime NOT NULL,
  PRIMARY KEY (`nucleonplus_rebate_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__nucleonplus_referralbonuses`
--

CREATE TABLE IF NOT EXISTS `#__nucleonplus_referralbonuses` (
  `nucleonplus_referralbonus_id` int(11) NOT NULL AUTO_INCREMENT,
  `reward_id` int(11) NOT NULL COMMENT 'The Order''s Reward of other Member that pays the account_id',
  `account_id` int(11) NOT NULL COMMENT 'Account ID of the Referrer',
  `referral_type` varchar(50) NOT NULL COMMENT 'dr => direct referral, ir => indirect referral. Based on the Reward of reward_id',
  `points` decimal(10,2) NOT NULL,
  `void` smallint(1) NOT NULL DEFAULT '0',
  `payout_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `created_on` datetime NOT NULL,
  `modified_by` int(11) NOT NULL,
  `modified_on` datetime NOT NULL,
  PRIMARY KEY (`nucleonplus_referralbonus_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__nucleonplus_rewardpackages`
--

CREATE TABLE IF NOT EXISTS `#__nucleonplus_rewardpackages` (
  `nucleonplus_rewardpackage_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `slots` smallint(2) NOT NULL COMMENT 'Number of slots in the rewards sytem',
  `prpv` smallint(6) NOT NULL COMMENT 'Product Rebate Point Value',
  `drpv` smallint(6) NOT NULL COMMENT 'Direct Referral Point Value',
  `irpv` smallint(6) NOT NULL COMMENT 'Indirect Referral Point Value',
  PRIMARY KEY (`nucleonplus_rewardpackage_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

--
-- Dumping data for table `#__nucleonplus_rewardpackages`
--

INSERT INTO `#__nucleonplus_rewardpackages` (`nucleonplus_rewardpackage_id`, `name`, `description`, `slots`, `prpv`, `drpv`, `irpv`) VALUES
(1, 'Reward 1', '1 Slot in the Rewards Sytem', 1, 1000, 50, 10),
(2, 'Reward 2', '2 Slots in the Rewards Sytem', 2, 1000, 50, 10),
(3, 'Reward 3', '3 Slots in the Rewards Sytem', 3, 1000, 50, 10),
(4, 'Reward 4', '4 Slots in the Rewards Sytem', 4, 1000, 50, 10),
(5, 'Reward 5', '5 Slots in the Rewards System', 5, 1000, 50, 10),
(6, 'Reward 6', '6 Slots in the Rewards System', 6, 1000, 50, 10),
(7, 'Reward 7', '7 Slots in the Rewards System', 7, 1000, 50, 10),
(8, 'Reward 8', '8 Slots in the Rewards System', 8, 1000, 50, 10),
(9, 'Reward 9', '9 Slots in the Rewards System', 9, 1000, 50, 10),
(10, 'Reward 10', '10 Slots in the Rewards System', 10, 1000, 50, 10);

-- --------------------------------------------------------

--
-- Table structure for table `#__nucleonplus_rewards`
--

CREATE TABLE IF NOT EXISTS `#__nucleonplus_rewards` (
  `nucleonplus_reward_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT 'Order ID',
  `product_name` varchar(50) NOT NULL,
  `customer_id` int(11) NOT NULL COMMENT 'Account ID',
  `rewardpackage_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `payout_id` int(11) NOT NULL,
  `slots` int(11) NOT NULL COMMENT 'Number of Slots',
  `prpv` smallint(6) NOT NULL COMMENT 'Product Rebate Point Value',
  `drpv` smallint(6) NOT NULL COMMENT 'Direct Referral Point Value',
  `irpv` smallint(6) NOT NULL COMMENT 'Indirect Referral Point Value',
  `void` smallint(1) NOT NULL,
  PRIMARY KEY (`nucleonplus_reward_id`),
  KEY `status` (`status`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `#__nucleonplus_slots`
--

CREATE TABLE IF NOT EXISTS `#__nucleonplus_slots` (
  `nucleonplus_slot_id` int(11) NOT NULL AUTO_INCREMENT,
  `reward_id` int(11) NOT NULL,
  `lf_slot_id` int(11) NOT NULL COMMENT 'Slot in the left leg',
  `rt_slot_id` int(11) NOT NULL COMMENT 'Slot in the right leg',
  `consumed` smallint(1) NOT NULL DEFAULT '0' COMMENT 'Indicates if this slot was place under another slot',
  `void` smallint(1) NOT NULL DEFAULT '0',
  `created_by` int(11) DEFAULT NULL COMMENT 'The user who created this slot',
  `created_on` datetime DEFAULT NULL COMMENT 'Date this slot was created',
  `modified_by` int(11) NOT NULL,
  `modified_on` datetime NOT NULL,
  PRIMARY KEY (`nucleonplus_slot_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
