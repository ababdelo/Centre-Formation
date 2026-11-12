
CREATE DATABASE IF NOT EXISTS centre_formation
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE centre_formation;

CREATE TABLE IF NOT EXISTS utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(64) NOT NULL,
    prenom VARCHAR(64) NOT NULL,
    email VARCHAR(128) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    date_naissance DATE NOT NULL,
    role ENUM('ADMIN', 'FORMATEUR', 'ETUDIANT') NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS formateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL UNIQUE,
    identifiant VARCHAR(32) UNIQUE,
    specialite VARCHAR(128) NOT NULL,
    statut ENUM('ACTIF', 'INACTIF') DEFAULT 'ACTIF',
    rib VARCHAR(32) NOT NULL,

    CONSTRAINT fk_formateur_utilisateur
        FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS groupe (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(16) UNIQUE,
    nom VARCHAR(128) NOT NULL,
    filiere VARCHAR(128) NOT NULL,
    annee SMALLINT NOT NULL,
    capacity TINYINT NOT NULL,
    statut ENUM('PLANIFIE', 'EN_COURS', 'TERMINE', 'ANNULE') DEFAULT 'PLANIFIE'
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS etudiant (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL UNIQUE,
    matricule VARCHAR(16) UNIQUE,
    telephone VARCHAR(16) NOT NULL,
    statut ENUM('ACTIF', 'SUSPENDU', 'ABANDONNE', 'TERMINE') DEFAULT 'ACTIF',
    id_groupe INT DEFAULT NULL,

    CONSTRAINT fk_etudiant_utilisateur
        FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur(id)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT fk_etudiant_groupe
        FOREIGN KEY (id_groupe)
        REFERENCES groupe(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS module (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(16) NOT NULL UNIQUE,
    intitule VARCHAR(128) NOT NULL,
    description TEXT DEFAULT NULL,
    volume_horaire SMALLINT NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS affectation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_groupe INT NOT NULL,
    id_module INT NOT NULL,
    id_formateur INT NOT NULL,
    annee SMALLINT NOT NULL,

    CONSTRAINT fk_affectation_groupe
        FOREIGN KEY (id_groupe)
        REFERENCES groupe(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_affectation_module
        FOREIGN KEY (id_module)
        REFERENCES module(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT fk_affectation_formateur
        FOREIGN KEY (id_formateur)
        REFERENCES formateur(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB;
