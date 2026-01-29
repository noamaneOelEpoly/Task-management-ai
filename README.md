# Task Manager - Application de Gestion de Tâches

Une application web moderne de gestion de tâches développée avec **Laravel 12**, **Tailwind CSS 4** et **Vite**.

**Développeurs:** Noamane Ouldelabbar et Anas Hamma

---

## 📋 Fonctionnalités

### ✅ Gestion Complète des Tâches
- **Créer** : Ajouter de nouvelles tâches avec titre, description, catégorie et date d'échéance
- **Consulter** : Afficher toutes vos tâches avec pagination
- **Modifier** : Éditer les détails d'une tâche existante
- **Supprimer** : Supprimer définitivement une tâche
- **Marquer complétée** : Toggle d'un clic pour marquer/démarquer une tâche

### 📅 Gestion du Temps
- Dates d'échéance avec heure précise
- Détection automatique des tâches en retard
- Tri et filtrage par date
- Historique de completion avec timestamps

### 🏷️ Catégorisation
- Créer des catégories personnalisées
- Assigner des couleurs à chaque catégorie
- Filtrer les tâches par catégorie
- Comptage automatique des tâches par catégorie
- Affichage des tâches associées à chaque catégorie

### 📊 Tableau de Bord
- **Statistiques visuelles** : Total, complétées, en retard, pourcentage de progression
- **Panneau latéral** : Accès rapide aux catégories
- **Filtrage rapide** : Filtrer par catégorie directement depuis le dashboard

### 🔐 Authentification & Sécurité
- Système d'authentification utilisateur
- Policies d'autorisation pour protéger les données
- Chaque utilisateur voit et gère uniquement ses propres tâches
- Validation des formulaires côté serveur

### 📱 Design Responsive
- Interface moderne avec TailwindCSS
- Compatible mobile, tablette et desktop
- Navigation intuitive avec menu et breadcrumbs
- Formulaires validés avec messages d'erreur clairs

---

## 🛠 Stack Technologique

| Composant | Technologie | Version |
|-----------|------------|---------|
| **Framework Backend** | Laravel | 12 |
| **Langage Serveur** | PHP | 8.2+ |
| **Template Engine** | Blade | - |
| **Styling Frontend** | Tailwind CSS | 4 |
| **Build Tool** | Vite | 7 |
| **Gestionnaire de Paquets JS** | NPM | - |
| **Gestionnaire de Paquets PHP** | Composer | - |
| **Base de Données** | SQLite | - |
| **ORM** | Eloquent | Laravel 12 |

### Architecture
- **Patterns**: MVC, RESTful
- **Controllers**: TaskController, CategoryController
- **Models**: User, Task, Category
- **Policies**: TaskPolicy, CategoryPolicy
- **Migrations**: Schema versionnée avec timestamps

---

## 🚀 Installation et Utilisation

### Prérequis
- PHP 8.2 ou supérieur
- Composer (gestionnaire de dépendances PHP)
- Node.js 18+ et NPM
- SQLite (inclus par défaut avec PHP)

### Installation Automatisée (Windows)

Exécutez le script setup.ps1 fourni :
```powershell
powershell -ExecutionPolicy Bypass -File setup.ps1
```

Ce script fera automatiquement :
- Vérification de PHP, Composer et Node.js
- Installation des dépendances PHP (`composer install`)
- Installation des dépendances NPM (`npm install`)
- Génération de la clé d'application
- Création de la base de données SQLite
- Exécution des migrations et seeders
- Compilation des assets
- Affichage des instructions de lancement

### Installation Manuelle

1. **Installer les dépendances PHP**
```bash
composer install
```

2. **Installer les dépendances Node**
```bash
npm install
```

3. **Configurer l'application**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Créer la base de données**
```bash
# Windows
fsutil file createnew database/database.sqlite 0

# Linux/Mac
touch database/database.sqlite
```

5. **Exécuter les migrations et seeders**
```bash
php artisan migrate:fresh --seed
```

6. **Compiler les assets**
```bash
npm run build
```

---

## 🎯 Lancer l'Application

### Mode Développement
```bash
# Terminal 1 - Serveur Laravel (écoute sur http://localhost:8000)
php artisan serve

# Terminal 2 - Build Vite avec hot reload
npm run dev
```

Ouvrez votre navigateur et allez à `http://localhost:8000`

### Identifiants de Test
- **Email** : test@example.com
- **Mot de passe** : password

### Mode Production
```bash
npm run build
php artisan migrate --force
php artisan serve
```

---

## 📖 Guide Utilisateur Simple

### Créer une Tâche
1. Cliquez sur le bouton **"+ Nouvelle Tâche"** dans le dashboard
2. Remplissez les champs :
   - **Titre** (requis) : Le nom de votre tâche
   - **Description** (optionnel) : Détails supplémentaires
   - **Catégorie** (optionnel) : Choisissez ou créez une catégorie
   - **Date d'échéance** (optionnel) : Quand doit-elle être terminée ?
3. Cliquez sur **"Créer la tâche"**

**Exemple:**
```
Titre: Faire l'épicerie
Description: Lait, pain, œufs, fruits frais
Catégorie: Courses
Date d'échéance: 30/01/2025 18:00
```

### Gérer une Tâche
- **Consulter** : Cliquez sur le titre pour voir les détails
- **Marquer complétée** : Cliquez la case à cocher ou utilisez le lien "Marquer complétée"
- **Éditer** : Cliquez l'icône ✎ (crayon)
- **Supprimer** : Cliquez l'icône 🗑 (poubelle) et confirmez

### Gérer les Catégories
1. Accédez à la section **"Catégories"** via le menu
2. **Créer** : Cliquez **"+ Nouvelle Catégorie"** et remplissez :
   - **Nom** : Le nom de la catégorie
   - **Description** (optionnel) : À quoi sert cette catégorie
   - **Couleur** (optionnel) : Choisissez une couleur personnalisée
3. **Modifier ou Supprimer** : Cliquez les icônes ✎ ou 🗑

### Filtrer et Rechercher
- Utilisez le **panneau latéral** pour filtrer les tâches par catégorie
- Cliquez sur une catégorie pour voir uniquement ses tâches
- Le tableau de bord affiche automatiquement les statistiques filtrées

---

## 📂 Structure du Projet

```
ProjetPHP/
├── app/
│   ├── Http/Controllers/
│   │   ├── TaskController.php          # Logique CRUD des tâches
│   │   └── CategoryController.php      # Logique CRUD des catégories
│   ├── Models/
│   │   ├── User.php                    # Modèle utilisateur
│   │   ├── Task.php                    # Modèle tâche avec helpers
│   │   └── Category.php                # Modèle catégorie
│   └── Policies/
│       ├── TaskPolicy.php              # Autorisation des tâches
│       └── CategoryPolicy.php          # Autorisation des catégories
│
├── database/
│   ├── migrations/
│   │   ├── *_create_users_table.php
│   │   ├── *_create_categories_table.php
│   │   └── *_create_tasks_table.php
│   ├── seeders/
│   │   └── DatabaseSeeder.php          # Données d'exemple
│   └── database.sqlite                 # Base de données
│
├── resources/
│   ├── css/
│   │   └── app.css                     # Styles Tailwind CSS
│   ├── js/
│   │   ├── app.js
│   │   └── bootstrap.js
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php           # Layout principal
│       ├── tasks/
│       │   ├── index.blade.php         # Liste et statistiques
│       │   ├── create.blade.php        # Créer une tâche
│       │   ├── edit.blade.php          # Éditer une tâche
│       │   └── show.blade.php          # Détails d'une tâche
│       └── categories/
│           ├── index.blade.php         # Liste des catégories
│           ├── create.blade.php        # Créer une catégorie
│           ├── edit.blade.php          # Éditer une catégorie
│           └── show.blade.php          # Tâches d'une catégorie
│
├── routes/
│   ├── web.php                         # Routes de l'application
│   └── auth.php                        # Routes d'authentification
│
├── config/
│   ├── app.php
│   ├── database.php                    # Configuration SQLite
│   └── autres configurations
│
├── composer.json                       # Dépendances PHP
├── package.json                        # Dépendances NPM
├── tailwind.config.js                  # Configuration Tailwind
├── vite.config.js                      # Configuration Vite
├── .env.example                        # Exemple de configuration
└── setup.ps1                           # Script d'installation
```

---

## 🔄 Workflow Typique

1. **Authentification** → Connexion avec test@example.com / password
2. **Dashboard** → Voir statistiques et filtres
3. **Créer tâche** → Utiliser le bouton "+ Nouvelle Tâche"
4. **Organiser** → Créer des catégories pour mieux classer
5. **Gérer** → Éditer, marquer complétée ou supprimer les tâches
6. **Consulter stats** → Suivre sa progression en temps réel

---

## 🎓 Points Clés

### Permissions
- Seul le propriétaire d'une tâche peut la modifier ou la supprimer
- Chaque utilisateur a son propre espace isolé
- Les données sont protégées au niveau base de données et application

### Performance
- Pagination des listes (10 éléments par défaut)
- Eager loading des relations
- Caching et optimisations Laravel intégrés

### Validation
- Validation côté serveur pour tous les formulaires
- Messages d'erreur clairs et localisés
- Confirmations pour les opérations destructrices

---

## 📞 Support

Pour toute question ou problème :
- Consultez le fichier de configuration .env
- Vérifiez les logs dans `storage/logs/`
- Assurez-vous que PHP, Composer et Node.js sont à jour

---

**Version:** 1.0.0  
**Licence:** MIT  
**Dernière mise à jour:** 29 janvier 2026
