-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Feb 07, 2025 at 09:05 PM
-- Server version: 10.5.26-MariaDB
-- PHP Version: 8.3.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `getsport_stream`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `email`) VALUES
(1, 'admin', 'Parola12!34', 'admin@streamthunder.com');

-- --------------------------------------------------------

--
-- Table structure for table `leagues`
--

CREATE TABLE `leagues` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `country` varchar(100) DEFAULT NULL,
  `logo_url` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leagues`
--

INSERT INTO `leagues` (`id`, `name`, `slug`, `country`, `logo_url`, `status`, `created_at`) VALUES
(1, 'Champions League', 'champions-league', 'Europe', 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f5/UEFA_Champions_League.svg/800px-UEFA_Champions_League.svg.png', 'active', '2025-02-05 15:41:34'),
(2, 'Europa League', 'europa-league', 'Europe', 'https://upload.wikimedia.org/wikipedia/en/thumb/0/03/UEFA_Europa_League.svg/800px-UEFA_Europa_League.svg.png', 'active', '2025-02-05 15:41:34'),
(3, 'Premier League', 'premier-league', 'England', 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f2/Premier_League_Logo.svg/800px-Premier_League_Logo.svg.png', 'active', '2025-02-05 15:41:34'),
(4, 'La Liga', 'la-liga', 'Spain', 'https://www.sofascore.ro/static/images/flags/es.png', 'active', '2025-02-05 15:41:34'),
(5, 'Serie A', 'serie-a', 'Italy', 'https://1000logos.net/wp-content/uploads/2019/01/Italian-Serie-A-Logo.png', 'active', '2025-02-05 15:41:34'),
(6, 'Bundesliga', 'bundesliga', 'Germany', 'https://upload.wikimedia.org/wikipedia/en/thumb/d/df/Bundesliga_logo_%282017%29.svg/800px-Bundesliga_logo_%282017%29.svg.png', 'active', '2025-02-05 15:41:34'),
(7, 'Ligue 1', 'ligue-1', 'France', 'https://www.sofascore.ro/static/images/flags/fr.png?v=2', 'active', '2025-02-05 15:41:34'),
(8, 'Superliga', 'superliga', 'Romania', 'https://upload.wikimedia.org/wikipedia/ro/archive/6/6c/20240207093050%21SuperLiga_Romania.svg', 'active', '2025-02-05 15:41:34'),
(9, 'Copa del Rey', 'copa-del-rey', 'Spain', 'https://www.sofascore.ro/static/images/flags/es.png', 'active', '2025-02-05 17:59:55'),
(10, 'Coppa Italia', 'coppa-italia', 'Italy', 'https://www.sofascore.ro/static/images/flags/it.png', 'active', '2025-02-05 18:01:03'),
(11, 'DFB Pokal', 'dfb-pokal', 'Germany', 'https://www.sofascore.ro/static/images/flags/de.png', 'active', '2025-02-05 18:02:10'),
(12, 'EFL Cup', 'efl-cup', 'UK', 'https://www.sofascore.ro/static/images/flags/en.png', 'active', '2025-02-05 18:03:42'),
(13, 'Coupe de France', 'coupe-de-france', 'France', 'https://www.sofascore.ro/static/images/flags/fr.png?v=2', 'active', '2025-02-05 18:05:07'),
(14, 'KNVB beker', 'knvb-beker', 'Netherland', 'https://www.sofascore.ro/static/images/flags/nl.png', 'active', '2025-02-05 18:09:55'),
(15, 'Saudi Pro League', 'saudi-pro-league', 'Saudi Arabia', 'https://upload.wikimedia.org/wikipedia/en/thumb/7/75/Roshn_Saudi_League_Logo.svg/1200px-Roshn_Saudi_League_Logo.svg.png', 'active', '2025-02-07 15:29:00');

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `ip_address` varchar(50) NOT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `username` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

CREATE TABLE `matches` (
  `id` int(11) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `meta_title` varchar(255) DEFAULT NULL,
  `meta_title_ro` varchar(255) DEFAULT NULL,
  `meta_title_en` varchar(255) DEFAULT NULL,
  `meta_title_fr` varchar(255) DEFAULT NULL,
  `meta_description` text DEFAULT NULL,
  `meta_description_ro` text DEFAULT NULL,
  `meta_description_en` text DEFAULT NULL,
  `meta_description_fr` text DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `canonical_url` varchar(255) DEFAULT NULL,
  `robots_meta` varchar(50) DEFAULT 'index,follow',
  `custom_schema` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`custom_schema`)),
  `league` int(11) DEFAULT NULL,
  `home_team` int(11) DEFAULT NULL,
  `away_team` int(11) DEFAULT NULL,
  `date` datetime NOT NULL,
  `sport` varchar(50) NOT NULL DEFAULT 'football',
  `status` enum('upcoming','live','ended','pending','postponed','cancelled') NOT NULL DEFAULT 'upcoming',
  `stream_url` text DEFAULT NULL,
  `alternative_stream1` text DEFAULT NULL,
  `alternative_stream2` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `matches`
--

INSERT INTO `matches` (`id`, `slug`, `meta_title`, `meta_title_ro`, `meta_title_en`, `meta_title_fr`, `meta_description`, `meta_description_ro`, `meta_description_en`, `meta_description_fr`, `meta_keywords`, `canonical_url`, `robots_meta`, `custom_schema`, `league`, `home_team`, `away_team`, `date`, `sport`, `status`, `stream_url`, `alternative_stream1`, `alternative_stream2`, `created_at`) VALUES
(14, 'fc-boto-ani-vs-dinamo-bucure-ti-2025-02-07', 'FC Botoșani vs Dinamo București - Live Stream | Superliga', NULL, NULL, NULL, 'Watch FC Botoșani vs Dinamo București live stream. Superliga match on February 7, 2025. High quality football streaming with live scores and updates.', NULL, NULL, NULL, 'FC Botoșani, Dinamo București, Superliga, live football, soccer stream, watch online, live match', NULL, 'index,follow', NULL, 8, 52, 53, '2025-02-07 20:00:00', 'football', 'upcoming', 'https://canale-tv.com/channel/dgsp1.html', '', '', '2025-02-07 15:03:02'),
(15, 'bayern-munich-vs-bremen-2025-02-07', 'Bayern Munich vs Bremen - Live Stream | Bundesliga', NULL, NULL, NULL, 'Watch Bayern Munich vs Bremen live stream. Bundesliga match on February 7, 2025. High quality football streaming with live scores and match updates.', NULL, NULL, NULL, 'Bayern Munich, Bremen, Bundesliga, live stream, football, soccer, watch online', NULL, 'index,follow', NULL, 6, 4, 50, '2025-02-07 21:30:00', 'football', 'upcoming', 'https://canale-tv.com/channel/dgsp2.html', '', '', '2025-02-07 15:03:35'),
(16, 'juventus-vs-como-2025-02-07', 'Como vs Juventus - Live Stream | Serie A', NULL, NULL, NULL, 'Watch Como vs Juventus live stream. Serie A match on February 7, 2025. High quality football streaming with live scores and updates.', NULL, NULL, NULL, 'Como, Juventus, Serie A, live football, soccer stream, watch online, live match', NULL, 'index,follow', NULL, 5, 51, 10, '2025-02-07 21:45:00', 'football', 'upcoming', 'https://canale-tv.com/channel/primasp1.html', '', '', '2025-02-07 15:04:04'),
(17, 'psg-vs-monaco-2025-02-07', 'PSG vs Monaco - Live Stream | Ligue 1', NULL, NULL, NULL, 'Watch PSG vs Monaco live stream. Ligue 1 match on February 7, 2025. High quality football streaming with live scores and updates.', NULL, NULL, NULL, 'PSG, Monaco, Ligue 1, live football, soccer stream, watch online, live match', NULL, 'index,follow', NULL, 7, 5, 57, '2025-02-07 22:05:00', 'football', 'upcoming', 'https://canale-tv.com/channel/primasp2.html', '', '', '2025-02-07 15:06:56'),
(18, 'nantes-vs-brest-2025-02-07', 'Nantes vs Brest - Live Stream | Ligue 1', NULL, NULL, NULL, 'Watch Nantes vs Brest live stream. Ligue 1 match on February 7, 2025. High quality football streaming with live scores and match updates.', NULL, NULL, NULL, 'Nantes, Brest, Ligue 1, live stream, football, soccer, watch online', NULL, 'index,follow', NULL, 7, 49, 58, '2025-02-07 20:00:00', 'football', 'upcoming', 'https://canale-tv.com/channel/dgsp1.html', '', '', '2025-02-07 15:08:01'),
(19, 'valladolid-vs-rayo-vallecano-2025-02-07', 'Rayo Vallecano vs Valladolid - Live Stream | La Liga', NULL, NULL, NULL, 'Watch Rayo Vallecano vs Valladolid live stream. La Liga match on February 7, 2025. High quality football streaming with live scores and updates.', NULL, NULL, NULL, 'Rayo Vallecano, Valladolid, La Liga, live football, soccer stream, watch online, live match', NULL, 'index,follow', NULL, 4, 60, 59, '2025-02-07 22:00:00', 'football', 'upcoming', 'https://canale-tv.com/channel/dgsp1.html', '', '', '2025-02-07 15:10:52'),
(20, 'al-nassr-fc-vs-al-fayha-fc-2025-02-07', '', NULL, NULL, NULL, '', NULL, NULL, NULL, '', NULL, 'index,follow', NULL, 15, 61, 63, '2025-02-07 17:00:00', 'football', 'ended', 'https://canale-tv.com/channel/primasp1.html', '', '', '2025-02-07 15:31:27');

-- --------------------------------------------------------

--
-- Table structure for table `stats_history`
--

CREATE TABLE `stats_history` (
  `id` int(11) NOT NULL,
  `match_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `total_views` int(11) DEFAULT 0,
  `unique_viewers` int(11) DEFAULT 0,
  `peak_viewers` int(11) DEFAULT 0,
  `avg_watch_time` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stream_sources`
--

CREATE TABLE `stream_sources` (
  `id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `url` text NOT NULL,
  `priority` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stream_stats`
--

CREATE TABLE `stream_stats` (
  `id` int(11) NOT NULL,
  `match_id` int(11) DEFAULT NULL,
  `views` int(11) DEFAULT 0,
  `unique_viewers` int(11) DEFAULT 0,
  `avg_watch_time` int(11) DEFAULT 0,
  `peak_viewers` int(11) DEFAULT 0,
  `created_at` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `league_id` int(11) DEFAULT NULL,
  `logo_url` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`id`, `name`, `slug`, `league_id`, `logo_url`, `status`, `created_at`) VALUES
(1, 'Real Madrid', 'real-madrid', 4, 'https://upload.wikimedia.org/wikipedia/en/thumb/5/56/Real_Madrid_CF.svg/120px-Real_Madrid_CF.svg.png', 'active', '2025-02-05 15:41:34'),
(2, 'Barcelona', 'barcelona', 4, 'https://upload.wikimedia.org/wikipedia/en/thumb/4/47/FC_Barcelona_%28crest%29.svg/120px-FC_Barcelona_%28crest%29.svg.png', 'active', '2025-02-05 15:41:34'),
(3, 'Manchester City', 'manchester-city', 3, 'https://upload.wikimedia.org/wikipedia/en/thumb/e/eb/Manchester_City_FC_badge.svg/120px-Manchester_City_FC_badge.svg.png', 'active', '2025-02-05 15:41:34'),
(4, 'Bayern Munich', 'bayern-munich', 6, 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1b/FC_Bayern_M%C3%BCnchen_logo_%282017%29.svg/120px-FC_Bayern_M%C3%BCnchen_logo_%282017%29.svg.png', 'active', '2025-02-05 15:41:34'),
(5, 'PSG', 'psg', 7, 'https://upload.wikimedia.org/wikipedia/en/thumb/a/a7/Paris_Saint-Germain_F.C..svg/120px-Paris_Saint-Germain_F.C..svg.png', 'active', '2025-02-05 15:41:34'),
(6, 'Manchester United', 'manchester-united', 3, 'https://upload.wikimedia.org/wikipedia/en/thumb/7/7a/Manchester_United_FC_crest.svg/120px-Manchester_United_FC_crest.svg.png', 'active', '2025-02-05 15:41:34'),
(7, 'Liverpool', 'liverpool', 3, 'https://upload.wikimedia.org/wikipedia/en/thumb/0/0c/Liverpool_FC.svg/120px-Liverpool_FC.svg.png', 'active', '2025-02-05 15:41:34'),
(8, 'Chelsea', 'chelsea', 3, 'https://upload.wikimedia.org/wikipedia/en/thumb/c/cc/Chelsea_FC.svg/120px-Chelsea_FC.svg.png', 'active', '2025-02-05 15:41:34'),
(9, 'Arsenal', 'arsenal', 3, 'https://upload.wikimedia.org/wikipedia/en/thumb/5/53/Arsenal_FC.svg/120px-Arsenal_FC.svg.png', 'active', '2025-02-05 15:41:34'),
(10, 'Juventus', 'juventus', 5, 'https://img.sofascore.com/api/v1/team/2687/image', 'active', '2025-02-05 15:41:34'),
(11, 'AC Milan', 'ac-milan', 5, 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d0/Logo_of_AC_Milan.svg/120px-Logo_of_AC_Milan.svg.png', 'active', '2025-02-05 15:41:34'),
(12, 'Inter Milan', 'inter-milan', 5, 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/FC_Internazionale_Milano_2021.svg/120px-FC_Internazionale_Milano_2021.svg.png', 'active', '2025-02-05 15:41:34'),
(13, 'CFR Cluj', 'cfr-cluj', 8, 'https://img.sofascore.com/api/v1/team/5287/image', 'active', '2025-02-05 17:57:45'),
(14, 'FC Hermannstadt', 'fc-hermannstadt', 8, 'https://img.sofascore.com/api/v1/team/260356/image', 'active', '2025-02-05 17:58:14'),
(15, 'U Craiova', 'u-craiova', 8, 'https://img.sofascore.com/api/v1/team/116223/image', 'active', '2025-02-05 17:58:36'),
(16, 'U Cluj', 'u-cluj', 8, 'https://img.sofascore.com/api/v1/team/7734/image', 'active', '2025-02-05 17:58:59'),
(17, 'Leganés', 'legan-s', 9, 'https://img.sofascore.com/api/v1/team/2845/image', 'active', '2025-02-05 18:00:25'),
(20, 'AS Rome', 'as-rome', 5, 'https://img.sofascore.com/api/v1/team/2702/image', 'active', '2025-02-05 18:01:45'),
(21, 'Bayer 04 Leverkusen ', 'bayer-04-leverkusen-', 11, 'https://img.sofascore.com/api/v1/team/2681/image', 'active', '2025-02-05 18:02:35'),
(22, ' FC Köln ', '-fc-k-ln-', 11, 'https://img.sofascore.com/api/v1/team/2671/image', 'active', '2025-02-05 18:03:01'),
(23, 'Newcastle United', 'newcastle-united', 12, 'https://img.sofascore.com/api/v1/team/39/image', 'active', '2025-02-05 18:04:13'),
(26, 'AS Cannes', 'as-cannes', 13, 'https://img.sofascore.com/api/v1/team/1755/image', 'active', '2025-02-05 18:05:29'),
(27, 'SU Dives Cabourg', 'su-dives-cabourg', 13, 'https://img.sofascore.com/api/v1/team/397143/image', 'active', '2025-02-05 18:06:01'),
(28, 'RC Strasbourg ', 'rc-strasbourg-', 13, 'https://img.sofascore.com/api/v1/team/1659/image', 'active', '2025-02-05 18:06:25'),
(29, 'Stade Briochin  ', 'stade-briochin-', 13, 'https://img.sofascore.com/api/v1/team/211608/image', 'active', '2025-02-05 18:07:43'),
(30, 'Nice', 'nice', 13, 'https://img.sofascore.com/api/v1/team/1661/image', 'active', '2025-02-05 18:08:04'),
(31, 'Toulouse', 'toulouse', 13, 'https://img.sofascore.com/api/v1/team/1681/image', 'active', '2025-02-05 18:08:20'),
(32, 'Guingamp', 'guingamp', 13, 'https://img.sofascore.com/api/v1/team/1654/image', 'active', '2025-02-05 18:08:41'),
(33, 'Go Ahead Eagles ', 'go-ahead-eagles-', 14, 'https://img.sofascore.com/api/v1/team/2979/image', 'active', '2025-02-05 18:10:32'),
(34, ' Noordwijk ', '-noordwijk-', 14, 'https://img.sofascore.com/api/v1/team/44346/image', 'active', '2025-02-05 18:10:53'),
(35, 'Poli Iași', 'poli-ia-i', 8, 'https://img.sofascore.com/api/v1/team/44319/image', 'active', '2025-02-05 21:27:17'),
(36, 'UTA Arad', 'uta-arad', 8, 'https://img.sofascore.com/api/v1/team/204657/image', 'active', '2025-02-05 21:27:32'),
(37, 'Sepsi OSK', 'sepsi-osk', 8, 'https://img.sofascore.com/api/v1/team/189789/image', 'active', '2025-02-05 21:27:45'),
(39, 'Petrolul Ploiești', 'petrolul-ploie-ti', 8, 'https://img.sofascore.com/api/v1/team/25856/image', 'active', '2025-02-05 21:28:21'),
(40, 'FCSB', 'fcsb', 8, 'https://img.sofascore.com/api/v1/team/3301/image', 'active', '2025-02-05 21:28:33'),
(41, 'Fiorentina', 'fiorentina', 5, 'https://img.sofascore.com/api/v1/team/2693/image', 'active', '2025-02-05 21:29:03'),
(43, 'Real Sociedad', 'real-sociedad', 4, 'https://img.sofascore.com/api/v1/team/2824/image', 'active', '2025-02-05 21:33:01'),
(44, 'Osasuna', 'osasuna', 4, 'https://img.sofascore.com/api/v1/team/2820/image', 'active', '2025-02-05 21:33:44'),
(45, 'Valencia', 'valencia', 4, 'https://img.sofascore.com/api/v1/team/2828/image', 'active', '2025-02-05 21:33:58'),
(46, 'FC Barcelona', 'fc-barcelona', 4, 'https://img.sofascore.com/api/v1/team/2817/image', 'active', '2025-02-05 21:34:14'),
(47, 'Tottenham Hotspur', 'tottenham-hotspur', 3, 'https://img.sofascore.com/api/v1/team/33/image', 'active', '2025-02-05 21:35:08'),
(48, 'FC Farul Constanța ', 'fc-farul-constan-a-', 8, 'https://img.sofascore.com/api/v1/team/3294/image', 'active', '2025-02-05 21:37:21'),
(49, 'Nantes', 'nantes', 13, 'https://static.flashscore.com/res/image/data/nZSxLDZA-YBWLmubi.png', 'active', '2025-02-07 14:58:32'),
(50, 'Bremen', 'bremen', 6, 'https://static.flashscore.com/res/image/data/lEp5rDFG-EVomMG4o.png', 'active', '2025-02-07 14:58:55'),
(51, 'Como', 'como', 10, 'https://static.flashscore.com/res/image/data/Cf5pQNyS-hpmw8K1h.png', 'active', '2025-02-07 14:59:16'),
(52, 'FC Botoșani', 'fc-boto-ani', 8, 'https://static.flashscore.com/res/image/data/S2pBtCEa-tGfxyy2G.png', 'active', '2025-02-07 14:59:54'),
(53, 'Dinamo București', 'dinamo-bucure-ti', 8, 'https://static.flashscore.com/res/image/data/Wfyr2BEa-EyedO184.png', 'active', '2025-02-07 15:00:04'),
(57, 'Monaco', 'monaco', 13, 'https://static.flashscore.com/res/image/data/GzvUVteM-44KQTmnj.png', 'active', '2025-02-07 15:06:21'),
(58, 'Brest', 'brest', 13, 'https://static.flashscore.com/res/image/data/r9aDJ9eM-Q15G9lAm.png', 'active', '2025-02-07 15:07:25'),
(59, 'Valladolid', 'valladolid', 4, 'https://static.flashscore.com/res/image/data/pCJjU8yB-htRvrFH6.png', 'active', '2025-02-07 15:10:04'),
(60, 'Rayo Vallecano', 'rayo-vallecano', 4, 'https://static.flashscore.com/res/image/data/jqaha0f5-EPWyZtpb.png', 'active', '2025-02-07 15:10:30'),
(61, 'Al-Nassr FC', 'al-nassr-fc', 15, 'https://upload.wikimedia.org/wikipedia/en/9/9d/Logo_Al-Nassr.png', 'active', '2025-02-07 15:29:47'),
(63, 'Al-Fayha FC', 'al-fayha-fc', 15, 'https://upload.wikimedia.org/wikipedia/en/thumb/0/0f/Al-Fayha_FC.svg/1200px-Al-Fayha_FC.svg.png', 'active', '2025-02-07 15:31:01');

-- --------------------------------------------------------

--
-- Table structure for table `user_actions`
--

CREATE TABLE `user_actions` (
  `id` int(11) NOT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `action_type` varchar(50) DEFAULT NULL,
  `action_details` text DEFAULT NULL,
  `page_url` varchar(255) DEFAULT NULL,
  `match_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_visits`
--

CREATE TABLE `user_visits` (
  `id` int(11) NOT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `page_url` varchar(255) DEFAULT NULL,
  `referrer_url` text DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `device_type` varchar(50) DEFAULT NULL,
  `browser` varchar(50) DEFAULT NULL,
  `os` varchar(50) DEFAULT NULL,
  `entry_time` datetime DEFAULT NULL,
  `exit_time` datetime DEFAULT NULL,
  `time_spent` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_visits`
--

INSERT INTO `user_visits` (`id`, `session_id`, `ip_address`, `user_agent`, `page_url`, `referrer_url`, `country`, `device_type`, `browser`, `os`, `entry_time`, `exit_time`, `time_spent`, `created_at`) VALUES
(1, '7007525b50ed04897bab22f6548cf6ed', '195.90.215.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36', '/', '', NULL, 'desktop', 'Chrome', 'Windows 10', '2025-02-07 19:49:33', NULL, 1, '2025-02-07 17:49:33'),
(2, '4dacf4f469c190926e09cd3f50892c4e', '84.232.151.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '/', '', NULL, 'desktop', 'Chrome', 'Windows 10', '2025-02-07 19:49:38', NULL, 0, '2025-02-07 17:49:38'),
(3, '6cf6e98e58ebec32c4255ebdfef12f48', '107.158.96.18', 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_4_1 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.4.1 Mobile/15E148 Safari/604.1', '/', '', NULL, 'phone', 'Safari', 'Mac OS X', '2025-02-07 19:49:39', NULL, 0, '2025-02-07 17:49:39'),
(4, '1047b66d8fb9907d7b458376a08d7c9a', '170.130.63.127', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0', '/', '', NULL, 'desktop', 'Firefox', 'Windows 10', '2025-02-07 19:49:40', NULL, 0, '2025-02-07 17:49:40'),
(5, '483c8b2ca6ef6d525b4e730e52face3e', '38.153.223.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0', '/stream.php?match_id=18', 'https://www.getsportnews.uk/', NULL, 'desktop', 'Firefox', 'Windows 10', '2025-02-07 19:49:41', NULL, 0, '2025-02-07 17:49:41'),
(6, '483c8b2ca6ef6d525b4e730e52face3e', '38.153.223.200', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0', '/index.php', 'https://www.getsportnews.uk/', NULL, 'desktop', 'Firefox', 'Windows 10', '2025-02-07 19:49:41', NULL, 0, '2025-02-07 17:49:41'),
(7, 'bd7ef52a91df6dfb67d64dcb6860d169', '173.234.227.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36 Edg/110.0.1587.63', '/', '', NULL, 'desktop', 'Chrome', 'Windows 10', '2025-02-07 19:49:43', NULL, 0, '2025-02-07 17:49:43'),
(8, 'ab555b8d1fc616a550a672e07cdc6d44', '170.130.63.127', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0', '/stream.php?match_id=22', 'https://www.getsportnews.uk/', NULL, 'desktop', 'Firefox', 'Windows 10', '2025-02-07 19:49:53', NULL, 0, '2025-02-07 17:49:53'),
(9, 'ab555b8d1fc616a550a672e07cdc6d44', '170.130.63.127', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/110.0', '/index.php', 'https://www.getsportnews.uk/', NULL, 'desktop', 'Firefox', 'Windows 10', '2025-02-07 19:49:54', NULL, 1, '2025-02-07 17:49:54'),
(10, '43da00c86cd8077ee84e5ddac5e10d35', '173.234.194.95', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36', '/', '', NULL, 'desktop', 'Chrome', 'Windows 10', '2025-02-07 19:49:58', NULL, 0, '2025-02-07 17:49:58'),
(11, '7a3fde95d94375f6b0d3bc29e091f83e', '173.234.194.95', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36', '/stream.php?match_id=23', 'https://www.getsportnews.uk/', NULL, 'desktop', 'Chrome', 'Windows 10', '2025-02-07 19:50:11', NULL, 0, '2025-02-07 17:50:11'),
(12, '7a3fde95d94375f6b0d3bc29e091f83e', '173.234.194.95', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36', '/index.php', 'https://www.getsportnews.uk/', NULL, 'desktop', 'Chrome', 'Windows 10', '2025-02-07 19:50:11', NULL, 0, '2025-02-07 17:50:11'),
(13, 'd441e0c26904f384fd3292c0e4143275', '156.253.178.53', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:125.0) Gecko/20100101 Firefox/125.0', '/', '', NULL, 'desktop', 'Firefox', 'Windows 10', '2025-02-07 19:50:12', NULL, 0, '2025-02-07 17:50:12'),
(14, 'eed36081f5dd0f396edb1becb4ca9c81', '120.233.53.28', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36', '/', '', NULL, 'desktop', 'Chrome', 'Windows 10', '2025-02-07 19:50:14', NULL, 0, '2025-02-07 17:50:14'),
(15, '315e2fd02d3ed9539dc792f9e96dce3b', '173.208.39.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36', '/', '', NULL, 'desktop', 'Chrome', 'Windows 10', '2025-02-07 19:50:14', NULL, 0, '2025-02-07 17:50:14'),
(16, '3112d4f623c64211d405d4cedda5004d', '173.234.227.51', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36', '/', '', NULL, 'desktop', 'Chrome', 'Mac OS X', '2025-02-07 19:50:25', NULL, 0, '2025-02-07 17:50:25'),
(17, 'ff6a70c8dad7e8889acbe35e9dad2ea7', '173.208.39.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36', '/stream.php?match_id=18', 'https://www.getsportnews.uk/', NULL, 'desktop', 'Chrome', 'Windows 10', '2025-02-07 19:50:27', NULL, 0, '2025-02-07 17:50:27'),
(18, 'ff6a70c8dad7e8889acbe35e9dad2ea7', '173.208.39.233', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36', '/index.php', 'https://www.getsportnews.uk/', NULL, 'desktop', 'Chrome', 'Windows 10', '2025-02-07 19:50:28', NULL, 1, '2025-02-07 17:50:28'),
(19, 'a78b8dff4e690f44052fbee1562eefd7', '192.126.168.213', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36', '/', '', NULL, 'desktop', 'Chrome', 'Windows 10', '2025-02-07 19:50:35', NULL, 0, '2025-02-07 17:50:35'),
(20, '084ae18c65207cd8355cb174a1b1d1e0', '173.234.227.51', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36', '/stream.php?match_id=21', 'https://www.getsportnews.uk/', NULL, 'desktop', 'Chrome', 'Mac OS X', '2025-02-07 19:50:38', NULL, 0, '2025-02-07 17:50:38'),
(21, '084ae18c65207cd8355cb174a1b1d1e0', '173.234.227.51', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36', '/index.php', 'https://www.getsportnews.uk/', NULL, 'desktop', 'Chrome', 'Mac OS X', '2025-02-07 19:50:39', NULL, 1, '2025-02-07 17:50:39'),
(22, '23fafa9d347e15c2788a96b66813af1d', '173.208.39.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36 Edg/110.0.1587.63', '/', '', NULL, 'desktop', 'Chrome', 'Windows 10', '2025-02-07 19:50:42', NULL, 0, '2025-02-07 17:50:42'),
(23, 'a312a88171632497d4e9004e05f1508b', '173.208.39.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36 Edg/110.0.1587.63', '/stream.php?match_id=18', 'https://www.getsportnews.uk/', NULL, 'desktop', 'Chrome', 'Windows 10', '2025-02-07 19:50:55', NULL, 0, '2025-02-07 17:50:55'),
(24, 'a312a88171632497d4e9004e05f1508b', '173.208.39.215', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/110.0.0.0 Safari/537.36 Edg/110.0.1587.63', '/index.php', 'https://www.getsportnews.uk/', NULL, 'desktop', 'Chrome', 'Windows 10', '2025-02-07 19:50:56', NULL, 1, '2025-02-07 17:50:56'),
(25, '6d7c2de21bc9d9522681876759e8cf78', '156.228.77.67', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36', '/', '', NULL, 'desktop', 'Chrome', 'Windows 10', '2025-02-07 19:51:11', NULL, 0, '2025-02-07 17:51:11'),
(26, '4dacf4f469c190926e09cd3f50892c4e', '84.232.151.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '/', '', NULL, 'desktop', 'Chrome', 'Windows 10', '2025-02-07 20:42:53', NULL, 3195, '2025-02-07 18:42:53'),
(27, '4dacf4f469c190926e09cd3f50892c4e', '84.232.151.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '/', '', NULL, 'desktop', 'Chrome', 'Windows 10', '2025-02-07 20:49:49', NULL, 3611, '2025-02-07 18:49:49'),
(28, '4dacf4f469c190926e09cd3f50892c4e', '84.232.151.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '/', '', NULL, 'desktop', 'Chrome', 'Windows 10', '2025-02-07 20:52:03', NULL, 3745, '2025-02-07 18:52:03'),
(29, '4dacf4f469c190926e09cd3f50892c4e', '84.232.151.18', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '/', '', NULL, 'desktop', 'Chrome', 'Windows 10', '2025-02-07 20:53:44', NULL, 3846, '2025-02-07 18:53:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leagues`
--
ALTER TABLE `leagues`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `league` (`league`),
  ADD KEY `home_team` (`home_team`),
  ADD KEY `away_team` (`away_team`);

--
-- Indexes for table `stats_history`
--
ALTER TABLE `stats_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_match_date` (`match_id`,`date`);

--
-- Indexes for table `stream_sources`
--
ALTER TABLE `stream_sources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `match_id` (`match_id`);

--
-- Indexes for table `stream_stats`
--
ALTER TABLE `stream_stats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `match_id` (`match_id`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `league_id` (`league_id`);

--
-- Indexes for table `user_actions`
--
ALTER TABLE `user_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `match_id` (`match_id`);

--
-- Indexes for table `user_visits`
--
ALTER TABLE `user_visits`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `leagues`
--
ALTER TABLE `leagues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `stats_history`
--
ALTER TABLE `stats_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stream_sources`
--
ALTER TABLE `stream_sources`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stream_stats`
--
ALTER TABLE `stream_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `user_actions`
--
ALTER TABLE `user_actions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_visits`
--
ALTER TABLE `user_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `matches`
--
ALTER TABLE `matches`
  ADD CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`league`) REFERENCES `leagues` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `matches_ibfk_2` FOREIGN KEY (`home_team`) REFERENCES `teams` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `matches_ibfk_3` FOREIGN KEY (`away_team`) REFERENCES `teams` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stats_history`
--
ALTER TABLE `stats_history`
  ADD CONSTRAINT `stats_history_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stream_sources`
--
ALTER TABLE `stream_sources`
  ADD CONSTRAINT `stream_sources_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stream_stats`
--
ALTER TABLE `stream_stats`
  ADD CONSTRAINT `stream_stats_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teams`
--
ALTER TABLE `teams`
  ADD CONSTRAINT `teams_ibfk_1` FOREIGN KEY (`league_id`) REFERENCES `leagues` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `user_actions`
--
ALTER TABLE `user_actions`
  ADD CONSTRAINT `user_actions_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
