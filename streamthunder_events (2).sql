-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 05, 2025 at 12:13 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `streamthunder_events`
--

-- --------------------------------------------------------

--
-- Table structure for table `evenimente`
--

CREATE TABLE `evenimente` (
  `id` int(11) NOT NULL,
  `sport` varchar(50) DEFAULT NULL,
  `echipa1` varchar(100) DEFAULT NULL,
  `echipa2` varchar(100) DEFAULT NULL,
  `data` date DEFAULT NULL,
  `ora` time DEFAULT NULL,
  `link_stream` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leagues`
--

CREATE TABLE `leagues` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `country_code` varchar(10) NOT NULL,
  `display_name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leagues_new`
--

CREATE TABLE IF NOT EXISTS `leagues_new` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `flag_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `leagues`
--

INSERT INTO `leagues` (`id`, `name`, `country_code`, `display_name`, `category`, `sort_order`) VALUES
(1, 'Champions League', 'eu', 'UEFA: Champions League', 'European', 1),
(2, 'Europa League', 'eu', 'UEFA: Europa League', 'European', 2),
(3, 'Conference League', 'eu', 'UEFA: Conference League', 'European', 3),
(4, 'Premier League', 'gb-eng', 'ANGLIA: Premier League', 'National', 4),
(5, 'Championship', 'gb-eng', 'ANGLIA: Championship', 'National', 5),
(6, 'La Liga', 'es', 'SPANIA: La Liga', 'National', 6),
(7, 'La Liga B', 'es', 'SPANIA: La Liga 2', 'National', 7),
(8, 'Serie A', 'it', 'ITALIA: Serie A', 'National', 8),
(9, 'Serie B', 'it', 'ITALIA: Serie B', 'National', 9),
(10, 'Bundesliga', 'de', 'GERMANIA: Bundesliga', 'National', 10),
(11, 'Bundesliga 2', 'de', 'GERMANIA: Bundesliga 2', 'National', 11),
(12, 'Ligue 1', 'fr', 'FRANTA: Ligue 1', 'National', 12),
(13, 'Ligue 2', 'fr', 'FRANTA: Ligue 2', 'National', 13),
(14, 'Superliga', 'ro', 'ROMANIA: Superliga', 'National', 14),
(15, 'Liga 2', 'ro', 'ROMANIA: Liga 2', 'National', 15),
(16, 'Eredivisie', 'nl', 'OLANDA: Eredivisie', 'National', 16),
(17, 'Primeira Liga', 'pt', 'PORTUGALIA: Primeira Liga', 'National', 17),
(18, 'Super Lig', 'tr', 'TURCIA: Super Lig', 'National', 18);

-- --------------------------------------------------------

--
-- Table structure for table `matches`
--

CREATE TABLE `matches` (
  `id` int(11) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `league` varchar(100) NOT NULL,
  `home_team_id` int(11) NOT NULL,
  `away_team_id` int(11) NOT NULL,
  `match_time` datetime NOT NULL,
  `sport` varchar(50) NOT NULL DEFAULT 'football',
  `status` enum('LIVE','FT','UPCOMING') NOT NULL DEFAULT 'UPCOMING',
  `stream_link` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `stream_url` varchar(255) DEFAULT NULL,
  `league_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `matches`
--

INSERT INTO `matches` (`id`, `slug`, `league`, `home_team_id`, `away_team_id`, `match_time`, `sport`, `status`, `stream_link`, `created_at`, `stream_url`, `league_id`) VALUES
(12, 'alaves-vs-ac-milan', '', 72, 14, '2025-02-05 14:00:00', 'football', 'LIVE', NULL, '2025-02-04 22:17:29', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `logo_url` varchar(255) NOT NULL,
  `country_code` varchar(2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`id`, `name`, `logo_url`, `country_code`, `created_at`) VALUES
(1, 'FCSB', 'https://e1.pngegg.com/pngimages/350/380/png-clipart-logo-de-la-ligue-des-champions-fc-fcsb-stadionul-steaua-national-arena-uefa-europa-league-uefa-champions-league-football-baza-sportiva-fcsb-thumbnail.png', 'ro', '2025-01-25 19:59:36'),
(2, 'CFR Cluj', 'https://upload.wikimedia.org/wikipedia/en/thumb/7/7e/CFR_Cluj_badge.svg/1200px-CFR_Cluj_badge.svg.png', 'ro', '2025-01-25 19:59:36'),
(3, 'Rapid București', 'https://upload.wikimedia.org/wikipedia/en/thumb/8/82/FC_Rapid_Bucuresti_logo.svg/1200px-FC_Rapid_Bucuresti_logo.svg.png', 'ro', '2025-01-25 19:59:36'),
(4, 'Universitatea Craiova', 'https://upload.wikimedia.org/wikipedia/en/thumb/0/02/CS_Universitatea_Craiova_logo.svg/1200px-CS_Universitatea_Craiova_logo.svg.png', 'ro', '2025-01-25 19:59:36'),
(5, 'Dinamo București', 'https://toppng.com/uploads/preview/fc-dinamo-bucuresti-2008-vector-logo-11574298016ojojjpurrl.png', 'ro', '2025-01-25 19:59:36'),
(6, 'Manchester City', 'https://upload.wikimedia.org/wikipedia/en/thumb/e/eb/Manchester_City_FC_badge.svg/120px-Manchester_City_FC_badge.svg.png', 'gb', '2025-01-25 19:59:36'),
(7, 'Liverpool', 'https://upload.wikimedia.org/wikipedia/en/thumb/0/0c/Liverpool_FC.svg/120px-Liverpool_FC.svg.png', 'gb', '2025-01-25 19:59:36'),
(8, 'Manchester United', 'https://upload.wikimedia.org/wikipedia/en/thumb/7/7a/Manchester_United_FC_crest.svg/120px-Manchester_United_FC_crest.svg.png', 'gb', '2025-01-25 19:59:36'),
(9, 'Arsenal', 'https://upload.wikimedia.org/wikipedia/en/thumb/5/53/Arsenal_FC.svg/120px-Arsenal_FC.svg.png', 'gb', '2025-01-25 19:59:36'),
(10, 'Chelsea', 'https://upload.wikimedia.org/wikipedia/en/thumb/c/cc/Chelsea_FC.svg/120px-Chelsea_FC.svg.png', 'gb', '2025-01-25 19:59:36'),
(11, 'Real Madrid', 'https://upload.wikimedia.org/wikipedia/en/thumb/5/56/Real_Madrid_CF.svg/120px-Real_Madrid_CF.svg.png', 'es', '2025-01-25 19:59:36'),
(12, 'Barcelona', 'https://upload.wikimedia.org/wikipedia/en/thumb/4/47/FC_Barcelona_%28crest%29.svg/120px-FC_Barcelona_%28crest%29.svg.png', 'es', '2025-01-25 19:59:36'),
(13, 'Atletico Madrid', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSQ7WUXmeSddkoouLR88Lcp5iDpq71l3aDiCg&s', 'es', '2025-01-25 19:59:36'),
(14, 'AC Milan', 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d0/Logo_of_AC_Milan.svg/1200px-Logo_of_AC_Milan.svg.png', 'it', '2025-01-25 19:59:36'),
(15, 'Inter Milan', 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/FC_Internazionale_Milano_2021.svg/120px-FC_Internazionale_Milano_2021.svg.png', 'it', '2025-01-25 19:59:36'),
(16, 'Juventus', 'https://banner2.cleanpng.com/20180702/exr/aax1ilyx7.webp', 'it', '2025-01-25 19:59:36'),
(17, 'Bayern Munich', 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1b/FC_Bayern_M%C3%BCnchen_logo_%282017%29.svg/120px-FC_Bayern_M%C3%BCnchen_logo_%282017%29.svg.png', 'de', '2025-01-25 19:59:36'),
(18, 'Borussia Dortmund', 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/67/Borussia_Dortmund_logo.svg/120px-Borussia_Dortmund_logo.svg.png', 'de', '2025-01-25 19:59:36'),
(19, 'Paris Saint-Germain', 'https://upload.wikimedia.org/wikipedia/en/thumb/a/a7/Paris_Saint-Germain_F.C..svg/120px-Paris_Saint-Germain_F.C..svg.png', 'fr', '2025-01-25 19:59:36'),
(20, 'Farul Constanța', 'https://upload.wikimedia.org/wikipedia/ro/thumb/d/dd/FC_Farul_Constanta.svg/1200px-FC_Farul_Constanta.svg.png', 'ro', '2025-01-25 20:04:00'),
(21, 'Sepsi OSK', 'https://upload.wikimedia.org/wikipedia/en/thumb/e/ec/ACS_Sepsi_OSK_Sfantu_Gheorghe_logo.svg/1200px-ACS_Sepsi_OSK_Sfantu_Gheorghe_logo.svg.png', 'ro', '2025-01-25 20:04:00'),
(22, 'FC Voluntari', 'https://upload.wikimedia.org/wikipedia/ro/thumb/d/d9/FC_Voluntari.svg/1200px-FC_Voluntari.svg.png', 'ro', '2025-01-25 20:04:00'),
(23, 'FC Hermannstadt', 'https://upload.wikimedia.org/wikipedia/ro/5/5b/Logo_FC_Hermannstadt.png', 'ro', '2025-01-25 20:04:00'),
(24, 'Petrolul Ploiești', 'https://upload.wikimedia.org/wikipedia/ro/thumb/e/e0/FC_Petrolul_Ploiesti.svg/1200px-FC_Petrolul_Ploiesti.svg.png', 'ro', '2025-01-25 20:04:00'),
(25, 'FC Botoșani', 'https://fcbt.ro/wp-content/uploads/2022/01/player.png', 'ro', '2025-01-25 20:04:00'),
(26, 'UTA Arad', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQWeYnnqU9blysHEPFd197mK_h47jbu4ZEkHw&s', 'ro', '2025-01-25 20:04:00'),
(27, 'FC U Craiova 1948', 'https://upload.wikimedia.org/wikipedia/en/1/1a/FC_U_Craiova_1948_logo.png', 'ro', '2025-01-25 20:04:00'),
(28, 'Oțelul Galați', 'https://upload.wikimedia.org/wikipedia/ro/thumb/5/5d/ASC_Otelul_Galati.svg/1200px-ASC_Otelul_Galati.svg.png', 'ro', '2025-01-25 20:04:00'),
(29, 'Poli Iași', 'https://upload.wikimedia.org/wikipedia/ro/thumb/8/86/FC_Poli_Iasi_%282023%29.svg/1200px-FC_Poli_Iasi_%282023%29.svg.png', 'ro', '2025-01-25 20:04:00'),
(30, 'Tottenham', 'https://upload.wikimedia.org/wikipedia/en/thumb/b/b4/Tottenham_Hotspur.svg/120px-Tottenham_Hotspur.svg.png', 'gb', '2025-01-25 20:04:00'),
(31, 'Aston Villa', 'https://banner2.cleanpng.com/20180802/ehg/-5b7397a3d9d774.18027853153430211589237770.jpg', 'gb', '2025-01-25 20:04:00'),
(32, 'Brighton', 'https://upload.wikimedia.org/wikipedia/en/thumb/f/fd/Brighton_%26_Hove_Albion_logo.svg/120px-Brighton_%26_Hove_Albion_logo.svg.png', 'gb', '2025-01-25 20:04:00'),
(33, 'West Ham', 'https://upload.wikimedia.org/wikipedia/en/thumb/c/c2/West_Ham_United_FC_logo.svg/120px-West_Ham_United_FC_logo.svg.png', 'gb', '2025-01-25 20:04:00'),
(34, 'Brentford', 'https://upload.wikimedia.org/wikipedia/en/thumb/2/2a/Brentford_FC_crest.svg/120px-Brentford_FC_crest.svg.png', 'gb', '2025-01-25 20:04:00'),
(35, 'Newcastle', 'https://upload.wikimedia.org/wikipedia/en/thumb/5/56/Newcastle_United_Logo.svg/120px-Newcastle_United_Logo.svg.png', 'gb', '2025-01-25 20:04:00'),
(36, 'Crystal Palace', 'https://upload.wikimedia.org/wikipedia/en/thumb/a/a2/Crystal_Palace_FC_logo_%282022%29.svg/1200px-Crystal_Palace_FC_logo_%282022%29.svg.png', 'gb', '2025-01-25 20:04:00'),
(37, 'Fulham', 'https://upload.wikimedia.org/wikipedia/en/thumb/e/eb/Fulham_FC_%28shield%29.svg/120px-Fulham_FC_%28shield%29.svg.png', 'gb', '2025-01-25 20:04:00'),
(38, 'Nottingham Forest', 'https://upload.wikimedia.org/wikipedia/en/thumb/e/e5/Nottingham_Forest_F.C._logo.svg/120px-Nottingham_Forest_F.C._logo.svg.png', 'gb', '2025-01-25 20:04:00'),
(39, 'Wolves', 'https://upload.wikimedia.org/wikipedia/en/thumb/f/fc/Wolverhampton_Wanderers.svg/120px-Wolverhampton_Wanderers.svg.png', 'gb', '2025-01-25 20:04:00'),
(40, 'Bournemouth', 'https://upload.wikimedia.org/wikipedia/en/thumb/e/e5/AFC_Bournemouth_%282013%29.svg/120px-AFC_Bournemouth_%282013%29.svg.png', 'gb', '2025-01-25 20:04:00'),
(41, 'Luton Town', 'https://upload.wikimedia.org/wikipedia/en/thumb/9/9d/Luton_Town_logo.svg/1200px-Luton_Town_logo.svg.png', 'gb', '2025-01-25 20:04:00'),
(42, 'Sheffield United', 'https://upload.wikimedia.org/wikipedia/en/thumb/9/9c/Sheffield_United_FC_logo.svg/120px-Sheffield_United_FC_logo.svg.png', 'gb', '2025-01-25 20:04:00'),
(43, 'Burnley', 'https://upload.wikimedia.org/wikipedia/en/thumb/6/6d/Burnley_FC_Logo.svg/1200px-Burnley_FC_Logo.svg.png', 'gb', '2025-01-25 20:04:00'),
(44, 'Napoli', 'https://a.espncdn.com/combiner/i?img=/i/teamlogos/soccer/500/114.png', 'it', '2025-01-25 20:04:00'),
(45, 'Lazio', 'https://upload.wikimedia.org/wikipedia/en/thumb/c/ce/S.S._Lazio_badge.svg/120px-S.S._Lazio_badge.svg.png', 'it', '2025-01-25 20:04:00'),
(46, 'Roma', 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f7/AS_Roma_logo_%282017%29.svg/120px-AS_Roma_logo_%282017%29.svg.png', 'it', '2025-01-25 20:04:00'),
(47, 'Atalanta', 'https://upload.wikimedia.org/wikipedia/en/thumb/6/66/AtalantaBC.svg/120px-AtalantaBC.svg.png', 'it', '2025-01-25 20:04:00'),
(48, 'Fiorentina', 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/79/ACF_Fiorentina.svg/120px-ACF_Fiorentina.svg.png', 'it', '2025-01-25 20:04:00'),
(49, 'Bologna', 'https://upload.wikimedia.org/wikipedia/commons/thumb/5/5b/Bologna_F.C._1909_logo.svg/1200px-Bologna_F.C._1909_logo.svg.png', 'it', '2025-01-25 20:04:00'),
(50, 'Torino', 'https://upload.wikimedia.org/wikipedia/en/thumb/2/2e/Torino_FC_Logo.svg/120px-Torino_FC_Logo.svg.png', 'it', '2025-01-25 20:04:00'),
(51, 'Monza', 'https://upload.wikimedia.org/wikipedia/en/thumb/a/a7/AC_Monza_logo_%282021%29.svg/1200px-AC_Monza_logo_%282021%29.svg.png', 'it', '2025-01-25 20:04:00'),
(52, 'Udinese', 'https://upload.wikimedia.org/wikipedia/en/thumb/c/ce/Udinese_Calcio_logo.svg/120px-Udinese_Calcio_logo.svg.png', 'it', '2025-01-25 20:04:00'),
(53, 'Sassuolo', 'https://upload.wikimedia.org/wikipedia/en/thumb/1/1c/US_Sassuolo_Calcio_logo.svg/120px-US_Sassuolo_Calcio_logo.svg.png', 'it', '2025-01-25 20:04:00'),
(54, 'Empoli', 'https://a.espncdn.com/combiner/i?img=/i/teamlogos/soccer/500/2574.png', 'it', '2025-01-25 20:04:00'),
(55, 'Frosinone', 'https://a.espncdn.com/combiner/i?img=/i/teamlogos/soccer/500/4057.png', 'it', '2025-01-25 20:04:00'),
(56, 'Genoa', 'https://upload.wikimedia.org/wikipedia/en/thumb/2/2c/Genoa_CFC_crest.svg/640px-Genoa_CFC_crest.svg.png', 'it', '2025-01-25 20:04:00'),
(57, 'Verona', 'https://upload.wikimedia.org/wikipedia/en/thumb/9/92/Hellas_Verona_FC_logo_%282020%29.svg/120px-Hellas_Verona_FC_logo_%282020%29.svg.png', 'it', '2025-01-25 20:04:00'),
(58, 'Cagliari', 'https://upload.wikimedia.org/wikipedia/en/thumb/6/61/Cagliari_Calcio_1920.svg/120px-Cagliari_Calcio_1920.svg.png', 'it', '2025-01-25 20:04:00'),
(59, 'Salernitana', 'https://upload.wikimedia.org/wikipedia/commons/d/d9/Salernitana_logo_1948-49.jpg?20191216142116', 'it', '2025-01-25 20:04:00'),
(60, 'Girona', 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f7/Girona_FC_Logo.svg/1200px-Girona_FC_Logo.svg.png', 'es', '2025-01-25 20:04:00'),
(61, 'Athletic Bilbao', 'https://seeklogo.com/images/A/athletic-bilbao-logo-80FA400F39-seeklogo.com.png', 'es', '2025-01-25 20:04:00'),
(62, 'Real Sociedad', 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f1/Real_Sociedad_logo.svg/120px-Real_Sociedad_logo.svg.png', 'es', '2025-01-25 20:04:00'),
(63, 'Real Betis', 'https://upload.wikimedia.org/wikipedia/en/thumb/1/13/Real_betis_logo.svg/120px-Real_betis_logo.svg.png', 'es', '2025-01-25 20:04:00'),
(64, 'Valencia', 'https://upload.wikimedia.org/wikipedia/en/thumb/c/ce/Valenciacf.svg/120px-Valenciacf.svg.png', 'es', '2025-01-25 20:04:00'),
(65, 'Las Palmas', 'https://upload.wikimedia.org/wikipedia/en/thumb/2/20/UD_Las_Palmas_logo.svg/1200px-UD_Las_Palmas_logo.svg.png', 'es', '2025-01-25 20:04:00'),
(66, 'Rayo Vallecano', 'https://upload.wikimedia.org/wikipedia/en/thumb/d/d8/Rayo_Vallecano_logo.svg/640px-Rayo_Vallecano_logo.svg.png', 'es', '2025-01-25 20:04:00'),
(67, 'Osasuna', 'https://upload.wikimedia.org/wikipedia/en/thumb/3/38/CA_Osasuna_2024_crest.svg/1200px-CA_Osasuna_2024_crest.svg.png', 'es', '2025-01-25 20:04:00'),
(68, 'Villarreal', 'https://upload.wikimedia.org/wikipedia/en/thumb/b/b9/Villarreal_CF_logo-en.svg/640px-Villarreal_CF_logo-en.svg.png', 'es', '2025-01-25 20:04:00'),
(69, 'Getafe', 'https://upload.wikimedia.org/wikipedia/en/thumb/4/46/Getafe_logo.svg/800px-Getafe_logo.svg.png', 'es', '2025-01-25 20:04:00'),
(70, 'Mallorca', 'https://upload.wikimedia.org/wikipedia/en/thumb/e/e0/Rcd_mallorca.svg/120px-Rcd_mallorca.svg.png', 'es', '2025-01-25 20:04:00'),
(71, 'Sevilla', 'https://upload.wikimedia.org/wikipedia/en/thumb/3/3b/Sevilla_FC_logo.svg/120px-Sevilla_FC_logo.svg.png', 'es', '2025-01-25 20:04:00'),
(72, 'Alaves', 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f8/Deportivo_Alaves_logo_%282020%29.svg/1200px-Deportivo_Alaves_logo_%282020%29.svg.png', 'es', '2025-01-25 20:04:00'),
(73, 'Celta Vigo', 'https://upload.wikimedia.org/wikipedia/en/thumb/1/12/RC_Celta_de_Vigo_logo.svg/120px-RC_Celta_de_Vigo_logo.svg.png', 'es', '2025-01-25 20:04:00'),
(74, 'Granada', 'https://upload.wikimedia.org/wikipedia/en/thumb/d/d5/Logo_of_Granada_Club_de_F%C3%BAtbol.svg/1200px-Logo_of_Granada_Club_de_F%C3%BAtbol.svg.png', 'es', '2025-01-25 20:04:00'),
(75, 'Cadiz', 'https://upload.wikimedia.org/wikipedia/en/thumb/5/58/C%C3%A1diz_CF_logo.svg/120px-C%C3%A1diz_CF_logo.svg.png', 'es', '2025-01-25 20:04:00'),
(76, 'Almeria', 'https://upload.wikimedia.org/wikipedia/en/4/4a/UD_Almeria_logo.svg', 'es', '2025-01-25 20:04:00'),
(77, 'U Cluj', 'https://upload.wikimedia.org/wikipedia/ro/thumb/3/3e/U_Cluj.svg/1200px-U_Cluj.svg.png', 'ro', '2025-01-26 10:15:32'),
(78, 'Unirea Slobozia', 'https://upload.wikimedia.org/wikipedia/ro/7/73/Unirea_Slobozia_Logo_2024.png', 'ro', '2025-01-26 10:16:37'),
(79, 'Gloria Buzau', 'https://upload.wikimedia.org/wikipedia/en/d/dd/FC_Buz%C4%83u_logo.png', 'ro', '2025-01-26 10:17:02'),
(80, 'Parma', 'https://upload.wikimedia.org/wikipedia/commons/4/40/Logo_Parma_Calcio_1913_%28adozione_2016%29.png', 'it', '2025-01-26 10:51:25'),
(81, 'Lecce', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSYq27rbmgKkKG5LDETKr0j7mLZNjHOidPgdg&s', 'it', '2025-01-26 10:52:15');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `evenimente`
--
ALTER TABLE `evenimente`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leagues`
--
ALTER TABLE `leagues`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leagues_new`
--
ALTER TABLE `leagues_new`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_slug` (`slug`),
  ADD KEY `home_team_id` (`home_team_id`),
  ADD KEY `away_team_id` (`away_team_id`),
  ADD KEY `league_id` (`league_id`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `evenimente`
--
ALTER TABLE `evenimente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leagues`
--
ALTER TABLE `leagues`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `leagues_new`
--
ALTER TABLE `leagues_new`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `matches`
--
ALTER TABLE `matches`
  ADD CONSTRAINT `fk_matches_league` FOREIGN KEY (`league_id`) REFERENCES `leagues_new` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`home_team_id`) REFERENCES `teams` (`id`),
  ADD CONSTRAINT `matches_ibfk_2` FOREIGN KEY (`away_team_id`) REFERENCES `teams` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
