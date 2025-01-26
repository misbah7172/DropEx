-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 26, 2025 at 07:29 PM
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
-- Database: `DropEx`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_credentials`
--

CREATE TABLE `admin_credentials` (
  `admin_id` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `last_login` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_credentials`
--

INSERT INTO `admin_credentials` (`admin_id`, `password`, `name`, `email`, `last_login`) VALUES
('999', '1234', 'Elon Mask', 'iamelon@gmail.com', '2025-01-27 00:02:50');

-- --------------------------------------------------------

--
-- Stand-in structure for view `arrived`
-- (See below for the actual view)
--
CREATE TABLE `arrived` (
`TrackingID` int(11)
,`StaffID` varchar(30)
,`S_Name` varchar(30)
,`S_Add` varchar(50)
,`S_City` varchar(20)
,`S_State` varchar(20)
,`S_Contact` bigint(20)
,`R_Name` varchar(30)
,`R_Add` varchar(50)
,`R_City` varchar(20)
,`R_State` varchar(20)
,`R_Contact` bigint(20)
,`Weight_Kg` decimal(10,2)
,`Price` decimal(10,2)
,`Dispatched_Time` timestamp
,`Shipped` timestamp
,`Out_for_delivery` timestamp
,`Delivered` timestamp
);

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `Address` varchar(100) NOT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `Contact` bigint(20) NOT NULL,
  `Email` varchar(40) NOT NULL,
  `Manager_id` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`Address`, `city`, `state`, `Contact`, `Email`, `Manager_id`) VALUES
('Paris, France', 'Paris', 'France', 331234567890, 'paris_branch@gmail.com', 'DE1124'),
('Rome, Italy', 'Rome', 'Italy', 390612345678, 'rome_branch@gmail.com', 'DE1127'),
('Sydney, Australia', 'Sydney', 'Australia', 61298765432, 'sydney_branch@gmail.com', 'DE1129'),
('Moscow, Russia', 'Moscow', 'Russia', 74951234567, 'moscow_branch@gmail.com', 'DE1130'),
('Madrid, Spain', 'Madrid', 'Spain', 349112233445, 'madrid_branch@gmail.com', 'DE1131'),
('Tokyo, Japan', 'Tokyo', 'Japan', 813123456789, 'tokyo_branch@gmail.com', 'DE1135'),
('Dhaka, Bangladesh', 'Dhaka', 'Bangladesh', 880177654321, 'dhaka_branch@gmail.com', 'DE1137'),
('Berlin, Germany', 'Barlin', 'Germany', 493012345678, 'berlin_branch@gmail.com', 'DE1139'),
('Beijing, China', 'Beijing', 'China', 8613509876543, 'beijing_branch@gmail.com', 'DE1140'),
('New York, USA', 'New York', 'USA', 12123456789, 'newyork_branch@gmail.com', 'DE1141'),
('Cairo, Egypt', 'Cairo', 'Egypt', 2023456789, 'cairo_branch@gmail.com', 'DE1142'),
('Cape Town, South Africa', 'Cape Town', 'South Africa', 27821234567, 'capetown_branch@gmail.com', 'DE1143'),
('Bangkok, Thailand', 'Bangkok', 'Thailand', 66234567890, 'bangkok_branch@gmail.com', 'DE1144'),
('Hanoi, Vietnam', 'Hanoi', 'Vietnam', 84234567890, 'hanoi_branch@gmail.com', 'DE1145'),
('Singapore City, Singapore', 'Singapore City', 'Singapore', 6587654321, 'singapore_branch@gmail.com', 'DE1146'),
('Kuala Lumpur, Malaysia', 'Kuala Lumpur', 'Malaysia', 60387654321, 'kualalumpur_branch@gmail.com', 'DE1147'),
('Jakarta, Indonesia', 'Jakarta', 'Indonesia', 62212345678, 'jakarta_branch@gmail.com', 'DE1148'),
('Seoul, South Korea', 'Seoul', 'South Korea', 82234567890, 'seoul_branch@gmail.com', 'DE1149'),
('Manila, Philippines', 'Manila', 'Philippines', 63234567890, 'manila_branch@gmail.com', 'DE1150'),
('Rangoon, Myanmar', 'Rangoon', 'Myanmar', 95123456789, 'rangoon_branch@gmail.com', 'DE1151'),
('Phnom Penh, Cambodia', 'Phnom Penh', 'Cambodia', 85512345678, 'phnompenh_branch@gmail.com', 'DE1152'),
('Vientiane, Laos', 'Vientiane', 'Laos', 85612345678, 'vientiane_branch@gmail.com', 'DE1153'),
('Brunei City, Brunei', 'Brunei City', 'Brunei', 67312345678, 'brunei_branch@gmail.com', 'DE1154'),
('Ulaanbaatar, Mongolia', 'Ulaanbaatar', 'Mongolia', 97612345678, 'ulaanbaatar_branch@gmail.com', 'DE1155'),
('Ashgabat, Turkmenistan', 'Ashgabat', 'Turkmenistan', 99312345678, 'ashgabat_branch@gmail.com', 'DE1156'),
('Tashkent, Uzbekistan', 'Tashkent', 'Uzbekistan', 99812345678, 'tashkent_branch@gmail.com', 'DE1157'),
('Dushanbe, Tajikistan', 'Dushanbe', 'Tajikistan', 99212345678, 'dushanbe_branch@gmail.com', 'DE1158'),
('Nur-Sultan, Kazakhstan', 'Nur-Sultan', 'Kazakhstan', 77123456789, 'nursultan_branch@gmail.com', 'DE1159'),
('Bishkek, Kyrgyzstan', 'Bishkek', 'Kyrgyzstan', 99612345678, 'bishkek_branch@gmail.com', 'DE1160'),
('Yerevan, Armenia', 'Yerevan', 'Armenia', 37412345678, 'yerevan_branch@gmail.com', 'DE1161'),
('Baku, Azerbaijan', 'Baku', 'Azerbaijan', 99412345678, 'baku_branch@gmail.com', 'DE1163'),
('Kabul, Afghanistan', 'Kabul', 'Afghanistan', 93712345678, 'kabul_branch@gmail.com', 'DE1164'),
('Male, Maldives', 'Male', 'Maldives', 96012345678, 'male_branch@gmail.com', 'DE1165'),
('Colombo, Sri Lanka', 'Colombo', 'Sri Lanka', 94123456789, 'colombo_branch@gmail.com', 'DE1166'),
('Thimphu, Bhutan', 'Thimphu', 'Bhutan', 97512345678, 'thimphu_branch@gmail.com', 'DE1167'),
('Ulaanbaatar, Mongolia', 'Ulaanbaatar', 'Mongolia', 97698765432, 'ulaanbaatar_branch2@gmail.com', 'DE1168'),
('Yerevan, Armenia', 'Yerevan', 'Armenia', 37498765432, 'yerevan_branch2@gmail.com', 'DE1169'),
('Tbilisi, Georgia', 'Tbilisi', 'Georgia', 99598765432, 'tbilisi_branch2@gmail.com', 'DE1170'),
('Baku, Azerbaijan', 'Baku', 'Azerbaijan', 99498765432, 'baku_branch2@gmail.com', 'DE1171'),
('Kabul, Afghanistan', 'Kabul', 'Afghanistan', 93798765432, 'kabul_branch2@gmail.com', 'DE1172'),
('London, UK', 'London', 'UK', 442071234567, 'london_branch@gmail.com', 'DE9913');

-- --------------------------------------------------------

--
-- Stand-in structure for view `delivered`
-- (See below for the actual view)
--
CREATE TABLE `delivered` (
`TrackingID` int(11)
,`StaffID` varchar(30)
,`S_Name` varchar(30)
,`S_Add` varchar(50)
,`S_City` varchar(20)
,`S_State` varchar(20)
,`S_Contact` bigint(20)
,`R_Name` varchar(30)
,`R_Add` varchar(50)
,`R_City` varchar(20)
,`R_State` varchar(20)
,`R_Contact` bigint(20)
,`Weight_Kg` decimal(10,2)
,`Price` decimal(10,2)
,`Dispatched_Time` timestamp
,`Shipped` timestamp
,`Out_for_delivery` timestamp
,`Delivered` timestamp
);

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `f_id` int(11) NOT NULL,
  `Cust_name` varchar(30) NOT NULL,
  `Cust_mail` varchar(50) NOT NULL,
  `Cust_msg` varchar(500) NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`f_id`, `Cust_name`, `Cust_mail`, `Cust_msg`, `id`) VALUES
(2, 'Tahimul Amin', 'tah12@gmail.com', 'Good Service', 3),
(2, 'Tahimul Amin', 'tah12@gmail.com', 'Good Delivery', 4),
(2, 'Tahimul Amin', 'tah12@gmail.com', 'hello', 5),
(1, 'Tahamid ', 'tah@gmail.com', 'hiiii', 6);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `tracking_id` varchar(50) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `status_timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_read` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `online_request`
--

CREATE TABLE `online_request` (
  `serial` int(11) NOT NULL,
  `S_Name` varchar(30) NOT NULL,
  `S_Add` varchar(50) NOT NULL,
  `S_City` varchar(20) NOT NULL,
  `S_State` varchar(20) NOT NULL,
  `S_Contact` bigint(20) NOT NULL,
  `R_Name` varchar(30) NOT NULL,
  `R_Add` varchar(50) NOT NULL,
  `R_City` varchar(20) NOT NULL,
  `R_State` varchar(20) NOT NULL,
  `R_Contact` bigint(20) NOT NULL,
  `Weight_Kg` decimal(10,2) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Dispatched_Time` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `image` longblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `online_request`
--

INSERT INTO `online_request` (`serial`, `S_Name`, `S_Add`, `S_City`, `S_State`, `S_Contact`, `R_Name`, `R_Add`, `R_City`, `R_State`, `R_Contact`, `Weight_Kg`, `Price`, `Dispatched_Time`, `user_id`, `status`, `image`) VALUES
(14, 'Misbah', 'Dhaka', 'dhaka', 'Bangladesh', 123455, 'Neyamul', 'Italy', 'Paris', 'Italy', 6756342, 2.00, 2800.00, '2025-01-21 17:02:42', 2, 'approved', NULL),
(15, 'Tahimul', 'Dhaka, Bangladesh', 'Dhaka', 'Bangladesh', 12345678, 'Neyamul', 'Paris, Italy', 'Paris', 'Italy', 987654, 10.00, 14000.00, '2025-01-21 18:21:47', 2, 'approved', NULL),
(21, 'tahimul', 'drtfd', 'ddrd', 'Bangladesh', 2452, 'sdfsdf', 'sdfsd', 'sdf', 'Italy', 453, 2.00, 2800.00, '2025-01-21 18:53:48', 2, 'rejected', NULL),
(22, 'tahimul', 'weyrg', 'dsg', 'Bangladesh', 3463, 'sdfsd', 'dfg', 'dfg', 'Italy', 34535, 5.00, 7000.00, '2025-01-21 18:54:10', 2, 'approved', NULL),
(23, 'tahimul', 'ert', 'ert', 'Bangladesh', 3425, 'fgdfg', 'fdg', 'sdfg', 'Italy', 345, 21.00, 29400.00, '2025-01-21 18:56:19', 2, 'approved', NULL),
(24, 'Tahimul Amin', 'fgdfg', 'dfg', 'Bangladesh', 345, 'dfgd', 'fgdfg', 'dfg', 'Italy', 234, 30.00, 42000.00, '2025-01-21 18:58:52', 2, 'approved', NULL),
(25, 'tahimul', 'dfgss', 'dfs', 'Bangladesh', 3453254, 'dsfsdf', 'sdf', 'sdf', 'Italy', 234, 34.00, 47600.00, '2025-01-21 19:11:43', 2, 'approved', NULL),
(26, 'tahimul', 'sydcus', 'sdcs', 'Bangladesh', 23456, 'sds', 'sdcs', 'sdc', 'Italy', 3456, 4.00, 5600.00, '2025-01-21 20:54:05', 2, 'approved', NULL),
(27, 'tahimul', 'hgfgh', 'fdgh', 'Bangladesh', 7653456, 'misbah', 'jfhf', 'htdthd', 'Italy', 875, 45.00, 63000.00, '2025-01-21 21:55:06', 2, 'approved', NULL),
(28, 'tahimul', 'htfth', 'dgrd', 'Bangladesh', 756, 'hgfh', 'gdhgd', 'hgd', 'Italy', 765675, 23.00, 32200.00, '2025-01-21 21:57:49', 2, 'rejected', NULL),
(29, 'tahimul', 'sdfserg', 'srgserg', 'Bangladesh', 3414, 'fdgdfg', 'sgrg', 'rereg', 'Italy', 24525, 3.00, 4200.00, '2025-01-23 09:17:44', 2, 'approved', NULL),
(30, 'tahmid', 'werfewfg', 'rfgvsfdv', 'Bangladesh', 3452, 'fsdfg', 'sdfgsg', 'sdfgdg', 'Italy', 2342, 3.00, 4200.00, '2025-01-23 17:17:53', 1, 'approved', NULL),
(31, 'tahmid', 'dfsd', 'sdfgsgs', 'Bangladesh', 342, 'sdfsf', 'sdgsf', 'fgsdg', 'Italy', 24534, 5.00, 7000.00, '2025-01-23 18:02:18', 1, 'approved', ''),
(32, 'tahmid', 'hzgzfcxd', 'xcvzxv', 'Bangladesh', 4365, 'sdfd', 'xzdfs', 'dfsdf', 'Italy', 34234, 5.00, 7000.00, '2025-01-23 18:06:52', 1, 'approved', 0x53637265656e73686f745f323032342d31322d32335f31395f33315f31312e706e67),
(33, 'tahmid', 'werwr', 'werwr', 'Bangladesh', 234124, 'cvgbcb', 'dfgdfgc', 'fvbdfgb', 'Italy', 342, 1.00, 1400.00, '2025-01-23 18:52:38', 1, 'approved', 0x53637265656e73686f745f323032352d30312d31335f30305f30315f34352e706e67),
(34, 'tahmid', 'sdfsgdsfgvsfg', 'sdfsfd', 'Italy', 1413, 'wrtwer', 'fsdf', 'fsgs', 'Bangladesh', 2133, 3.00, 4200.00, '2025-01-24 14:08:08', 1, 'pending', 0x53637265656e73686f745f323032352d30312d31335f30305f30315f33332e706e67),
(35, 'tahmid', 'Gulshan', 'Dhaka', 'Bangladesh', 98765, 'Abir', 'sdfg', 'sjdf', 'Italy', 78654, 100.00, 140000.00, '2025-01-26 12:14:48', 1, 'approved', 0x53637265656e73686f745f32303235303131372d3136303134377e322e706e67);

-- --------------------------------------------------------

--
-- Table structure for table `parcel`
--

CREATE TABLE `parcel` (
  `request_id` int(11) DEFAULT NULL,
  `TrackingID` int(11) NOT NULL,
  `StaffID` varchar(30) NOT NULL,
  `S_Name` varchar(30) NOT NULL,
  `S_Add` varchar(50) NOT NULL,
  `S_City` varchar(20) NOT NULL,
  `S_State` varchar(20) NOT NULL,
  `S_Contact` bigint(20) NOT NULL,
  `R_Name` varchar(30) NOT NULL,
  `R_Add` varchar(50) NOT NULL,
  `R_City` varchar(20) NOT NULL,
  `R_State` varchar(20) NOT NULL,
  `R_Contact` bigint(20) NOT NULL,
  `Weight_Kg` decimal(10,2) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Dispatched_Time` timestamp NOT NULL DEFAULT current_timestamp(),
  `image` longblob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `parcel`
--

INSERT INTO `parcel` (`request_id`, `TrackingID`, `StaffID`, `S_Name`, `S_Add`, `S_City`, `S_State`, `S_Contact`, `R_Name`, `R_Add`, `R_City`, `R_State`, `R_Contact`, `Weight_Kg`, `Price`, `Dispatched_Time`, `image`) VALUES
(NULL, 1407, 'DE9913', 'Mehedi hasan', 'Australia , good people town 1300', 'Sydney', 'Australia', 87655, 'Afrin sultana', 'Japan', 'Tokyo', 'Japan', 654844845, 48.00, 52800.00, '2025-01-14 19:52:00', NULL),
(NULL, 1408, 'DE9913', 'Mosaraf karim', 'Australia ,  1300', 'Sydney', 'Australia', 4367900, 'Samrat', 'Brazil', 'Rio de Janeiro', 'Brazil', 77876558, 79.00, 126400.00, '2025-01-14 19:54:37', NULL),
(NULL, 1415, 'DE1122', 'Tahimul', 'Badda,Dhaka', 'Dhaka', 'Bangladesh', 4567654, 'Ramim', 'Tokyo, Japan,good people city 223', 'Tokyo,japan,house', 'Japan', 765434, 5.00, 9000.00, '2025-01-18 08:56:25', NULL),
(30, 146157, 'DE1137', 'tahmid', 'werfewfg', 'rfgvsfdv', 'Bangladesh', 3452, 'fsdfg', 'sdfgsg', 'sdfgdg', 'Italy', 2342, 3.00, 4200.00, '2025-01-23 17:17:53', NULL),
(NULL, 241711, 'DE8888', 'tahimul', 'sydcus', 'sdcs', 'Bangladesh', 23456, 'sds', 'sdcs', 'sdc', 'Italy', 3456, 4.00, 5600.00, '2025-01-21 20:54:05', NULL),
(31, 252965, 'DE1137', 'tahmid', 'dfsd', 'sdfgsgs', 'Bangladesh', 342, 'sdfsf', 'sdgsf', 'fgsdg', 'Italy', 24534, 5.00, 7000.00, '2025-01-23 18:02:18', ''),
(NULL, 374600, 'DE1124', 'dfgsdg', 'sdfgsdg', 'sdfsdf', 'France', 4345, 'fdsgs', 'sdfg', 'sdfgdh', 'Italy', 324, 3.00, 4800.00, '2025-01-21 12:39:40', NULL),
(NULL, 392643, 'DE1137', 'asfasf', 'wetgtw', 'regq', 'Bangladesh', 2142, 'dfsdf', 'sdg', 'sdg', 'Italy', 3552352, 2.00, 2800.00, '2025-01-20 06:08:44', NULL),
(NULL, 417492, 'DE8888', 'Tahimul Amin', 'fgdfg', 'dfg', 'Bangladesh', 345, 'dfgd', 'fgdfg', 'dfg', 'Italy', 234, 30.00, 42000.00, '2025-01-21 18:58:52', NULL),
(NULL, 422046, 'DE1124', 'asfasf', 'wetgtw', 'regq', 'Bangladesh', 2142, 'dfsdf', 'sdg', 'sdg', 'Italy', 3552352, 2.00, 2800.00, '2025-01-20 06:08:44', NULL),
(NULL, 459215, 'DE1137', 'tahmid', 'hzgzfcxd', 'xcvzxv', 'Bangladesh', 4365, 'sdfd', 'xzdfs', 'dfsdf', 'Italy', 34234, 5.00, 7000.00, '2025-01-23 18:06:52', 0x53637265656e73686f745f323032342d31322d32335f31395f33315f31312e706e67),
(NULL, 478674, 'DE8888', 'tahimul', 'ert', 'ert', 'Bangladesh', 3425, 'fgdfg', 'fdg', 'sdfg', 'Italy', 345, 21.00, 29400.00, '2025-01-21 18:56:19', NULL),
(35, 544364, 'DE1137', 'tahmid', 'Gulshan', 'Dhaka', 'Bangladesh', 98765, 'Abir', 'sdfg', 'sjdf', 'Italy', 78654, 100.00, 140000.00, '2025-01-26 12:14:48', 0x53637265656e73686f745f32303235303131372d3136303134377e322e706e67),
(NULL, 549849, 'DE8888', 'tahimul', 'dfgss', 'dfs', 'Bangladesh', 3453254, 'dsfsdf', 'sdf', 'sdf', 'Italy', 234, 34.00, 47600.00, '2025-01-21 19:11:43', NULL),
(NULL, 637927, 'DE1137', 'tahmid', 'werwr', 'werwr', 'Bangladesh', 234124, 'cvgbcb', 'dfgdfgc', 'fvbdfgb', 'Italy', 342, 1.00, 1400.00, '2025-01-23 18:52:38', 0x53637265656e73686f745f323032352d30312d31335f30305f30315f34352e706e67),
(NULL, 667565, 'DE1137', 'asfasf', 'wetgtw', 'regq', 'Bangladesh', 2142, 'dfsdf', 'sdg', 'sdg', 'Italy', 3552352, 2.00, 2800.00, '2025-01-20 06:08:44', NULL),
(NULL, 674046, 'DE8888', 'tahimul', 'weyrg', 'dsg', 'Bangladesh', 3463, 'sdfsd', 'dfg', 'dfg', 'Italy', 34535, 5.00, 7000.00, '2025-01-21 18:54:10', NULL),
(29, 691780, 'DE1137', 'tahimul', 'sdfserg', 'srgserg', 'Bangladesh', 3414, 'fdgdfg', 'sgrg', 'rereg', 'Italy', 24525, 3.00, 4200.00, '2025-01-23 09:17:44', NULL),
(NULL, 749949, 'DE1137', 'asfasf', 'wetgtw', 'regq', 'Bangladesh', 2142, 'dfsdf', 'sdg', 'sdg', 'Italy', 3552352, 2.00, 2800.00, '2025-01-20 06:08:44', NULL),
(NULL, 804435, 'DE8888', 'Misbah', 'Dhaka', 'dhaka', 'Bangladesh', 123455, 'Neyamul', 'Italy', 'Paris', 'Italy', 6756342, 2.00, 2800.00, '2025-01-21 17:02:42', NULL),
(NULL, 841194, 'DE8888', 'tahimul', 'dfg', 'dfgdf', 'Bangladesh', 654, 'tghdfg', 'fddfg', 'dfg', 'Italy', 76543, 3.00, 4200.00, '2025-01-21 18:34:08', NULL),
(NULL, 898625, 'DE1137', 'tahimul', 'hgfgh', 'fdgh', 'Bangladesh', 7653456, 'misbah', 'jfhf', 'htdthd', 'Italy', 875, 45.00, 63000.00, '2025-01-21 21:55:06', NULL),
(NULL, 992826, 'DE8888', 'tahimul', 'dfgd', 'dfgdfg', 'Bangladesh', 345, 'sdfsf', 'sdfs', 'sdf', 'Italy', 5635, 2.00, 2800.00, '2025-01-21 18:46:36', NULL);

--
-- Triggers `parcel`
--
DELIMITER $$
CREATE TRIGGER `placeParcel` AFTER INSERT ON `parcel` FOR EACH ROW BEGIN
	UPDATE staff SET Credits=Credits+5 WHERE StaffID=NEW.StaffID;
    
    INSERT INTO status (TrackingID, StaffID, Dispatched)
    VALUES ( NEW.TrackingID, NEW.StaffID, NEW.Dispatched_Time);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `pricing`
--

CREATE TABLE `pricing` (
  `p_id` int(11) NOT NULL,
  `State_1` varchar(30) NOT NULL,
  `State_2` varchar(30) NOT NULL,
  `Cost` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `pricing`
--

INSERT INTO `pricing` (`p_id`, `State_1`, `State_2`, `Cost`) VALUES
(1, 'Bangladesh', 'France', 1100),
(2, 'Bangladesh', 'Germany', 1200),
(3, 'Bangladesh', 'Spain', 1300),
(4, 'Bangladesh', 'Italy', 1400),
(5, 'Bangladesh', 'USA', 1500),
(6, 'Bangladesh', 'UK', 1600),
(7, 'Bangladesh', 'Australia', 1700),
(8, 'Bangladesh', 'Japan', 1800),
(9, 'Bangladesh', 'China', 1900),
(10, 'Bangladesh', 'Russia', 1000),
(11, 'Bangladesh', 'Egypt', 1100),
(12, 'Bangladesh', 'South Africa', 1200),
(13, 'Bangladesh', 'Brazil', 1300),
(14, 'France', 'Germany', 1400),
(15, 'France', 'Spain', 1500),
(16, 'France', 'Italy', 1600),
(17, 'France', 'USA', 1700),
(18, 'France', 'UK', 1800),
(19, 'France', 'Australia', 1900),
(20, 'France', 'Japan', 1000),
(21, 'France', 'China', 1100),
(22, 'France', 'Russia', 1200),
(23, 'France', 'Egypt', 1300),
(24, 'France', 'South Africa', 1400),
(25, 'France', 'Brazil', 1500),
(26, 'Germany', 'Spain', 1600),
(27, 'Germany', 'Italy', 1700),
(28, 'Germany', 'USA', 1800),
(29, 'Germany', 'UK', 1900),
(30, 'Germany', 'Australia', 1000),
(31, 'Germany', 'Japan', 1100),
(32, 'Germany', 'China', 1200),
(33, 'Germany', 'Russia', 1300),
(34, 'Germany', 'Egypt', 1400),
(35, 'Germany', 'South Africa', 1500),
(36, 'Germany', 'Brazil', 1600),
(37, 'Spain', 'Italy', 1700),
(38, 'Spain', 'USA', 1800),
(39, 'Spain', 'UK', 1900),
(40, 'Spain', 'Australia', 1000),
(41, 'Spain', 'Japan', 1100),
(42, 'Spain', 'China', 1200),
(43, 'Spain', 'Russia', 1300),
(44, 'Spain', 'Egypt', 1400),
(45, 'Spain', 'South Africa', 1500),
(46, 'Spain', 'Brazil', 1600),
(47, 'Italy', 'USA', 1700),
(48, 'Italy', 'UK', 1800),
(49, 'Italy', 'Australia', 1900),
(50, 'Italy', 'Japan', 1000),
(51, 'Italy', 'China', 1100),
(52, 'Italy', 'Russia', 1200),
(53, 'Italy', 'Egypt', 1300),
(54, 'Italy', 'South Africa', 1400),
(55, 'Italy', 'Brazil', 1500),
(56, 'USA', 'UK', 1600),
(57, 'USA', 'Australia', 1700),
(58, 'USA', 'Japan', 1800),
(59, 'USA', 'China', 1900),
(60, 'USA', 'Russia', 1000),
(61, 'USA', 'Egypt', 1100),
(62, 'USA', 'South Africa', 1200),
(63, 'USA', 'Brazil', 1300),
(64, 'UK', 'Australia', 1400),
(65, 'UK', 'Japan', 1500),
(66, 'UK', 'China', 1600),
(67, 'UK', 'Russia', 1700),
(68, 'UK', 'Egypt', 1800),
(69, 'UK', 'South Africa', 1900),
(70, 'UK', 'Brazil', 1000),
(71, 'Australia', 'Japan', 1100),
(72, 'Australia', 'China', 1200),
(73, 'Australia', 'Russia', 1300),
(74, 'Australia', 'Egypt', 1400),
(75, 'Australia', 'South Africa', 1500),
(76, 'Australia', 'Brazil', 1600),
(77, 'Japan', 'China', 1700),
(78, 'Japan', 'Russia', 1800),
(79, 'Japan', 'Egypt', 1900),
(80, 'Japan', 'South Africa', 1000),
(81, 'Japan', 'Brazil', 1100),
(82, 'China', 'Russia', 1200),
(83, 'China', 'Egypt', 1300),
(84, 'China', 'South Africa', 1400),
(85, 'China', 'Brazil', 1500),
(86, 'Russia', 'Egypt', 1600),
(87, 'Russia', 'South Africa', 1700),
(88, 'Russia', 'Brazil', 1800),
(89, 'Egypt', 'South Africa', 1900),
(90, 'Egypt', 'Brazil', 1000),
(91, 'South Africa', 'Brazil', 1100);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `StaffID` varchar(30) NOT NULL,
  `Name` varchar(30) NOT NULL,
  `Designation` varchar(30) NOT NULL,
  `branch` varchar(255) DEFAULT NULL,
  `Gender` varchar(10) NOT NULL,
  `DOB` date NOT NULL,
  `DOJ` date NOT NULL,
  `Salary` int(11) NOT NULL,
  `Mobile` bigint(20) NOT NULL,
  `Email` varchar(30) NOT NULL,
  `Credits` int(11) NOT NULL DEFAULT 0,
  `pass` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`StaffID`, `Name`, `Designation`, `branch`, `Gender`, `DOB`, `DOJ`, `Salary`, `Mobile`, `Email`, `Credits`, `pass`) VALUES
('DE1124', 'Md. AR Hossain', 'Manager', 'France', 'M', '1980-02-14', '2025-01-13', 55000, 1234567890, 'arhossain@gmail.com', 10, '1234'),
('DE1129', 'Rahul Islam', 'Manager', 'Australia', 'M', '1983-12-05', '2025-01-13', 65000, 4455667788, 'rahulislam@gmail.com', 0, '1234'),
('DE1130', 'Md. Arifur Rahman', 'Manager', 'Russia', 'M', '1987-04-18', '2025-01-13', 47000, 1234876543, 'arifurrahman@gmail.com', 0, '1234'),
('DE1131', 'Nashidah Rahman', 'Manager', 'Spain', 'F', '1994-02-22', '2025-01-13', 31000, 7766554433, 'nashidahrahman@gmail.com', 0, '1234'),
('DE1135', 'Md. Rakib Hossain', 'Manager', 'Japan', 'M', '1985-04-25', '2025-01-13', 60000, 9876543211, 'rakibhossain@gmail.com', 0, '1234'),
('DE1137', 'Abdullah Al Noman', 'Manager', 'Bangladesh', 'M', '1992-09-20', '2025-01-13', 48000, 9876543213, 'abdullahnoman@gmail.com', 55, '1234'),
('DE1140', 'Rumana Sultana', 'Manager', 'China', 'F', '1984-01-28', '2025-01-13', 60000, 9876543216, 'rumanasultana@gmail.com', 0, '1234'),
('DE1141', 'Moinul Haque', 'Manager', 'USA', 'M', '1991-05-16', '2025-01-13', 46000, 9876543217, 'moinulhaque@gmail.com', 0, '1234'),
('DE1142', 'Shahed Ali', 'Manager', 'Egypt', 'M', '1988-08-22', '2025-01-13', 55000, 9876543218, 'shahedali@gmail.com', 0, '1234'),
('DE1143', 'Shirin Akhter', 'Manager', 'South Africa', 'F', '1993-03-18', '2025-01-13', 43000, 9876543219, 'shirinakhter@gmail.com', 0, '1234'),
('DE1145', 'Sadia Rahman', 'Manager', 'Sri Lanka', 'F', '1990-02-15', '2025-01-13', 53000, 9876543220, 'sadiarahman@gmail.com', 0, '1234'),
('DE1146', 'Tanvir Hossain', 'Manager', 'Canada', 'M', '1985-09-25', '2025-01-13', 60000, 9876543230, 'tanvirhossain@gmail.com', 0, '1234'),
('DE1147', 'Nabila Chowdhury', 'Manager', 'Pakistan', 'F', '1992-11-11', '2025-01-13', 48000, 9876543240, 'nabilachowdhury@gmail.com', 0, '1234'),
('DE1148', 'Adil Sheikh', 'Manager', 'Nepal', 'M', '1988-08-10', '2025-01-13', 47000, 9876543250, 'adilsheikh@gmail.com', 0, '1234'),
('DE1149', 'Farhana Yasmin', 'Manager', 'Singapore', 'F', '1991-03-20', '2025-01-13', 46000, 9876543260, 'farhanayasmin@gmail.com', 0, '1234'),
('DE1150', 'Riyad Khan', 'Manager', 'Malaysia', 'M', '1984-07-05', '2025-01-13', 59000, 9876543270, 'riyadkhan@gmail.com', 0, '1234'),
('DE1151', 'Mehnaz Haque', 'Manager', 'Thailand', 'F', '1995-12-30', '2025-01-13', 45000, 9876543280, 'mehnazhaque@gmail.com', 0, '1234'),
('DE1152', 'Zahid Hassan', 'Manager', 'Indonesia', 'M', '1987-05-19', '2025-01-13', 52000, 9876543290, 'zahidhassan@gmail.com', 0, '1234'),
('DE1153', 'Jannatul Ferdous', 'Manager', 'Vietnam', 'F', '1993-10-01', '2025-01-13', 44000, 9876543300, 'jannatulferdous@gmail.com', 0, '1234'),
('DE1154', 'Azim Khan', 'Manager', 'Philippines', 'M', '1989-01-25', '2025-01-13', 56000, 9876543310, 'azimkhan@gmail.com', 0, '1234'),
('DE1155', 'Lubna Sultana', 'Manager', 'Myanmar', 'F', '1990-04-18', '2025-01-13', 43000, 9876543320, 'lubnasultana@gmail.com', 0, '1234'),
('DE1156', 'Hafizur Rahman', 'Manager', 'Hong Kong', 'M', '1986-08-12', '2025-01-13', 57000, 9876543330, 'hafizurahman@gmail.com', 0, '1234'),
('DE1157', 'Sumaiya Akter', 'Manager', 'South Korea', 'F', '1994-09-14', '2025-01-13', 47000, 9876543340, 'sumaiyaakter@gmail.com', 0, '1234'),
('DE1158', 'Kamrul Islam', 'Manager', 'Vietnam', 'M', '1991-12-22', '2025-01-13', 53000, 9876543350, 'kamrulislam@gmail.com', 0, '1234'),
('DE1159', 'Tanzina Afrin', 'Manager', 'Brunei', 'F', '1992-01-17', '2025-01-13', 46000, 9876543360, 'tanzinaafrin@gmail.com', 0, '1234'),
('DE1160', 'Shakib Al Hasan', 'Manager', 'Bhutan', 'M', '1980-11-01', '2025-01-13', 61000, 9876543370, 'shakibalhasan@gmail.com', 0, '1234'),
('DE1161', 'Shirin Rahman', 'Manager', 'Maldives', 'F', '1989-03-12', '2025-01-13', 49000, 9876543380, 'shirinrahman@gmail.com', 0, '1234'),
('DE1162', 'Rafiq Hossain', 'Manager', 'Cambodia', 'M', '1985-07-07', '2025-01-13', 62000, 9876543390, 'rafiqhossain@gmail.com', 0, '1234'),
('DE1163', 'Fatema Khanam', 'Manager', 'Laos', 'F', '1993-06-21', '2025-01-13', 45000, 9876543400, 'fatemakhanam@gmail.com', 0, '1234'),
('DE1164', 'Ashraf Ali', 'Manager', 'Timor-Leste', 'M', '1987-10-30', '2025-01-13', 52000, 9876543410, 'ashrafali@gmail.com', 0, '1234'),
('DE1165', 'Munira Sultana', 'Manager', 'Mongolia', 'F', '1995-02-28', '2025-01-13', 46000, 9876543420, 'munirasultana@gmail.com', 0, '1234'),
('DE1166', 'Asif Ahmed', 'Manager', 'Afghanistan', 'M', '1983-04-10', '2025-01-13', 60000, 9876543430, 'asifahmed@gmail.com', 0, '1234'),
('DE1167', 'Sadia Hasan', 'Manager', 'Uzbekistan', 'F', '1992-11-08', '2025-01-13', 49000, 9876543440, 'sadiahasan@gmail.com', 0, '1234'),
('DE1168', 'Shamsul Islam', 'Manager', 'Kazakhstan', 'M', '1988-06-16', '2025-01-13', 55000, 9876543450, 'shamsulislam@gmail.com', 0, '1234'),
('DE1169', 'Rokeya Begum', 'Manager', 'Kyrgyzstan', 'F', '1993-08-03', '2025-01-13', 44000, 9876543460, 'rokeyabegum@gmail.com', 0, '1234'),
('DE1170', 'Tariqul Islam', 'Manager', 'Tajikistan', 'M', '1986-02-18', '2025-01-13', 53000, 9876543470, 'tariqulislam@gmail.com', 0, '1234'),
('DE1171', 'Nasima Akter', 'Manager', 'Turkmenistan', 'F', '1990-09-09', '2025-01-13', 46000, 9876543480, 'nasimaakter@gmail.com', 0, '1234'),
('DE1172', 'Abir Hossain', 'Manager', 'Armenia', 'M', '1982-12-20', '2025-01-13', 62000, 9876543490, 'abirhossain@gmail.com', 0, '1234'),
('DE6688', 'Ahmed Khan', 'Manager', 'France', 'Male', '1980-05-15', '2015-03-01', 120000, 1712345601, 'ahmed.khaun@gmail.com', 5, '1234'),
('DE8888', 'Misbah', 'Staff', 'Bangladesh', 'M', '2002-01-01', '2020-01-02', 50000, 123456, 'misbah@gmail.com', 40, '1234'),
('DE9913', 'Alim Hosaian', 'Manager', 'UK', 'M', '2006-01-07', '2015-07-07', 25000, 87654345, 'alimalim@gmail.com', 25, '1234');

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `TrackingID` int(11) NOT NULL,
  `StaffID` varchar(30) NOT NULL,
  `Dispatched` timestamp NULL DEFAULT NULL,
  `Shipped` timestamp NULL DEFAULT NULL,
  `Out_for_delivery` timestamp NULL DEFAULT NULL,
  `Delivered` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`TrackingID`, `StaffID`, `Dispatched`, `Shipped`, `Out_for_delivery`, `Delivered`) VALUES
(1401, 'DE1125', '2025-01-13 20:12:54', '2025-01-13 20:14:40', '2025-01-13 20:14:52', '2025-01-13 20:15:02'),
(1407, 'DE9913', '2025-01-14 19:52:00', '2025-01-20 15:57:42', '2025-01-20 15:57:57', '2025-01-20 15:58:43'),
(1408, 'DE9913', '2025-01-14 19:54:37', '2025-01-15 05:56:19', '2025-01-16 09:13:57', '2025-01-16 09:14:27'),
(146157, 'DE1137', '2025-01-23 17:17:53', NULL, NULL, NULL),
(241711, 'DE8888', '2025-01-21 20:54:05', NULL, NULL, NULL),
(252965, 'DE1137', '2025-01-23 18:02:18', NULL, NULL, NULL),
(374600, 'DE1124', '2025-01-21 12:39:40', NULL, NULL, NULL),
(392643, 'DE1137', '2025-01-20 06:08:44', NULL, NULL, NULL),
(417492, 'DE8888', '2025-01-21 18:58:52', NULL, NULL, NULL),
(422046, 'DE1124', '2025-01-20 06:08:44', NULL, NULL, NULL),
(459215, 'DE1137', '2025-01-23 18:06:52', NULL, NULL, NULL),
(478674, 'DE8888', '2025-01-21 18:56:19', NULL, NULL, NULL),
(544364, 'DE1137', '2025-01-26 12:14:48', NULL, NULL, NULL),
(549849, 'DE8888', '2025-01-21 19:11:43', NULL, NULL, NULL),
(637927, 'DE1137', '2025-01-23 18:52:38', NULL, NULL, NULL),
(667565, 'DE1137', '2025-01-20 06:08:44', NULL, NULL, NULL),
(674046, 'DE8888', '2025-01-21 18:54:10', NULL, NULL, NULL),
(691780, 'DE1137', '2025-01-23 09:17:44', NULL, NULL, NULL),
(749949, 'DE1137', '2025-01-20 06:08:44', NULL, NULL, NULL),
(804435, 'DE8888', '2025-01-21 17:02:42', NULL, NULL, NULL),
(841194, 'DE8888', '2025-01-21 18:34:08', NULL, NULL, NULL),
(898625, 'DE1137', '2025-01-21 21:55:06', NULL, NULL, NULL),
(992826, 'DE8888', '2025-01-21 18:46:36', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `last_login`, `name`) VALUES
(1, 'tahmid', 'tah12@gmail.com', '$2y$10$Jh86wYMLY2Syqa61ISyg5eWG5F89Q3xRf2pyhR3jllTxTt9MB0VKa', '2025-01-15 16:22:17', '2025-01-25 18:35:48', 'Tahamid '),
(2, 'tahimul', 'tahimul@gmail.com', '$2y$10$vJa4e3a0NpK/smw63UZhJetpeTWoj/6ajtm8Tcx/IQJ8rzQ6/QjC.', '2025-01-15 16:24:21', '2025-01-22 14:39:38', 'Tahimul Amin');

-- --------------------------------------------------------

--
-- Structure for view `arrived`
--
DROP TABLE IF EXISTS `arrived`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `arrived`  AS SELECT `p`.`TrackingID` AS `TrackingID`, `p`.`StaffID` AS `StaffID`, `p`.`S_Name` AS `S_Name`, `p`.`S_Add` AS `S_Add`, `p`.`S_City` AS `S_City`, `p`.`S_State` AS `S_State`, `p`.`S_Contact` AS `S_Contact`, `p`.`R_Name` AS `R_Name`, `p`.`R_Add` AS `R_Add`, `p`.`R_City` AS `R_City`, `p`.`R_State` AS `R_State`, `p`.`R_Contact` AS `R_Contact`, `p`.`Weight_Kg` AS `Weight_Kg`, `p`.`Price` AS `Price`, `p`.`Dispatched_Time` AS `Dispatched_Time`, `s`.`Shipped` AS `Shipped`, `s`.`Out_for_delivery` AS `Out_for_delivery`, `s`.`Delivered` AS `Delivered` FROM (`parcel` `p` join `status` `s`) WHERE `p`.`TrackingID` = `s`.`TrackingID` AND `s`.`Delivered` is null ;

-- --------------------------------------------------------

--
-- Structure for view `delivered`
--
DROP TABLE IF EXISTS `delivered`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `delivered`  AS SELECT `p`.`TrackingID` AS `TrackingID`, `p`.`StaffID` AS `StaffID`, `p`.`S_Name` AS `S_Name`, `p`.`S_Add` AS `S_Add`, `p`.`S_City` AS `S_City`, `p`.`S_State` AS `S_State`, `p`.`S_Contact` AS `S_Contact`, `p`.`R_Name` AS `R_Name`, `p`.`R_Add` AS `R_Add`, `p`.`R_City` AS `R_City`, `p`.`R_State` AS `R_State`, `p`.`R_Contact` AS `R_Contact`, `p`.`Weight_Kg` AS `Weight_Kg`, `p`.`Price` AS `Price`, `p`.`Dispatched_Time` AS `Dispatched_Time`, `s`.`Shipped` AS `Shipped`, `s`.`Out_for_delivery` AS `Out_for_delivery`, `s`.`Delivered` AS `Delivered` FROM (`parcel` `p` join `status` `s`) WHERE `p`.`TrackingID` = `s`.`TrackingID` AND `s`.`Delivered` is not null ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_credentials`
--
ALTER TABLE `admin_credentials`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_users_feedback` (`f_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `online_request`
--
ALTER TABLE `online_request`
  ADD PRIMARY KEY (`serial`),
  ADD KEY `fk_users_online_request` (`user_id`);

--
-- Indexes for table `parcel`
--
ALTER TABLE `parcel`
  ADD PRIMARY KEY (`TrackingID`),
  ADD KEY `fk_staff_parcel` (`StaffID`),
  ADD KEY `request_id` (`request_id`);

--
-- Indexes for table `pricing`
--
ALTER TABLE `pricing`
  ADD PRIMARY KEY (`p_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`StaffID`),
  ADD KEY `idx_staff_id` (`StaffID`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD UNIQUE KEY `TrackID` (`TrackingID`),
  ADD KEY `StaffID` (`StaffID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `online_request`
--
ALTER TABLE `online_request`
  MODIFY `serial` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `parcel`
--
ALTER TABLE `parcel`
  MODIFY `TrackingID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=992827;

--
-- AUTO_INCREMENT for table `pricing`
--
ALTER TABLE `pricing`
  MODIFY `p_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `branches`
--
ALTER TABLE `branches`
  ADD CONSTRAINT `Manager` FOREIGN KEY (`Manager_id`) REFERENCES `staff` (`StaffID`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `fk_users_feedback` FOREIGN KEY (`f_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `online_request`
--
ALTER TABLE `online_request`
  ADD CONSTRAINT `fk_users_online_request` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `parcel`
--
ALTER TABLE `parcel`
  ADD CONSTRAINT `fk_staff_parcel` FOREIGN KEY (`StaffID`) REFERENCES `staff` (`StaffID`),
  ADD CONSTRAINT `parcel_ibfk_1` FOREIGN KEY (`request_id`) REFERENCES `online_request` (`serial`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `status`
--
ALTER TABLE `status`
  ADD CONSTRAINT `delivery_status` FOREIGN KEY (`TrackingID`) REFERENCES `parcel` (`TrackingID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
