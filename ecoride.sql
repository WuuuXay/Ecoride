-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 22 mai 2025 à 17:20
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ecoride`
--

-- --------------------------------------------------------

--
-- Structure de la table `avis`
--

CREATE TABLE `avis` (
  `id` int(11) NOT NULL,
  `auteur_id` int(11) DEFAULT NULL,
  `cible_id` int(11) DEFAULT NULL,
  `covoiturage_id` int(11) DEFAULT NULL,
  `note` int(11) NOT NULL,
  `commentaire` longtext NOT NULL,
  `valide` tinyint(1) NOT NULL,
  `date_creation` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `covoiturage`
--

CREATE TABLE `covoiturage` (
  `id` int(11) NOT NULL,
  `chauffeur_id` int(11) DEFAULT NULL,
  `voiture_id` int(11) DEFAULT NULL,
  `depart` varchar(255) NOT NULL,
  `arrivee` varchar(255) NOT NULL,
  `date_depart` datetime NOT NULL,
  `date_arrivee` datetime NOT NULL,
  `prix` double NOT NULL,
  `places_disponibles` int(11) NOT NULL,
  `ecologique` tinyint(1) NOT NULL,
  `incident` tinyint(1) NOT NULL,
  `annule` tinyint(1) NOT NULL,
  `description_incident` longtext DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `statut` varchar(20) NOT NULL,
  `credits_attribues` tinyint(1) NOT NULL,
  `date_fin` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `covoiturage`
--

INSERT INTO `covoiturage` (`id`, `chauffeur_id`, `voiture_id`, `depart`, `arrivee`, `date_depart`, `date_arrivee`, `prix`, `places_disponibles`, `ecologique`, `incident`, `annule`, `description_incident`, `description`, `statut`, `credits_attribues`, `date_fin`) VALUES
(3, 4, 3, 'Paris', 'Lyon', '2025-05-23 17:09:06', '2025-05-23 22:09:06', 25, 3, 1, 0, 0, NULL, NULL, 'a_venir', 0, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20250521090321', '2025-05-22 12:20:43', 260);

-- --------------------------------------------------------

--
-- Structure de la table `participation`
--

CREATE TABLE `participation` (
  `id` int(11) NOT NULL,
  `passager_id` int(11) DEFAULT NULL,
  `covoiturage_id` int(11) DEFAULT NULL,
  `confirme` tinyint(1) NOT NULL,
  `date_participation` datetime NOT NULL,
  `annule` tinyint(1) NOT NULL,
  `validation_trajet` tinyint(1) NOT NULL,
  `commentaire_probleme` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int(11) NOT NULL,
  `email` varchar(180) NOT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`roles`)),
  `password` varchar(255) NOT NULL,
  `pseudo` varchar(50) NOT NULL,
  `credits` int(11) NOT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL,
  `photo_profil` varchar(255) DEFAULT NULL,
  `note_moyenne` double DEFAULT NULL,
  `is_chauffeur` tinyint(1) NOT NULL,
  `is_passager` tinyint(1) NOT NULL,
  `accepte_fumeurs` tinyint(1) DEFAULT NULL,
  `accepte_animaux` tinyint(1) DEFAULT NULL,
  `preferences_supplementaires` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `email`, `roles`, `password`, `pseudo`, `credits`, `telephone`, `adresse`, `is_active`, `photo_profil`, `note_moyenne`, `is_chauffeur`, `is_passager`, `accepte_fumeurs`, `accepte_animaux`, `preferences_supplementaires`) VALUES
(4, 'user@ecoride.fr', '[\"ROLE_USER\"]', '$2y$13$0vKDEKLVNeJqsPI4aZzp1Oszp9V6kgQaTZyw3jjMqMh3xt2XNxrFK', 'JohnDoe', 20, NULL, NULL, 1, NULL, 0, 0, 1, NULL, NULL, NULL),
(5, 'admin@ecoride.fr', '[\"ROLE_ADMIN\"]', '$2y$13$3jHQip4sLefMpUK2sWSQJuxmVD34kxL/3nSu0wHIHk5pactPre7Kq', 'AdminBoss', 20, NULL, NULL, 1, NULL, 0, 0, 1, NULL, NULL, NULL),
(6, 'employe@ecoride.fr', '[\"ROLE_EMPLOYE\"]', '$2y$13$Ni2qbpD27QSRuGD16WgD/e9BlE8BDJnsnhOJHFGECyE7vGlmfhlPu', 'EmployeeOne', 20, NULL, NULL, 1, NULL, 0, 0, 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `voiture`
--

CREATE TABLE `voiture` (
  `id` int(11) NOT NULL,
  `proprietaire_id` int(11) NOT NULL,
  `marque` varchar(50) NOT NULL,
  `modele` varchar(50) NOT NULL,
  `couleur` varchar(50) NOT NULL,
  `energie` varchar(20) NOT NULL,
  `plaque_immatriculation` varchar(20) NOT NULL,
  `nb_places` int(11) NOT NULL,
  `date_premiere_immatriculation` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `voiture`
--

INSERT INTO `voiture` (`id`, `proprietaire_id`, `marque`, `modele`, `couleur`, `energie`, `plaque_immatriculation`, `nb_places`, `date_premiere_immatriculation`) VALUES
(3, 4, 'Tesla', 'Model 3', 'Blanc', 'Électrique', 'AA-123-AA', 4, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `avis`
--
ALTER TABLE `avis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_8F91ABF060BB6FE6` (`auteur_id`),
  ADD KEY `IDX_8F91ABF0A96E5E09` (`cible_id`),
  ADD KEY `IDX_8F91ABF062671590` (`covoiturage_id`);

--
-- Index pour la table `covoiturage`
--
ALTER TABLE `covoiturage`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_28C79E8985C0B3BE` (`chauffeur_id`),
  ADD KEY `IDX_28C79E89181A8BA` (`voiture_id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `participation`
--
ALTER TABLE `participation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_AB55E24F71A51189` (`passager_id`),
  ADD KEY `IDX_AB55E24F62671590` (`covoiturage_id`);

--
-- Index pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_1D1C63B3E7927C74` (`email`);

--
-- Index pour la table `voiture`
--
ALTER TABLE `voiture`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_E9E2810F76C50E4A` (`proprietaire_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `avis`
--
ALTER TABLE `avis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `covoiturage`
--
ALTER TABLE `covoiturage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `participation`
--
ALTER TABLE `participation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `voiture`
--
ALTER TABLE `voiture`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `avis`
--
ALTER TABLE `avis`
  ADD CONSTRAINT `FK_8F91ABF060BB6FE6` FOREIGN KEY (`auteur_id`) REFERENCES `utilisateur` (`id`),
  ADD CONSTRAINT `FK_8F91ABF062671590` FOREIGN KEY (`covoiturage_id`) REFERENCES `covoiturage` (`id`),
  ADD CONSTRAINT `FK_8F91ABF0A96E5E09` FOREIGN KEY (`cible_id`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `covoiturage`
--
ALTER TABLE `covoiturage`
  ADD CONSTRAINT `FK_28C79E89181A8BA` FOREIGN KEY (`voiture_id`) REFERENCES `voiture` (`id`),
  ADD CONSTRAINT `FK_28C79E8985C0B3BE` FOREIGN KEY (`chauffeur_id`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `participation`
--
ALTER TABLE `participation`
  ADD CONSTRAINT `FK_AB55E24F62671590` FOREIGN KEY (`covoiturage_id`) REFERENCES `covoiturage` (`id`),
  ADD CONSTRAINT `FK_AB55E24F71A51189` FOREIGN KEY (`passager_id`) REFERENCES `utilisateur` (`id`);

--
-- Contraintes pour la table `voiture`
--
ALTER TABLE `voiture`
  ADD CONSTRAINT `FK_E9E2810F76C50E4A` FOREIGN KEY (`proprietaire_id`) REFERENCES `utilisateur` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
