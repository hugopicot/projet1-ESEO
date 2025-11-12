-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 11 nov. 2025 à 12:04
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `projet_site1`
--

-- --------------------------------------------------------

--
-- Structure de la table `ajouter`
--

DROP TABLE IF EXISTS `ajouter`;
CREATE TABLE IF NOT EXISTS `ajouter` (
  `id_portefeuille` int NOT NULL,
  `id_asset` int NOT NULL,
  `allocation_pct` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id_portefeuille`,`id_asset`),
  KEY `id_asset` (`id_asset`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `ajouter`
--

INSERT INTO `ajouter` (`id_portefeuille`, `id_asset`, `allocation_pct`) VALUES
(1, 1, 50.00),
(1, 2, 25.00),
(2, 4, 75.00),
(3, 3, 100.00);

-- --------------------------------------------------------

--
-- Structure de la table `asset`
--

DROP TABLE IF EXISTS `asset`;
CREATE TABLE IF NOT EXISTS `asset` (
  `id_asset` int NOT NULL AUTO_INCREMENT,
  `nom_asset` varchar(100) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `symbole` varchar(20) DEFAULT NULL,
  `devise` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id_asset`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `asset`
--

INSERT INTO `asset` (`id_asset`, `nom_asset`, `type`, `symbole`, `devise`) VALUES
(1, 'Bitcoin', 'Crypto', 'BTC', 'USD'),
(2, 'Apple', 'Action', 'AAPL', 'USD'),
(3, 'Euro', 'Monnaie', 'EUR', 'EUR'),
(4, 'Tesla', 'Action', 'TSLA', 'USD');

-- --------------------------------------------------------

--
-- Structure de la table `choisir`
--

DROP TABLE IF EXISTS `choisir`;
CREATE TABLE IF NOT EXISTS `choisir` (
  `id_compte` int NOT NULL,
  `id_asset` int NOT NULL,
  `date_choix` date DEFAULT NULL,
  PRIMARY KEY (`id_compte`,`id_asset`),
  KEY `id_asset` (`id_asset`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `choisir`
--

INSERT INTO `choisir` (`id_compte`, `id_asset`, `date_choix`) VALUES
(1, 1, '2025-01-10'),
(1, 2, '2025-01-11'),
(2, 3, '2025-02-01'),
(3, 4, '2025-03-01');

-- --------------------------------------------------------

--
-- Structure de la table `consentement`
--

DROP TABLE IF EXISTS `consentement`;
CREATE TABLE IF NOT EXISTS `consentement` (
  `id_consentement` int NOT NULL AUTO_INCREMENT,
  `type_consentement` varchar(100) DEFAULT NULL,
  `granted_at` datetime DEFAULT NULL,
  `revoked_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_consentement`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `consentement`
--

INSERT INTO `consentement` (`id_consentement`, `type_consentement`, `granted_at`, `revoked_at`) VALUES
(1, 'Newsletter', '2025-01-01 10:00:00', NULL),
(2, 'Partage Données', '2025-02-15 09:30:00', '2025-07-01 14:00:00'),
(3, 'Marketing', '2025-05-20 12:00:00', NULL),
(4, 'accepté', '2025-11-10 18:46:40', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `contenir`
--

DROP TABLE IF EXISTS `contenir`;
CREATE TABLE IF NOT EXISTS `contenir` (
  `id_portefeuille` int NOT NULL,
  `id_compte` int NOT NULL,
  PRIMARY KEY (`id_portefeuille`,`id_compte`),
  KEY `id_compte` (`id_compte`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `contenir`
--

INSERT INTO `contenir` (`id_portefeuille`, `id_compte`) VALUES
(1, 1),
(2, 2),
(3, 3);

-- --------------------------------------------------------

--
-- Structure de la table `destiner`
--

DROP TABLE IF EXISTS `destiner`;
CREATE TABLE IF NOT EXISTS `destiner` (
  `id_contact` int NOT NULL,
  `email_user` varchar(100) NOT NULL,
  PRIMARY KEY (`id_contact`,`email_user`),
  KEY `email_user` (`email_user`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `destiner`
--

INSERT INTO `destiner` (`id_contact`, `email_user`) VALUES
(1, 'alice@mail.com'),
(2, 'bob@mail.com'),
(3, 'carol@mail.com');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id_contact` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `date_envoi` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_contact`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`id_contact`, `nom`, `email`, `message`, `date_envoi`) VALUES
(1, 'cacaboudin', 'jean@exemple.com', 'slayyyyyyy', '2025-11-03 09:49:18'),
(2, 'cacaboudin', 'jean@exemple.com', 'hitl\r\nerer', '2025-11-03 09:50:54'),
(3, 'cacaboudin', 'siloe.boisson@reseau.eseo.fr', 'w<cw', '2025-11-03 09:55:16'),
(4, 'cacaboudin', 'siloe.boisson@gmx.fr', 'gugezzuifgzi', '2025-11-03 09:56:19');

-- --------------------------------------------------------

--
-- Structure de la table `portefeuille`
--

DROP TABLE IF EXISTS `portefeuille`;
CREATE TABLE IF NOT EXISTS `portefeuille` (
  `id_portefeuille` int NOT NULL AUTO_INCREMENT,
  `nom_portefeuille` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id_portefeuille`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `portefeuille`
--

INSERT INTO `portefeuille` (`id_portefeuille`, `nom_portefeuille`) VALUES
(1, 'Portefeuille Principal'),
(2, 'Portefeuille Crypto'),
(3, 'Portefeuille Épargne');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `email_user` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(100) NOT NULL,
  `country` varchar(100) DEFAULT 'France',
  `role` enum('Admin','Utilisateur') DEFAULT 'Utilisateur',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `subscription_plan` varchar(50) DEFAULT 'none',
  `subscription_start` date DEFAULT NULL,
  `subscription_end` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email_user`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email_user`, `password`, `country`, `role`, `created_at`, `subscription_plan`, `subscription_start`, `subscription_end`) VALUES
(3, 'Sidraxx', 'siloe.boisson@reseau.eseo.fr', '$2y$10$.MQWjJv1x4Ib99fZHZ9If.bzczmrBOL6T08Vc7KHz3ZwddR5FxJLG', 'Suisse', 'Utilisateur', '2025-10-15 13:59:31', 'none', NULL, NULL),
(4, 'Hugo Picot', 'hugopicotpro@gmail.com', '$2y$10$6wNgp9S5QJ1XIxSfdemJyuAphyCn/AGI46PfTmfE.mtikZJNo.EoG', 'France', 'Utilisateur', '2025-11-03 13:10:24', 'starter', '2025-11-03', '2025-12-03'),
(5, 'oijfr', 'hugopicot44@gmail.com', '$2y$10$eIJA6dKC3x4iPlnQB8L2WuOW15IkL3JeDSXTlu9RRN4EM4DfWv5Z6', 'France', 'Utilisateur', '2025-11-03 15:51:25', 'starter', '2025-11-03', '2025-12-03'),
(6, 'hugopicotpro@gmail.com', 'barreauyael4@gmail.com', '$2y$10$83V.F6vH6eK.t7Uu0cv0aO2xGnnMsBFag8qj3fA2hG93AEhcwbMa2', 'France', 'Utilisateur', '2025-11-03 15:55:28', 'none', NULL, NULL),
(7, 'hugopicotpro@gmail.com', 'test@test.com', '$2y$10$RNsHknuRYHeUeUumcJn90utBt6S0cWzpeMgTkqZ92KKbjBfYMBb6.', 'France', 'Utilisateur', '2025-11-03 16:00:19', 'starter', '2025-11-03', '2025-12-03'),
(8, 'ap', 'ap@gmail.com', '$2y$10$uS/MGQ74a/QwHPHBhw8L1e3D4VNbOMz26v02hqvo7oNYy4Zo9eQlq', 'France', 'Utilisateur', '2025-11-03 16:25:29', 'pro', '2025-11-03', '2025-12-03'),
(9, 'hugopicotpro@gmail.com', 'dfsghzer@gmail.com', '$2y$10$EClW/ZmdAXWF2OrYC/Xco.W5p77yVkBHNyQ/VqzHW4fpzcx5/a4i6', 'France', 'Utilisateur', '2025-11-04 07:10:13', 'pro', '2025-11-04', '2025-12-04'),
(10, 'efz fzsa', 'aze@gmail.com', '$2y$10$c7Gp.W0.NT8BIQtR5tJrdeTsnxe54xy52nlmkxrys66rQCP2rGYWK', 'France', 'Utilisateur', '2025-11-04 07:13:06', 'starter', '2025-11-04', '2025-12-04'),
(11, 'ojnd', 'pa@gmail.com', '$2y$10$4bDk6.TGO.WBBkatFXCn6uccv68tLv9AJ9WtLR2QRh8Hxy6ZOi59C', 'France', 'Utilisateur', '2025-11-04 10:14:33', 'starter', '2025-11-04', '2025-12-04'),
(12, 'hugo', 'hugo@gmail.com', '$2y$10$Q1TZlAb.0MRbhAoAeAQSP.0G2T7m4scfQbWr5.dhQU0iyX6xWZ0am', 'France', 'Utilisateur', '2025-11-05 10:03:54', 'pro', '2025-11-05', '2025-12-05'),
(13, 'hugo', 'gijuef@gmail.com', '$2y$10$hJHGWrFTvgHNK.uEa8trTeKRERhZkCVFToxvuvLLzehLdFub5hhB2', 'France', 'Utilisateur', '2025-11-05 10:43:58', 'starter', '2025-11-05', '2025-12-05'),
(14, 'hugo', 'pic@gmail.com', '$2y$10$kOjL2eC4L6PDoFJ9xAohFucYoAXSsEW1XiMlApmY/tBvr2onw0qSK', 'France', 'Utilisateur', '2025-11-10 18:16:30', 'starter', '2025-11-10', '2025-12-10');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
