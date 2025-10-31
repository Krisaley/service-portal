#!/bin/bash

# Laravel Field Service Management - Phase 1 Setup Script
# This script sets up the development environment for Phase 1

set -e

echo "🚀 Setting up Laravel Field Service Management - Phase 1"
echo "=================================================="

# Check if we're in the right directory
if [ ! -f "composer.json" ]; then
    echo "❌ Error: Please run this script from the project root directory"
    exit 1
fi

echo "📦 Installing PHP dependencies..."
if command -v sail &> /dev/null; then
    ./vendor/bin/sail composer install
else
    composer install
fi

echo "📝 Setting up environment..."
if [ ! -f ".env" ]; then
    cp .env.example .env
    echo "✅ Created .env file from template"
else
    echo "⚠️  .env file already exists, skipping..."
fi

echo "🔑 Generating application key..."
if command -v sail &> /dev/null; then
    ./vendor/bin/sail artisan key:generate
else
    php artisan key:generate
fi

echo "🐳 Starting Docker containers..."
if command -v sail &> /dev/null; then
    ./vendor/bin/sail up -d
else
    docker-compose up -d
fi

# Wait for database to be ready
echo "⏳ Waiting for database to be ready..."
sleep 10

echo "🗄️  Running database migrations..."
if command -v sail &> /dev/null; then
    ./vendor/bin/sail artisan migrate
else
    php artisan migrate
fi

echo "👥 Installing Jetstream with Teams..."
if command -v sail &> /dev/null; then
    ./vendor/bin/sail artisan jetstream:install livewire --teams
else
    php artisan jetstream:install livewire --teams
fi

echo "📦 Installing NPM dependencies..."
if command -v sail &> /dev/null; then
    ./vendor/bin/sail npm install
else
    npm install
fi

echo "🎨 Building frontend assets..."
if command -v sail &> /dev/null; then
    ./vendor/bin/sail npm run build
else
    npm run build
fi

echo "📁 Creating modules directory structure..."
mkdir -p modules
mkdir -p database/modules
mkdir -p public/modules

echo "✅ Phase 1 setup complete!"
echo ""
echo "🎯 Next Steps:"
echo "1. Visit http://localhost to access the application"
echo "2. Register your first user (will create a team)"
echo "3. Start developing the module system"
echo ""
echo "🛠️  Useful Commands:"
if command -v sail &> /dev/null; then
    echo "- Start containers: ./vendor/bin/sail up -d"
    echo "- Stop containers: ./vendor/bin/sail down"
    echo "- View logs: ./vendor/bin/sail logs"
    echo "- Run tests: ./vendor/bin/sail test"
    echo "- Artisan commands: ./vendor/bin/sail artisan [command]"
else
    echo "- Start containers: docker-compose up -d"
    echo "- Stop containers: docker-compose down"
    echo "- View logs: docker-compose logs"
    echo "- Artisan commands: php artisan [command]"
fi
echo ""
echo "📋 Phase 1 Progress:"
echo "- [x] M1: Laravel Foundation Setup"
echo "- [ ] M2: Module Management System" 
echo "- [ ] M3: UI Shell & Navigation"
echo "- [ ] M4: Core Security & Settings"
echo "- [ ] M5: Testing Framework & Validation"