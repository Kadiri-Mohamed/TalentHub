# 🔐 TalentHub – Multi-Role Authentication System (PHP MVC)

## 📌 Présentation

**TalentHub Auth System** est un projet éducatif développé en **PHP orienté objet**, visant à créer un système d’authentification **multi-rôles** réutilisable, basé sur une architecture **MVC sans framework**.

Il sert de socle technique pour une future plateforme de recrutement en gérant uniquement :
- L’authentification
- Les rôles
- La protection des routes

---

## 🎯 Fonctionnalités

### 🔐 Authentification
- Inscription (Candidate, Recruiter)
- Connexion (tous les rôles)
- Déconnexion sécurisée
- Gestion des sessions PHP
- Redirection selon le rôle après login

### 👥 Gestion des rôles
- Candidate → `/candidate/dashboard`
- Recruiter → `/recruiter/dashboard`
- Admin → `/admin/dashboard`
- Vérification du rôle à chaque accès de route protégée

---

## 🧱 Architecture

Projet structuré selon une architecture MVC :

app/
├── Controllers/
├── Models/
├── Repositories/
├── Services/
├── Views/
├── Core/
└── public/
└── index.php

---

## 🗄️ Base de données

**Tables principales :**
- `users` (id, name, email, password, role_id)
- `roles` (id, name)

### Relation :

- Role 1 ----- * User

## 👤 Auteur

**Nom** : Kadiri Mohamed  
**Année** : 2025–2026  
**Contexte** : Projet académique pour l'apprentissage du développement backend en PHP

---

## 📄 Licence

Projet éducatif - Utilisation libre pour fins d'apprentissage