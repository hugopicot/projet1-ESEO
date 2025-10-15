-- Nova sample dataset for local testing
-- Generated for project1-ESEO

SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET time_zone = '+00:00';

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

DROP TABLE IF EXISTS `user_consent`;
DROP TABLE IF EXISTS `destiner`;
DROP TABLE IF EXISTS `choisir`;
DROP TABLE IF EXISTS `ajouter`;
DROP TABLE IF EXISTS `contenir`;
DROP TABLE IF EXISTS `posseder`;
DROP TABLE IF EXISTS `rang`;
DROP TABLE IF EXISTS `waitlist`;
DROP TABLE IF EXISTS `contact`;
DROP TABLE IF EXISTS `consentement`;
DROP TABLE IF EXISTS `asset`;
DROP TABLE IF EXISTS `portefeuille`;
DROP TABLE IF EXISTS `compte`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `role`;

-- Table: role
CREATE TABLE `role` (
  `id_role` int NOT NULL AUTO_INCREMENT,
  `type` varchar(100) NOT NULL,
  PRIMARY KEY (`id_role`),
  UNIQUE KEY `uk_role_type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `role` (`id_role`, `type`) VALUES
(1, 'Admin'),
(2, 'Client');

-- Table: users
CREATE TABLE `users` (
  `email` varchar(100) UNIQUE NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `date_de_naissance` date DEFAULT NULL,
  CONSTRAINT chk_majorite CHECK (date_de_naissance <= CURDATE() - INTERVAL 18 YEAR),
  `num_tel` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `users` (`email`, `prenom`, `nom`, `date_de_naissance`, `num_tel`, `created_at`) VALUES
('alice@mail.com', 'Alice', 'Durand', '2000-05-15', '0601020304', '2024-11-02 09:30:00'),
('bob@mail.com', 'Bob', 'Leclerc', '1995-08-22', '0605060708', '2024-11-15 13:45:00'),
('carol@mail.com', 'Carol', 'Moreau', '1998-11-22', '0611223344', '2024-12-01 10:15:00'),
('david@mail.com', 'David', 'Lambert', '1992-04-10', '0712345678', '2025-01-05 16:20:00'),
('emma@mail.com', 'Emma', 'Bernard', '1999-07-18', '0687654321', '2025-01-18 11:05:00'),
('frank@mail.com', 'Frank', 'Rousseau', '1994-12-05', '0654321876', '2025-02-02 08:50:00');

-- Table: compte
CREATE TABLE `compte` (
  `id_compte` int UNIQUE NOT NULL AUTO_INCREMENT,
  `libelle_compte` varchar(100) NOT NULL,
  `exp` date DEFAULT NULL,
  `plan` varchar(100) DEFAULT NULL,
  `statut` varchar(50) DEFAULT 'active',
  PRIMARY KEY (`id_compte`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `compte` (`id_compte`, `libelle_compte`, `exp`, `plan`, `statut`) VALUES
(1, 'Compte Nova Pro - Alice', '2026-12-31', 'Pro', 'active'),
(2, 'Compte Nova Pro - Bob', '2025-09-30', 'Pro', 'active'),
(3, 'Compte Nova Starter - Carol', '2025-04-15', 'Starter', 'trial'),
(4, 'Compte Nova Pro - David', '2027-06-30', 'Pro', 'active'),
(5, 'Compte Nova Pro - Emma', '2025-12-01', 'Pro', 'paused'),
(6, 'Compte Nova Starter - Frank', '2025-05-01', 'Starter', 'active');

-- Table: portefeuille
CREATE TABLE `portefeuille` (
  `id_portefeuille` int NOT NULL AUTO_INCREMENT,
  `nom_portefeuille` varchar(100) NOT NULL,
  `objectif` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_portefeuille`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `portefeuille` (`id_portefeuille`, `nom_portefeuille`, `objectif`) VALUES
(1, 'Core Portfolio', 'Balanced exposure to global equities and bonds'),
(2, 'Growth Portfolio', 'Focus on high growth technology assets'),
(3, 'Income Portfolio', 'Stable dividend and fixed income instruments'),
(4, 'Sustainable Portfolio', 'ESG compliant holdings'),
(5, 'Retirement Portfolio', 'Long term capital preservation');

-- Table: asset
CREATE TABLE `asset` (
  `id_asset` int NOT NULL AUTO_INCREMENT,
  `nom_asset` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `symbole` varchar(20) DEFAULT NULL,
  `devise` varchar(10) DEFAULT 'EUR',
  PRIMARY KEY (`id_asset`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `asset` (`id_asset`, `nom_asset`, `type`, `symbole`, `devise`) VALUES
(1, 'Bitcoin', 'Crypto', 'BTC', 'USD'),
(2, 'Ethereum', 'Crypto', 'ETH', 'USD'),
(3, 'Apple', 'Equity', 'AAPL', 'USD'),
(4, 'Tesla', 'Equity', 'TSLA', 'USD'),
(5, 'BNP Paribas', 'Equity', 'BNP', 'EUR'),
(6, 'Amundi MSCI World', 'ETF', 'CW8', 'EUR'),
(7, 'Euro', 'Currency', 'EUR', 'EUR'),
(8, 'US Treasury 10Y', 'Bond', 'UST10', 'USD');

-- Table: consentement
CREATE TABLE `consentement` (
  `id_consentement` int NOT NULL AUTO_INCREMENT,
  `type_consentement` varchar(100) NOT NULL,
  `granted_at` datetime NOT NULL,
  `revoked_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_consentement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `consentement` (`id_consentement`, `type_consentement`, `granted_at`, `revoked_at`) VALUES
(1, 'Product Updates', '2025-01-01 09:30:00', NULL),
(2, 'Marketing Emails', '2025-02-15 11:00:00', '2025-06-01 10:00:00'),
(3, 'Beta Program', '2025-03-05 08:00:00', NULL),
(4, 'Data Analysis', '2025-04-10 15:30:00', NULL),
(5, 'Partner Offers', '2025-05-25 14:45:00', '2025-08-02 09:20:00'),
(6, 'Waitlist Updates', '2025-06-18 13:00:00', NULL);

-- Table: contact
CREATE TABLE `contact` (
  `id_contact` int NOT NULL AUTO_INCREMENT,
  `email_contact` varchar(255) NOT NULL,
  `nom_contact` varchar(255) NOT NULL,
  `subject` varchar(150) DEFAULT NULL,
  `message` text,
  `preferred_channel` varchar(50) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_contact`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `contact` (`id_contact`, `email_contact`, `nom_contact`, `subject`, `message`, `preferred_channel`, `created_at`) VALUES
(1, 'alice.contact@mail.com', 'Alice Dupont', 'Product demo request', 'Would like a personalized demo for the finance team.', 'email', '2025-01-08 10:30:00'),
(2, 'bob.contact@mail.com', 'Bob Martin', 'Billing question', 'Please clarify the pricing difference between Pro and Enterprise tiers.', 'phone', '2025-01-11 15:20:00'),
(3, 'charlie.contact@mail.com', 'Charlie Leroy', 'Integration support', 'Need help connecting Nova to our ERP system.', 'email', '2025-01-20 09:10:00'),
(4, 'support@nova.app', 'Nova Support', 'General inquiry backlog', 'Internal note: follow up with waitlist leads every Monday.', 'internal', '2025-01-25 08:45:00'),
(5, 'sales@nova.app', 'Nova Sales', 'Q2 events coordination', 'Track conversions coming from the upcoming webinars.', 'internal', '2025-02-02 14:05:00');

-- Table: waitlist
CREATE TABLE `waitlist` (
  `id_waitlist` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `source` varchar(50) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `joined_at` datetime NOT NULL,
  `notes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_waitlist`),
  UNIQUE KEY `uk_waitlist_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `waitlist` (`id_waitlist`, `email`, `source`, `status`, `joined_at`, `notes`) VALUES
(1, 'eden@example.com', 'landing', 'confirmed', '2025-01-05 10:15:00', 'Invited to beta cohort A'),
(2, 'noah@example.com', 'landing', 'pending', '2025-01-06 09:40:00', 'Awaiting manual review'),
(3, 'sophia@example.com', 'referral', 'confirmed', '2025-01-07 18:25:00', 'Referral code NOVA2025'),
(4, 'liam@example.com', 'event', 'contacted', '2025-01-09 13:05:00', 'Met at Paris Fintech Forum'),
(5, 'mia@example.com', 'landing', 'removed', '2025-01-10 11:55:00', 'User asked to be removed'),
(6, 'lucas@example.com', 'campaign', 'pending', '2025-01-12 08:10:00', 'Waiting for consent confirmation');

-- Table: rang (role to account)
CREATE TABLE `rang` (
  `id_role` int NOT NULL,
  `id_compte` int NOT NULL,
  PRIMARY KEY (`id_role`, `id_compte`),
  UNIQUE KEY `uk_rang_compte` (`id_compte`),
  CONSTRAINT `fk_rang_role` FOREIGN KEY (`id_role`) REFERENCES `role` (`id_role`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_rang_compte` FOREIGN KEY (`id_compte`) REFERENCES `compte` (`id_compte`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `rang` (`id_role`, `id_compte`) VALUES
(1, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 6);

-- Table: posseder (user to account)
CREATE TABLE `posseder` (
  `email_user` varchar(100) NOT NULL,
  `id_compte` int NOT NULL,
  PRIMARY KEY (`email_user`, `id_compte`),
  UNIQUE KEY `uk_posseder_compte` (`id_compte`),
  CONSTRAINT `fk_posseder_user` FOREIGN KEY (`email_user`) REFERENCES `users` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_posseder_compte` FOREIGN KEY (`id_compte`) REFERENCES `compte` (`id_compte`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `posseder` (`email_user`, `id_compte`) VALUES
('alice@mail.com', 1),
('bob@mail.com', 2),
('carol@mail.com', 3),
('david@mail.com', 4),
('emma@mail.com', 5),
('frank@mail.com', 6);

-- Table: contenir (portfolio to account)
CREATE TABLE `contenir` (
  `id_portefeuille` int NOT NULL,
  `id_compte` int NOT NULL,
  PRIMARY KEY (`id_portefeuille`, `id_compte`),
  CONSTRAINT `fk_contenir_portefeuille` FOREIGN KEY (`id_portefeuille`) REFERENCES `portefeuille` (`id_portefeuille`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_contenir_compte` FOREIGN KEY (`id_compte`) REFERENCES `compte` (`id_compte`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `contenir` (`id_portefeuille`, `id_compte`) VALUES
(1, 1),
(1, 2),
(2, 3),
(3, 4),
(4, 5),
(5, 6);

-- Table: ajouter (portfolio to asset allocation)
CREATE TABLE `ajouter` (
  `id_portefeuille` int NOT NULL,
  `id_asset` int NOT NULL,
  `allocation_pct` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id_portefeuille`, `id_asset`),
  CONSTRAINT `fk_ajouter_portefeuille` FOREIGN KEY (`id_portefeuille`) REFERENCES `portefeuille` (`id_portefeuille`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ajouter_asset` FOREIGN KEY (`id_asset`) REFERENCES `asset` (`id_asset`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `ajouter` (`id_portefeuille`, `id_asset`, `allocation_pct`) VALUES
(1, 1, 40.00),
(1, 3, 35.00),
(1, 8, 25.00),
(2, 2, 45.00),
(2, 4, 30.00),
(2, 6, 25.00),
(3, 5, 50.00),
(3, 7, 30.00),
(3, 8, 20.00),
(4, 2, 30.00),
(4, 6, 40.00),
(4, 7, 30.00),
(5, 1, 20.00),
(5, 3, 40.00),
(5, 5, 20.00),
(5, 8, 20.00);

-- Table: choisir (account selected assets)
CREATE TABLE `choisir` (
  `id_compte` int NOT NULL,
  `id_asset` int NOT NULL,
  `date_choix` date DEFAULT NULL,
  PRIMARY KEY (`id_compte`, `id_asset`),
  CONSTRAINT `fk_choisir_compte` FOREIGN KEY (`id_compte`) REFERENCES `compte` (`id_compte`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_choisir_asset` FOREIGN KEY (`id_asset`) REFERENCES `asset` (`id_asset`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `choisir` (`id_compte`, `id_asset`, `date_choix`) VALUES
(1, 1, '2025-01-10'),
(1, 3, '2025-01-10'),
(2, 2, '2025-01-12'),
(2, 6, '2025-01-12'),
(3, 5, '2025-01-15'),
(3, 7, '2025-01-15'),
(4, 2, '2025-01-22'),
(4, 8, '2025-01-22'),
(5, 3, '2025-01-25'),
(5, 7, '2025-01-25'),
(6, 6, '2025-01-28'),
(6, 1, '2025-01-28');

-- Table: destiner (contact targeting users)
CREATE TABLE `destiner` (
  `id_contact` int NOT NULL,
  `email_user` varchar(100) NOT NULL,
  PRIMARY KEY (`id_contact`, `email_user`),
  CONSTRAINT `fk_destiner_contact` FOREIGN KEY (`id_contact`) REFERENCES `contact` (`id_contact`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_destiner_user` FOREIGN KEY (`email_user`) REFERENCES `users` (`email`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `destiner` (`id_contact`, `email_user`) VALUES
(1, 'alice@mail.com'),
(1, 'emma@mail.com'),
(2, 'bob@mail.com'),
(3, 'david@mail.com'),
(4, 'carol@mail.com'),
(5, 'frank@mail.com');

-- Table: user_consent (user to consent tracking)
CREATE TABLE `user_consent` (
  `email_user` varchar(100) NOT NULL,
  `id_consentement` int NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'granted',
  `status_at` datetime NOT NULL,
  PRIMARY KEY (`email_user`, `id_consentement`),
  CONSTRAINT `fk_user_consent_user` FOREIGN KEY (`email_user`) REFERENCES `users` (`email`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_user_consent_consent` FOREIGN KEY (`id_consentement`) REFERENCES `consentement` (`id_consentement`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `user_consent` (`email_user`, `id_consentement`, `status`, `status_at`) VALUES
('alice@mail.com', 1, 'granted', '2025-01-01 09:30:00'),
('alice@mail.com', 3, 'granted', '2025-03-07 08:45:00'),
('bob@mail.com', 2, 'revoked', '2025-06-01 10:00:00'),
('carol@mail.com', 1, 'granted', '2025-02-01 12:10:00'),
('carol@mail.com', 3, 'granted', '2025-03-12 14:05:00'),
('david@mail.com', 4, 'granted', '2025-04-12 09:50:00'),
('emma@mail.com', 5, 'revoked', '2025-08-02 09:20:00'),
('emma@mail.com', 1, 'granted', '2025-05-02 11:00:00'),
('frank@mail.com', 6, 'granted', '2025-06-20 13:25:00');

COMMIT;
SET FOREIGN_KEY_CHECKS = 1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



