-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mer. 11 juin 2025 à 09:35
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
-- Structure de la table `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=107 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `classes`
--

INSERT INTO `classes` (`id`, `name`) VALUES
(18, '1ere 601'),
(19, '1ere 602'),
(20, '1ere 603'),
(21, '1ere 604'),
(22, '1ere 605'),
(23, '1ere 606'),
(24, '1ere 607'),
(25, '1ere 611'),
(26, '1ere 612'),
(27, '1ere 613'),
(28, '1ere 614'),
(29, '1ere 641'),
(1, '2nd 500'),
(2, '2nd 500 CNED'),
(3, '2nd 501'),
(4, '2nd 502'),
(5, '2nd 503'),
(6, '2nd 504'),
(7, '2nd 505'),
(8, '2nd 506'),
(9, '2nd 507'),
(10, '2nd 508'),
(11, '2nd 509'),
(12, '2nd 510'),
(13, '2nd 511'),
(16, '2nd 514'),
(17, '6eme 600 CNED'),
(48, '8001MADE'),
(49, '801 CPI CPRP'),
(50, '802 CRSA'),
(51, '804 ELEC'),
(52, '804A'),
(53, '805 FONDERIE'),
(54, '805A'),
(55, '806 GA'),
(56, '806A'),
(57, '807 MGTMN'),
(58, '807A'),
(59, '809 CIELer1'),
(60, '810 CIELir1'),
(61, '811A BTS1A'),
(62, '812A BTS1'),
(63, '819ABTS1'),
(64, '851 CPGE1'),
(65, '852 CPGE1'),
(66, '853 CPGE1'),
(67, '900 DNMADE'),
(68, '9002MADA'),
(69, '9002MADE'),
(70, '900A DNM'),
(71, '901 CPI CPRP'),
(72, '902 CRSA'),
(73, '904 ELEC'),
(74, '904A'),
(75, '905 FONDERIE'),
(76, '905A'),
(77, '906 GA'),
(78, '906A'),
(79, '907 CFA MONTIGNY'),
(80, '907 MGTMN'),
(81, '907A'),
(82, '909 CIEL ER'),
(83, '909ER A'),
(84, '910 CIEL IR'),
(85, '910IR A'),
(86, '911A'),
(87, '912A'),
(88, '919A'),
(89, '951'),
(90, '952'),
(91, '953'),
(92, '990 A'),
(93, '990 DNM3'),
(94, '990 DNMAD3'),
(95, '9903MADA'),
(96, '995'),
(97, 'CPGEPCSI'),
(47, 'DNMADE 800'),
(98, 'EXTERIEUR'),
(99, 'FMS2'),
(100, 'FMS3'),
(101, 'Ing EEIGM'),
(102, 'LPRO BIO'),
(103, 'LPROCND'),
(104, 'LPROFOND'),
(105, 'TCND GRETA'),
(106, 'TCND GRETA 2'),
(32, 'Terminale 701'),
(33, 'Terminale 702'),
(34, 'Terminale 703'),
(35, 'Terminale 704'),
(36, 'Terminale 705'),
(37, 'Terminale 706'),
(38, 'Terminale 707'),
(39, 'Terminale 708'),
(40, 'Terminale 711'),
(41, 'Terminale 712'),
(42, 'Terminale 713'),
(43, 'Terminale 714'),
(44, 'Terminale 715'),
(45, 'Terminale 741'),
(46, 'Terminale 750');

-- --------------------------------------------------------

--
-- Structure de la table `enregistrements`
--

DROP TABLE IF EXISTS `enregistrements`;
CREATE TABLE IF NOT EXISTS `enregistrements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `machine_id` int NOT NULL,
  `modele_id` int DEFAULT NULL,
  `material_id` int NOT NULL,
  `date_enregistrement` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `quantite` int NOT NULL,
  `epaisseur` int DEFAULT NULL,
  `professor_id` int DEFAULT NULL,
  `class_id` int DEFAULT NULL,
  `responsible_id` int DEFAULT NULL,
  `variantes` varchar(255) DEFAULT NULL,
  `variantes_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_professor` (`professor_id`),
  KEY `fk_class` (`class_id`),
  KEY `fk_responsible` (`responsible_id`)
) ENGINE=MyISAM AUTO_INCREMENT=406 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `enregistrements`
--

INSERT INTO `enregistrements` (`id`, `machine_id`, `modele_id`, `material_id`, `date_enregistrement`, `quantite`, `epaisseur`, `professor_id`, `class_id`, `responsible_id`, `variantes`, `variantes_id`) VALUES
(405, 1, 1, 31, '2025-06-11 08:31:00', 1, NULL, 121, NULL, 6, NULL, NULL),
(404, 1, 1, 31, '2025-06-11 08:26:37', 1, NULL, 117, NULL, 6, NULL, NULL);

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
(3, 'Pack&Strat\r\n', 'P&S', NULL),
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
  `stock` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `material_type_id` (`material_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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
(60, 'Papier A1 noir et blanc', 5, 'Papier', 100),
(61, 'chaussons aux pommes ', 1, 'pommes', 100);

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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=505 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `professors`
--

INSERT INTO `professors` (`id`, `last_name`, `first_name`) VALUES
(54, 'BASSER', 'Miloud'),
(55, 'BYRNE', 'Samantha'),
(56, 'CHOBAUD', 'Jean-Paul'),
(57, 'CITERNE', 'Nicolas'),
(58, 'DAIZE', 'Joel'),
(59, 'HERMANN', 'Alexandra'),
(60, 'HERMOUET', 'Florence'),
(61, 'JOUHANT', 'Richard'),
(62, 'KOHLER', 'SEVERINE'),
(63, 'LAMBERT', 'Damien'),
(64, 'LEBOUBE', 'SEVERINE'),
(65, 'MARQUIS', 'NATHALIE'),
(66, 'MARZOUG', 'ZAHRA'),
(67, 'MCLAUGHLIN', 'Jonathan'),
(68, 'MICHEL', 'Jean-Phillipe'),
(69, 'MOYON', 'Charles-Edouard'),
(70, 'NIEL', 'HERVE'),
(71, 'SAN FELICE', 'Luigi'),
(72, 'SASSI', 'Sandrine'),
(73, 'SEJOURNE', 'Jean-Marc'),
(74, 'TARANTOLA', 'Bernard'),
(75, 'THOMAS', 'Aude'),
(76, 'THOMAS', 'Johan'),
(77, 'VALENCE', 'Corinne'),
(78, 'VALERIO', 'Noemie'),
(79, 'WILLAUME', 'Hérvé'),
(80, 'DORN', 'Daniel'),
(81, 'BALAND', 'Sophie'),
(82, 'BARDIN-MONNIER', 'M'),
(83, 'BLAISIUS', 'Olivier'),
(84, 'BYRNE', ''),
(85, 'CIMAN', 'Jean Louis'),
(86, 'CONTINI', 'HUGHES'),
(87, 'DAUDEY', ''),
(88, 'DOURY', ''),
(89, 'DOURY', 'Julien'),
(90, 'ERPUYAN', 'Murat'),
(91, 'FERRY', 'col'),
(92, 'FONTANEZ', 'Nicole'),
(93, 'FREY', 'colle AGL'),
(94, 'FREY', 'Isabelle'),
(95, 'HADDOU', 'Amir'),
(96, 'JACOB', 'Cyril'),
(97, 'KOWALSKA', ''),
(98, 'MARCHAL', 'Timothé'),
(99, 'MARIUCCI', 'Monique'),
(100, 'MASSON', 'Cyril'),
(101, 'PENIGAUD', ''),
(102, 'PENIGAUD', 'Alain'),
(103, 'PERDRIX', 'colle AGL'),
(104, 'RAVILLON', ''),
(105, 'RICHARDS', 'ARIANE'),
(106, 'WEBER', 'Christian'),
(107, 'WEISSE', 'Jean-François'),
(108, 'A2OR44', ''),
(109, 'A2OR45', ''),
(110, 'A2OR46', ''),
(111, 'A2OR47', ''),
(112, 'A2OR48', ''),
(113, 'A2OR49', ''),
(114, 'A2OR50', ''),
(115, 'A2OR51', ''),
(116, 'A2OR52', ''),
(117, 'ACKERMANN', 'Christophe'),
(118, 'ADOUL', 'Eric'),
(119, 'AF01', ''),
(120, 'AF02', ''),
(121, 'AGENOR', 'LAURENT'),
(122, 'ALF1', ''),
(123, 'ALF2', ''),
(124, 'ALIPS', 'Patrick'),
(125, 'ALO1', ''),
(126, 'ANDRE', 'Sébastien'),
(127, 'ANF1', ''),
(128, 'ANTOINE', 'Séverine'),
(129, 'APPARITEUR', ''),
(130, 'AR-BGT1', ''),
(131, 'AR-PRO1', ''),
(132, 'AR01', ''),
(133, 'ARF1 BTS', ''),
(134, 'ARF1BAC', ''),
(135, 'ARF2', ''),
(136, 'ARZUR', 'Yvon'),
(137, 'AVIRON', ''),
(138, 'B1OR35', ''),
(139, 'B1OR36', ''),
(140, 'B1OR37', ''),
(141, 'B1OR38', ''),
(142, 'BADACHE', 'Hamid'),
(143, 'BAILLE', 'Remy'),
(144, 'BANA', 'EDITH'),
(145, 'BARBOSA', 'Christian'),
(146, 'BARDOOMONT', 'Coralie'),
(147, 'BEAUVIER', 'EDITH'),
(148, 'BEITZ', 'Philippe'),
(149, 'BELHAMI', 'Mohammed'),
(150, 'BELVA', 'Julie'),
(151, 'BENARDEAU', 'Florian'),
(152, 'BENCHERIF', 'RACHID'),
(153, 'BENCHERIF OUEDGHIRI', 'RACHID'),
(154, 'BERTRAND', 'MARITE'),
(155, 'BEYLING', 'Pierre'),
(156, 'BIASSE', 'Guillaume'),
(157, 'BONEL', 'Eric'),
(158, 'BONNARD', 'Eve'),
(159, 'BONTEMPS', 'Floryse'),
(160, 'BOSCHIERO', 'Marie-Luce'),
(161, 'BOUAYED', 'AMINE'),
(162, 'BOURGEOIS', 'Laure'),
(163, 'BOUTON', 'Christophe'),
(164, 'BRICARD', 'Manuel'),
(165, 'BRUCIAMACCHIE', 'Catherine'),
(166, 'BRUYERE', 'Stéphanie'),
(167, 'bts colin', ''),
(168, 'BTS LV', ''),
(169, 'BTS LV ADAM', 'Anne'),
(170, 'BTS LV BENCHERIF OUEDGHIRI', 'Rachid'),
(171, 'BTS LV BOUKARINE', 'Jahid'),
(172, 'BTS LV BRICHEUX', 'Eric'),
(173, 'BTS LV CAPUT', 'Rachel'),
(174, 'BTS LV CHAKAI', 'Claire'),
(175, 'BTS LV CHARLOTTE', 'Maria'),
(176, 'BTS LV CHAROTTE', 'Maria'),
(177, 'BTS LV CLOSEN', 'DAVID'),
(178, 'BTS LV COLIN', 'Anne'),
(179, 'BTS LV DAVIS', 'Delphine'),
(180, 'BTS LV DEBRUILLE', 'Angélique'),
(181, 'BTS LV DOSSO', 'Enzo'),
(182, 'BTS LV DUPUIS', 'Aude'),
(183, 'BTS LV ERPUYAN MURAT', 'Vasif'),
(184, 'BTS LV GONTHIE', 'Sophie'),
(185, 'BTS LV HOURCADE', 'EMMANUEL'),
(186, 'BTS LV JACQUES', 'Nathalie'),
(187, 'BTS LV LAMBOLEZ', 'Valérie'),
(188, 'BTS LV LOUIS', 'Christophe'),
(189, 'BTS LV MECO PACHECO', 'Calcilda'),
(190, 'BTS LV MERDJET YAHIA', 'Nordine'),
(191, 'BTS LV MORET', 'Isabelle'),
(192, 'BTS LV NUNEZ', 'Anna-Maria'),
(193, 'BTS LV PERL', 'Brigitte'),
(194, 'BTS LV REISER', 'Barbara'),
(195, 'BTS LV THOMAS', 'Julie'),
(196, 'BTS LV TREMBLEAU', 'Axelle'),
(197, 'BTS LV WIRTZ', 'Kateryn'),
(198, 'BURCKEL', 'CEDRIC'),
(199, 'BYSTRITZKY', 'Isabelle'),
(200, 'CADORET', 'Lucie'),
(201, 'CAILLIEREZ', 'Cedric'),
(202, 'CAMPANI', 'Marion'),
(203, 'CANALS', 'Martin'),
(204, 'CANNIZZARO', 'Leonard'),
(205, 'CAPUT', 'Rachel'),
(206, 'CHAGROUNE', ''),
(207, 'CHANTOURY', 'Remy'),
(208, 'CHELGHAF', 'Lucas'),
(209, 'CHERCHEUSE ENSGSI', ''),
(210, 'CHERCHEUSE ENSIC', ''),
(211, 'CHERCHEUSE INRAE', ''),
(212, 'CHERCHEUSE LORIA', ''),
(213, 'CHERCHEUSE OBSERVATOIRE STRASBOURG', ''),
(214, 'CHOLIN', 'Justine'),
(215, 'CHOMEL', 'Léa'),
(216, 'CLEYMAND', 'Emmanuelle'),
(217, 'CNED', ''),
(218, 'CNED', 'ALLD'),
(219, 'CNED', 'ARABE'),
(220, 'CNED', 'ENS SC'),
(221, 'CNED', 'HG'),
(222, 'CNED', 'Mathématiques'),
(223, 'COLINET', 'Yves'),
(224, 'COLLIER', 'Michael'),
(225, 'COLLIGNON', 'Valerie'),
(226, 'COLNEL', 'Jean'),
(227, 'CONCOURS ASS', 'JURY 1'),
(228, 'CONCOURS ASS', 'JURY 2'),
(229, 'CONSTANS', 'Stephanie'),
(230, 'COTTIN', 'LUC'),
(231, 'COUZINIE', 'Simon'),
(232, 'CUGNIER', 'Séverine'),
(233, 'CUVELLE', 'Audrey'),
(234, 'CUVINOT-GUERNE', 'Martine'),
(235, 'CZERWIEC', 'Thierry'),
(236, 'DARDAINE', 'CELINE'),
(237, 'DARDEVET', 'Romain'),
(238, 'DE AMARAL', 'Cindy'),
(239, 'DEHOVE', 'Thomas'),
(240, 'DELF A1 1', ''),
(241, 'DELF A1 2', ''),
(242, 'DELF A1 3', ''),
(243, 'DELF A1 4', ''),
(244, 'DELF A1 5', ''),
(245, 'DELF A2 1', ''),
(246, 'DELF A2 10', ''),
(247, 'DELF A2 2', ''),
(248, 'DELF A2 3', ''),
(249, 'DELF A2 4', ''),
(250, 'DELF A2 5', ''),
(251, 'DELF A2 6', ''),
(252, 'DELF A2 7', ''),
(253, 'DELF A2 8', ''),
(254, 'DELF A2 9', ''),
(255, 'DELF B1 1', ''),
(256, 'DELF B1 2', ''),
(257, 'DELF B1 3', ''),
(258, 'DELF B1 4', ''),
(259, 'DELF B1 5', ''),
(260, 'DELORME', 'Daniel'),
(261, 'DELWAULLE', 'Coralie'),
(262, 'DEMANGE', 'CLAIRE'),
(263, 'DEMARLE', 'MANDY'),
(264, 'DEMENGEON', 'Florian'),
(265, 'DEUTSCH', 'Olivier'),
(266, 'DEVIGNON', 'Ophélie'),
(267, 'DIDELOT', 'Emilie'),
(268, 'DIDION', 'LEONIE'),
(269, 'DIOU', 'Charlotte'),
(270, 'DOLCKER', 'PASCAL'),
(271, 'DORN', 'Daniel'),
(272, 'DUKATENZEILER', 'Tom'),
(273, 'DUMET', 'Frédéric'),
(274, 'DUPUIS', 'Aude'),
(275, 'DUVOID', 'Julie'),
(276, 'E2-ME4', ''),
(277, 'EDUCATEUR', 'SPORTIF'),
(278, 'EF01', ''),
(279, 'EF02', ''),
(280, 'EL OUALID', 'Souffiane'),
(281, 'ERPUYAN', 'X'),
(282, 'ES01', ''),
(283, 'ES02', ''),
(284, 'ESF1', ''),
(285, 'ESF2', ''),
(286, 'FERIGUTTI', 'HELENE'),
(287, 'FERREIRA', 'Laurence'),
(288, 'FERREIRA-HOLLEVILLE', ''),
(289, 'FERRIGNO', 'Sandie'),
(290, 'FILLOUX', 'Eulalie'),
(291, 'FISCHER', 'Caroline'),
(292, 'FONTINHA', 'Nathalie'),
(293, 'FOUCAULT', 'Boris'),
(294, 'FOURNIER', 'David'),
(295, 'FRESSE', 'Emmanuel'),
(296, 'GABON', 'MADELEINE'),
(297, 'GAILLARD', 'Jean'),
(298, 'GAILLARD', 'Philippe'),
(299, 'GARCIA', 'JUAN'),
(300, 'GARNIER', 'Agathe'),
(301, 'GENCHI', 'QUENTIN'),
(302, 'GEOFFROY', 'Sylvie'),
(303, 'GERARD', 'ODILE'),
(304, 'GERARDIN', 'Bruno'),
(305, 'GILLET', 'Florent'),
(306, 'GILSON', 'JEAN-LOUIS'),
(307, 'GIROT', 'Maxime'),
(308, 'GODFROY-GAUTHIER', 'JEAN MICHEL'),
(309, 'GOUMONT', 'DIDIER'),
(310, 'GUERARD', 'Olivier'),
(311, 'GUIET', 'EMELINE'),
(312, 'GUITARD', 'Céline'),
(313, 'HALIM', 'Latifa'),
(314, 'HAMIDI', 'Said'),
(315, 'HANRION', 'Claude'),
(316, 'HAUROIGNE', 'Pascal'),
(317, 'HEINE', 'BENJAMIN'),
(318, 'HENRIOT', 'Laurent'),
(319, 'HOCQUAUX', 'Stanislas'),
(320, 'HOLLEVILLE', 'FABIEN'),
(321, 'HOLZMANN', 'Arnaud'),
(322, 'HOUBART', 'Juline'),
(323, 'HU', 'Guo Hua'),
(324, 'HUIN', 'Didier'),
(325, 'IF01', ''),
(326, 'IT01', ''),
(327, 'ITF01', ''),
(328, 'JACQUEMET', 'Cedric'),
(329, 'JACQUINET', 'Arnaud'),
(330, 'JACQUINET', 'Samuel'),
(331, 'JAKUBOWICZ', 'Marc'),
(332, 'JAMEY', 'Patrice'),
(333, 'JEANDINOT', 'ALEXIA'),
(334, 'JEANNINGROS', 'Emilie'),
(335, 'JOACHIM', 'ARNAUD'),
(336, 'JOLY', 'Brigitte'),
(337, 'JONCA', 'FRANCOIS'),
(338, 'JONQUIERES', 'Anne'),
(339, 'KALTENBACH', 'THEOPHILE'),
(340, 'KECK', 'Didier'),
(341, 'KEMMAR', 'Nacer'),
(342, 'KERGUSTAN', 'Martial'),
(343, 'KERRIEN', 'Tiphaine'),
(344, 'KIENNEMANN', 'Valérie'),
(345, 'KOPERA', 'Laurent'),
(346, 'KRAUFFEL', 'Anthony'),
(347, 'KUNC', 'Christelle'),
(348, 'KURASIAK', 'Marie-Christine'),
(349, 'L\'HUILLIER', 'Guy'),
(350, 'L\'HUILLIER', 'JULIEN'),
(351, 'LA VAULLEE', 'Nathalie'),
(352, 'LAMBERT', 'Damien'),
(353, 'LAMOURET', 'MARIE HELENE'),
(354, 'LAPREVOTE', 'CHRISTINE'),
(355, 'LARIBI', 'SABRINA'),
(356, 'LAUR', 'Emmanuelle'),
(357, 'LAYEN', 'Cécric'),
(358, 'LE NAOUR', 'Anne'),
(359, 'LEBON', 'AMANDINE'),
(360, 'LEBOUBE', 'SEVERINE'),
(361, 'LEGAY', 'Patricia'),
(362, 'LEGENDRE', 'Philippe'),
(363, 'LEGOUVERNEUR', 'Raphaelle'),
(364, 'LEMENANT', 'Stéphanie'),
(365, 'LEMMER', 'Laurence'),
(366, 'LEROY', 'ARNAUD'),
(367, 'LEROY1', 'ARNAUD'),
(368, 'LIU', 'Thomas'),
(369, 'LIU1', 'THOMAS'),
(370, 'LOEUILLET', 'Christophe'),
(371, 'LOUIS', 'Christine'),
(372, 'LOUIS', 'CHRISTOPHE'),
(373, 'M\'HAMED', 'Amin'),
(374, 'M\'HAMED', 'AMINE'),
(375, 'MAITRE', 'Apprentissage'),
(376, 'MANN', 'LIDWINE'),
(377, 'MARCHAL', 'Patrice'),
(378, 'MARTELLI', 'PIERRE-WILLIAM'),
(379, 'MARTIN', 'Tanguy'),
(380, 'MASINI', 'Adèle'),
(381, 'MASSON', 'Bertrand'),
(382, 'MATHFSTG', ''),
(383, 'MATHIEU', 'Laurence'),
(384, 'MATTE', 'Jean-François'),
(385, 'MEJRI', 'Mohammed'),
(386, 'MERABTI DJAOUANI', 'FAHIMA'),
(387, 'MEZIANE', 'Mohamed'),
(388, 'MICHON-VACELET', 'Gabrielle'),
(389, 'MIGUEL-BREBION', 'MAXENCE'),
(390, 'MINAUD', 'Antoine'),
(391, 'MOINE', 'Claire'),
(392, 'MONIEZ', 'Sophie'),
(393, 'MOTTI', 'Xavier'),
(394, 'MULLER', 'Hélène'),
(395, 'MULLER', 'SEBASTIEN'),
(396, 'NABOUDET', 'Noelie'),
(397, 'NEUMANN', 'Christian'),
(398, 'NEUTS', 'Bernard'),
(399, 'NGONDY', 'CECILE'),
(400, 'NICOLAS', 'Nancy'),
(401, 'NICOLAS-PIERRE', 'Delphine'),
(402, 'NOEL', 'STANISLAS'),
(403, 'PAGANESSI', 'Laura'),
(404, 'PARISI', 'ESTELLE'),
(405, 'PATIZEL', 'Béatrice'),
(406, 'PELLISSIER', 'Sarah'),
(407, 'PERRIN', 'MARIE-CHARLOTTE'),
(408, 'PERROSE', 'Jean-Noël'),
(409, 'PETITDEMANGE', 'Karine'),
(410, 'PF01', ''),
(411, 'PF1', ''),
(412, 'PF2', ''),
(413, 'PFEIFFER', 'Renaud'),
(414, 'PIEGLE', 'Armelle'),
(415, 'PIEROT', 'Didier'),
(416, 'PIERRE FERIGUTTI', 'HELENE'),
(417, 'PIERROT', 'Lionel'),
(418, 'PILKE', 'KARINE'),
(419, 'PO-PRO1', ''),
(420, 'PO01', ''),
(421, 'PO1', ''),
(422, 'PO2', ''),
(423, 'POCACHARD', 'JEROME'),
(424, 'POCACHARD1', 'JEROME'),
(425, 'PRADEZYNSKI', 'Sylvie'),
(426, 'PREMILAT', 'Anne'),
(427, 'PREMILAT', 'EMMANUEL'),
(428, 'PREPARATION', 'ORAUX'),
(429, 'PREVOST', 'SYLVIE'),
(430, 'PREVOT', 'CHRISTOPHE'),
(431, 'PROFESSEUR', 'AC'),
(432, 'RACUNICA', 'Gary'),
(433, 'RAKOTOMALALA', 'MBININA'),
(434, 'RAMUS', 'Alexis'),
(435, 'RAPIN', 'Cécile'),
(436, 'RAVAUX', 'JEAN-PIERRE'),
(437, 'RAVENEL', 'Patrick'),
(438, 'RENAULD', 'Alain'),
(439, 'RF01', ''),
(440, 'ROGUE', 'Axel'),
(441, 'ROMO MORENO', 'DINO'),
(442, 'ROPARS', 'Michaela'),
(443, 'ROTHIOT', 'Carine'),
(444, 'ROUSSEAU', 'Celine'),
(445, 'ROYAUD', 'Isabelle'),
(446, 'RU-PRO1', ''),
(447, 'RUF1', ''),
(448, 'SAFFROY', 'Annabelle'),
(449, 'SAKHRI', 'MOHSEN'),
(450, 'SAN FELICE', 'Luigi'),
(451, 'SCHMITT', 'ERIC'),
(452, 'SCHMITT', 'Nicolas'),
(453, 'SCHNEIDER', 'Catherine'),
(454, 'SCHNEPF', 'François'),
(455, 'SEJOURNE', 'Jean-Marc'),
(456, 'SELIMOTIC', 'DJENAN'),
(457, 'SERRE SANCHEZ GUILLO', 'CLEMENS JOSE'),
(458, 'SIMONAIRE', 'Stephane'),
(459, 'SIRJACQUES', 'Yvon'),
(460, 'SIX', 'Jean-Luc'),
(461, 'SKA', 'ANNE'),
(462, 'SOUDIERE', 'Christophe'),
(463, 'SPRLAK', 'Claire'),
(464, 'STAG ARTS PLA', ''),
(465, 'STEFFEN', 'Didier'),
(466, 'STURM', 'Mathilde'),
(467, 'SUPELJAK', 'Yann'),
(468, 'SUPPIOT', 'Alain'),
(469, 'SUTY', 'Elisabeth'),
(470, 'SYDA', 'Jean-Baptiste'),
(471, 'TECHNICIENNE CND', ''),
(472, 'TECHNICIENNE ROBOTIQUE', ''),
(473, 'THEBAULT', 'Max'),
(474, 'THENOT', 'Adrien'),
(475, 'THIRION', 'FREDERIC'),
(476, 'THOMAS', 'Laëtitia'),
(477, 'THUMMEN', 'Edith'),
(478, 'TOUSSAINT', 'Eric'),
(479, 'TRAVAIL PERSONNEL', ''),
(480, 'TREMBLEAU', 'AXELLE'),
(481, 'TRIBOUT', 'Sylvie'),
(482, 'TROUSSON', 'Didier'),
(483, 'TU-BGT01', ''),
(484, 'TU01', ''),
(485, 'TUF01', ''),
(486, 'TUF02', ''),
(487, 'VAILLANT', 'Pascal'),
(488, 'VALENCE', 'Corinne'),
(489, 'VARGAS', 'Jordi'),
(490, 'VAUTRIN', 'OLIVIER'),
(491, 'VERGER-POISSENOT', 'CECILE'),
(492, 'VERNIER', 'Laurent'),
(493, 'VINCENT', 'Hervé'),
(494, 'VIOLONI', 'Caroline'),
(495, 'VIROLLAUD', 'Hélène'),
(496, 'VOISIN', 'VIRGINIE'),
(497, 'WINCKLER', 'Bruno'),
(498, 'YAHYAOUI', 'Yasmine'),
(499, 'ZCPS', 'Maths'),
(500, 'ZET', 'Anglais'),
(501, 'ZET1', 'Maths'),
(502, 'ZITELLA', 'Danielle'),
(503, 'DELOCHE', 'Thierry'),
(504, '', '');

-- --------------------------------------------------------

--
-- Structure de la table `responsibles`
--

DROP TABLE IF EXISTS `responsibles`;
CREATE TABLE IF NOT EXISTS `responsibles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `total` int DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `responsibles`
--

INSERT INTO `responsibles` (`id`, `name`, `total`) VALUES
(6, '1\r\n', 2),
(7, '2\r\n', 0),
(8, '3', 0);

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
  `stock` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `variantes`
--

INSERT INTO `variantes` (`id`, `id_materiaux`, `description`, `stock`) VALUES
(1, 34, '3 mm', 0),
(2, 34, '6 mm', 0),
(3, 34, '8 mm', 0),
(5, 39, 'A4', 0),
(6, 39, 'A3', 0),
(7, 39, 'A2\r\n', 0),
(8, 39, 'A1', 0),
(9, 39, 'A1 N&B', 0),
(10, 39, 'A2 N&B\r\n', 0),
(11, 39, 'A3 N&B', 0),
(12, 39, 'A4 N&B', 0);

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
