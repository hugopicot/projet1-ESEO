-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 12 nov. 2025 à 22:19
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
-- Structure de la table `compte`
--

DROP TABLE IF EXISTS `compte`;
CREATE TABLE IF NOT EXISTS `compte` (
  `id_compte` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `source` enum('powens','manual','moralis') NOT NULL,
  `external_id` varchar(64) DEFAULT NULL,
  `powens_account_pk` int DEFAULT NULL,
  `categorie` enum('assurance_vie','actions_fonds','immobilier','bank','crypto','pret','autres') NOT NULL,
  `subtype` varchar(64) DEFAULT NULL,
  `libelle` varchar(255) DEFAULT NULL,
  `devise` char(3) NOT NULL DEFAULT 'EUR',
  `balance` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `balance_eur` decimal(20,4) NOT NULL DEFAULT '0.0000',
  `status` enum('active','archived') NOT NULL DEFAULT 'active',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_compte`),
  UNIQUE KEY `uniq_user_source_external` (`user_id`,`source`,`external_id`),
  KEY `idx_user_category` (`user_id`,`categorie`),
  KEY `idx_user_status` (`user_id`,`status`),
  KEY `fk_compte_powens_account` (`powens_account_pk`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `compte`
--

INSERT INTO `compte` (`id_compte`, `user_id`, `source`, `external_id`, `powens_account_pk`, `categorie`, `subtype`, `libelle`, `devise`, `balance`, `balance_eur`, `status`, `created_at`, `updated_at`) VALUES
(1, 15, 'manual', 'bitcoin', NULL, 'crypto', 'crypto', 'Bitcoin', 'EUR', 10806411.0000, 10806411.0000, 'active', '2025-11-11 15:50:31', '2025-11-12 22:55:51'),
(2, 15, 'manual', 'dogecoin', NULL, 'crypto', 'crypto', 'Dogecoin', 'EUR', 2170856.3227, 2170856.3227, '', '2025-11-11 15:54:43', '2025-11-11 18:36:54'),
(7, 15, 'manual', 'solana', NULL, 'crypto', 'crypto', 'Solana', 'EUR', 1492375.2800, 1492375.2800, 'active', '2025-11-11 18:21:29', '2025-11-12 22:55:51'),
(10, 15, 'manual', 'ethereum', NULL, 'crypto', 'crypto', 'Ethereum', 'EUR', 96197141.9500, 96197141.9500, '', '2025-11-11 18:32:16', '2025-11-11 18:34:37'),
(11, 15, 'manual', 'paypal-usd', NULL, 'crypto', 'crypto', 'PayPal USD', 'EUR', 271100.7006, 271100.7006, 'active', '2025-11-11 18:36:45', '2025-11-12 22:55:52'),
(12, 15, 'manual', 'official-trump', NULL, 'crypto', 'crypto', 'Official-trump', 'EUR', 29450358.8700, 29450358.8700, '', '2025-11-11 18:37:53', '2025-11-11 18:40:24'),
(14, 15, 'manual', 'hyperliquid', NULL, 'crypto', 'crypto', 'Hyperliquid', 'EUR', 331400.0000, 331400.0000, 'active', '2025-11-11 18:44:09', '2025-11-12 22:55:53'),
(22, 15, 'manual', 'binancecoin', NULL, 'crypto', 'crypto', 'BNB', 'EUR', 76067660.9200, 76067660.9200, '', '2025-11-12 07:58:22', '2025-11-12 09:29:11'),
(28, 15, 'manual', 'popcat', NULL, 'crypto', 'crypto', 'Popcat', 'EUR', 115672.0000, 115672.0000, 'active', '2025-11-12 10:23:11', '2025-11-12 22:58:54'),
(32, 16, 'manual', 'bitcoin', NULL, 'crypto', 'crypto', 'Bitcoin', 'EUR', 2108904.0000, 2108904.0000, 'active', '2025-11-12 17:11:21', '2025-11-12 23:02:57'),
(33, 21, 'manual', 'bitcoin', NULL, 'crypto', 'crypto', 'Bitcoin', 'EUR', 1053504.0000, 1053504.0000, 'active', '2025-11-12 21:33:42', '2025-11-12 21:33:42'),
(34, 22, 'manual', 'bitcoin', NULL, 'crypto', 'crypto', 'Bitcoin', 'EUR', 1142193.0000, 1142193.0000, 'active', '2025-11-12 22:01:12', '2025-11-12 22:18:48'),
(36, 22, 'manual', 'solana', NULL, 'crypto', 'crypto', 'Solana', 'EUR', 148092.0000, 148092.0000, 'active', '2025-11-12 22:19:05', '2025-11-12 22:19:05'),
(37, 22, 'manual', 'binancecoin', NULL, 'crypto', 'crypto', 'BNB', 'EUR', 19299794.2900, 19299794.2900, 'active', '2025-11-12 22:33:34', '2025-11-12 22:33:34');

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
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `consentement`
--

INSERT INTO `consentement` (`id_consentement`, `type_consentement`, `granted_at`, `revoked_at`) VALUES
(1, 'Newsletter', '2025-01-01 10:00:00', NULL),
(2, 'Partage Données', '2025-02-15 09:30:00', '2025-07-01 14:00:00'),
(3, 'Marketing', '2025-05-20 12:00:00', NULL),
(4, 'accepté', '2025-11-10 18:46:40', NULL),
(5, 'accepté', '2025-11-12 17:41:48', NULL),
(6, 'accepté', '2025-11-12 17:42:27', NULL);

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
-- Structure de la table `positions_manual`
--

DROP TABLE IF EXISTS `positions_manual`;
CREATE TABLE IF NOT EXISTS `positions_manual` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `categorie` enum('actions_fonds','crypto','immobilier','assurance_vie','autres') NOT NULL,
  `external_id` varchar(128) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `quantity` decimal(20,8) NOT NULL DEFAULT '0.00000000',
  `purchase_price` decimal(20,8) DEFAULT NULL,
  `currency` char(3) NOT NULL DEFAULT 'EUR',
  `data_source` varchar(32) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_cat_external` (`user_id`,`categorie`,`external_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `positions_manual`
--

INSERT INTO `positions_manual` (`id`, `user_id`, `categorie`, `external_id`, `name`, `quantity`, `purchase_price`, `currency`, `data_source`, `created_at`, `updated_at`) VALUES
(11, 15, 'crypto', 'paypal-usd', 'PayPal USD', 314312.00000000, 0.70000000, 'EUR', 'coingecko', '2025-11-11 18:36:45', '2025-11-12 10:09:36'),
(14, 15, 'crypto', 'hyperliquid', 'Hyperliquid', 10000.00000000, 10.00000000, 'EUR', 'coingecko', '2025-11-11 18:44:09', '2025-11-11 18:44:09'),
(28, 15, 'crypto', 'popcat', 'Popcat', 1000000.00000000, 0.00001000, 'EUR', 'coingecko', '2025-11-12 10:23:11', '2025-11-12 10:23:11'),
(29, 15, 'crypto', 'solana', 'Solana', 11231.00000000, 11.28414380, 'EUR', 'coingecko', '2025-11-12 10:23:40', '2025-11-12 15:53:50'),
(31, 15, 'crypto', 'bitcoin', 'Bitcoin', 123.00000000, 10.00000000, 'EUR', 'coingecko', '2025-11-12 15:54:51', '2025-11-12 15:54:51'),
(32, 16, 'crypto', 'bitcoin', 'Bitcoin', 24.00000000, 20000.00000000, 'EUR', 'manual', '2025-11-12 17:11:21', '2025-11-12 23:02:57'),
(33, 21, 'crypto', 'bitcoin', 'Bitcoin', 12.00000000, 2.00000000, 'EUR', 'coingecko', '2025-11-12 21:33:42', '2025-11-12 21:33:42'),
(34, 22, 'crypto', 'bitcoin', 'Bitcoin', 13.00000000, 12.44000000, 'EUR', 'coingecko', '2025-11-12 22:01:12', '2025-11-12 22:18:48'),
(36, 22, 'crypto', 'solana', 'Solana', 12341.00000000, 12.00000000, 'EUR', 'manual', '2025-11-12 22:19:05', '2025-11-12 22:19:05'),
(37, 22, 'crypto', 'binancecoin', 'BNB', 23411.00000000, 234.00000000, 'EUR', 'coingecko', '2025-11-12 22:33:34', '2025-11-12 22:33:34');

-- --------------------------------------------------------

--
-- Structure de la table `powens_accounts`
--

DROP TABLE IF EXISTS `powens_accounts`;
CREATE TABLE IF NOT EXISTS `powens_accounts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `connection_id` varchar(64) DEFAULT NULL,
  `account_id` varchar(64) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` varchar(64) DEFAULT NULL,
  `currency` varchar(12) DEFAULT NULL,
  `balance` decimal(18,2) DEFAULT NULL,
  `raw_json` longtext,
  `updated_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_account` (`user_id`,`account_id`),
  KEY `idx_user_conn_acc` (`user_id`,`connection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `powens_connections`
--

DROP TABLE IF EXISTS `powens_connections`;
CREATE TABLE IF NOT EXISTS `powens_connections` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `connection_id` varchar(64) DEFAULT NULL,
  `code` varchar(128) DEFAULT NULL,
  `state` text,
  `access_token` text,
  `refresh_token` text,
  `token_expires_at` datetime DEFAULT NULL,
  `latest_status` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_conn` (`user_id`,`connection_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `powens_connections`
--

INSERT INTO `powens_connections` (`id`, `user_id`, `connection_id`, `code`, `state`, `access_token`, `refresh_token`, `token_expires_at`, `latest_status`, `created_at`, `updated_at`) VALUES
(1, 15, '26', 'RGbbnPme6WYd/naXUU_3odUfzysutZF0qD/YMh6GxCMcsoQlZi2fNJCsZSz5q8N5UMsG9XZ0VcCQJzdJpciVAVy2L2bx4yhhlljYIMSUlL3U4L_YLEF5JhXgbUtfBIVx', 'eyJpZCI6InN0X21tenM4dGluOXMiLCJ0cyI6MTc2Mjg2NTgwNjIxOSwicmV0dXJuX3RvIjoiL2h0bWwvY2F0ZWdvcnlfYmFuay5waHAiLCJvcmlnaW4iOiJodHRwOi8vaHVnbzo4MDkwIn0=', NULL, NULL, NULL, 'success', '2025-11-11 12:57:22', '2025-11-11 12:57:22'),
(2, 15, '27', 'a5ntA5Tct3MRPg45SqHkQ_Hol3YFTb90RmmWZUB9TTxGK6X3BWTS0BWhEI7MiuwnrH9Zu8nhTWuCfSozQRZxUlugSdOTTPwxD80KBKsvQlxk0p6uhRmW5TzBJP9/mOIW', 'eyJpZCI6InN0XzJ2cXV4NmVmMWhwIiwidHMiOjE3NjI4NjU4NDg0NTIsInJldHVybl90byI6Ii9odG1sL2NhdGVnb3J5X2FjdGlvbnMucGhwIiwib3JpZ2luIjoiaHR0cDovL2h1Z286ODA5MCJ9', NULL, NULL, NULL, 'success', '2025-11-11 12:57:51', '2025-11-11 12:57:51'),
(3, 15, '28', 'fXpqdfzqvi5bbmuJx_sgFx1hzoENjpd5SBs6jADYg3Ni5X79llF06Boj5v9mH_6H8dX1kc7TTGAtlvJcQXJBcJuQ8hsPPLZSvd_Z8WRgVTqJ/foNl3xXpm6wtWxnzDXr', 'eyJpZCI6InN0XzRzOWhpajI4MTNqIiwidHMiOjE3NjI4NjYzMzQzMTIsInJldHVybl90byI6Ii9odG1sL2NhdGVnb3J5X2FjdGlvbnMucGhwIiwib3JpZ2luIjoiaHR0cDovL2h1Z286ODA5MCJ9', NULL, NULL, NULL, 'success', '2025-11-11 13:05:57', '2025-11-11 13:05:57'),
(4, 15, '29', 'iIGsdd5robH6S_Evuljuigt6X6/H5r9l63HwAgrMM/AV323Lfgg1R_KXTRSlXrIXB3MpmGVNds4_OfPgAX37qbL9_4M4UP2c6v49KdK_efcf8HlKASBms3hqvsRbkRYi', 'eyJpZCI6InN0X21nb2NoOTQ5MW8iLCJ0cyI6MTc2Mjg2NjQ3NzAwOSwicmV0dXJuX3RvIjoiL2h0bWwvY2F0ZWdvcnlfYWN0aW9ucy5waHAiLCJvcmlnaW4iOiJodHRwOi8vaHVnbzo4MDkwIn0=', NULL, NULL, NULL, 'success', '2025-11-11 13:08:39', '2025-11-11 13:08:39'),
(5, 15, '30', 'WZfWzNwYSvGdSJ1nWYw1bXtL/kyBqE018OP5yjRXpK9GLXRc/YmDt_BI8eIPJTJNofthApEMZxsSXbGD7EEZ89FTIzpnJyEEVPz9A8w/Ghl6Sn6AKNerPq4UcdbF1vCv', 'eyJpZCI6InN0X2ZiazU2dThzbWJtIiwidHMiOjE3NjI4NjY4NzQyOTMsInJldHVybl90byI6Ii9odG1sL2NhdGVnb3J5X2FjdGlvbnMucGhwIiwib3JpZ2luIjoiaHR0cDovL2h1Z286ODA5MCJ9', NULL, NULL, NULL, 'success', '2025-11-11 13:14:59', '2025-11-11 13:14:59'),
(6, 15, '31', 'Xk7qV2lH0sWEVJtZ46JNW60KSCKchmAFVAsVzqnQQqQ7nQa9MPwNJgvAKMhwtMvD8L53UzzjDUoIpSoTeFUrI57Pn4OJPX4rM17vplUbv830EY2MDHOOQqrKxNNvazl4', 'eyJpZCI6InN0X2Z5cjBibWR0ZXp3IiwidHMiOjE3NjI4NjgzMDU2OTcsInJldHVybl90byI6Ii9odG1sL2NhdGVnb3J5X2FjdGlvbnMucGhwIiwib3JpZ2luIjoiaHR0cDovL2h1Z286ODA5MCJ9', NULL, NULL, NULL, 'success', '2025-11-11 13:38:47', '2025-11-11 13:38:47'),
(7, 15, '32', 'YssbGd/8Xnw4OSuk8MXWfqmWF5a0MdQsvLnaMZLvE18UPamKSpVI9mTHlm83JLkPJW5vuQFYP8Fxa3Jd1HY7Y4yeXL8FLvQ1zSYuKoeT9xmQekeLotHY_wvDC5VfPHbG', 'eyJpZCI6InN0X2Z0bXc1bG1rOHhzIiwidHMiOjE3NjI4NjkwMDMwMDYsInJldHVybl90byI6Ii9odG1sL2NhdGVnb3J5X2FjdGlvbnMucGhwIiwib3JpZ2luIjoiaHR0cDovL2h1Z286ODA5MCJ9', NULL, NULL, NULL, 'success', '2025-11-11 13:50:20', '2025-11-11 13:50:20'),
(8, 15, '33', '0O8H16haDrLl_sITUNew2dXn/_kD6fnLzDXAVL5bebZqRwOyf3aqZo7UJ1prVRNwuLNrpkZr0yH9jSnFOp5dh1rJZTEx/JzucH32cztlQ/g1gAdlA6YWwRg5RorVeNB2', 'eyJpZCI6InN0XzkwcmdueHI1MnliIiwidHMiOjE3NjI4NjkzMTQ1MTYsInJldHVybl90byI6Ii9odG1sL2NhdGVnb3J5X2FjdGlvbnMucGhwIiwib3JpZ2luIjoiaHR0cDovL2h1Z286ODA5MCJ9', NULL, NULL, NULL, 'success', '2025-11-11 13:55:35', '2025-11-11 13:55:35'),
(9, 15, '34', 'KMvt/6VIhzz/9pLZRqAZijuBSE2LmzOUbUcZBwp_/olWVkdSl7uWCIXa7RVV_lpYwa8sb69fpKiJ5Bv1E0oDMXTJvC2h_N3rrHMuNOIp3q5VdsfZfVK/iIhfTsFpPDZl', 'eyJpZCI6InN0X3o2Mjh0YjY2NXQiLCJ0cyI6MTc2Mjg2OTY0NTM1OSwicmV0dXJuX3RvIjoiL2h0bWwvY2F0ZWdvcnlfYWN0aW9ucy5waHAiLCJvcmlnaW4iOiJodHRwOi8vaHVnbzo4MDkwIn0=', NULL, NULL, NULL, 'success', '2025-11-11 14:01:03', '2025-11-11 14:01:03'),
(10, 15, '35', '45zaujJCdHU4txkDvihqfro0f19ehyauVH0sl60LSpSNMAX8INo99ifycNuOlARldbB19W50MQivuHLioWdntNf8jRB0DzLzMubqi_z74y24QpnKpmX42UKhKjhzowrS', 'eyJpZCI6InN0Xzg2Y21hbG1jN2VjIiwidHMiOjE3NjI4Njk3OTgyNjcsInJldHVybl90byI6Ii9odG1sL2NhdGVnb3J5X2FjdGlvbnMucGhwIiwib3JpZ2luIjoiaHR0cDovL2h1Z286ODA5MCJ9', NULL, NULL, NULL, 'success', '2025-11-11 14:03:35', '2025-11-11 14:03:35');

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
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email_user`, `password`, `country`, `role`, `created_at`, `subscription_plan`, `subscription_start`, `subscription_end`) VALUES
(3, 'Sidraxx', 'siloe.boisson@reseau.eseo.fr', '$2y$10$.MQWjJv1x4Ib99fZHZ9If.bzczmrBOL6T08Vc7KHz3ZwddR5FxJLG', 'Suisse', 'Utilisateur', '2025-10-15 13:59:31', 'none', NULL, NULL),
(4, 'Hugo Picot', 'hugopicotpro@gmail.com', '$2y$10$6wNgp9S5QJ1XIxSfdemJyuAphyCn/AGI46PfTmfE.mtikZJNo.EoG', 'France', 'Admin', '2025-11-03 13:10:24', 'starter', '2025-11-03', '2025-12-03'),
(5, 'oijfr', 'hugopicot44@gmail.com', '$2y$10$eIJA6dKC3x4iPlnQB8L2WuOW15IkL3JeDSXTlu9RRN4EM4DfWv5Z6', 'France', 'Utilisateur', '2025-11-03 15:51:25', 'starter', '2025-11-03', '2025-12-03'),
(6, 'hugopicotpro@gmail.com', 'barreauyael4@gmail.com', '$2y$10$83V.F6vH6eK.t7Uu0cv0aO2xGnnMsBFag8qj3fA2hG93AEhcwbMa2', 'France', 'Utilisateur', '2025-11-03 15:55:28', 'none', NULL, NULL),
(7, 'hugopicotpro@gmail.com', 'test@test.com', '$2y$10$RNsHknuRYHeUeUumcJn90utBt6S0cWzpeMgTkqZ92KKbjBfYMBb6.', 'France', 'Utilisateur', '2025-11-03 16:00:19', 'starter', '2025-11-03', '2025-12-03'),
(8, 'ap', 'ap@gmail.com', '$2y$10$uS/MGQ74a/QwHPHBhw8L1e3D4VNbOMz26v02hqvo7oNYy4Zo9eQlq', 'France', 'Utilisateur', '2025-11-03 16:25:29', 'pro', '2025-11-03', '2025-12-03'),
(9, 'hugopicotpro@gmail.com', 'dfsghzer@gmail.com', '$2y$10$EClW/ZmdAXWF2OrYC/Xco.W5p77yVkBHNyQ/VqzHW4fpzcx5/a4i6', 'France', 'Utilisateur', '2025-11-04 07:10:13', 'pro', '2025-11-04', '2025-12-04'),
(10, 'efz fzsa', 'aze@gmail.com', '$2y$10$c7Gp.W0.NT8BIQtR5tJrdeTsnxe54xy52nlmkxrys66rQCP2rGYWK', 'France', 'Utilisateur', '2025-11-04 07:13:06', 'starter', '2025-11-04', '2025-12-04'),
(11, 'ojnd', 'pa@gmail.com', '$2y$10$4bDk6.TGO.WBBkatFXCn6uccv68tLv9AJ9WtLR2QRh8Hxy6ZOi59C', 'France', 'Utilisateur', '2025-11-04 10:14:33', 'starter', '2025-11-04', '2025-12-04'),
(12, 'hugo', 'hugo@gmail.com', '$2y$10$Q1TZlAb.0MRbhAoAeAQSP.0G2T7m4scfQbWr5.dhQU0iyX6xWZ0am', 'France', 'Utilisateur', '2025-11-05 10:03:54', 'pro', '2025-11-05', '2025-12-05'),
(13, 'hugo', 'gijuef@gmail.com', '$2y$10$hJHGWrFTvgHNK.uEa8trTeKRERhZkCVFToxvuvLLzehLdFub5hhB2', 'France', 'Utilisateur', '2025-11-05 10:43:58', 'starter', '2025-11-05', '2025-12-05'),
(14, 'hugo', 'pic@gmail.com', '$2y$10$kOjL2eC4L6PDoFJ9xAohFucYoAXSsEW1XiMlApmY/tBvr2onw0qSK', 'France', 'Utilisateur', '2025-11-10 18:16:30', 'starter', '2025-11-10', '2025-12-10'),
(15, 'aez', 'qasz@gmail.com', '$2y$10$zXmVduTPKgPUK5LTZKYxBeAwb3MBoS9cdWey7xvFS4MSW0ny2OzBe', 'France', 'Utilisateur', '2025-11-11 12:33:03', 'pro', '2025-11-11', '2025-12-11'),
(16, 'hugo', 'qaszd@gmail.com', '$2y$10$kNHSsgws7/o3J8ROnk/caevV5TvrSzarj0a4R4M9lxIUNSKN1QDre', 'France', 'Utilisateur', '2025-11-12 16:02:10', 'pro', '2025-11-12', '2025-12-12'),
(17, 'Hugo Picot', 'Hugop@gmail.com', '$2y$10$6AJRSOYCBZj8Cly0SNzHOe7hnVJN/7ap0gWN4UTuGC9.TlCdrhTla', 'France', 'Utilisateur', '2025-11-12 16:42:57', 'pro', '2025-11-12', '2025-12-12'),
(18, 'hugo', 'Hugopi@gmail.com', '$2y$10$PuuRmIiKT2/Alk1lcjLjrOZ6dKzH22oBETd8iZJ.PRO7ZV1CPeOt2', 'France', 'Utilisateur', '2025-11-12 16:46:31', 'starter', '2025-11-12', '2025-12-12'),
(19, 'zrtez', 'azer@gmail.com', '$2y$10$rb84DK7C5aRrQL8zl1SxNev39ctWAZqzw.brjC0Xx.APYOzovcdXu', 'France', 'Utilisateur', '2025-11-12 17:56:19', 'pro', '2025-11-12', '2025-12-12'),
(20, 'dzd', 'qsdfq@gmail.com', '$2y$10$Z55VYm6qCDq4RobnQEQ1J..73Atwe0vM4BqS.TRda2TRRZuOhOLva', 'France', 'Utilisateur', '2025-11-12 19:19:44', 'pro', '2025-11-12', '2025-12-12'),
(21, 'a', 'hugopicz@gmail.com', '$2y$10$PXm1xBS83zP5jAIUSU5MJ.DSMzOxBHV1otiCVZ0N8VHJUgKhTli46', 'France', 'Utilisateur', '2025-11-12 20:28:54', 'pro', '2025-11-12', '2025-12-12'),
(22, 'Hugo Picot', 'workbalancet@gmail.com', '$2y$10$a5XQIhzCi2kF9zErPTvLMuLFObc8iXcG5CrCCPRdlgcd3GZNre32O', 'France', 'Utilisateur', '2025-11-12 20:31:41', 'pro', '2025-11-12', '2025-12-12');

-- --------------------------------------------------------

--
-- Structure de la table `valuations_history`
--

DROP TABLE IF EXISTS `valuations_history`;
CREATE TABLE IF NOT EXISTS `valuations_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `external_id` varchar(128) NOT NULL,
  `categorie` enum('actions_fonds','crypto','immobilier','assurance_vie','autres') NOT NULL,
  `value_eur` decimal(20,8) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_asset_time` (`user_id`,`external_id`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `valuations_history`
--

INSERT INTO `valuations_history` (`id`, `user_id`, `external_id`, `categorie`, `value_eur`, `created_at`) VALUES
(1, 15, 'bitcoin', 'crypto', 2667180.00000000, '2025-11-11 21:54:38'),
(2, 15, 'solana', 'crypto', 1693006.20000000, '2025-11-11 21:54:38'),
(3, 15, 'paypal-usd', 'crypto', 184970.75840000, '2025-11-11 21:54:38'),
(4, 15, 'hyperliquid', 'crypto', 339600.00000000, '2025-11-11 21:54:38'),
(5, 15, 'bitcoin', 'crypto', 2678880.00000000, '2025-11-12 07:52:35'),
(6, 15, 'solana', 'crypto', 1678322.28000000, '2025-11-12 07:52:35'),
(7, 15, 'paypal-usd', 'crypto', 184995.83289600, '2025-11-12 07:52:36'),
(8, 15, 'hyperliquid', 'crypto', 341800.00000000, '2025-11-12 07:52:36'),
(9, 15, 'binancecoin', 'crypto', 65400.00000000, '2025-11-12 07:58:22'),
(10, 15, 'bitcoin', 'crypto', 7521696.00000000, '2025-11-12 09:28:59'),
(11, 15, 'solana', 'crypto', 5893646.10000000, '2025-11-12 09:28:59'),
(12, 15, 'paypal-usd', 'crypto', 184989.61784800, '2025-11-12 09:28:59'),
(13, 15, 'hyperliquid', 'crypto', 343500.00000000, '2025-11-12 09:29:00'),
(14, 15, 'binancecoin', 'crypto', 76067660.92000000, '2025-11-12 09:29:00'),
(15, 15, 'popcat', 'crypto', 145974.00000000, '2025-11-12 10:23:11'),
(16, 15, 'solana', 'crypto', 100000.00000000, '2025-11-12 10:38:10'),
(17, 15, 'paypal-usd', 'crypto', 271396.78250000, '2025-11-12 10:38:10'),
(18, 15, 'hyperliquid', 'crypto', 344000.00000000, '2025-11-12 10:38:11'),
(19, 15, 'solana', 'crypto', 1380500.00000000, '2025-11-12 14:33:48'),
(20, 15, 'paypal-usd', 'crypto', 271633.14508000, '2025-11-12 14:33:49'),
(21, 15, 'hyperliquid', 'crypto', 345600.00000000, '2025-11-12 14:33:50'),
(22, 15, 'popcat', 'crypto', 149651.00000000, '2025-11-12 14:33:50'),
(23, 15, 'solana', 'crypto', 1381000.00000000, '2025-11-12 15:48:07'),
(24, 15, 'paypal-usd', 'crypto', 271580.02635200, '2025-11-12 15:48:07'),
(25, 15, 'hyperliquid', 'crypto', 341900.00000000, '2025-11-12 15:48:08'),
(26, 15, 'popcat', 'crypto', 165594.00000000, '2025-11-12 15:48:08'),
(27, 15, 'bitcoin', 'crypto', 11050320.00000000, '2025-11-12 15:54:51'),
(28, 16, 'bitcoin', 'crypto', 1054956.00000000, '2025-11-12 17:11:21'),
(29, 15, 'bitcoin', 'crypto', 10793127.00000000, '2025-11-12 17:31:09'),
(30, 15, 'solana', 'crypto', 1480358.11000000, '2025-11-12 17:31:10'),
(31, 15, 'paypal-usd', 'crypto', 271164.50588800, '2025-11-12 17:31:10'),
(32, 15, 'hyperliquid', 'crypto', 326300.00000000, '2025-11-12 17:31:11'),
(33, 15, 'popcat', 'crypto', 115615.00000000, '2025-11-12 17:31:12'),
(34, 15, 'bitcoin', 'crypto', 10790175.00000000, '2025-11-12 19:37:29'),
(35, 15, 'solana', 'crypto', 1488219.81000000, '2025-11-12 19:37:29'),
(36, 15, 'paypal-usd', 'crypto', 271050.09632000, '2025-11-12 19:37:30'),
(37, 15, 'hyperliquid', 'crypto', 333900.00000000, '2025-11-12 19:37:30'),
(38, 15, 'popcat', 'crypto', 115615.00000000, '2025-11-12 19:37:33'),
(39, 15, 'bitcoin', 'crypto', 10778244.00000000, '2025-11-12 21:27:41'),
(40, 15, 'solana', 'crypto', 1489904.46000000, '2025-11-12 21:27:42'),
(41, 15, 'paypal-usd', 'crypto', 271106.67248000, '2025-11-12 21:27:42'),
(42, 15, 'hyperliquid', 'crypto', 333100.00000000, '2025-11-12 21:27:43'),
(43, 15, 'popcat', 'crypto', 121678.00000000, '2025-11-12 21:27:43'),
(44, 21, 'bitcoin', 'crypto', 1053504.00000000, '2025-11-12 21:33:42'),
(45, 22, 'bitcoin', 'crypto', 87590.00000000, '2025-11-12 22:01:12'),
(46, 22, 'solana', 'crypto', 148092.00000000, '2025-11-12 22:19:05'),
(47, 22, 'binancecoin', 'crypto', 19299794.29000000, '2025-11-12 22:33:34'),
(48, 15, 'bitcoin', 'crypto', 10806411.00000000, '2025-11-12 22:55:51'),
(49, 15, 'solana', 'crypto', 1492375.28000000, '2025-11-12 22:55:51'),
(50, 15, 'paypal-usd', 'crypto', 271100.70055200, '2025-11-12 22:55:52'),
(51, 15, 'hyperliquid', 'crypto', 331400.00000000, '2025-11-12 22:55:53'),
(52, 15, 'popcat', 'crypto', 121678.00000000, '2025-11-12 22:55:53'),
(53, 16, 'bitcoin', 'crypto', 1054452.00000000, '2025-11-12 22:59:27');

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `v_user_category_distribution`
-- (Voir ci-dessous la vue réelle)
--
DROP VIEW IF EXISTS `v_user_category_distribution`;
CREATE TABLE IF NOT EXISTS `v_user_category_distribution` (
`categorie` enum('assurance_vie','actions_fonds','immobilier','bank','crypto','pret','autres')
,`percent` decimal(48,2)
,`total_eur` decimal(42,4)
,`user_id` int
);

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `v_user_category_totals`
-- (Voir ci-dessous la vue réelle)
--
DROP VIEW IF EXISTS `v_user_category_totals`;
CREATE TABLE IF NOT EXISTS `v_user_category_totals` (
`categorie` enum('assurance_vie','actions_fonds','immobilier','bank','crypto','pret','autres')
,`total_eur` decimal(42,4)
,`user_id` int
);

-- --------------------------------------------------------

--
-- Structure de la vue `v_user_category_distribution`
--
DROP TABLE IF EXISTS `v_user_category_distribution`;

DROP VIEW IF EXISTS `v_user_category_distribution`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_user_category_distribution`  AS SELECT `t`.`user_id` AS `user_id`, `t`.`categorie` AS `categorie`, `t`.`total_eur` AS `total_eur`, (case when (`gt`.`grand_total` > 0) then round(((`t`.`total_eur` / `gt`.`grand_total`) * 100),2) else 0 end) AS `percent` FROM (`v_user_category_totals` `t` join (select `v_user_category_totals`.`user_id` AS `user_id`,sum(`v_user_category_totals`.`total_eur`) AS `grand_total` from `v_user_category_totals` group by `v_user_category_totals`.`user_id`) `gt` on((`gt`.`user_id` = `t`.`user_id`))) ;

-- --------------------------------------------------------

--
-- Structure de la vue `v_user_category_totals`
--
DROP TABLE IF EXISTS `v_user_category_totals`;

DROP VIEW IF EXISTS `v_user_category_totals`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_user_category_totals`  AS SELECT `compte`.`user_id` AS `user_id`, `compte`.`categorie` AS `categorie`, sum(`compte`.`balance_eur`) AS `total_eur` FROM `compte` WHERE (`compte`.`status` = 'active') GROUP BY `compte`.`user_id`, `compte`.`categorie` ;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `compte`
--
ALTER TABLE `compte`
  ADD CONSTRAINT `fk_compte_powens_account` FOREIGN KEY (`powens_account_pk`) REFERENCES `powens_accounts` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
