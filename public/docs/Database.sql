-- ============================================
-- SCRIPT DE BASE DE DONNÉES TALENT HUB
-- Données marocaines adaptées au nouveau schéma
-- ============================================

-- Création de la base de données
DROP DATABASE IF EXISTS talent_hub;
CREATE DATABASE talent_hub;
USE talent_hub;

-- ============================================
-- CRÉATION DES TABLES (basé sur l'ERD)
-- ============================================

CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE
);

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE candidates (
    user_id INT PRIMARY KEY,    
    salary_min DECIMAL(10,2),
    salary_max DECIMAL(10,2),
    cv_path VARCHAR(255) NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE recruiters (
    user_id INT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE tags (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE candidate_tags (
    candidate_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (candidate_id, tag_id),
    FOREIGN KEY (candidate_id) REFERENCES candidates(user_id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

CREATE TABLE offers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    recruiter_id INT NOT NULL,
    category_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    is_archived BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    salary_min DECIMAL(10,2),
    salary_max DECIMAL(10,2),
    location VARCHAR(100),
    job_type ENUM('CDI', 'CDD', 'Stage', 'Alternance', 'Freelance') DEFAULT 'CDI',
    FOREIGN KEY (recruiter_id) REFERENCES recruiters(user_id),
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

CREATE TABLE offer_tags (
    offer_id INT NOT NULL,
    tag_id INT NOT NULL,
    PRIMARY KEY (offer_id, tag_id),
    FOREIGN KEY (offer_id) REFERENCES offers(id) ON DELETE CASCADE,
    FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
);

CREATE TABLE applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    offer_id INT NOT NULL,
    candidate_id INT NOT NULL,
    status ENUM('pending', 'accepted', 'rejected') DEFAULT 'pending',
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    cv_path VARCHAR(255),
    cover_letter TEXT,
    FOREIGN KEY (offer_id) REFERENCES offers(id),
    FOREIGN KEY (candidate_id) REFERENCES candidates(user_id)
);

-- ============================================
-- INSERTION DES DONNÉES
-- ============================================

-- Rôles
INSERT INTO roles (name) VALUES 
('candidate'),
('recruiter'),
('admin');

-- Tags (compétences)
INSERT INTO tags (name) VALUES 
('PHP'),
('Laravel'),
('Symfony'),
('JavaScript'),
('React'),
('Vue.js'),
('Node.js'),
('Python'),
('Django'),
('Java'),
('Spring'),
('MySQL'),
('PostgreSQL'),
('MongoDB'),
('Docker'),
('AWS'),
('Git'),
('HTML/CSS'),
('TypeScript'),
('Angular'),
('Marketing Digital'),
('SEO'),
('Social Media'),
('UI/UX'),
('Figma'),
('Photoshop'),
('Data Analysis'),
('Machine Learning'),
('Français'),
('Anglais'),
('Arabe');

-- Catégories
INSERT INTO categories (name) VALUES
('Développement Web'),
('Marketing Digital'),
('Design Graphique'),
('Ressources Humaines'),
('Finance & Comptabilité'),
('Vente & Commerce'),
('Data Science & IA'),
('Management & Direction');

-- Admin
INSERT INTO users (role_id, name, email, password) VALUES
(3, 'Admin TalentHub', 'admin@talenthub.ma', '$2y$10$mNQvQNQvQNQvQNQvQNQvQO'); -- Password: admin123

-- Recruteurs (entreprises marocaines)
INSERT INTO users (role_id, name, email, password) VALUES
(2, 'Maroc Telecom', 'recrutement@iam.ma', '$2y$10$mNQvQNQvQNQvQNQvQNQvQO'),
(2, 'Attijariwafa Bank', 'rh@attijari.ma', '$2y$10$mNQvQNQvQNQvQNQvQNQvQO'),
(2, 'OCP Group', 'carrieres@ocp.ma', '$2y$10$mNQvQNQvQNQvQNQvQNQvQO'),
(2, 'CGI Maroc', 'jobs@cgi.ma', '$2y$10$mNQvQNQvQNQvQNQvQNQvQO'),
(2, 'Capgemini Maroc', 'recrutement@capgemini.ma', '$2y$10$mNQvQNQvQNQvQNQvQNQvQO'),
(2, 'Marjane', 'emploi@marjane.ma', '$2y$10$mNQvQNQvQNQvQNQvQNQvQO');

INSERT INTO recruiters (user_id, company_name) VALUES
(2, 'Maroc Telecom'),
(3, 'Attijariwafa Bank'),
(4, 'OCP Group'),
(5, 'CGI Maroc'),
(6, 'Capgemini Maroc'),
(7, 'Marjane Holding');

-- Candidats marocains
INSERT INTO users (role_id, name, email, password) VALUES
(1, 'Ahmed Benani', 'ahmed.benani@email.com', '$2y$10$mNQvQNQvQNQvQNQvQNQvQO'),
(1, 'Fatima Zahra Alaoui', 'fatima.alaoui@email.com', '$2y$10$mNQvQNQvQNQvQNQvQNQvQO'),
(1, 'Mehdi El Kadi', 'mehdi.elkadi@email.com', '$2y$10$mNQvQNQvQNQvQNQvQNQvQO'),
(1, 'Samira Bennis', 'samira.bennis@email.com', '$2y$10$mNQvQNQvQNQvQNQvQNQvQO'),
(1, 'Youssef Cherkaoui', 'youssef.cherkaoui@email.com', '$2y$10$mNQvQNQvQNQvQNQvQNQvQO'),
(1, 'Nadia El Fassi', 'nadia.elfassi@email.com', '$2y$10$mNQvQNQvQNQvQNQvQNQvQO'),
(1, 'Karim Mansouri', 'karim.mansouri@email.com', '$2y$10$mNQvQNQvQNQvQNQvQNQvQO'),
(1, 'Leila Benjelloun', 'leila.benjelloun@email.com', '$2y$10$mNQvQNQvQNQvQNQvQNQvQO');

INSERT INTO candidates (user_id, salary_min, salary_max, cv_path) VALUES
(8, 12000.00, 18000.00, '/cvs/ahmed_benani_cv.pdf'),
(9, 15000.00, 22000.00, '/cvs/fatima_alaoui_cv.pdf'),
(10, 18000.00, 25000.00, '/cvs/mehdi_elkadi_cv.pdf'),
(11, 10000.00, 15000.00, '/cvs/samira_bennis_cv.pdf'),
(12, 20000.00, 30000.00, '/cvs/youssef_cherkaoui_cv.pdf'),
(13, 13000.00, 18000.00, '/cvs/nadia_elfassi_cv.pdf'),
(14, 16000.00, 22000.00, '/cvs/karim_mansouri_cv.pdf'),
(15, 14000.00, 19000.00, '/cvs/leila_benjelloun_cv.pdf');

-- Tags des candidats (compétences)
INSERT INTO candidate_tags (candidate_id, tag_id) VALUES
-- Ahmed (PHP/Laravel)
(8, 1), (8, 2), (8, 12), (8, 4), (8, 29), (8, 30),

-- Fatima (React/Node.js)
(9, 5), (9, 7), (9, 4), (9, 19), (9, 12), (9, 29), (9, 30),

-- Mehdi (Python/Data)
(10, 8), (10, 9), (10, 27), (10, 28), (10, 12), (10, 29), (10, 30),

-- Samira (Marketing)
(11, 21), (11, 22), (11, 23), (11, 29), (11, 30), (11, 31),

-- Youssef (Java/Full Stack)
(12, 10), (12, 11), (12, 4), (12, 12), (12, 29), (12, 30),

-- Nadia (UI/UX)
(13, 24), (13, 25), (13, 26), (13, 4), (13, 29), (13, 30),

-- Karim (Symfony)
(14, 1), (14, 3), (14, 12), (14, 4), (14, 29), (14, 30),

-- Leila (Vue.js)
(15, 1), (15, 6), (15, 4), (15, 12), (15, 29), (15, 30);

-- Offres d'emploi
INSERT INTO offers (recruiter_id, category_id, title, description, is_archived, salary_min, salary_max, location, job_type) VALUES
(2, 1, 'Développeur PHP Laravel Senior', 'Nous recherchons un développeur PHP Laravel expérimenté pour rejoindre notre équipe digitale à Rabat. Missions: développement backend, API REST, optimisation des performances.', FALSE, 18000.00, 25000.00, 'Rabat', 'CDI'),

(3, 1, 'Développeur Full Stack JavaScript', 'Poste de développeur Full Stack React/Node.js dans notre département innovation à Casablanca. Stack moderne: React, Node.js, MongoDB, Docker.', FALSE, 20000.00, 28000.00, 'Casablanca', 'CDI'),

(4, 7, 'Data Scientist', 'Rejoignez notre équipe Data Science pour travailler sur des projets d''intelligence artificielle et d''analyse prédictive. Python, Machine Learning, Big Data.', FALSE, 25000.00, 35000.00, 'Casablanca', 'CDI'),

(5, 1, 'Développeur Symfony', 'Développeur Symfony expérimenté pour notre plateforme e-commerce. Environnement agile, équipe internationale, projets innovants.', FALSE, 17000.00, 23000.00, 'Casablanca', 'CDI'),

(6, 1, 'Ingénieur DevOps', 'Poste DevOps avec AWS, Docker, Kubernetes. Automatisation des déploiements, monitoring, infrastructure cloud.', FALSE, 22000.00, 30000.00, 'Rabat', 'CDI'),

(7, 2, 'Chef de Projet Marketing Digital', 'Management d''équipe marketing, stratégie digitale, campagnes SEO/SEM, analytics. Expérience en e-commerce requise.', FALSE, 20000.00, 27000.00, 'Casablanca', 'CDI'),

(2, 1, 'Développeur Vue.js', 'Développeur frontend Vue.js pour nos applications web. Travail en équipe, méthodologie agile, environnement stimulant.', FALSE, 15000.00, 22000.00, 'Marrakech', 'CDI'),

(4, 3, 'Designer UI/UX Senior', 'Création d''interfaces utilisateur, prototypage, tests utilisateurs. Maîtrise de Figma, Adobe XD, design system.', FALSE, 16000.00, 22000.00, 'Casablanca', 'CDI'),

(3, 5, 'Analyste Financier', 'Analyse financière, reporting, contrôle de gestion. Maîtrise Excel, Power BI, connaissances comptables.', FALSE, 18000.00, 24000.00, 'Casablanca', 'CDI'),

(5, 1, 'Développeur Python Django', 'Développement backend avec Python Django, API REST, bases de données relationnelles. Environnement startup.', FALSE, 16000.00, 22000.00, 'Tanger', 'CDI');

-- Tags des offres
INSERT INTO offer_tags (offer_id, tag_id) VALUES
-- Offre 1: PHP Laravel
(1, 1), (1, 2), (1, 12), (1, 29),

-- Offre 2: React/Node.js
(2, 5), (2, 7), (2, 14), (2, 15), (2, 30),

-- Offre 3: Data Science
(3, 8), (3, 27), (3, 28), (3, 12), (3, 30),

-- Offre 4: Symfony
(4, 1), (4, 3), (4, 12), (4, 29),

-- Offre 5: DevOps
(5, 15), (5, 16), (5, 17), (5, 30),

-- Offre 6: Marketing
(6, 21), (6, 22), (6, 23), (6, 29), (6, 30),

-- Offre 7: Vue.js
(7, 6), (7, 4), (7, 18), (7, 30),

-- Offre 8: UI/UX
(8, 24), (8, 25), (8, 4), (8, 30),

-- Offre 9: Finance
(9, 29), (9, 30), (9, 31),

-- Offre 10: Python Django
(10, 8), (10, 9), (10, 12), (10, 30);

-- Candidatures
INSERT INTO applications (offer_id, candidate_id, status, cv_path, cover_letter) VALUES
(1, 8, 'pending', '/cvs/ahmed_benani_cv.pdf', 'Je suis intéressé par ce poste car j''ai 5 ans d''expérience en PHP Laravel.'),
(1, 14, 'accepted', '/cvs/karim_mansouri_cv.pdf', 'Expérience confirmée en Symfony et Laravel, je pense être le candidat idéal.'),
(2, 9, 'reviewed', '/cvs/fatima_alaoui_cv.pdf', 'Développeuse Full Stack avec expertise React/Node.js, motivée par ce challenge.'),
(3, 10, 'pending', '/cvs/mehdi_elkadi_cv.pdf', 'Data Scientist passionné par l''IA, je possède une solide expérience en Python.'),
(4, 8, 'rejected', '/cvs/ahmed_benani_cv.pdf', 'Candidature spontanée pour un poste Symfony.'),
(4, 14, 'pending', '/cvs/karim_mansouri_cv.pdf', 'Développeur Symfony expérimenté, je suis intéressé par votre offre.'),
(5, 12, 'pending', '/cvs/youssef_cherkaoui_cv.pdf', 'Ingénieur DevOps avec certification AWS, je recherche un nouveau défi.'),
(6, 11, 'accepted', '/cvs/samira_bennis_cv.pdf', 'Chef de projet marketing avec 7 ans d''expérience dans le digital.'),
(7, 15, 'pending', '/cvs/leila_benjelloun_cv.pdf', 'Développeuse Vue.js passionnée, je souhaite rejoindre votre équipe.'),
(8, 13, 'reviewed', '/cvs/nadia_elfassi_cv.pdf', 'Designer UI/UX créative, je serais ravie de contribuer à vos projets.');