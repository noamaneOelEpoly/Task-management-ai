#!/bin/bash

# Task Manager - Quick Test Runner for Linux/Mac
# Script pour exécuter rapidement les tests

echo ""
echo "╔════════════════════════════════════════════════════════════════╗"
echo "║          Task Manager - Test Runner (Linux/Mac)                ║"
echo "║         Exécution rapide des tests automatisés                 ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Vérifier PHP
if ! command -v php &> /dev/null; then
    echo -e "${RED}❌ PHP n'est pas installé${NC}"
    exit 1
fi
echo -e "${GREEN}✅ PHP trouvé${NC}"

# Vérifier PHPUnit
if [ ! -f "vendor/bin/phpunit" ]; then
    echo -e "${RED}❌ PHPUnit n'est pas installé${NC}"
    echo "   Exécutez: composer install"
    exit 1
fi
echo -e "${GREEN}✅ PHPUnit trouvé${NC}"

echo ""
echo -e "${BLUE}🎯 SÉLECTIONNEZ L'OPTION:${NC}"
echo ""
echo "  1. Exécuter TOUS les tests"
echo "  2. Tests UNITAIRES uniquement"
echo "  3. Tests FONCTIONNELS uniquement"
echo "  4. Test spécifique"
echo "  5. Avec rapport de couverture de code"
echo "  6. Mode Watch (auto-run lors des modifications)"
echo ""

read -p "Entrez votre choix (1-6): " choice

echo ""
echo -e "${BLUE}🚀 EXÉCUTION DES TESTS${NC}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""

case $choice in
    1)
        echo "▶️  Exécution de tous les tests..."
        ./vendor/bin/phpunit
        ;;
    2)
        echo "▶️  Exécution des tests unitaires..."
        ./vendor/bin/phpunit --testsuite Unit
        ;;
    3)
        echo "▶️  Exécution des tests fonctionnels..."
        ./vendor/bin/phpunit --testsuite Feature
        ;;
    4)
        echo "Fichiers de test disponibles:"
        find tests -name "*.php" -type f | sed 's/^/  • /'
        echo ""
        read -p "Entrez le nom du test (ex: TaskModelTest): " testFile
        echo ""
        echo "▶️  Exécution du test: $testFile"
        ./vendor/bin/phpunit --filter "$testFile"
        ;;
    5)
        echo "▶️  Exécution avec rapport de couverture de code..."
        echo "   (Cela peut prendre plus de temps)"
        echo ""
        ./vendor/bin/phpunit --coverage-text
        ;;
    6)
        echo "▶️  Mode Watch activé..."
        echo "   Les tests s'exécuteront automatiquement lors des modifications"
        echo ""
        ./vendor/bin/phpunit --watch
        ;;
    *)
        echo -e "${RED}❌ Choix invalide${NC}"
        exit 1
        ;;
esac

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ TOUS LES TESTS ONT RÉUSSI!${NC}"
else
    echo -e "${YELLOW}⚠️  CERTAINS TESTS ONT ÉCHOUÉ${NC}"
fi

echo ""
