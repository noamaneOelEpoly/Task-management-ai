# Task Manager - Full Automation Script
# Script qui execute les tests ET genere les screenshots automatiquement

Write-Host ""
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host "        TASK MANAGER - AUTOMATION COMPLETE" -ForegroundColor Cyan
Write-Host "     Tests + Screenshots + Reports" -ForegroundColor Cyan
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host ""

# Variables de configuration
$startTime = Get-Date
$logsDir = "logs/automation"
$timestamp = $startTime.ToString("yyyy-MM-dd_HH-mm-ss")
$logFile = "$logsDir/automation_$timestamp.log"

# Créer le dossier de logs
if (!(Test-Path $logsDir)) {
    New-Item -ItemType Directory -Path $logsDir -Force | Out-Null
}

# Fonction pour logger
function LogMessage {
    param([string]$message, [string]$level = "INFO")
    $logEntry = "[$(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')] [$level] $message"
    Add-Content -Path $logFile -Value $logEntry
    
    switch ($level) {
        "SUCCESS" { Write-Host $message -ForegroundColor Green }
        "ERROR" { Write-Host $message -ForegroundColor Red }
        "WARNING" { Write-Host $message -ForegroundColor Yellow }
        "INFO" { Write-Host $message -ForegroundColor Cyan }
        default { Write-Host $message }
    }
}

# Demarrer le logging
LogMessage "================================================================" "INFO"
LogMessage "                    STARTING AUTOMATION" "INFO"
LogMessage "================================================================" "INFO"
LogMessage "" "INFO"

# Verifier les prerequis
LogMessage "Checking prerequisites..." "INFO"

$prereqsOk = $true

# Verifier PHP
if (!(Get-Command php -ErrorAction SilentlyContinue)) {
    LogMessage "ERROR: PHP is not installed" "ERROR"
    $prereqsOk = $false
} else {
    LogMessage "OK: PHP found" "SUCCESS"
}

# Verifier Composer
if (!(Get-Command composer -ErrorAction SilentlyContinue)) {
    LogMessage "ERROR: Composer is not installed" "ERROR"
    $prereqsOk = $false
} else {
    LogMessage "OK: Composer found" "SUCCESS"
}

# Verifier Node.js
if (!(Get-Command node -ErrorAction SilentlyContinue)) {
    LogMessage "ERROR: Node.js is not installed" "ERROR"
    $prereqsOk = $false
} else {
    LogMessage "OK: Node.js found" "SUCCESS"
}

if (!$prereqsOk) {
    LogMessage "" "INFO"
    LogMessage "ERROR: CANNOT CONTINUE" "ERROR"
    LogMessage "Install missing components" "ERROR"
    exit 1
}

LogMessage "" "INFO"
LogMessage "================================================================" "INFO"
LogMessage ""

# Menu d'options
Write-Host "SELECT ACTIONS TO PERFORM:" -ForegroundColor Cyan
Write-Host ""
Write-Host "  1. Run TESTS ONLY" -ForegroundColor White
Write-Host "  2. Generate SCREENSHOTS ONLY" -ForegroundColor White
Write-Host "  3. Tests + Screenshots (COMPLETE)" -ForegroundColor White
Write-Host "  4. Tests + Coverage Report + Screenshots" -ForegroundColor White
Write-Host ""

$option = Read-Host "Enter your choice (1-4)"

LogMessage "Option selected: $option" "INFO"
LogMessage "" "INFO"

# Determiner les actions
$runTests = $false
$runScreenshots = $false
$runCoverage = $false

switch ($option) {
    "1" { $runTests = $true }
    "2" { $runScreenshots = $true }
    "3" { $runTests = $true; $runScreenshots = $true }
    "4" { $runTests = $true; $runCoverage = $true; $runScreenshots = $true }
    default { LogMessage "ERROR: Invalid choice" "ERROR"; exit 1 }
}

# ================================================================
# EXECUTE TESTS
# ================================================================

if ($runTests) {
    LogMessage "================================================================" "INFO"
    LogMessage "RUNNING TESTS" "INFO"
    LogMessage "================================================================" "INFO"
    LogMessage "" "INFO"
    
    $composerBin = "vendor\bin\phpunit.bat"
    
    if (!(Test-Path $composerBin)) {
        LogMessage "ERROR: PHPUnit is not installed" "ERROR"
        LogMessage "   Run: composer install" "ERROR"
    } else {
        LogMessage "Executing PHPUnit..." "INFO"
        LogMessage "" "INFO"
        
        if ($runCoverage) {
            LogMessage "Mode: With coverage report" "INFO"
            & $composerBin --coverage-text 2>&1 | Tee-Object -FilePath $logFile -Append
        } else {
            & $composerBin 2>&1 | Tee-Object -FilePath $logFile -Append
        }
        
        if ($LASTEXITCODE -eq 0) {
            LogMessage "" "INFO"
            LogMessage "SUCCESS: ALL TESTS PASSED!" "SUCCESS"
        } else {
            LogMessage "" "INFO"
            LogMessage "WARNING: SOME TESTS FAILED" "WARNING"
        }
    }
    
    LogMessage "" "INFO"
}

# ================================================================
# GENERATE SCREENSHOTS
# ================================================================

if ($runScreenshots) {
    LogMessage "================================================================" "INFO"
    LogMessage "GENERATING SCREENSHOTS" "INFO"
    LogMessage "================================================================" "INFO"
    LogMessage "" "INFO"
    
    # Verifier si le serveur est en cours d'execution
    LogMessage "Checking Laravel server..." "INFO"
    
    $serverCheck = Invoke-WebRequest -Uri "http://localhost:8000" -Method Get -ErrorAction SilentlyContinue
    
    if (!$serverCheck) {
        LogMessage "WARNING: Laravel server is not accessible" "WARNING"
        LogMessage "   URL tested: http://localhost:8000" "WARNING"
        LogMessage "" "INFO"
        LogMessage "To generate screenshots:" "INFO"
        LogMessage "  1. Open a new terminal" "INFO"
        LogMessage "  2. Run: php artisan serve" "INFO"
        LogMessage "  3. Then run this script again" "INFO"
        LogMessage "" "INFO"
        
        $continueScreenshots = Read-Host "Continue anyway? (y/n)"
        
        if ($continueScreenshots -ne "y") {
            LogMessage "ERROR: Screenshot generation cancelled" "WARNING"
            $runScreenshots = $false
        }
    } else {
        LogMessage "OK: Laravel server is accessible" "SUCCESS"
        LogMessage "" "INFO"
    }
    
    if ($runScreenshots) {
        LogMessage "Running capture script..." "INFO"
        
        if (Test-Path "generate-screenshots.ps1") {
            & .\generate-screenshots.ps1 2>&1 | Tee-Object -FilePath $logFile -Append
            
            if ($LASTEXITCODE -eq 0) {
                LogMessage "SUCCESS: Screenshots generated!" "SUCCESS"
            } else {
                LogMessage "WARNING: Error generating screenshots" "WARNING"
            }
        } else {
            LogMessage "ERROR: generate-screenshots.ps1 not found" "ERROR"
        }
    }
    
    LogMessage "" "INFO"
}

# ================================================================
# FINAL SUMMARY
# ================================================================

$endTime = Get-Date
$duration = ($endTime - $startTime).TotalSeconds

LogMessage "================================================================" "INFO"
LogMessage "SUCCESS: AUTOMATION COMPLETED" "SUCCESS"
LogMessage "================================================================" "INFO"
LogMessage "" "INFO"
LogMessage "Execution time: $([Math]::Round($duration, 2)) seconds" "INFO"
LogMessage "" "INFO"

if ($runTests) {
    LogMessage "Tests:" "INFO"
    $testResults = Get-ChildItem -Path "tests" -Filter "*.php" -Recurse | Measure-Object
    LogMessage "   Total test files: $($testResults.Count)" "INFO"
}

if ($runScreenshots) {
    LogMessage "Screenshots:" "INFO"
    if (Test-Path "screenshots") {
        $screenshots = Get-ChildItem -Path "screenshots" -Filter "*.png"
        LogMessage "   Total images: $($screenshots.Count)" "INFO"
        $screenshots | ForEach-Object {
            LogMessage "   - $($_.Name)" "INFO"
        }
    }
}

LogMessage "" "INFO"
LogMessage "Log file: $logFile" "INFO"
LogMessage "" "INFO"

Write-Host ""
Write-Host "================================================================" -ForegroundColor Green
Write-Host "SUCCESS: AUTOMATION COMPLETED!" -ForegroundColor Green
Write-Host "================================================================" -ForegroundColor Green
Write-Host ""

Write-Host "Press any key to close..." -ForegroundColor Gray
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
