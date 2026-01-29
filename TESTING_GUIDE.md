# Tests & Screenshots - Task Manager

Guide complet pour exécuter les tests automatisés et générer les screenshots de l'application.

---

## 📋 Table des Matières

1. [Tests Automatisés](#tests-automatisés)
2. [Screenshots](#screenshots)
3. [Automatisation Complète](#automatisation-complète)
4. [Structure des Tests](#structure-des-tests)
5. [Résolution des Problèmes](#résolution-des-problèmes)

---

## 🧪 Tests Automatisés

### Aperçu

Le projet contient des tests complets utilisant **PHPUnit** :

- **Tests Unitaires** : Validation des modèles (User, Task, Category)
- **Tests Fonctionnels** : Validation des contrôleurs et des routes

### Exécuter les Tests

#### Option 1 : Menu Interactif (Recommandé)

```powershell
powershell -ExecutionPolicy Bypass -File run-tests.ps1
```

Vous pouvez ensuite choisir :
- 1: Exécuter TOUS les tests
- 2: Tests unitaires uniquement
- 3: Tests fonctionnels uniquement
- 4: Test spécifique
- 5: Avec rapport de couverture de code
- 6: Avec rapport HTML

#### Option 2 : Ligne de Commande Directe

```bash
# Tous les tests
vendor/bin/phpunit

# Tests unitaires uniquement
vendor/bin/phpunit --testsuite Unit

# Tests fonctionnels uniquement
vendor/bin/phpunit --testsuite Feature

# Test spécifique
vendor/bin/phpunit --filter TaskModelTest

# Avec rapport de couverture
vendor/bin/phpunit --coverage-text

# Rapport HTML (dans coverage/index.html)
vendor/bin/phpunit --coverage-html coverage
```

### Fichiers de Test

#### Tests Unitaires

```
tests/Unit/
├── TaskModelTest.php          # Tests du modèle Task
├── CategoryModelTest.php      # Tests du modèle Category
└── ExampleTest.php            # Exemple de base
```

**TaskModelTest.php** contient :
- ✅ Création de tâche
- ✅ Relations (user, category)
- ✅ Marquage comme complétée
- ✅ Suppression
- ✅ Détection des tâches en retard
- ✅ Assignation des statuts
- ✅ Validation du titre

**CategoryModelTest.php** contient :
- ✅ Création de catégorie
- ✅ Relations (user, tasks)
- ✅ Suppression et modification
- ✅ Couleur par défaut
- ✅ Comptage des tâches
- ✅ Validation du nom

#### Tests Fonctionnels

```
tests/Feature/
├── TaskControllerTest.php      # Tests du contrôleur Task
├── CategoryControllerTest.php  # Tests du contrôleur Category
├── ProfileTest.php             # Tests du profil utilisateur
└── Auth/                        # Tests d'authentification
```

**TaskControllerTest.php** contient :
- ✅ Affichage du dashboard
- ✅ Affichage du formulaire de création
- ✅ Création d'une tâche
- ✅ Affichage des détails
- ✅ Affichage du formulaire d'édition
- ✅ Modification d'une tâche
- ✅ Suppression d'une tâche
- ✅ Permissions (ne pas modifier les tâches d'autres)
- ✅ Filtrage par catégorie
- ✅ Validation des données

**CategoryControllerTest.php** contient :
- ✅ Affichage de la liste
- ✅ Affichage du formulaire de création
- ✅ Création d'une catégorie
- ✅ Affichage des détails
- ✅ Modification
- ✅ Suppression
- ✅ Permissions
- ✅ Validation des données

### Résultats Attendus

Lors de l'exécution réussie des tests, vous verrez :

```
PHPUnit 10.5.x by Sebastian Bergmann and contributors.

Unit (8 tests, 0 assertions)
.........  8 / 8 (100%)

Feature (12 tests, 45 assertions)
............  12 / 12 (100%)

OK (20 tests, 45 assertions)
```

---

## 📸 Screenshots

### Fonctionnalité

Le script `generate-screenshots.ps1` capture automatiquement :

- **01-login.png** : Page de connexion
- **02-dashboard.png** : Tableau de bord (statistiques, filtrage)
- **03-create-task.png** : Formulaire de création de tâche
- **04-categories.png** : Gestion des catégories
- **05-create-category.png** : Formulaire de création de catégorie

### Générer les Screenshots

#### Prérequis

1. **Le serveur Laravel doit être en cours d'exécution** :
```bash
# Terminal 1
php artisan serve
```

2. **Vite doit compiler les assets** :
```bash
# Terminal 2
npm run dev
```

#### Exécution

```powershell
powershell -ExecutionPolicy Bypass -File generate-screenshots.ps1
```

Le script va :
- Installer Puppeteer si nécessaire
- Se connecter avec `test@example.com / password`
- Naviguer sur chaque page
- Prendre des captures d'écran
- Sauvegarder les PNG dans `screenshots/`

### Localisation des Screenshots

```
screenshots/
├── 01-login.png
├── 02-dashboard.png
├── 03-create-task.png
├── 04-categories.png
└── 05-create-category.png
```

### Utilisation

Vous pouvez utiliser ces screenshots pour :
- 📖 Documentation du projet
- 🎓 Présentations et démonstrations
- 📚 README amélioré
- 🖼️ Portfolio professionnel

---

## 🤖 Automatisation Complète

### Script Unifié

Exécutez tout en une seule commande :

```powershell
powershell -ExecutionPolicy Bypass -File run-automation.ps1
```

### Options du Menu

```
1. Tests UNIQUEMENT
2. Screenshots UNIQUEMENT
3. Tests + Screenshots (COMPLET)
4. Tests + Rapport couverture + Screenshots
```

### Résultat

Le script génère automatiquement :
- ✅ Rapport des tests
- ✅ Rapport de couverture de code (optionnel)
- ✅ Screenshots des interfaces
- ✅ Fichier de log détaillé

Les logs sont sauvegardés dans `logs/automation/automation_YYYY-MM-DD_HH-MM-SS.log`

---

## 📊 Structure des Tests

### Arborescence

```
tests/
├── TestCase.php                    # Classe de base
├── Unit/
│   ├── TaskModelTest.php
│   ├── CategoryModelTest.php
│   └── ExampleTest.php
├── Feature/
│   ├── TaskControllerTest.php
│   ├── CategoryControllerTest.php
│   ├── ProfileTest.php
│   └── Auth/
└── phpunit.xml                     # Configuration
```

### Configuration PHPUnit

Le fichier `phpunit.xml` configure :
- Base de données SQLite en mémoire pour les tests
- Cache et sessions en mémoire
- Suites de tests (Unit, Feature)
- Répertoire de couverture

---

## 🔧 Résolution des Problèmes

### Les tests échouent

**Problème** : `Class 'Tests\TestCase' not found`

**Solution** :
```bash
composer install
composer dump-autoload
```

### PHPUnit non trouvé

**Solution** :
```bash
composer require --dev phpunit/phpunit
```

### Le serveur n'est pas accessible pour les screenshots

**Solution** :
1. Ouvrez un terminal
2. Exécutez `php artisan serve`
3. Attendez le message "Server running on..."
4. Puis exécutez le script de screenshots

### Puppeteer ne compile pas

**Solution** :
```bash
npm install puppeteer --save-dev
```

Si le problème persiste, installez les dépendances système :
- **Windows** : Aucune dépendance supplémentaire
- **Linux** : `sudo apt-get install gconf-service libasound2 libatk1.0-0 ...`
- **macOS** : Devrait fonctionner automatiquement

### Screenshot blanc/vide

**Vérifiez** :
- Le serveur Laravel est en cours d'exécution
- L'URL est correcte (http://localhost:8000)
- Les assets sont compilés (`npm run dev`)
- L'authentification fonctionne

---

## 📈 Métriques et Rapports

### Couverture de Code

Générez un rapport détaillé :

```bash
vendor/bin/phpunit --coverage-html coverage
```

Puis ouvrez `coverage/index.html` dans un navigateur.

### Rapport de Test Détaillé

```bash
vendor/bin/phpunit --verbose
```

Affiche chaque test individuel avec ses résultats.

### Filtrer des Tests

```bash
# Exécuter un test spécifique
vendor/bin/phpunit --filter "test_can_create_task"

# Exécuter tous les tests d'un fichier
vendor/bin/phpunit --filter "TaskModelTest"

# Exécuter avec un mot-clé
vendor/bin/phpunit --filter "task"
```

---

## ✨ Bonnes Pratiques

### Avant de Committer du Code

```bash
# 1. Exécuter les tests
vendor/bin/phpunit

# 2. Vérifier la couverture
vendor/bin/phpunit --coverage-text

# 3. Vérifier les standards
./vendor/bin/phpstan analyse app/

# 4. Formater le code
./vendor/bin/pint
```

### Ajouter un Nouveau Test

1. Créez le fichier dans `tests/Unit/` ou `tests/Feature/`
2. Étendez `Tests\TestCase`
3. Utilisez `RefreshDatabase` pour les tests avec BD
4. Écrivez les cas de test

Exemple :
```php
<?php
namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MyTest extends TestCase
{
    use RefreshDatabase;

    public function test_something()
    {
        $task = Task::factory()->create();
        $this->assertTrue($task->exists);
    }
}
```

---

## 📞 Support

Pour plus d'informations :
- 📖 [Documentation PHPUnit](https://phpunit.de/documentation.html)
- 📖 [Documentation Puppeteer](https://pptr.dev/)
- 📖 [Laravel Testing](https://laravel.com/docs/testing)

---

**Version:** 1.0.0  
**Dernière mise à jour:** 29 janvier 2026
