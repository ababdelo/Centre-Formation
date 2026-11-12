INSERT INTO utilisateur
    (nom, prenom, email, mot_de_passe, date_naissance, role)
VALUES
    ('Alami', 'Said', 'alami.said@gmail.com', 'admin@123', '1985-04-12', 'ADMIN'),

    ('Benali', 'taha', '2005031500123@ofppt-edu.ma', 'Taha@123', '2005-03-15', 'ETUDIANT'),
    ('El Amrani', 'nisrine', '2004072200456@ofppt-edu.ma', 'Nisrine@123', '2004-07-22', 'ETUDIANT'),
    ('Alaoui', 'Ayman', '2005110800789@ofppt-edu.ma', 'Ayman@123', '2005-11-08', 'ETUDIANT'),

    ('Berrada', 'Nadia', 'nadia.berrada@ofppt-edu.ma', 'Nadia@123', '1990-06-23', 'FORMATEUR'),
    ('Idrissi', 'Mohamed', 'mohamed.idrissi@ofppt-edu.ma', 'Mohamed@123', '1987-09-14', 'FORMATEUR');


INSERT INTO groupe
    (code, nom, filiere, annee, capacity, statut)
VALUES
    ('DEV201', 'DEV201', 'Développement Digital', 2026, 20, 'EN_COURS'),
    ('DEV103', 'DEV103', 'Développement Digital', 2026, 20, 'EN_COURS'),
    ('DEV204', 'DEV204', 'Développement Digital', 2026, 20, 'EN_COURS'),
    ('INF201', 'INF201', 'Infrastructure Digitale', 2026, 20, 'EN_COURS'),
    ('IA201',  'IA201',  'Intelligence Artificielle', 2026, 20, 'EN_COURS');


INSERT INTO formateur
    (id_utilisateur, identifiant, specialite, statut, rib)
VALUES
    (5, 'FOR2026001', 'Développement Digital', 'ACTIF', 'TEST-RIB-FORM-001'),
    (6, 'FOR2026002', 'Infrastructure et Réseaux', 'ACTIF', 'TEST-RIB-FORM-002');


INSERT INTO etudiant
    (id_utilisateur, matricule, telephone, statut, id_groupe)
VALUES
    (2, '2005031500123', '0X0001', 'ACTIF', 2),
    (3, '2004072200456', '0X0002', 'ACTIF', 1),
    (4, '2005110800789', '0X0003', 'ACTIF', 5);


INSERT INTO module
    (code, intitule, description, volume_horaire)
VALUES
('DD-M103', 'Programmer en Orienté Objet', NULL, 60),
('DD-M104', 'Développer des sites web statiques', NULL, 60),
('DD-M105', 'Programmer en JavaScript', NULL, 60),
('DD-M107', 'Développer des sites web dynamiques', NULL, 60),
('DD-M202', 'Approche agile', NULL, 120),
('DD-M203', 'Gestion des données', NULL, 90),

('ID-M103', 'Concevoir un réseau informatique', NULL, 60),
('ID-M206', 'Sécurité d''une infrastructure digitale', NULL, 75),

('IA-M104', 'Mathématiques pour le traitement des données', NULL, 60),
('IA-ADA204', 'Projet de spécialisation IA', NULL, 60);