# ╔════════════════════════════════════════════════════════════════╗
# ║              TASK MANAGER - SETUP SCRIPT                        ║
# ║         Application de Gestion de Tâches Laravel                ║
# ║        Développeurs: Noamane Ouldelabbar, Anas Hamma            ║
# ╚════════════════════════════════════════════════════════════════╝

Write-Host ""
Write-Host "╔════════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║              TASK MANAGER - SETUP SCRIPT                        ║" -ForegroundColor Cyan
Write-Host "║              Installation Automatisée Windows                   ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

# ═══════════════════════════════════════════════════════════════════
# VÉRIFICATION DES PRÉREQUIS
# ═══════════════════════════════════════════════════════════════════

Write-Host "🔍 Vérification des prérequis..." -ForegroundColor Blue
Write-Host ""

# Vérifier PHP
Write-Host "  Vérification de PHP..." -ForegroundColor Gray
if (!(Get-Command php -ErrorAction SilentlyContinue)) {
    Write-Host "  ❌ PHP n'est pas installé ou non accessible" -ForegroundColor Red
    Write-Host "     Installez PHP 8.2+ depuis https://www.php.net/downloads" -ForegroundColor Yellow
    exit 1
}
$phpVersion = php -v | Select-Object -First 1
Write-Host "  ✅ PHP trouvé: $phpVersion" -ForegroundColor Green

# Vérifier Composer
Write-Host "  Vérification de Composer..." -ForegroundColor Gray
if (!(Get-Command composer -ErrorAction SilentlyContinue)) {
    Write-Host "  ❌ Composer n'est pas installé ou non accessible" -ForegroundColor Red
    Write-Host "     Installez Composer depuis https://getcomposer.org/" -ForegroundColor Yellow
    exit 1
}
$composerVersion = composer --version
Write-Host "  ✅ Composer trouvé: $composerVersion" -ForegroundColor Green

# Vérifier Node.js
Write-Host "  Vérification de Node.js..." -ForegroundColor Gray
if (!(Get-Command node -ErrorAction SilentlyContinue)) {
    Write-Host "  ❌ Node.js n'est pas installé ou non accessible" -ForegroundColor Red
    Write-Host "     Installez Node.js 18+ depuis https://nodejs.org/" -ForegroundColor Yellow
    exit 1
}
$nodeVersion = node -v
Write-Host "  ✅ Node.js trouvé: $nodeVersion" -ForegroundColor Green

Write-Host ""

# ═══════════════════════════════════════════════════════════════════
# INSTALLATION DES DÉPENDANCES
# ═══════════════════════════════════════════════════════════════════

Write-Host "📦 Installation des dépendances..." -ForegroundColor Blue
Write-Host ""

Write-Host "  📥 Installation des dépendances PHP (composer install)..." -ForegroundColor Cyan
composer install
if ($LASTEXITCODE -ne 0) {
    Write-Host "  ❌ Erreur lors de l'installation des dépendances PHP" -ForegroundColor Red
    exit 1
}
Write-Host "  ✅ Dépendances PHP installées" -ForegroundColor Green

Write-Host ""

Write-Host "  📥 Installation des dépendances NPM (npm install)..." -ForegroundColor Cyan
npm install
if ($LASTEXITCODE -ne 0) {
    Write-Host "  ❌ Erreur lors de l'installation des dépendances NPM" -ForegroundColor Red
    exit 1
}
Write-Host "  ✅ Dépendances NPM installées" -ForegroundColor Green

Write-Host ""

# ═══════════════════════════════════════════════════════════════════
# CONFIGURATION DE L'APPLICATION
# ═══════════════════════════════════════════════════════════════════

Write-Host "⚙️  Configuration de l'application..." -ForegroundColor Blue
Write-Host ""

Write-Host "  🔑 Génération de la clé d'application..." -ForegroundColor Cyan
php artisan key:generate
if ($LASTEXITCODE -ne 0) {
    Write-Host "  ❌ Erreur lors de la génération de la clé" -ForegroundColor Red
    exit 1
}
Write-Host "  ✅ Clé d'application générée" -ForegroundColor Green

Write-Host ""

# ═══════════════════════════════════════════════════════════════════
# BASE DE DONNÉES
# ═══════════════════════════════════════════════════════════════════

Write-Host "🗄️  Configuration de la base de données SQLite..." -ForegroundColor Blue
Write-Host ""

Write-Host "  📁 Création du fichier database.sqlite..." -ForegroundColor Cyan
$dbPath = "database\database.sqlite"
if (!(Test-Path $dbPath)) {
    New-Item -ItemType File -Path $dbPath -Force | Out-Null
    Write-Host "  ✅ Fichier database.sqlite créé" -ForegroundColor Green
} else {
    Write-Host "  ⚠️  Fichier database.sqlite existe déjà" -ForegroundColor Yellow
}

Write-Host ""

Write-Host "  🔄 Exécution des migrations et seeders..." -ForegroundColor Cyan
php artisan migrate:fresh --seed
if ($LASTEXITCODE -ne 0) {
    Write-Host "  ❌ Erreur lors de la migration/seeding" -ForegroundColor Red
    exit 1
}
Write-Host "  ✅ Migrations et seeders exécutés" -ForegroundColor Green

Write-Host ""

# ═══════════════════════════════════════════════════════════════════
# COMPILATION DES ASSETS
# ═══════════════════════════════════════════════════════════════════

Write-Host "🎨 Compilation des assets (CSS, JavaScript)..." -ForegroundColor Blue
Write-Host ""

Write-Host "  🔨 Build Vite..." -ForegroundColor Cyan
npm run build
if ($LASTEXITCODE -ne 0) {
    Write-Host "  ❌ Erreur lors du build Vite" -ForegroundColor Red
    exit 1
}
Write-Host "  ✅ Assets compilés avec succès" -ForegroundColor Green

Write-Host ""

# ═══════════════════════════════════════════════════════════════════
# SUCCÈS - AFFICHER LES INSTRUCTIONS
# ═══════════════════════════════════════════════════════════════════

Write-Host "╔════════════════════════════════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║          ✅ INSTALLATION COMPLÉTÉE AVEC SUCCÈS !               ║" -ForegroundColor Green
Write-Host "╚════════════════════════════════════════════════════════════════╝" -ForegroundColor Green

Write-Host ""
Write-Host "🚀 PROCHAINES ÉTAPES - LANCER L'APPLICATION:" -ForegroundColor Cyan
Write-Host ""

Write-Host "  1️⃣  Ouvrez DEUX terminaux PowerShell (ou Terminal VS Code)" -ForegroundColor White
Write-Host ""

Write-Host "  2️⃣  Dans le PREMIER terminal, lancez le serveur Laravel:" -ForegroundColor White
Write-Host "      cd c:\Users\noamane\ProjetPHP" -ForegroundColor Yellow
Write-Host "      php artisan serve" -ForegroundColor Yellow
Write-Host ""

Write-Host "  3️⃣  Dans le DEUXIÈME terminal, lancez le build Vite:" -ForegroundColor White
Write-Host "      cd c:\Users\noamane\ProjetPHP" -ForegroundColor Yellow
Write-Host "      npm run dev" -ForegroundColor Yellow
Write-Host ""

Write-Host "  4️⃣  Ouvrez votre navigateur et allez à:" -ForegroundColor White
Write-Host "      http://localhost:8000" -ForegroundColor Cyan
Write-Host ""

Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Gray
Write-Host ""
Write-Host "👤 COMPTE DE TEST:" -ForegroundColor White
Write-Host "   Email:     test@example.com" -ForegroundColor Cyan
Write-Host "   Mot passe: password" -ForegroundColor Cyan
Write-Host ""

Write-Host "📖 DOCUMENTATION:" -ForegroundColor White
Write-Host "   Consultez le fichier README.md pour plus d'informations" -ForegroundColor Gray
Write-Host ""

Write-Host "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" -ForegroundColor Gray
Write-Host ""
Write-Host "Appuyez sur une touche pour fermer ce script..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

php artisan migrate:fresh --seed

Write-Host ""
Write-Host "📦 Compilation des assets..." -ForegroundColor Blue
npm run build

Write-Host ""
Write-Host "================================" -ForegroundColor Green
Write-Host "✓ Installation complétée!" -ForegroundColor Green
Write-Host "================================" -ForegroundColor Green
Write-Host ""
Write-Host "Pour démarrer l'application:" -ForegroundColor Yellow
Write-Host ""
Write-Host "Terminal 1: php artisan serve" -ForegroundColor Blue
Write-Host "Terminal 2: npm run dev" -ForegroundColor Blue
Write-Host ""
Write-Host "Puis visitez: http://localhost:8000" -ForegroundColor Cyan
Write-Host ""
Write-Host "Identifiants de test:" -ForegroundColor Yellow
Write-Host "Email: test@example.com"
Write-Host "Mot de passe: password"
Write-Host ""
