-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 18 jan. 2024 à 10:09
-- Version du serveur : 8.0.31
-- Version de PHP : 8.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `booking`
--

-- --------------------------------------------------------

--
-- Structure de la table `annonce`
--

DROP TABLE IF EXISTS `annonce`;
CREATE TABLE IF NOT EXISTS `annonce` (
  `idAnnonce` int NOT NULL AUTO_INCREMENT,
  `idUtilisateur` int NOT NULL,
  `disponibilite_debut` date NOT NULL,
  `disponibilite_fin` date NOT NULL,
  `emplacement` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `prix` int NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `animaux` tinyint(1) NOT NULL,
  `enfants` tinyint(1) NOT NULL,
  `accessibilite` tinyint(1) NOT NULL,
  `image` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`idAnnonce`),
  KEY `idUtilisateur` (`idUtilisateur`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `annonce`
--

INSERT INTO `annonce` (`idAnnonce`, `idUtilisateur`, `disponibilite_debut`, `disponibilite_fin`, `emplacement`, `prix`, `description`, `animaux`, `enfants`, `accessibilite`, `image`) VALUES
(1, 1, '2024-01-19', '2024-04-12', 'Gare Lille Flandres', 84, 'Appartement 60m², vue sur la gare de lille, proche des transports en communs et peu de bruits la nuit.', 1, 1, 0, '1_2024-01-19.png'),
(2, 2, '2024-01-01', '2024-01-31', 'Avenue Georges Pompidou', 94, 'Jolie maison en location, avec vue sur le parc', 1, 1, 1, '2_2024-01-01.png'),
(3, 1, '2024-01-10', '2024-01-31', 'Rue Baudelaire', 127, 'Maison des années 1990, rénovée de 100m² tout rond, avec proximité des transports en communs', 1, 1, 1, '3_2024-01-10.png'),
(5, 3, '2024-02-02', '2024-03-08', 'Rue Sarkozy', 75, 'Jolie maison avec piscine et divers équipements. 3 chambres avec lits doubles ainsi que 2 clic-clacs 2 places.', 0, 0, 1, '5_2024-02-02.png'),
(6, 12, '2023-12-01', '2024-03-22', 'Boulevard Dupont, Strasbourg', 86, 'Petit appartement avec un joli espace bien éclairé. 2 lits doubles avec plusieurs équipements utiles. Très bien isolé pour l\'hiver et climatisé l\'été', 0, 1, 0, '6_2023-12-01.png');

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

DROP TABLE IF EXISTS `avis`;
CREATE TABLE IF NOT EXISTS `avis` (
  `idAvis` int NOT NULL AUTO_INCREMENT,
  `idUtilisateur` int NOT NULL,
  `idAnnonce` int NOT NULL,
  `Note` tinyint NOT NULL,
  `Commentaires` text COLLATE utf8mb4_general_ci NOT NULL,
  `dateAvis` datetime NOT NULL,
  PRIMARY KEY (`idAvis`),
  KEY `idUtilisateur` (`idUtilisateur`),
  KEY `idAnnonce` (`idAnnonce`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `avis`
--

INSERT INTO `avis` (`idAvis`, `idUtilisateur`, `idAnnonce`, `Note`, `Commentaires`, `dateAvis`) VALUES
(1, 2, 1, 4, 'Je recommande fortement, l\'emplacement est idéale pour un passage sur Lille néanmoins le bruit est constant quand on ouvre les fenêtres.', '2024-01-23 14:21:33');

-- --------------------------------------------------------

--
-- Structure de la table `equipementannonce`
--

DROP TABLE IF EXISTS `equipementannonce`;
CREATE TABLE IF NOT EXISTS `equipementannonce` (
  `idAnnonce` int NOT NULL,
  `CodeEquipement` int NOT NULL,
  KEY `idAnnonce` (`idAnnonce`,`CodeEquipement`),
  KEY `CodeEquipement` (`CodeEquipement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `equipementannonce`
--

INSERT INTO `equipementannonce` (`idAnnonce`, `CodeEquipement`) VALUES
(1, 1),
(1, 3),
(1, 5);

-- --------------------------------------------------------

--
-- Structure de la table `liste_equipement`
--

DROP TABLE IF EXISTS `liste_equipement`;
CREATE TABLE IF NOT EXISTS `liste_equipement` (
  `CodeEquipement` int NOT NULL,
  `LibelleEquipement` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`CodeEquipement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `liste_equipement`
--

INSERT INTO `liste_equipement` (`CodeEquipement`, `LibelleEquipement`) VALUES
(1, 'Four'),
(2, 'Réfrigérateur'),
(3, 'Aspirateur'),
(4, 'Parasol'),
(5, 'Congélateur');

-- --------------------------------------------------------

--
-- Structure de la table `messagerie`
--

DROP TABLE IF EXISTS `messagerie`;
CREATE TABLE IF NOT EXISTS `messagerie` (
  `idMessage` int NOT NULL AUTO_INCREMENT,
  `idUtilisateur` int NOT NULL,
  `idReceveur` int NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`idMessage`),
  KEY `idUtilisateur` (`idUtilisateur`),
  KEY `idReceveur` (`idReceveur`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `messagerie`
--

INSERT INTO `messagerie` (`idMessage`, `idUtilisateur`, `idReceveur`, `message`, `date`) VALUES
(1, 2, 1, 'Bonjour, l\'appartement est-il disponible du 19 au 23 ?', '2024-01-17 14:24:02'),
(2, 1, 2, 'Bonjour, oui l\'appartement est disponible, comment voulez-vous payer et quand arrivez-vous ?', '2024-01-17 16:24:35');

-- --------------------------------------------------------

--
-- Structure de la table `regles`
--

DROP TABLE IF EXISTS `regles`;
CREATE TABLE IF NOT EXISTS `regles` (
  `idRegles` int NOT NULL,
  `idAnnonce` int NOT NULL,
  `regle` text COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`idRegles`),
  KEY `idAnnonce` (`idAnnonce`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `regles`
--

INSERT INTO `regles` (`idRegles`, `idAnnonce`, `regle`) VALUES
(1, 1, '- Ne pas crier le soir pour les voisins\r\n- Nettoyer un minimum avant de partir\r\n- Prendre les escaliers doucement pour ne pas embêter les voisins\r\n- Ne pas fumer dans l\'appartement\r\n- Pas de drogues pour l\'odeur');

-- --------------------------------------------------------

--
-- Structure de la table `reservation`
--

DROP TABLE IF EXISTS `reservation`;
CREATE TABLE IF NOT EXISTS `reservation` (
  `idReservation` int NOT NULL AUTO_INCREMENT,
  `idAnnonce` int NOT NULL,
  `idUtilisateur` int NOT NULL,
  `dateDebut` date NOT NULL,
  `dateFin` date NOT NULL,
  PRIMARY KEY (`idReservation`),
  KEY `idAnnonce` (`idAnnonce`,`idUtilisateur`),
  KEY `idUtilisateur` (`idUtilisateur`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reservation`
--

INSERT INTO `reservation` (`idReservation`, `idAnnonce`, `idUtilisateur`, `dateDebut`, `dateFin`) VALUES
(1, 1, 2, '2024-01-19', '2024-01-23');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `idUtilisateur` int NOT NULL AUTO_INCREMENT,
  `nom` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `prenom` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `adresse` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `age` int NOT NULL,
  `code_postal` int NOT NULL,
  `ville` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `telephone` varchar(10) COLLATE utf8mb4_general_ci NOT NULL,
  `hote` tinyint(1) NOT NULL,
  `voyageur` tinyint(1) NOT NULL,
  `admin` tinyint(1) NOT NULL,
  `motdepasse` text COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`idUtilisateur`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`idUtilisateur`, `nom`, `prenom`, `adresse`, `age`, `code_postal`, `ville`, `telephone`, `hote`, `voyageur`, `admin`, `motdepasse`, `email`) VALUES
(1, 'Dupont', 'Antoine', '15 rue du Général de Gaulles', 21, 59000, 'Lille', '0659478456', 1, 1, 0, '$2y$10$bpYRgv5fnzen0v8MheT/TeWixDL/89G.LVhdBQ0FeEDpOjQa59moS', 'dupont.antoine@gmail.com'),
(2, 'Dubois', 'Marc', '3 avenue François Mitterrand', 25, 59000, 'Lille', '0678491258', 1, 1, 1, '$2y$10$bpYRgv5fnzen0v8MheT/TeWixDL/89G.LVhdBQ0FeEDpOjQa59moS', 'dubois.marc@hotmail.fr'),
(3, 'Dutrouc', 'Damien', '3 rue Baudelaire', 35, 59360, 'Lille', '0659892632', 1, 1, 0, '$2y$10$bpYRgv5fnzen0v8MheT/TeWixDL/89G.LVhdBQ0FeEDpOjQa59moS', 'dutrouc.marc@outlook.fr'),
(12, 'Petit', 'Alexandre', '25 avenue des champs', 36, 59210, 'Lille', '0659471892', 1, 1, 0, '$2y$10$bpYRgv5fnzen0v8MheT/TeWixDL/89G.LVhdBQ0FeEDpOjQa59moS', 'petit.alexandre@gmail.com');

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `annonce`
--
ALTER TABLE `annonce`
  ADD CONSTRAINT `annonce_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`idUtilisateur`);

--
-- Contraintes pour la table `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `avis_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`idUtilisateur`),
  ADD CONSTRAINT `avis_ibfk_2` FOREIGN KEY (`idAnnonce`) REFERENCES `annonce` (`idAnnonce`);

--
-- Contraintes pour la table `equipementannonce`
--
ALTER TABLE `equipementannonce`
  ADD CONSTRAINT `equipementannonce_ibfk_1` FOREIGN KEY (`idAnnonce`) REFERENCES `annonce` (`idAnnonce`),
  ADD CONSTRAINT `equipementannonce_ibfk_2` FOREIGN KEY (`CodeEquipement`) REFERENCES `liste_equipement` (`CodeEquipement`);

--
-- Contraintes pour la table `messagerie`
--
ALTER TABLE `messagerie`
  ADD CONSTRAINT `messagerie_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`idUtilisateur`),
  ADD CONSTRAINT `messagerie_ibfk_2` FOREIGN KEY (`idReceveur`) REFERENCES `utilisateur` (`idUtilisateur`);

--
-- Contraintes pour la table `regles`
--
ALTER TABLE `regles`
  ADD CONSTRAINT `regles_ibfk_1` FOREIGN KEY (`idAnnonce`) REFERENCES `annonce` (`idAnnonce`);

--
-- Contraintes pour la table `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_ibfk_1` FOREIGN KEY (`idUtilisateur`) REFERENCES `utilisateur` (`idUtilisateur`),
  ADD CONSTRAINT `reservation_ibfk_2` FOREIGN KEY (`idAnnonce`) REFERENCES `annonce` (`idAnnonce`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
