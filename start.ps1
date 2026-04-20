Write-Host "Starting Conference Scheduler Application..." -ForegroundColor Green

Write-Host ""
Write-Host "Step 1: Starting Docker services..." -ForegroundColor Yellow
docker compose up -d

Write-Host ""
Write-Host "Step 2: Waiting for database to be ready..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

Write-Host ""
Write-Host "Step 3: Running database migrations..." -ForegroundColor Yellow
$env:DATABASE_URL = "postgresql://app:!ChangeMe!@localhost:52042/app?serverVersion=16&charset=utf8"
php bin/console doctrine:migrations:migrate --no-interaction

Write-Host ""
Write-Host "Step 4: Skipping webpack build (using AssetMapper instead)..." -ForegroundColor Yellow

Write-Host ""
Write-Host "Step 5: Starting Symfony development server..." -ForegroundColor Yellow
Write-Host "Application will be available at: http://127.0.0.1:8000/" -ForegroundColor Cyan
Write-Host "Mailpit (email testing) available at: http://127.0.0.1:8025/" -ForegroundColor Cyan
Write-Host "Press Ctrl+C to stop the server" -ForegroundColor Red
Write-Host ""
php -S 127.0.0.1:8000 -t public