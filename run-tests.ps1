# Task Manager - Test Runner Script
# Script pour exécuter tous les tests automatiquement

Write-Host "`n╔════════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║          Task Manager - Test Runner                            ║" -ForegroundColor Cyan
Write-Host "║         Exécution automatisée des tests                         ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════════════╝`n" -ForegroundColor Cyan

# Vérifier PHP
if (!(Get-Command php -ErrorAction SilentlyContinue)) {
    Write-Host "❌ PHP n'est pas installé" -ForegroundColor Red
    exit 1
}

# Vérifier PHPUnit
$composerBin = "vendor\bin\phpunit.bat"
if (!(Test-Path $composerBin)) {
    Write-Host "❌ PHPUnit n'est pas installé. Exécutez: composer install" -ForegroundColor Red
    exit 1
}

Write-Host "═══════════════════════════════════════════════════════════════════" -ForegroundColor Gray
Write-Host "📊 CONFIGURATION DES TESTS" -ForegroundColor Cyan
Write-Host "═══════════════════════════════════════════════════════════════════" -ForegroundColor Gray
Write-Host ""
Write-Host "  Framework:        PHPUnit" -ForegroundColor White
Write-Host "  Répertoire tests: tests/" -ForegroundColor White
Write-Host "  Configuration:    phpunit.xml" -ForegroundColor White
Write-Host ""

# Afficher les fichiers de test disponibles
Write-Host "📋 FICHIERS DE TEST DÉTECTÉS:" -ForegroundColor Cyan
Write-Host ""

$unitTests = Get-ChildItem -Path "tests\Unit" -Filter "*.php" -Recurse | Measure-Object
$featureTests = Get-ChildItem -Path "tests\Feature" -Filter "*.php" -Recurse | Measure-Object

Write-Host "  Tests Unitaires:  $($unitTests.Count) fichiers" -ForegroundColor Yellow
Get-ChildItem -Path "tests\Unit" -Filter "*.php" -Recurse | ForEach-Object {
    Write-Host "    • $($_.Name)" -ForegroundColor Gray
}

Write-Host ""
Write-Host "  Tests Fonctionnels: $($featureTests.Count) fichiers" -ForegroundColor Yellow
Get-ChildItem -Path "tests\Feature" -Filter "*.php" -Recurse | ForEach-Object {
    Write-Host "    • $($_.Name)" -ForegroundColor Gray
}

Write-Host ""
Write-Host "═══════════════════════════════════════════════════════════════════" -ForegroundColor Gray
Write-Host ""

# Menu de sélection
Write-Host "🎯 SÉLECTIONNEZ L'OPTION:" -ForegroundColor Cyan
Write-Host ""
Write-Host "  1. Exécuter TOUS les tests" -ForegroundColor White
Write-Host "  2. Exécuter les tests UNITAIRES uniquement" -ForegroundColor White
Write-Host "  3. Exécuter les tests FONCTIONNELS uniquement" -ForegroundColor White
Write-Host "  4. Exécuter un test spécifique" -ForegroundColor White
Write-Host "  5. Exécuter avec rapport de couverture de code" -ForegroundColor White
Write-Host "  6. Exécuter et générer un rapport HTML" -ForegroundColor White
Write-Host ""

$choice = Read-Host "Entrez votre choix (1-6)"

Write-Host ""
Write-Host "═══════════════════════════════════════════════════════════════════" -ForegroundColor Gray
Write-Host "🚀 EXÉCUTION DES TESTS" -ForegroundColor Cyan
Write-Host "═══════════════════════════════════════════════════════════════════" -ForegroundColor Gray
Write-Host ""

switch ($choice) {
    "1" {
        Write-Host "▶️  Exécution de tous les tests..." -ForegroundColor Cyan
        & $composerBin
    }
    "2" {
        Write-Host "▶️  Exécution des tests unitaires..." -ForegroundColor Cyan
        & $composerBin --testsuite Unit
    }
    "3" {
        Write-Host "▶️  Exécution des tests fonctionnels..." -ForegroundColor Cyan
        & $composerBin --testsuite Feature
    }
    "4" {
        Write-Host "Fichiers de test disponibles:" -ForegroundColor White
        Get-ChildItem -Path "tests" -Filter "*.php" -Recurse | Select-Object -ExpandProperty Name | ForEach-Object { Write-Host "  • $_" -ForegroundColor Gray }
        Write-Host ""
        $testFile = Read-Host "Entrez le nom du fichier de test (ex: TaskModelTest.php)"
        Write-Host ""
        Write-Host "▶️  Exécution du test: $testFile" -ForegroundColor Cyan
        & $composerBin --filter $testFile
    }
    "5" {
        Write-Host "▶️  Exécution avec rapport de couverture de code..." -ForegroundColor Cyan
        Write-Host "   (Cela peut prendre plus de temps)" -ForegroundColor Yellow
        Write-Host ""
        & $composerBin --coverage-text
    }
    "6" {
        Write-Host "▶️  Exécution avec rapport HTML..." -ForegroundColor Cyan
        Write-Host "   Rapport sera généré dans: coverage/index.html" -ForegroundColor Yellow
        Write-Host ""
        & $composerBin --coverage-html coverage
        Write-Host ""
        Write-Host "✅ Rapport HTML généré!" -ForegroundColor Green
        Write-Host "   Ouvrir: coverage/index.html" -ForegroundColor Cyan
    }
    default {
        Write-Host "❌ Choix invalide" -ForegroundColor Red
        exit 1
    }
}

Write-Host ""
Write-Host "═══════════════════════════════════════════════════════════════════" -ForegroundColor Gray

if ($LASTEXITCODE -eq 0) {
    Write-Host "✅ TOUS LES TESTS ONT RÉUSSI!" -ForegroundColor Green
} else {
    Write-Host "⚠️  CERTAINS TESTS ONT ÉCHOUÉ" -ForegroundColor Yellow
    Write-Host "    Vérifiez les messages d'erreur ci-dessus" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "📖 Pour plus de détails, consultez:" -ForegroundColor Cyan
Write-Host "   • phpunit.xml - Configuration des tests" -ForegroundColor Gray
Write-Host "   • tests/ - Répertoire des tests" -ForegroundColor Gray
Write-Host ""

Write-Host "Appuyez sur une touche pour fermer..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
