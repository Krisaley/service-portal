@echo off
REM Laravel Field Service Management - Phase 1 Setup Script (Windows)
REM This script sets up the development environment for Phase 1

echo 🚀 Setting up Laravel Field Service Management - Phase 1
echo ==================================================

REM Check if we're in the right directory
if not exist "composer.json" (
    echo ❌ Error: Please run this script from the project root directory
    pause
    exit /b 1
)

echo 📦 Installing PHP dependencies...
if exist "vendor\bin\sail.bat" (
    call vendor\bin\sail.bat composer install
) else (
    composer install
)

echo 📝 Setting up environment...
if not exist ".env" (
    copy .env.example .env
    echo ✅ Created .env file from template
) else (
    echo ⚠️  .env file already exists, skipping...
)

echo 🔑 Generating application key...
if exist "vendor\bin\sail.bat" (
    call vendor\bin\sail.bat artisan key:generate
) else (
    php artisan key:generate
)

echo 🐳 Starting Docker containers...
if exist "vendor\bin\sail.bat" (
    call vendor\bin\sail.bat up -d
) else (
    docker-compose up -d
)

REM Wait for database to be ready
echo ⏳ Waiting for database to be ready...
timeout /t 10 /nobreak >nul

echo 🗄️  Running database migrations...
if exist "vendor\bin\sail.bat" (
    call vendor\bin\sail.bat artisan migrate
) else (
    php artisan migrate
)

echo 👥 Installing Jetstream with Teams...
if exist "vendor\bin\sail.bat" (
    call vendor\bin\sail.bat artisan jetstream:install livewire --teams
) else (
    php artisan jetstream:install livewire --teams
)

echo 📦 Installing NPM dependencies...
if exist "vendor\bin\sail.bat" (
    call vendor\bin\sail.bat npm install
) else (
    npm install
)

echo 🎨 Building frontend assets...
if exist "vendor\bin\sail.bat" (
    call vendor\bin\sail.bat npm run build
) else (
    npm run build
)

echo 📁 Creating modules directory structure...
if not exist "modules" mkdir modules
if not exist "database\modules" mkdir database\modules
if not exist "public\modules" mkdir public\modules

echo ✅ Phase 1 setup complete!
echo.
echo 🎯 Next Steps:
echo 1. Visit http://localhost to access the application
echo 2. Register your first user (will create a team)
echo 3. Start developing the module system
echo.
echo 🛠️  Useful Commands:
if exist "vendor\bin\sail.bat" (
    echo - Start containers: vendor\bin\sail.bat up -d
    echo - Stop containers: vendor\bin\sail.bat down
    echo - View logs: vendor\bin\sail.bat logs
    echo - Run tests: vendor\bin\sail.bat test
    echo - Artisan commands: vendor\bin\sail.bat artisan [command]
) else (
    echo - Start containers: docker-compose up -d
    echo - Stop containers: docker-compose down
    echo - View logs: docker-compose logs
    echo - Artisan commands: php artisan [command]
)
echo.
echo 📋 Phase 1 Progress:
echo - [x] M1: Laravel Foundation Setup
echo - [ ] M2: Module Management System
echo - [ ] M3: UI Shell ^& Navigation
echo - [ ] M4: Core Security ^& Settings
echo - [ ] M5: Testing Framework ^& Validation

pause