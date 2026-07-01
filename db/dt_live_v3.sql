-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 14, 2023 at 02:01 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dt_live_v3`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `user_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `type` int(11) NOT NULL COMMENT '1- all access',
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `user_name`, `email`, `password`, `type`, `status`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@admin.com', '$2y$10$TiPWXGHgw0txVkj07fY5DOy1Dde1uTgA0W9OZhzKiIue.UNJXC6.q', 1, 1, '2022-04-14 17:28:24', '2023-08-19 09:36:49');

-- --------------------------------------------------------

--
-- Table structure for table `app_section`
--

CREATE TABLE `app_section` (
  `id` int(11) NOT NULL,
  `is_home_screen` int(11) NOT NULL DEFAULT 1 COMMENT '1- home screen, 2- other screen	',
  `type_id` int(11) NOT NULL COMMENT 'FK = Type Table',
  `video_type` int(11) NOT NULL COMMENT '1- Video, 2- Show, 3- Category, 4-Language, 5- Upcoming	',
  `upcoming_type` int(11) NOT NULL DEFAULT 0 COMMENT '1- Video, 2- Show',
  `title` text NOT NULL,
  `video_id` text NOT NULL COMMENT 'All Multiple Ids',
  `screen_layout` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `avatar`
--

CREATE TABLE `avatar` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `banner`
--

CREATE TABLE `banner` (
  `id` int(11) NOT NULL,
  `is_home_screen` int(11) NOT NULL DEFAULT 1 COMMENT '1- home screen, 2- other screen',
  `type_id` int(11) NOT NULL COMMENT 'FK = Type Table',
  `video_type` int(11) NOT NULL DEFAULT 1 COMMENT '1- Video, 2- Show, 3- Category, 4-Language, 5- Upcoming	',
  `upcoming_type` int(11) NOT NULL DEFAULT 0 COMMENT '1- Video, 2- Show',
  `video_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookmark`
--

CREATE TABLE `bookmark` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL COMMENT 'FK = Type Table',
  `video_type` int(11) NOT NULL DEFAULT 1 COMMENT '1- Video, 2- Show, 3- Language, 4- Category, 5- Upcoming',
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cast`
--

CREATE TABLE `cast` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `personal_info` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `channel`
--

CREATE TABLE `channel` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `landscape` varchar(255) NOT NULL,
  `is_title` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `channel_banner`
--

CREATE TABLE `channel_banner` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `link` text NOT NULL,
  `order_no` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `channel_section`
--

CREATE TABLE `channel_section` (
  `id` int(11) NOT NULL,
  `channel_id` text NOT NULL,
  `type_id` int(11) NOT NULL COMMENT 'Fk = Type Table',
  `video_type` int(11) NOT NULL COMMENT '1- Video, 2- Show, 3- Language, 4- Category, 5- Upcoming	',
  `title` text NOT NULL,
  `video_id` text NOT NULL COMMENT 'Multiple Id',
  `tv_show_id` text NOT NULL,
  `language_id` text NOT NULL,
  `category_ids` text NOT NULL,
  `section_type` int(11) NOT NULL DEFAULT 1 COMMENT '1- Normal Screen, 2- Banner Screen',
  `screen_layout` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `download`
--

CREATE TABLE `download` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL COMMENT 'Fk = Type Table',
  `video_type` int(11) NOT NULL COMMENT '1- Video, 2- Show, 3- Language, 4- Category, 5- Upcoming	',
  `other_id` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `general_setting`
--

CREATE TABLE `general_setting` (
  `id` int(11) NOT NULL,
  `key` text NOT NULL,
  `value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `general_setting`
--

INSERT INTO `general_setting` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'app_name', 'DTLive', '2022-08-03 12:38:42', '2023-05-01 05:03:52'),
(2, 'host_email', 'support@divinetechs.com', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(3, 'app_version', '1.0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(4, 'Author', 'DivineTechs', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(5, 'email', 'support@divinetechs.com', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(6, 'contact', '917984859403', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(7, 'app_desripation', 'DivineTechs, a top web & mobile app development company offering innovative solutions for diverse industry verticals. We have creative and dedicated group of developers who are mastered in Apps Developments and Web Development with a nice in delivering quality solutions to customers across the globe.', '2022-08-03 12:38:42', '2023-07-11 03:56:21'),
(8, 'privacy_policy', 'support@divinetechs.com', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(10, 'instrucation', 'DTLive Instruction', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(11, 'app_logo', '1685437955.jpg', '2022-08-03 12:38:42', '2023-05-30 09:12:35'),
(12, 'website', 'https://www.divinetechs.com/', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(13, 'currency', 'inr', '2022-08-03 12:38:42', '2023-03-12 09:26:04'),
(14, 'currency_code', '$', '2022-08-03 12:38:42', '2023-07-03 09:06:39'),
(25, 'banner_ad', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(26, 'banner_adid', '', '2022-08-03 12:38:42', '2023-06-26 12:06:13'),
(27, 'interstital_ad', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(28, 'interstital_adid', '', '2022-08-03 12:38:42', '2023-06-26 12:06:13'),
(29, 'interstital_adclick', '', '2022-08-03 12:38:42', '2023-06-26 12:06:13'),
(30, 'reward_ad', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(31, 'reward_adid', '', '2022-08-03 12:38:42', '2023-06-26 12:06:13'),
(32, 'reward_adclick', '', '2022-08-03 12:38:42', '2023-06-26 12:06:20'),
(33, 'ios_banner_ad', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(34, 'ios_banner_adid', '', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(35, 'ios_interstital_ad', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(36, 'ios_interstital_adid', '', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(37, 'ios_interstital_adclick', '', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(38, 'ios_reward_ad', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(39, 'ios_reward_adid', '', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(40, 'ios_reward_adclick', '', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(41, 'fb_native_status', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(42, 'fb_native_id', '', '2022-08-03 12:38:42', '2023-06-26 12:06:38'),
(43, 'fb_banner_status', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(44, 'fb_banner_id', '', '2022-08-03 12:38:42', '2023-06-26 12:06:42'),
(45, 'fb_interstiatial_status', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(46, 'fb_interstiatial_id', '', '2022-08-03 12:38:42', '2023-06-26 12:06:44'),
(47, 'fb_rewardvideo_status', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(48, 'fb_rewardvideo_id', '', '2022-08-03 12:38:42', '2023-06-26 12:06:46'),
(49, 'fb_native_full_status', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(50, 'fb_native_full_id', '', '2022-08-03 12:38:42', '2023-06-26 12:06:48'),
(51, 'fb_ios_native_status', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(52, 'fb_ios_native_id', '', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(53, 'fb_ios_banner_status', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(54, 'fb_ios_banner_id', '', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(55, 'fb_ios_interstiatial_status', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(56, 'fb_ios_interstiatial_id', '', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(57, 'fb_ios_rewardvideo_status', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(58, 'fb_ios_rewardvideo_id', '', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(59, 'fb_ios_native_full_status', '0', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(60, 'fb_ios_native_full_id', '', '2022-08-03 12:38:42', '2022-11-23 10:42:53'),
(61, 'onesignal_apid', '', '2022-08-03 12:38:42', '2023-06-24 12:20:02'),
(62, 'onesignal_rest_key', '', '2022-08-03 12:38:42', '2023-06-24 12:20:02'),
(72, 'imdb_api_key', '', '2023-04-15 12:04:05', '2023-07-28 08:33:12');

-- --------------------------------------------------------

--
-- Table structure for table `language`
--

CREATE TABLE `language` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `package`
--

CREATE TABLE `package` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` float NOT NULL,
  `type_id` text NOT NULL,
  `watch_on_laptop_tv` varchar(255) NOT NULL,
  `ads_free_movies_shows` int(11) NOT NULL,
  `no_of_device` int(11) NOT NULL,
  `video_qulity` text NOT NULL,
  `type` varchar(255) NOT NULL,
  `time` varchar(255) NOT NULL,
  `android_product_package` varchar(255) NOT NULL,
  `ios_product_package` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_delete` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `package_detail`
--

CREATE TABLE `package_detail` (
  `id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `package_key` text NOT NULL,
  `package_value` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `page`
--

CREATE TABLE `page` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `page_name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `page_subtitle` varchar(255) NOT NULL,
  `icon` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `page`
--

INSERT INTO `page` (`id`, `page_name`, `title`, `description`, `page_subtitle`, `icon`, `status`, `created_at`, `updated_at`) VALUES
(1, 'about-us', 'About Us', '<h1><font color=\"#4d5156\" face=\"arial, sans-serif\"><span style=\"font-size: 14px;\"><b>DTLive&nbsp; About-US</b></span></font></h1>', '', '', 1, '2022-09-26 04:31:44', '2023-09-14 11:59:29'),
(2, 'privacy-policy', 'Privacy Policy', '<blockquote class=\"blockquote\"><b>DTLive Privacy Policy</b></blockquote>', '', '', 1, '2022-09-26 04:31:44', '2023-06-23 11:39:44'),
(3, 'terms-and-conditions', 'Terms and Conditions', '<blockquote class=\"blockquote\"><b>DTLive T&amp;C</b></blockquote>', '', '', 1, '2022-09-26 04:31:44', '2023-06-23 11:40:18'),
(4, 'refund-policy', 'Refund Policy', '<blockquote class=\"blockquote\"><p><b>Refund Policy</b></p></blockquote>', '', '', 1, '2023-01-21 10:21:24', '2023-06-23 11:41:14');

-- --------------------------------------------------------

--
-- Table structure for table `payment_option`
--

CREATE TABLE `payment_option` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `visibility` varchar(255) NOT NULL,
  `is_live` varchar(255) NOT NULL,
  `live_key_1` varchar(255) NOT NULL,
  `live_key_2` varchar(255) NOT NULL,
  `live_key_3` varchar(255) NOT NULL,
  `test_key_1` varchar(255) NOT NULL,
  `test_key_2` varchar(255) NOT NULL,
  `test_key_3` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Dumping data for table `payment_option`
--

INSERT INTO `payment_option` (`id`, `name`, `visibility`, `is_live`, `live_key_1`, `live_key_2`, `live_key_3`, `test_key_1`, `test_key_2`, `test_key_3`, `created_at`, `updated_at`) VALUES
(1, 'inapppurchage', '0', '0', '', '', '', '', '', '', '2022-07-29 06:26:54', '2023-07-28 08:34:31'),
(2, 'paypal', '0', '0', '', '', '', '', '', '', '2022-07-29 06:26:54', '2023-07-28 08:34:33'),
(3, 'razorpay', '0', '0', '', '', '', '', '', '', '2022-07-29 06:27:09', '2023-07-28 08:34:35'),
(4, 'flutterwave', '0', '0', '', '', '', '', '', '', '2022-07-29 06:27:09', '2023-07-28 08:33:59'),
(5, 'payumoney', '0', '0', '', '', '', '', '', '', '2022-07-29 06:27:17', '2023-07-28 08:34:23'),
(6, 'paytm', '0', '0', '', '', '', '', '', '', '2022-07-29 06:27:17', '2023-07-28 08:34:20'),
(7, 'stripe', '0', '0', '', '', '', '', '', '', '2023-05-06 06:36:30', '2023-07-28 08:34:14'),
(8, 'cash', '0', '0', '', '', '', '', '', '', '2023-06-27 07:26:08', '2023-07-10 12:00:24'),
(9, 'paystack', '0', '0', '', '', '', '', '', '', '2023-09-11 11:52:42', '2023-09-11 11:58:26'),
(10, 'instamojo', '0', '0', '', '', '', '', '', '', '2023-09-11 11:52:59', '2023-09-11 12:14:39');

-- --------------------------------------------------------

--
-- Table structure for table `rent_transction`
--

CREATE TABLE `rent_transction` (
  `id` int(11) NOT NULL,
  `unique_id` varchar(255) NOT NULL DEFAULT '' COMMENT 'FK = Coupon Table',
  `user_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL COMMENT 'Fk = Type Table',
  `video_type` int(11) NOT NULL COMMENT '1- Video, 2- Show, 3- Language, 4- Category, 5- Upcoming	',
  `video_id` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `payment_id` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `currency_code` varchar(255) NOT NULL,
  `expiry_date` date NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rent_video`
--

CREATE TABLE `rent_video` (
  `id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL COMMENT 'FK = Type Table',
  `video_type` int(11) NOT NULL COMMENT '1- Video, 2- Show, 3- Language, 4- Category, 5- Upcoming	',
  `video_id` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `time` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `smtp_setting`
--

CREATE TABLE `smtp_setting` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `protocol` varchar(255) NOT NULL,
  `host` varchar(255) NOT NULL,
  `port` varchar(255) NOT NULL,
  `user` varchar(255) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `from_name` varchar(255) NOT NULL,
  `from_email` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `smtp_setting`
--

INSERT INTO `smtp_setting` (`id`, `protocol`, `host`, `port`, `user`, `pass`, `from_name`, `from_email`, `status`, `created_at`, `updated_at`) VALUES
(1, 'smtp123', 'smtp.gmail.com', '587', 'admin@admin.com', 'admin', 'DTLive-Divinetech', 'admin@admin.com', 0, '2022-08-03 10:14:04', '2023-09-14 11:59:34');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_coupon`
--

CREATE TABLE `tbl_coupon` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `unique_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `amount_type` int(11) NOT NULL COMMENT '1- Price, 2- Percentage',
  `price` varchar(255) NOT NULL,
  `is_use` int(11) NOT NULL COMMENT '0- All, 1- One',
  `status` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_social_link`
--

CREATE TABLE `tbl_social_link` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `url` text NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_tv_login`
--

CREATE TABLE `tbl_tv_login` (
  `id` int(11) NOT NULL,
  `device_token` varchar(255) NOT NULL,
  `unique_code` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `unique_id` varchar(255) NOT NULL DEFAULT '' COMMENT 'FK = Coupon Table',
  `package_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `amount` varchar(255) NOT NULL,
  `payment_id` varchar(255) NOT NULL,
  `currency_code` varchar(255) NOT NULL,
  `expiry_date` date NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_delete` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tv_show`
--

CREATE TABLE `tv_show` (
  `id` int(11) NOT NULL,
  `channel_id` int(11) NOT NULL,
  `category_id` text NOT NULL,
  `language_id` text NOT NULL,
  `cast_id` text NOT NULL,
  `type_id` int(11) NOT NULL DEFAULT 0 COMMENT 'FK = Type Table',
  `video_type` int(11) NOT NULL COMMENT '1- Video, 2- Show, 3- Category, 4-Language, 5- Upcoming',
  `name` varchar(255) NOT NULL,
  `thumbnail` varchar(100) NOT NULL,
  `landscape` varchar(100) NOT NULL,
  `trailer_type` varchar(255) NOT NULL COMMENT 'server_video, external, youtube',
  `trailer_url` text NOT NULL,
  `description` text NOT NULL,
  `is_premium` int(11) NOT NULL,
  `is_title` int(11) NOT NULL,
  `release_date` varchar(255) NOT NULL DEFAULT '',
  `view` int(11) NOT NULL,
  `imdb_rating` float NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `director_id` text NOT NULL,
  `starring_id` text NOT NULL,
  `supporting_cast_id` text NOT NULL,
  `networks` text NOT NULL,
  `maturity_rating` text NOT NULL,
  `studios` text NOT NULL,
  `content_advisory` text NOT NULL,
  `viewing_rights` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tv_show_video`
--

CREATE TABLE `tv_show_video` (
  `id` int(11) NOT NULL,
  `show_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `video_type` varchar(100) NOT NULL COMMENT '1- Video, 2- Show, 3- Language, 4- Category, 5- Upcoming',
  `name` varchar(255) NOT NULL,
  `thumbnail` varchar(100) NOT NULL,
  `landscape` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `is_premium` int(11) NOT NULL DEFAULT 0,
  `is_title` int(11) NOT NULL,
  `download` int(11) NOT NULL COMMENT 'Is_Download',
  `video_upload_type` varchar(255) NOT NULL COMMENT 'server_video, external, youtube, vimeo',
  `video_320` varchar(255) NOT NULL,
  `video_480` varchar(255) NOT NULL,
  `video_720` varchar(255) NOT NULL,
  `video_1080` varchar(255) NOT NULL,
  `video_extension` varchar(100) NOT NULL,
  `video_duration` bigint(20) NOT NULL DEFAULT 0,
  `subtitle_type` varchar(255) NOT NULL COMMENT 'server_video, external',
  `subtitle_lang_1` varchar(255) NOT NULL,
  `subtitle_1` varchar(255) NOT NULL,
  `subtitle_lang_2` varchar(255) NOT NULL,
  `subtitle_2` varchar(255) NOT NULL,
  `subtitle_lang_3` varchar(255) NOT NULL,
  `subtitle_3` varchar(255) NOT NULL,
  `view` int(11) NOT NULL,
  `sortable` int(11) NOT NULL DEFAULT 1,
  `status` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `type`
--

CREATE TABLE `type` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` int(11) NOT NULL COMMENT '1- Video, 2- Show, 3- Category, 4-Language, 5- Upcoming\r\n',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL DEFAULT '',
  `name` varchar(100) NOT NULL,
  `mobile` varchar(25) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `image` varchar(100) NOT NULL,
  `type` int(11) NOT NULL DEFAULT 0 COMMENT '1- Facebook, 2- Google, 3- OTP, 4- Normal, 5- Apple',
  `status` int(11) NOT NULL DEFAULT 1,
  `expiry_date` varchar(255) NOT NULL,
  `api_token` varchar(255) NOT NULL DEFAULT '',
  `email_verify_token` varchar(255) NOT NULL DEFAULT '',
  `is_email_verify` varchar(255) NOT NULL DEFAULT '',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `video`
--

CREATE TABLE `video` (
  `id` int(11) NOT NULL,
  `channel_id` int(11) NOT NULL,
  `category_id` text NOT NULL,
  `language_id` text NOT NULL,
  `cast_id` text NOT NULL,
  `type_id` int(11) NOT NULL COMMENT 'FK = Type Table',
  `video_type` int(11) NOT NULL COMMENT '1- Video, 2- Show, 3- Category, 4-Language, 5- Upcoming',
  `name` varchar(255) NOT NULL,
  `thumbnail` varchar(100) NOT NULL,
  `landscape` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `is_premium` int(11) NOT NULL DEFAULT 0,
  `is_title` varchar(255) NOT NULL,
  `download` int(11) NOT NULL COMMENT 'Is_Download',
  `video_upload_type` varchar(255) DEFAULT 'server_video' COMMENT 'server_video, external, youtube, vimeo',
  `video_320` varchar(255) NOT NULL,
  `video_480` varchar(255) NOT NULL,
  `video_720` varchar(255) NOT NULL,
  `video_1080` varchar(255) NOT NULL,
  `video_extension` varchar(100) NOT NULL,
  `video_duration` int(11) NOT NULL DEFAULT 0,
  `trailer_type` varchar(255) NOT NULL COMMENT 'server_video, external, youtube',
  `trailer_url` text NOT NULL,
  `subtitle_type` varchar(255) NOT NULL COMMENT 'server_video, external',
  `subtitle_lang_1` varchar(255) NOT NULL,
  `subtitle_1` varchar(255) NOT NULL,
  `subtitle_lang_2` varchar(255) NOT NULL,
  `subtitle_2` varchar(255) NOT NULL,
  `subtitle_lang_3` varchar(255) NOT NULL,
  `subtitle_3` varchar(255) NOT NULL,
  `release_date` varchar(255) NOT NULL DEFAULT '',
  `release_year` varchar(255) NOT NULL,
  `imdb_rating` float NOT NULL,
  `view` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `director_id` text NOT NULL,
  `supporting_cast_id` text NOT NULL,
  `starring_id` text NOT NULL,
  `networks` text NOT NULL,
  `maturity_rating` text NOT NULL,
  `age_restriction` varchar(255) NOT NULL,
  `max_video_quality` varchar(255) NOT NULL,
  `release_tag` varchar(255) NOT NULL,
  `video_size` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

-- --------------------------------------------------------

--
-- Table structure for table `video_watch`
--

CREATE TABLE `video_watch` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `video_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL DEFAULT 0 COMMENT 'FK = Type Table',
  `video_type` int(11) NOT NULL DEFAULT 1 COMMENT '1- Video, 2- Show, 3- Language, 4- Category , 5- Upcoming	',
  `stop_time` varchar(255) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `app_section`
--
ALTER TABLE `app_section`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `avatar`
--
ALTER TABLE `avatar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookmark`
--
ALTER TABLE `bookmark`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cast`
--
ALTER TABLE `cast`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `channel`
--
ALTER TABLE `channel`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `channel_banner`
--
ALTER TABLE `channel_banner`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `channel_section`
--
ALTER TABLE `channel_section`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `download`
--
ALTER TABLE `download`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `general_setting`
--
ALTER TABLE `general_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `language`
--
ALTER TABLE `language`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `package`
--
ALTER TABLE `package`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `package_detail`
--
ALTER TABLE `package_detail`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `page`
--
ALTER TABLE `page`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_option`
--
ALTER TABLE `payment_option`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rent_transction`
--
ALTER TABLE `rent_transction`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rent_video`
--
ALTER TABLE `rent_video`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `smtp_setting`
--
ALTER TABLE `smtp_setting`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_coupon`
--
ALTER TABLE `tbl_coupon`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_social_link`
--
ALTER TABLE `tbl_social_link`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_tv_login`
--
ALTER TABLE `tbl_tv_login`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tv_show`
--
ALTER TABLE `tv_show`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tv_show_video`
--
ALTER TABLE `tv_show_video`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `type`
--
ALTER TABLE `type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `video`
--
ALTER TABLE `video`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `video_watch`
--
ALTER TABLE `video_watch`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `app_section`
--
ALTER TABLE `app_section`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `avatar`
--
ALTER TABLE `avatar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `banner`
--
ALTER TABLE `banner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookmark`
--
ALTER TABLE `bookmark`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cast`
--
ALTER TABLE `cast`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `channel`
--
ALTER TABLE `channel`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `channel_banner`
--
ALTER TABLE `channel_banner`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `channel_section`
--
ALTER TABLE `channel_section`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `download`
--
ALTER TABLE `download`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `general_setting`
--
ALTER TABLE `general_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `language`
--
ALTER TABLE `language`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `package`
--
ALTER TABLE `package`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `package_detail`
--
ALTER TABLE `package_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `page`
--
ALTER TABLE `page`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `payment_option`
--
ALTER TABLE `payment_option`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `rent_transction`
--
ALTER TABLE `rent_transction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rent_video`
--
ALTER TABLE `rent_video`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `session`
--
ALTER TABLE `session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `smtp_setting`
--
ALTER TABLE `smtp_setting`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_coupon`
--
ALTER TABLE `tbl_coupon`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_social_link`
--
ALTER TABLE `tbl_social_link`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_tv_login`
--
ALTER TABLE `tbl_tv_login`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tv_show`
--
ALTER TABLE `tv_show`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tv_show_video`
--
ALTER TABLE `tv_show_video`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `type`
--
ALTER TABLE `type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `video`
--
ALTER TABLE `video`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `video_watch`
--
ALTER TABLE `video_watch`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
