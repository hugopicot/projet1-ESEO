-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 13 oct. 2025 à 12:13
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
-- Base de données : `projet site1`
--

-- ----------------------------------------------------------

--
-- Structure de la table `ajouter`
--

DROP TABLE IF EXISTS `ajouter`;
CREATE TABLE IF NOT EXISTS `ajouter` (
  `id_portefeuille` int NOT NULL, # obligatoire pour identifier le portefeuille
  `id_asset` int NOT NULL, # chaque asset doit avoir un id
  `allocation_pct` decimal(5,2) DEFAULT NULL, # valeur nulle si aucune valeur n’est donnée
  PRIMARY KEY (`id_portefeuille`,`id_asset`), # empêche qu’un utilisateur soit ajouté deux fois
  KEY `id_asset` (`id_asset`) # index pour accélérer la recherche d’utilisateurs
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- Déchargement des données de la table `ajouter`
INSERT INTO `ajouter` (`id_portefeuille`, `id_asset`, `allocation_pct`) VALUES
(1, 1, 50.00),
(1, 2, 25.00),
(2, 4, 75.00),
(3, 3, 100.00);

-- Structure de la table `asset`
DROP TABLE IF EXISTS `asset`;
CREATE TABLE IF NOT EXISTS `asset` (
  `id_asset` int NOT NULL AUTO_INCREMENT, # l’identifiant est automatiquement généré à chaque nouvel enregistrement
  `nom_asset` varchar(100) NOT NULL, # le nom est obligatoire à ce niveau
  `type` varchar(50) DEFAULT NULL, # valeur nulle si aucune valeur n’est donnée
  `symbole` varchar(20) DEFAULT NULL, # valeur nulle si aucune valeur n’est donnée
  `devise` varchar(10) DEFAULT NULL, # valeur nulle si aucune valeur n’est donnée
  PRIMARY KEY (`id_asset`) # identifie chaque utilisateur de manière unique
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Déchargement des données de la table `asset`
INSERT INTO `asset` (`id_asset`, `nom_asset`, `type`, `symbole`, `devise`) VALUES
(1, 'Bitcoin', 'Crypto', 'BTC', 'USD'),
(2, 'Apple', 'Action', 'AAPL', 'USD'),
(3, 'Euro', 'Monnaie', 'EUR', 'EUR'),
(4, 'Tesla', 'Action', 'TSLA', 'USD');

-- Structure de la table `choisir`
DROP TABLE IF EXISTS `choisir`;
CREATE TABLE IF NOT EXISTS `choisir` (
  `id_compte` int NOT NULL, # l’identifiant d’un compte est obligatoire 
  `id_asset` int NOT NULL, # l’identifiant est obligatoire 
  `date_choix` date DEFAULT NULL, # la date est optionnelle si elle n’est pas renseignée
  PRIMARY KEY (`id_compte`,`id_asset`), # un même compte ne peut pas choisir deux fois le même utilisateur
  KEY `id_asset` (`id_asset`) # index pour faciliter le choix
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Déchargement des données de la table `choisir`
INSERT INTO `choisir` (`id_compte`, `id_asset`, `date_choix`) VALUES
(1, 1, '2025-01-10'),
(1, 2, '2025-01-11'),
(2, 3, '2025-02-01'),
(3, 4, '2025-03-01');

-- Structure de la table `compte`
DROP TABLE IF EXISTS `compte`;
CREATE TABLE IF NOT EXISTS `compte` (
  `id_compte` int NOT NULL AUTO_INCREMENT, # génère automatiquement un identifiant unique 
  `libelle_compte` varchar(100) DEFAULT NULL, # facultatif
  `exp` date DEFAULT NULL, # facultatif
  `plan` varchar(100) DEFAULT NULL, # facultatif
  PRIMARY KEY (`id_compte`) # garantit que chaque compte est unique
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Déchargement des données de la table `compte`
INSERT INTO `compte` (`id_compte`, `libelle_compte`, `exp`, `plan`) VALUES
(1, 'Compte Premium', '2026-12-31', 'Gold'),
(2, 'Compte Standard', '2025-08-30', 'Silver'),
(3, 'Compte Basique', '2025-03-15', 'Bronze');

-- Structure de la table `consentement`
DROP TABLE IF EXISTS `consentement`;
CREATE TABLE IF NOT EXISTS `consentement` (
  `id_consentement` int NOT NULL AUTO_INCREMENT, # génère un identifiant automatiquement
  `type_consentement` varchar(100) DEFAULT NULL, # peut être laissé vide 
  `granted_at` datetime DEFAULT NULL, # facultatif
  `revoked_at` datetime DEFAULT NULL, # facultatif
  PRIMARY KEY (`id_consentement`) # identifie chaque consentement de manière unique
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Déchargement des données de la table `consentement`
INSERT INTO `consentement` (`id_consentement`, `type_consentement`, `granted_at`, `revoked_at`) VALUES
(1, 'Newsletter', '2025-01-01 10:00:00', NULL),
(2, 'Partage Données', '2025-02-15 09:30:00', '2025-07-01 14:00:00'),
(3, 'Marketing', '2025-05-20 12:00:00', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `contact`
--

DROP TABLE IF EXISTS `contact`;
CREATE TABLE IF NOT EXISTS `contact` (
  `id_contact` int NOT NULL AUTO_INCREMENT, #identifiant obligatoire généré automatiquement à chaque nouvel enregistrement.
  `email_contact` varchar(255) NOT NULL, #email obligatoire pour enregistrer les contacts
  `nom_contact` varchar(255) NOT NULL, #obligatoire pour enregistrer les contacts
  PRIMARY KEY (`id_contact`) #identifie le contact de chaque utilisateur de manière unique

) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `contact`
--

INSERT INTO `contact` (`id_contact`, `email_contact`, `nom_contact`) VALUES
(1, 'alice.contact@mail.com', 'Alice Dupont'),
(2, 'bob.contact@mail.com', 'Bob Martin'),
(3, 'charlie.contact@mail.com', 'Charlie Leroy');

-- --------------------------------------------------------

--
-- Structure de la table `contenir`
--

DROP TABLE IF EXISTS `contenir`;
CREATE TABLE IF NOT EXISTS `contenir` (
  `id_portefeuille` int NOT NULL, #l’identifiant du portefeuille est obligatoire
  `id_compte` int NOT NULL, #l’identifiant du compte est obligatoire
  PRIMARY KEY (`id_portefeuille`,`id_compte`), #empêche qu’un même compte soit lié deux fois au même portefeuille
  KEY `id_compte` (`id_compte`) #clé secondaire pour faciliter les recherches par compte
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
  `id_contact` int NOT NULL, #l’identifiant du contact est obligatoire
  `email_user` varchar(100) NOT NULL, #l’adresse e-mail de l’utilisateur est obligatoire
  PRIMARY KEY (`id_contact`,`email_user`), #empêche qu’un même contact soit associé plusieurs fois au même utilisateur
  KEY `email_user` (`email_user`) #clé secondaire pour accélérer la recherche par adresse e-mail
)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `destiner`
--

INSERT INTO `destiner` (`id_contact`, `email_user`) VALUES
(1, 'alice@mail.com'),
(2, 'bob@mail.com'),
(3, 'carol@mail.com');

-- --------------------------------------------------------

--
-- Structure de la table `portefeuille`
--

DROP TABLE IF EXISTS `portefeuille`;
CREATE TABLE IF NOT EXISTS `portefeuille` (
  `id_portefeuille` int NOT NULL AUTO_INCREMENT, #génère automatiquement un identifiant unique pour chaque portefeuille
  `nom_portefeuille` varchar(100) DEFAULT NULL, #le nom du portefeuille est facultatif
  PRIMARY KEY (`id_portefeuille`) #garantit que chaque portefeuille est unique
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
-- Structure de la table `posseder`
--

DROP TABLE IF EXISTS `posseder`;
CREATE TABLE IF NOT EXISTS `posseder` (
  `email_user` varchar(100) NOT NULL, #l’adresse e-mail de l’utilisateur est obligatoire
  `id_compte` int NOT NULL, #l’identifiant du compte est obligatoire
  PRIMARY KEY (`email_user`,`id_compte`), #empêche qu’un même utilisateur possède deux fois le même compte
  UNIQUE KEY `id_compte` (`id_compte`) #garantit qu’un compte n’appartient qu’à un seul utilisateur
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `posseder`
--

INSERT INTO `posseder` (`email_user`, `id_compte`) VALUES
('alice@mail.com', 1),
('bob@mail.com', 2),
('carol@mail.com', 3);

-- --------------------------------------------------------

--
-- Structure de la table `rang`
--

DROP TABLE IF EXISTS `rang`;
CREATE TABLE IF NOT EXISTS `rang` (
  `id_role` int NOT NULL, #l’identifiant du rôle est obligatoire
  `id_compte` int NOT NULL, #l’identifiant du compte est obligatoire
  PRIMARY KEY (`id_role`,`id_compte`), #empêche qu’un même compte ait le même rôle plusieurs fois
  UNIQUE KEY `id_compte` (`id_compte`) #garantit qu’un compte ne peut être associé qu’à un seul rôle
)ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `rang`
--

INSERT INTO `rang` (`id_role`, `id_compte`) VALUES
(1, 1),
(2, 2),
(3, 3);

-- --------------------------------------------------------

--
-- Structure de la table `role`
--
DROP TABLE IF EXISTS `role`;
CREATE TABLE IF NOT EXISTS `role` (
  `id_role` int NOT NULL AUTO_INCREMENT, #génère automatiquement un identifiant unique pour chaque rôle
  `type` varchar(100) NOT NULL, #le type de rôle est obligatoire (ex: Admin, Utilisateur, etc.)
  PRIMARY KEY (`id_role`) #garantit que chaque rôle est unique
)ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `role`
--

INSERT INTO `role` (`id_role`, `type`) VALUES
(1, 'Admin'),
(2, 'Manager'),
(3, 'Utilisateur');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `email` varchar(100) NOT NULL, #l’adresse e-mail de l’utilisateur est obligatoire et unique
  `prenom` varchar(100) NOT NULL, #le prénom de l’utilisateur est obligatoire
  `nom` varchar(100) NOT NULL, #le nom de l’utilisateur est obligatoire
  `num_tel` varchar(20) DEFAULT NULL, #le numéro de téléphone est facultatif
  PRIMARY KEY (`email`) #garantit que chaque utilisateur est identifié de manière unique par son e-mail
)  ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`email`, `prenom`, `nom`, `num_tel`) VALUES
('alice@mail.com', 'Alice', 'Durand', '0601020304'),
('bob@mail.com', 'Bob', 'Leclerc', '0605060708'),
('carol@mail.com', 'Carol', 'Moreau', '0611223344');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

