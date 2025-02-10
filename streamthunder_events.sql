-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 25, 2025 at 10:43 PM
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
-- Table structure for table `matches`
--

CREATE TABLE `matches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `home_team_id` int(11) NOT NULL,
  `away_team_id` int(11) NOT NULL,
  `match_time` datetime NOT NULL,
  `stream_link` varchar(255) DEFAULT NULL,
  `sport` varchar(50) NOT NULL DEFAULT 'football',
  PRIMARY KEY (`id`),
  KEY `home_team_id` (`home_team_id`),
  KEY `away_team_id` (`away_team_id`),
  CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`home_team_id`) REFERENCES `teams` (`id`),
  CONSTRAINT `matches_ibfk_2` FOREIGN KEY (`away_team_id`) REFERENCES `teams` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `logo_url` text DEFAULT NULL,
  `country_code` varchar(10) DEFAULT NULL,
  `league` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teams`
--

INSERT INTO `teams` (`id`, `name`, `logo_url`, `country_code`, `league`, `created_at`) VALUES
(1, 'FCSB', 'https://upload.wikimedia.org/wikipedia/ro/thumb/d/d5/Sigla_FCSB.svg/120px-Sigla_FCSB.svg.png', 'ro', 'Liga I', '2025-01-25 19:59:36'),
(2, 'CFR Cluj', 'https://upload.wikimedia.org/wikipedia/ro/thumb/b/ba/CFR_Cluj_logo_2019.png/120px-CFR_Cluj_logo_2019.png', 'ro', 'Liga I', '2025-01-25 19:59:36'),
(3, 'Rapid București', 'https://upload.wikimedia.org/wikipedia/ro/thumb/3/32/FC_Rapid_Bucuresti.svg/120px-FC_Rapid_Bucuresti.svg.png', 'ro', 'Liga I', '2025-01-25 19:59:36'),
(4, 'Universitatea Craiova', 'https://upload.wikimedia.org/wikipedia/ro/thumb/8/83/CS_Universitatea_Craiova_logo.svg/120px-CS_Universitatea_Craiova_logo.svg.png', 'ro', 'Liga I', '2025-01-25 19:59:36'),
(5, 'Dinamo București', 'https://upload.wikimedia.org/wikipedia/ro/thumb/0/0a/FC_Dinamo_Bucuresti.svg/120px-FC_Dinamo_Bucuresti.svg.png', 'ro', 'Liga I', '2025-01-25 19:59:36'),
(6, 'Manchester City', 'https://upload.wikimedia.org/wikipedia/en/thumb/e/eb/Manchester_City_FC_badge.svg/120px-Manchester_City_FC_badge.svg.png', 'gb', 'Premier League', '2025-01-25 19:59:36'),
(7, 'Liverpool', 'https://upload.wikimedia.org/wikipedia/en/thumb/0/0c/Liverpool_FC.svg/120px-Liverpool_FC.svg.png', 'gb', 'Premier League', '2025-01-25 19:59:36'),
(8, 'Manchester United', 'https://upload.wikimedia.org/wikipedia/en/thumb/7/7a/Manchester_United_FC_crest.svg/120px-Manchester_United_FC_crest.svg.png', 'gb', 'Premier League', '2025-01-25 19:59:36'),
(9, 'Arsenal', 'https://upload.wikimedia.org/wikipedia/en/thumb/5/53/Arsenal_FC.svg/120px-Arsenal_FC.svg.png', 'gb', 'Premier League', '2025-01-25 19:59:36'),
(10, 'Chelsea', 'https://upload.wikimedia.org/wikipedia/en/thumb/c/cc/Chelsea_FC.svg/120px-Chelsea_FC.svg.png', 'gb', 'Premier League', '2025-01-25 19:59:36'),
(11, 'Real Madrid', 'https://upload.wikimedia.org/wikipedia/en/thumb/5/56/Real_Madrid_CF.svg/120px-Real_Madrid_CF.svg.png', 'es', 'La Liga', '2025-01-25 19:59:36'),
(12, 'Barcelona', 'https://upload.wikimedia.org/wikipedia/en/thumb/4/47/FC_Barcelona_%28crest%29.svg/120px-FC_Barcelona_%28crest%29.svg.png', 'es', 'La Liga', '2025-01-25 19:59:36'),
(13, 'Atletico Madrid', 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f4/Atletico_Madrid_2017_logo.svg/120px-Atletico_Madrid_2017_logo.svg.png', 'es', 'La Liga', '2025-01-25 19:59:36'),
(14, 'AC Milan', 'https://upload.wikimedia.org/wikipedia/commons/thumb/d/d0/AC_Milan_logo.svg/120px-AC_Milan_logo.svg.png', 'it', 'Serie A', '2025-01-25 19:59:36'),
(15, 'Inter Milan', 'https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/FC_Internazionale_Milano_2021.svg/120px-FC_Internazionale_Milano_2021.svg.png', 'it', 'Serie A', '2025-01-25 19:59:36'),
(16, 'Juventus', 'https://upload.wikimedia.org/wikipedia/commons/thumb/b/bc/Juventus_FC_2017_icon.svg/120px-Juventus_FC_2017_icon.svg.png', 'it', 'Serie A', '2025-01-25 19:59:36'),
(17, 'Bayern Munich', 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/1b/FC_Bayern_M%C3%BCnchen_logo_%282017%29.svg/120px-FC_Bayern_M%C3%BCnchen_logo_%282017%29.svg.png', 'de', 'Bundesliga', '2025-01-25 19:59:36'),
(18, 'Borussia Dortmund', 'https://upload.wikimedia.org/wikipedia/commons/thumb/6/67/Borussia_Dortmund_logo.svg/120px-Borussia_Dortmund_logo.svg.png', 'de', 'Bundesliga', '2025-01-25 19:59:36'),
(19, 'Paris Saint-Germain', 'https://upload.wikimedia.org/wikipedia/en/thumb/a/a7/Paris_Saint-Germain_F.C..svg/120px-Paris_Saint-Germain_F.C..svg.png', 'fr', 'Ligue 1', '2025-01-25 19:59:36'),
(20, 'Farul Constanța', 'https://upload.wikimedia.org/wikipedia/ro/thumb/c/c5/FCV_Farul_Constan%C8%9Ba_logo_2021.svg/120px-FCV_Farul_Constan%C8%9Ba_logo_2021.svg.png', 'ro', 'Liga I', '2025-01-25 20:04:00'),
(21, 'Sepsi OSK', 'https://upload.wikimedia.org/wikipedia/ro/thumb/5/51/Sepsi_OSK_logo.svg/120px-Sepsi_OSK_logo.svg.png', 'ro', 'Liga I', '2025-01-25 20:04:00'),
(22, 'FC Voluntari', 'https://upload.wikimedia.org/wikipedia/ro/thumb/3/39/FC_Voluntari_logo_2018.png/120px-FC_Voluntari_logo_2018.png', 'ro', 'Liga I', '2025-01-25 20:04:00'),
(23, 'FC Hermannstadt', 'https://upload.wikimedia.org/wikipedia/ro/thumb/1/14/AFC_Hermannstadt_logo.png/120px-AFC_Hermannstadt_logo.png', 'ro', 'Liga I', '2025-01-25 20:04:00'),
(24, 'Petrolul Ploiești', 'https://upload.wikimedia.org/wikipedia/ro/thumb/0/0d/FC_Petrolul_Ploie%C8%99ti_logo.svg/120px-FC_Petrolul_Ploie%C8%99ti_logo.svg.png', 'ro', 'Liga I', '2025-01-25 20:04:00'),
(25, 'FC Botoșani', 'https://upload.wikimedia.org/wikipedia/ro/thumb/2/23/FC_Boto%C8%99ani_logo.svg/120px-FC_Boto%C8%99ani_logo.svg.png', 'ro', 'Liga I', '2025-01-25 20:04:00'),
(26, 'UTA Arad', 'https://upload.wikimedia.org/wikipedia/ro/thumb/7/7c/UTA_Arad_logo.svg/120px-UTA_Arad_logo.svg.png', 'ro', 'Liga I', '2025-01-25 20:04:00'),
(27, 'FC U Craiova 1948', 'https://upload.wikimedia.org/wikipedia/ro/thumb/c/c2/FC_U_Craiova_1948_logo.svg/120px-FC_U_Craiova_1948_logo.svg.png', 'ro', 'Liga I', '2025-01-25 20:04:00'),
(28, 'Oțelul Galați', 'https://upload.wikimedia.org/wikipedia/ro/thumb/0/03/ASC_O%C8%9Belul_Gala%C8%9Bi_logo.svg/120px-ASC_O%C8%9Belul_Gala%C8%9Bi_logo.svg.png', 'ro', 'Liga I', '2025-01-25 20:04:00'),
(29, 'Poli Iași', 'https://upload.wikimedia.org/wikipedia/ro/thumb/5/5c/ACSM_Politehnica_Ia%C8%99i_logo.svg/120px-ACSM_Politehnica_Ia%C8%99i_logo.svg.png', 'ro', 'Liga I', '2025-01-25 20:04:00'),
(30, 'Tottenham', 'https://upload.wikimedia.org/wikipedia/en/thumb/b/b4/Tottenham_Hotspur.svg/120px-Tottenham_Hotspur.svg.png', 'gb', 'Premier League', '2025-01-25 20:04:00'),
(31, 'Aston Villa', 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f9/Aston_Villa_FC_crest_%282016%29.svg/120px-Aston_Villa_FC_crest_%282016%29.svg.png', 'gb', 'Premier League', '2025-01-25 20:04:00'),
(32, 'Brighton', 'https://upload.wikimedia.org/wikipedia/en/thumb/f/fd/Brighton_%26_Hove_Albion_logo.svg/120px-Brighton_%26_Hove_Albion_logo.svg.png', 'gb', 'Premier League', '2025-01-25 20:04:00'),
(33, 'West Ham', 'https://upload.wikimedia.org/wikipedia/en/thumb/c/c2/West_Ham_United_FC_logo.svg/120px-West_Ham_United_FC_logo.svg.png', 'gb', 'Premier League', '2025-01-25 20:04:00'),
(34, 'Brentford', 'https://upload.wikimedia.org/wikipedia/en/thumb/2/2a/Brentford_FC_crest.svg/120px-Brentford_FC_crest.svg.png', 'gb', 'Premier League', '2025-01-25 20:04:00'),
(35, 'Newcastle', 'https://upload.wikimedia.org/wikipedia/en/thumb/5/56/Newcastle_United_Logo.svg/120px-Newcastle_United_Logo.svg.png', 'gb', 'Premier League', '2025-01-25 20:04:00'),
(36, 'Crystal Palace', 'https://upload.wikimedia.org/wikipedia/en/thumb/0/0c/Crystal_Palace_FC_logo.svg/120px-Crystal_Palace_FC_logo.svg.png', 'gb', 'Premier League', '2025-01-25 20:04:00'),
(37, 'Fulham', 'https://upload.wikimedia.org/wikipedia/en/thumb/e/eb/Fulham_FC_%28shield%29.svg/120px-Fulham_FC_%28shield%29.svg.png', 'gb', 'Premier League', '2025-01-25 20:04:00'),
(38, 'Nottingham Forest', 'https://upload.wikimedia.org/wikipedia/en/thumb/e/e5/Nottingham_Forest_F.C._logo.svg/120px-Nottingham_Forest_F.C._logo.svg.png', 'gb', 'Premier League', '2025-01-25 20:04:00'),
(39, 'Wolves', 'https://upload.wikimedia.org/wikipedia/en/thumb/f/fc/Wolverhampton_Wanderers.svg/120px-Wolverhampton_Wanderers.svg.png', 'gb', 'Premier League', '2025-01-25 20:04:00'),
(40, 'Bournemouth', 'https://upload.wikimedia.org/wikipedia/en/thumb/e/e5/AFC_Bournemouth_%282013%29.svg/120px-AFC_Bournemouth_%282013%29.svg.png', 'gb', 'Premier League', '2025-01-25 20:04:00'),
(41, 'Luton Town', 'https://upload.wikimedia.org/wikipedia/en/thumb/8/8b/Luton_Town_F.C._logo.svg/120px-Luton_Town_F.C._logo.svg.png', 'gb', 'Premier League', '2025-01-25 20:04:00'),
(42, 'Sheffield United', 'https://upload.wikimedia.org/wikipedia/en/thumb/9/9c/Sheffield_United_FC_logo.svg/120px-Sheffield_United_FC_logo.svg.png', 'gb', 'Premier League', '2025-01-25 20:04:00'),
(43, 'Burnley', 'https://upload.wikimedia.org/wikipedia/en/thumb/6/62/Burnley_F.C._Logo.svg/120px-Burnley_F.C._Logo.svg.png', 'gb', 'Premier League', '2025-01-25 20:04:00'),
(44, 'Napoli', 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/28/S.S.C._Napoli_logo.svg/120px-S.S.C._Napoli_logo.svg.png', 'it', 'Serie A', '2025-01-25 20:04:00'),
(45, 'Lazio', 'https://upload.wikimedia.org/wikipedia/en/thumb/c/ce/S.S._Lazio_badge.svg/120px-S.S._Lazio_badge.svg.png', 'it', 'Serie A', '2025-01-25 20:04:00'),
(46, 'Roma', 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f7/AS_Roma_logo_%282017%29.svg/120px-AS_Roma_logo_%282017%29.svg.png', 'it', 'Serie A', '2025-01-25 20:04:00'),
(47, 'Atalanta', 'https://upload.wikimedia.org/wikipedia/en/thumb/6/66/AtalantaBC.svg/120px-AtalantaBC.svg.png', 'it', 'Serie A', '2025-01-25 20:04:00'),
(48, 'Fiorentina', 'https://upload.wikimedia.org/wikipedia/commons/thumb/7/79/ACF_Fiorentina.svg/120px-ACF_Fiorentina.svg.png', 'it', 'Serie A', '2025-01-25 20:04:00'),
(49, 'Bologna', 'https://upload.wikimedia.org/wikipedia/en/thumb/5/5b/Bologna_F.C._1909_logo.svg/120px-Bologna_F.C._1909_logo.svg.png', 'it', 'Serie A', '2025-01-25 20:04:00'),
(50, 'Torino', 'https://upload.wikimedia.org/wikipedia/en/thumb/2/2e/Torino_FC_Logo.svg/120px-Torino_FC_Logo.svg.png', 'it', 'Serie A', '2025-01-25 20:04:00'),
(51, 'Monza', 'https://upload.wikimedia.org/wikipedia/en/thumb/7/7e/AC_Monza_logo_%282019%29.svg/120px-AC_Monza_logo_%282019%29.svg.png', 'it', 'Serie A', '2025-01-25 20:04:00'),
(52, 'Udinese', 'https://upload.wikimedia.org/wikipedia/en/thumb/c/ce/Udinese_Calcio_logo.svg/120px-Udinese_Calcio_logo.svg.png', 'it', 'Serie A', '2025-01-25 20:04:00'),
(53, 'Sassuolo', 'https://upload.wikimedia.org/wikipedia/en/thumb/1/1c/US_Sassuolo_Calcio_logo.svg/120px-US_Sassuolo_Calcio_logo.svg.png', 'it', 'Serie A', '2025-01-25 20:04:00'),
(54, 'Empoli', 'https://upload.wikimedia.org/wikipedia/en/thumb/c/c4/Empoli_F.C._logo.svg/120px-Empoli_F.C._logo.svg.png', 'it', 'Serie A', '2025-01-25 20:04:00'),
(55, 'Frosinone', 'https://upload.wikimedia.org/wikipedia/en/thumb/9/9f/Frosinone_Calcio_logo.svg/120px-Frosinone_Calcio_logo.svg.png', 'it', 'Serie A', '2025-01-25 20:04:00'),
(56, 'Genoa', 'https://upload.wikimedia.org/wikipedia/en/thumb/4/46/Genoa_C.F.C._logo.svg/120px-Genoa_C.F.C._logo.svg.png', 'it', 'Serie A', '2025-01-25 20:04:00'),
(57, 'Verona', 'https://upload.wikimedia.org/wikipedia/en/thumb/9/92/Hellas_Verona_FC_logo_%282020%29.svg/120px-Hellas_Verona_FC_logo_%282020%29.svg.png', 'it', 'Serie A', '2025-01-25 20:04:00'),
(58, 'Cagliari', 'https://upload.wikimedia.org/wikipedia/en/thumb/6/61/Cagliari_Calcio_1920.svg/120px-Cagliari_Calcio_1920.svg.png', 'it', 'Serie A', '2025-01-25 20:04:00'),
(59, 'Salernitana', 'https://upload.wikimedia.org/wikipedia/commons/d/d9/Salernitana_logo_1948-49.jpg?20191216142116', 'it', 'Serie A', '2025-01-25 20:04:00'),
(60, 'Girona', 'https://upload.wikimedia.org/wikipedia/en/thumb/d/d3/Girona_FC_new_logo.svg/120px-Girona_FC_new_logo.svg.png', 'es', 'La Liga', '2025-01-25 20:04:00'),
(61, 'Athletic Bilbao', 'https://upload.wikimedia.org/wikipedia/en/thumb/9/98/Athletic_Club_Bilbao_logo.svg/120px-Athletic_Club_Bilbao_logo.svg.png', 'es', 'La Liga', '2025-01-25 20:04:00'),
(62, 'Real Sociedad', 'https://upload.wikimedia.org/wikipedia/en/thumb/f/f1/Real_Sociedad_logo.svg/120px-Real_Sociedad_logo.svg.png', 'es', 'La Liga', '2025-01-25 20:04:00'),
(63, 'Real Betis', 'https://upload.wikimedia.org/wikipedia/en/thumb/1/13/Real_betis_logo.svg/120px-Real_betis_logo.svg.png', 'es', 'La Liga', '2025-01-25 20:04:00'),
(64, 'Valencia', 'https://upload.wikimedia.org/wikipedia/en/thumb/c/ce/Valenciacf.svg/120px-Valenciacf.svg.png', 'es', 'La Liga', '2025-01-25 20:04:00'),
(65, 'Las Palmas', 'https://upload.wikimedia.org/wikipedia/en/thumb/5/cinquantacinque/UD_Las_Palmas_logo.svg/120px-UD_Las_Palmas_logo.svg.png', 'es', 'La Liga', '2025-01-25 20:04:00'),
(66, 'Rayo Vallecano', 'https://upload.wikimedia.org/wikipedia/en/thumb/1/17/Rayo_Vallecano_logo.png/120px-Rayo_Vallecano_logo.png', 'es', 'La Liga', '2025-01-25 20:04:00'),
(67, 'Osasuna', 'https://upload.wikimedia.org/wikipedia/en/thumb/c/c3/CA_Osasuna_logo.svg/120px-CA_Osasuna_logo.svg.png', 'es', 'La Liga', '2025-01-25 20:04:00'),
(68, 'Villarreal', 'https://upload.wikimedia.org/wikipedia/en/thumb/7/70/Villarreal_CF_logo.svg/120px-Villarreal_CF_logo.svg.png', 'es', 'La Liga', '2025-01-25 20:04:00'),
(69, 'Getafe', 'https://upload.wikimedia.org/wikipedia/en/thumb/4/46/Getafe_CF_logo.svg/120px-Getafe_CF_logo.svg.png', 'es', 'La Liga', '2025-01-25 20:04:00'),
(70, 'Mallorca', 'https://upload.wikimedia.org/wikipedia/en/thumb/e/e0/Rcd_mallorca.svg/120px-Rcd_mallorca.svg.png', 'es', 'La Liga', '2025-01-25 20:04:00'),
(71, 'Sevilla', 'https://upload.wikimedia.org/wikipedia/en/thumb/3/3b/Sevilla_FC_logo.svg/120px-Sevilla_FC_logo.svg.png', 'es', 'La Liga', '2025-01-25 20:04:00'),
(72, 'Alaves', 'https://upload.wikimedia.org/wikipedia/en/thumb/2/2e/Deportivo_Alav%C3%A9s_logo.svg/120px-Deportivo_Alav%C3%A9s_logo.svg.png', 'es', 'La Liga', '2025-01-25 20:04:00'),
(73, 'Celta Vigo', 'https://upload.wikimedia.org/wikipedia/en/thumb/1/12/RC_Celta_de_Vigo_logo.svg/120px-RC_Celta_de_Vigo_logo.svg.png', 'es', 'La Liga', '2025-01-25 20:04:00'),
(74, 'Granada', 'https://upload.wikimedia.org/wikipedia/en/thumb/5/5c/Granada_CF_logo.svg/120px-Granada_CF_logo.svg.png', 'es', 'La Liga', '2025-01-25 20:04:00'),
(75, 'Cadiz', 'https://upload.wikimedia.org/wikipedia/en/thumb/5/cinquantacinque/UD_Cadiz_logo.svg/120px-UD_Cadiz_logo.svg.png', 'es', 'La Liga', '2025-01-25 20:04:00'),
(76, 'Almeria', 'https://upload.wikimedia.org/wikipedia/en/thumb/2/23/UD_Almer%C3%ADa_logo.svg/120px-UD_Almer%C3%ADa_logo.svg.png', 'es', 'La Liga', '2025-01-25 20:04:00');

--
-- Dumping data for table `matches`
--

INSERT INTO `matches` (`id`, `home_team_id`, `away_team_id`, `match_time`, `stream_link`, `sport`, `created_at`) VALUES
(2, 6, 10, '2025-01-25 20:00:00', NULL, 'football', '2025-01-25 21:43:36');

-- --------------------------------------------------------

--
-- Table structure for table `stream_sources`
--

CREATE TABLE `stream_sources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `match_id` int(11) NOT NULL,
  `source_url` text NOT NULL,
  `language` varchar(50) NOT NULL DEFAULT 'Romanian',
  `source_type` enum('youtube','iframe','direct') NOT NULL DEFAULT 'iframe',
  `source_name` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `match_id` (`match_id`),
  CONSTRAINT `stream_sources_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `evenimente`
--
ALTER TABLE `evenimente`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `home_team_id` (`home_team_id`),
  ADD KEY `away_team_id` (`away_team_id`);

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
-- AUTO_INCREMENT for table `matches`
--
ALTER TABLE `matches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `matches`
--
ALTER TABLE `matches`
  ADD CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`home_team_id`) REFERENCES `teams` (`id`),
  ADD CONSTRAINT `matches_ibfk_2` FOREIGN KEY (`away_team_id`) REFERENCES `teams` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
