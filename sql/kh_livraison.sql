-- Base de données KH LIVRAISON V2
CREATE DATABASE IF NOT EXISTS kh_livraison CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE kh_livraison;

-- TABLE UTILISATEURS
CREATE TABLE IF NOT EXISTS utilisateurs (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    derniere_connexion TIMESTAMP NULL,
    actif BOOLEAN DEFAULT TRUE,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE ADRESSES
CREATE TABLE IF NOT EXISTS adresses (
    id_adresse INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    type_adresse ENUM('livraison', 'facturation', 'autre') DEFAULT 'livraison',
    adresse_ligne1 VARCHAR(255) NOT NULL,
    adresse_ligne2 VARCHAR(255),
    code_postal VARCHAR(10) NOT NULL,
    ville VARCHAR(100) NOT NULL,
    pays VARCHAR(100) DEFAULT 'France',
    adresse_par_defaut BOOLEAN DEFAULT FALSE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    INDEX idx_utilisateur (id_utilisateur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE SERVICES
CREATE TABLE IF NOT EXISTS services (
    id_service INT AUTO_INCREMENT PRIMARY KEY,
    nom_service VARCHAR(150) NOT NULL,
    description TEXT,
    prix_base DECIMAL(10, 2) NOT NULL,
    temps_moyen_livraison INT COMMENT 'en minutes',
    actif BOOLEAN DEFAULT TRUE,
    image_service VARCHAR(255),
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion des services
INSERT INTO services (nom_service, description, prix_base, temps_moyen_livraison, image_service) VALUES
('Livraison de repas à domicile', 'Vos plats préférés livrés chauds et à temps', 4.99, 30, 'images/service-1.jpg'),
('Livraison de colis express', 'Livraison rapide et sécurisée avec suivi en temps réel', 8.99, 120, 'images/service-2.jpg'),
('Livraison pour restaurants', 'Service dédié aux professionnels avec tarifs dégressifs', 12.99, 60, 'images/service-3.png');

-- TABLE COMMANDES
CREATE TABLE IF NOT EXISTS commandes (
    id_commande INT AUTO_INCREMENT PRIMARY KEY,
    id_utilisateur INT NOT NULL,
    id_service INT NOT NULL,
    id_adresse_livraison INT NOT NULL,
    statut ENUM('en_attente', 'confirmee', 'en_preparation', 'en_livraison', 'livree', 'annulee') DEFAULT 'en_attente',
    prix_total DECIMAL(10, 2) NOT NULL,
    instructions_livraison TEXT,
    date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_livraison_estimee TIMESTAMP NULL,
    date_livraison_reelle TIMESTAMP NULL,
    code_suivi VARCHAR(50) UNIQUE,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    FOREIGN KEY (id_service) REFERENCES services(id_service),
    FOREIGN KEY (id_adresse_livraison) REFERENCES adresses(id_adresse),
    INDEX idx_utilisateur (id_utilisateur),
    INDEX idx_statut (statut),
    INDEX idx_date (date_commande)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE CONTACTS
CREATE TABLE IF NOT EXISTS contacts (
    id_contact INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    telephone VARCHAR(20),
    message TEXT NOT NULL,
    date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    lu BOOLEAN DEFAULT FALSE,
    repondu BOOLEAN DEFAULT FALSE,
    INDEX idx_date (date_envoi),
    INDEX idx_lu (lu)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE LIVREURS (optionnel)
CREATE TABLE IF NOT EXISTS livreurs (
    id_livreur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    vehicule VARCHAR(100),
    plaque_immatriculation VARCHAR(50),
    disponible BOOLEAN DEFAULT TRUE,
    date_embauche DATE,
    INDEX idx_disponible (disponible)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE LIAISON COMMANDE-LIVREUR
CREATE TABLE IF NOT EXISTS commande_livreur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_commande INT NOT NULL,
    id_livreur INT NOT NULL,
    date_assignation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_commande) REFERENCES commandes(id_commande) ON DELETE CASCADE,
    FOREIGN KEY (id_livreur) REFERENCES livreurs(id_livreur),
    INDEX idx_commande (id_commande),
    INDEX idx_livreur (id_livreur)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- TABLE AVIS
CREATE TABLE IF NOT EXISTS avis (
    id_avis INT AUTO_INCREMENT PRIMARY KEY,
    id_commande INT NOT NULL,
    id_utilisateur INT NOT NULL,
    note INT CHECK (note BETWEEN 1 AND 5),
    commentaire TEXT,
    date_avis TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_commande) REFERENCES commandes(id_commande) ON DELETE CASCADE,
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur) ON DELETE CASCADE,
    INDEX idx_note (note)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- DONNÉES DE TEST
-- Utilisateur test (mot de passe: test123)
INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe) VALUES
('Dupont', 'Jean', 'jean.dupont@test.fr', '0612345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Adresse test
INSERT INTO adresses (id_utilisateur, adresse_ligne1, ville, code_postal, type_adresse) VALUES
(1, '123 Rue de la Livraison', 'Paris', '75000', 'livraison');

-- Commande test
INSERT INTO commandes (id_utilisateur, id_service, id_adresse_livraison, statut, prix_total, code_suivi) VALUES
(1, 2, 1, 'en_livraison', 8.99, 'KH2A3B4C5D6E');

SHOW TABLES;
