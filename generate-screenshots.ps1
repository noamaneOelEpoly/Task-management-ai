# Task Manager - Screenshot Generator Script
# This script captures the application interfaces using Puppeteer

Write-Host ""
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host "        Task Manager - Screenshot Generator" -ForegroundColor Cyan
Write-Host "           Automated interface capture" -ForegroundColor Cyan
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host ""

# Create screenshots folder
$screenshotsDir = "screenshots"
if (!(Test-Path $screenshotsDir)) {
    New-Item -ItemType Directory -Path $screenshotsDir | Out-Null
    Write-Host "OK: Folder 'screenshots' created`n" -ForegroundColor Green
}

# Check for Node.js and Puppeteer
Write-Host "Checking prerequisites..." -ForegroundColor Blue

if (!(Get-Command npm -ErrorAction SilentlyContinue)) {
    Write-Host "ERROR: NPM is not installed" -ForegroundColor Red
    exit 1
}
Write-Host "OK: NPM found`n" -ForegroundColor Green

# Install Puppeteer if necessary
Write-Host "Checking Puppeteer installation..." -ForegroundColor Cyan
npm list puppeteer > $null 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "  Puppeteer not found, installing..." -ForegroundColor Yellow
    npm install puppeteer --save-dev
}
Write-Host "OK: Puppeteer ready`n" -ForegroundColor Green

# Create scripts directory
$scriptsDir = "scripts"
if (!(Test-Path $scriptsDir)) {
    New-Item -ItemType Directory -Path $scriptsDir | Out-Null
}

$scriptPath = "$scriptsDir/capture-screenshots.js"

# Check if script exists
if (!(Test-Path $scriptPath)) {
    Write-Host "ERROR: $scriptPath not found" -ForegroundColor Red
    Write-Host "The JavaScript file should be created by the setup process" -ForegroundColor Yellow
    exit 1
}

# Check if Laravel server is running
Write-Host "Checking Laravel server..." -ForegroundColor Blue
$serverCheck = Invoke-WebRequest -Uri "http://localhost:8000" -Method Get -ErrorAction SilentlyContinue -UseBasicParsing
if (!$serverCheck) {
    Write-Host ""
    Write-Host "WARNING:" -ForegroundColor Yellow
    Write-Host "   The Laravel server does not appear to be running." -ForegroundColor Yellow
    Write-Host "   Make sure it is started with: php artisan serve" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "   Press any key to continue or Ctrl+C to cancel..." -ForegroundColor Gray
    $null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
}

# Run Puppeteer script
Write-Host ""
Write-Host "Running screenshot capture..." -ForegroundColor Cyan
Write-Host "================================================================" -ForegroundColor Gray
Write-Host ""

node $scriptPath

if ($LASTEXITCODE -eq 0) {
    Write-Host ""
    Write-Host "================================================================" -ForegroundColor Gray
    Write-Host ""
    Write-Host "SUCCESS: Screenshots generated!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Screenshots available in: screenshots/" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Files generated:" -ForegroundColor White
    Get-ChildItem -Path $screenshotsDir -Filter "*.png" | ForEach-Object {
        Write-Host "   - $_" -ForegroundColor Yellow
    }
    Write-Host ""
} else {
    Write-Host ""
    Write-Host "ERROR: Failed to capture screenshots" -ForegroundColor Red
    Write-Host "   Check that the Laravel server is running" -ForegroundColor Yellow
}
