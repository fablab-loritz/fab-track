-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 11 juin 2025 à 08:57
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
  `stock` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `material_type_id` (`material_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `materials`
--

INSERT INTO `materials` (`id`, `name`, `material_type_id`, `unit`, `stock`) VALUES
(30, 'Polystyrène extrudé', 3, 'mm', 100),
(31, 'PLA', 1, 'g', 100),
(32, 'ABS', 1, 'g', 100),
(33, 'PETG', 1, 'g', 100),
(34, 'MDF', 2, 'mm', 100),
(35, 'Plexy', 2, 'mm', 100),
(36, 'Carton', 3, 'mm', 100),
(37, 'Mousse', 3, 'mm', 100),
(38, 'Alu', 4, 'mm', 100),
(39, 'Papier A4', 5, 'Papier', 100),
(54, 'Papier A4 noir et blanc\r\n', 5, 'Papier', 100),
(55, 'Papier A3\r\n', 5, 'Papier', 100),
(56, 'Papier A3 noir et blanc\r\n', 5, 'Papier', 100),
(57, 'Papier A2 \r\n', 5, 'Papier', 100),
(58, 'Papier A2 noir et blanc\r\n', 5, 'Papier', 100),
(59, 'Papier A1', 5, 'Papier', 100),
(60, 'Papier A1 noir et blanc', 5, 'Papier', 100);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `materials`
--
ALTER TABLE `materials`
  ADD CONSTRAINT `materials_ibfk_1` FOREIGN KEY (`material_type_id`) REFERENCES `material_types` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
