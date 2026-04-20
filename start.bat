@echo off
echo Starting Conference Scheduler Application...

echo.
echo Step 1: Starting Docker services...
docker compose up -d
if %errorlevel% neq 0 (
    echo Failed to start Docker services
    exit /b 1
)

echo.
echo Step 2: Waiting for database to be ready...
timeout /t 10 /nobreak > nul

echo.
echo Step 3: Running database migrations...
set DATABASE_URL=postgresql://app:!ChangeMe!@localhost:52042/app?serverVersion=16^&charset=utf8
php bin/console doctrine:migrations:migrate --no-interaction
if %errorlevel% neq 0 (
    echo Failed to run migrations
    exit /b 1
)

echo.
echo Step 4: Clearing cache...
php bin/console cache:clear

echo.
echo Step 5: Starting Symfony development server...
echo Application will be available at: http://127.0.0.1:8000/
echo Mailpit (email testing) available at: http://127.0.0.1:8025/
echo Press Ctrl+C to stop the server
echo.
php -S 127.0.0.1:8000 -t public
