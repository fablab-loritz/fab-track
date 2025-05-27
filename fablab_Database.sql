-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : ven. 23 mai 2025 à 11:19
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
-- Base de données : `gestion`
--
CREATE DATABASE IF NOT EXISTS `gestion` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `gestion`;

-- --------------------------------------------------------

--
-- Structure de la table `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `enregistrements`
--

DROP TABLE IF EXISTS `enregistrements`;
CREATE TABLE IF NOT EXISTS `enregistrements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `machine_id` int NOT NULL,
  `modele_id` int NOT NULL,
  `material_id` int NOT NULL,
  `date_enregistrement` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `quantite` int NOT NULL,
  `epaisseur` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `enregistrements`
--

INSERT INTO `enregistrements` (`id`, `machine_id`, `modele_id`, `material_id`, `date_enregistrement`, `quantite`, `epaisseur`) VALUES
(46, 1, 1, 34, '2025-05-22 21:56:25', 1, 4);

-- --------------------------------------------------------

--
-- Structure de la table `machines`
--

DROP TABLE IF EXISTS `machines`;
CREATE TABLE IF NOT EXISTS `machines` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `category_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `machines`
--

INSERT INTO `machines` (`id`, `name`, `category`, `category_id`) VALUES
(1, 'Imprimante 3D', 'Imprimante 3D', NULL),
(2, 'Laser', 'Laser', NULL),
(3, 'P&S', 'P&S', NULL),
(4, 'CNC', 'CNC', NULL),
(5, 'Imprimante Papier', 'Imprimante Papier', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `materials`
--

DROP TABLE IF EXISTS `materials`;
CREATE TABLE IF NOT EXISTS `materials` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `material_type_id` int NOT NULL,
  `unit` varchar(20) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `material_type_id` (`material_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `materials`
--

INSERT INTO `materials` (`id`, `name`, `material_type_id`, `unit`) VALUES
(31, 'PLA', 1, 'g'),
(32, 'ABS', 1, 'g'),
(33, 'PETG', 1, 'g'),
(34, 'MDF', 2, 'mm'),
(35, 'Plexy', 2, 'mm'),
(36, 'Carton', 3, 'mm'),
(37, 'Mousse', 3, 'mm'),
(38, 'Alu', 4, 'mm'),
(39, 'Papier', 5, 'A4'),
(44, 'Polystyrène extrudé', 3, 'mm');

-- --------------------------------------------------------

--
-- Structure de la table `material_types`
--

DROP TABLE IF EXISTS `material_types`;
CREATE TABLE IF NOT EXISTS `material_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `material_types`
--

INSERT INTO `material_types` (`id`, `name`) VALUES
(4, 'CNC'),
(1, 'Imprimante 3D'),
(5, 'Imprimante Papier'),
(2, 'Laser'),
(3, 'P&S');

-- --------------------------------------------------------

--
-- Structure de la table `modele`
--

DROP TABLE IF EXISTS `modele`;
CREATE TABLE IF NOT EXISTS `modele` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `machine_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `machine_id` (`machine_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `modele`
--

INSERT INTO `modele` (`id`, `name`, `machine_id`) VALUES
(1, 'CR 10', 1),
(2, 'E3', 1),
(3, 'Raise', 1),
(4, 'Grande', 4),
(5, 'petite', 4),
(6, 'Kyocera', 3),
(7, 'Epson', 3),
(8, 'Tracer', 3);

-- --------------------------------------------------------

--
-- Structure de la table `professors`
--

DROP TABLE IF EXISTS `professors`;
CREATE TABLE IF NOT EXISTS `professors` (
  `id` int NOT NULL AUTO_INCREMENT,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `usernames` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `responsibles`
--

DROP TABLE IF EXISTS `responsibles`;
CREATE TABLE IF NOT EXISTS `responsibles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `usages`
--

DROP TABLE IF EXISTS `usages`;
CREATE TABLE IF NOT EXISTS `usages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `datetime` datetime NOT NULL,
  `class_id` int NOT NULL,
  `project` varchar(255) DEFAULT NULL,
  `professor_id` int DEFAULT NULL,
  `responsible_id` int DEFAULT NULL,
  `material_id` int NOT NULL,
  `machine_id` int NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `notes` text,
  PRIMARY KEY (`id`),
  KEY `class_id` (`class_id`),
  KEY `professor_id` (`professor_id`),
  KEY `responsible_id` (`responsible_id`),
  KEY `material_id` (`material_id`),
  KEY `machine_id` (`machine_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `variantes`
--

DROP TABLE IF EXISTS `variantes`;
CREATE TABLE IF NOT EXISTS `variantes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_materiaux` int NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `variantes`
--

INSERT INTO `variantes` (`id`, `id_materiaux`, `description`) VALUES
(1, 34, '3 mm'),
(2, 34, '6 mm'),
(3, 34, '8 mm'),
(4, 39, 'A4 C'),
(5, 39, 'A4 N&B'),
(6, 39, 'A3');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `materials`
--
ALTER TABLE `materials`
  ADD CONSTRAINT `materials_ibfk_1` FOREIGN KEY (`material_type_id`) REFERENCES `material_types` (`id`);

--
-- Contraintes pour la table `modele`
--
ALTER TABLE `modele`
  ADD CONSTRAINT `modele_ibfk_1` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`);

--
-- Contraintes pour la table `usages`
--
ALTER TABLE `usages`
  ADD CONSTRAINT `usages_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `usages_ibfk_2` FOREIGN KEY (`professor_id`) REFERENCES `professors` (`id`),
  ADD CONSTRAINT `usages_ibfk_3` FOREIGN KEY (`responsible_id`) REFERENCES `responsibles` (`id`),
  ADD CONSTRAINT `usages_ibfk_4` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`),
  ADD CONSTRAINT `usages_ibfk_5` FOREIGN KEY (`machine_id`) REFERENCES `machines` (`id`);
--
-- Base de données : `pereira`
--
CREATE DATABASE IF NOT EXISTS `pereira` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `pereira`;

-- --------------------------------------------------------

--
-- Structure de la table `abonnes`
--

DROP TABLE IF EXISTS `abonnes`;
CREATE TABLE IF NOT EXISTS `abonnes` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOM` varchar(25) NOT NULL,
  `PRENOM` varchar(20) NOT NULL,
  `DATENAIS` date NOT NULL,
  `VILLE` varchar(20) NOT NULL,
  `PSEUDO` varchar(10) NOT NULL,
  `MDP` varchar(10) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `abonnes`
--

INSERT INTO `abonnes` (`ID`, `NOM`, `PRENOM`, `DATENAIS`, `VILLE`, `PSEUDO`, `MDP`) VALUES
(3, 'GOUTER', 'HEUREs', '2025-01-01', 'NANCY', 'FAIM', '123456'),
(4, 'AUDENT', 'JAMAL', '2025-01-01', 'NANCY', 'AIE', 'AIE'),
(5, 'BIENDORMI', 'ADELE', '2025-01-01', 'NANCY', 'DODO', 'DODO'),
(6, 'CORBAC', '1000TR', '2025-01-01', 'NANCY', 'PIAF', 'OISEAU'),
(7, 'aze', 'aze', '2025-02-12', 'aze', 'aze', 'aze');

-- --------------------------------------------------------

--
-- Structure de la table `abonnes2`
--

DROP TABLE IF EXISTS `abonnes2`;
CREATE TABLE IF NOT EXISTS `abonnes2` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `NOM` varchar(25) NOT NULL,
  `PRENOM` varchar(20) NOT NULL,
  `DATENAIS` date NOT NULL,
  `VILLE` varchar(20) NOT NULL,
  `PSEUDO` varchar(10) NOT NULL,
  `MDP` varchar(128) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=MyISAM AUTO_INCREMENT=22 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `abonnes2`
--

INSERT INTO `abonnes2` (`ID`, `NOM`, `PRENOM`, `DATENAIS`, `VILLE`, `PSEUDO`, `MDP`) VALUES
(1, 'GOUTER', 'HEURE', '2025-01-01', 'NANCY', 'FAIM', '123456'),
(2, 'AUDENT', 'JAMAL', '2025-01-01', 'NANCY', 'AIE', 'AIE'),
(3, 'BIENDORMI', 'ADELE', '2025-01-01', 'NANCY', 'DODO', 'DODO'),
(4, 'CORBAC', '1000TR', '2025-01-01', 'NANCY', 'PIAF', 'OISEAU'),
(5, 'aze', 'aze', '2025-02-12', 'aze', 'aze', 'aze'),
(6, 'az', 'az', '2025-03-03', 'az', 'az', '$2y$10$h0nsnjIKJPVEuHb96MZZE.kpBekgvIkA7Y4cQVR2UAhx0pax.IPGS'),
(7, 'test', 'test', '9999-01-01', 'test', 'test', '$2y$10$Rq.rXGtGFLtV/A4F5DdY5ui3X.E0DWu.pZrdl9TkY7FUFCVfLqITK');

-- --------------------------------------------------------

--
-- Structure de la table `comptes`
--

DROP TABLE IF EXISTS `comptes`;
CREATE TABLE IF NOT EXISTS `comptes` (
  `IDC` int NOT NULL,
  `IDAbo` int NOT NULL,
  `DROITS` int NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
