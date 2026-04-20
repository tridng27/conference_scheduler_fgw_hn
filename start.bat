@echo off
echo Starting Conference Scheduler Application...

echo.
echo Step 1: Starting Docker services...
docker compose up -d

echo.
echo Step 2: Waiting for database to be ready...
timeout /t 10 /nobreak > nul

echo.
echo Step 3: Running database migrations...
set DATABASE_URL=postgresql://app:!ChangeMe!@localhost:52042/app?serverVersion=16&charset=utf8
php bin/console doctrine:migrations:migrate --no-interaction

echo.
echo Step 4: Skipping webpack build (using AssetMapper instead)...

echo.
echo Step 5: Starting Symfony development server...
echo Application will be available at: http://127.0.0.1:8000/
echo Press Ctrl+C to stop the server
echo.
php -S 127.0.0.1:8000 -t public