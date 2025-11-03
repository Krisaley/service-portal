#!/usr/bin/env bash

# Laravel FSM Phase 1 - Quick Start Script
# This script automates the deployment process

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Header
echo -e "${BLUE}"
echo "╔════════════════════════════════════════════════════════════╗"
echo "║   Laravel FSM Phase 1 - Quick Start Deployment            ║"
echo "║   Complete Laravel Application with Multi-Tenancy          ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo -e "${NC}"

# Function to print colored messages
print_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Function to check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Step 1: Check Prerequisites
print_info "Checking prerequisites..."

if ! command_exists docker; then
    print_error "Docker is not installed. Please install Docker: https://docs.docker.com/get-docker/"
    exit 1
fi
print_success "Docker found: $(docker --version)"

if ! command_exists docker-compose; then
    print_error "Docker Compose is not installed."
    exit 1
fi
print_success "Docker Compose found: $(docker-compose --version)"

# Step 2: Choose Deployment Mode
echo ""
print_info "Select deployment mode:"
echo "  1) Local Development (GitOps disabled)"
echo "  2) Production/Server (GitOps enabled)"
read -p "Enter choice [1-2]: " deployment_mode

# Step 3: Configure Environment
print_info "Creating .env file from template..."

if [ -f .env ]; then
    print_warning ".env file already exists"
    read -p "Overwrite existing .env? [y/N]: " overwrite
    if [[ ! $overwrite =~ ^[Yy]$ ]]; then
        print_info "Using existing .env file"
    else
        cp .env.example .env
        print_success ".env file created"
    fi
else
    cp .env.example .env
    print_success ".env file created"
fi

# Step 4: Set deployment-specific variables
if [ "$deployment_mode" == "1" ]; then
    # Local Development
    print_info "Configuring for local development..."

    sed -i.bak 's/GITOPS_ENABLED=.*/GITOPS_ENABLED=false/' .env
    sed -i.bak 's/APP_ENV=.*/APP_ENV=local/' .env
    sed -i.bak 's/APP_DEBUG=.*/APP_DEBUG=true/' .env
    sed -i.bak 's|APP_URL=.*|APP_URL=http://localhost:8080|' .env

    rm -f .env.bak

    print_success "Configured for local development"
else
    # Production/Server
    print_info "Configuring for production deployment..."

    # Get user inputs
    read -p "Enter your domain/IP (e.g., example.com or 192.168.1.100): " app_url
    read -p "Enter database password: " db_password
    read -p "Enter root database password: " db_root_password

    # GitOps configuration
    read -p "Enable GitOps auto-updates? [y/N]: " enable_gitops

    if [[ $enable_gitops =~ ^[Yy]$ ]]; then
        read -p "Enter Git repository URL: " git_repo
        read -p "Enter Git branch [main]: " git_branch
        git_branch=${git_branch:-main}
        read -p "Enter Git access token (leave empty for public repos): " git_token

        sed -i.bak 's/GITOPS_ENABLED=.*/GITOPS_ENABLED=true/' .env
        sed -i.bak "s|GIT_REPO=.*|GIT_REPO=${git_repo}|" .env
        sed -i.bak "s|GIT_BRANCH=.*|GIT_BRANCH=${git_branch}|" .env
        sed -i.bak "s|GIT_TOKEN=.*|GIT_TOKEN=${git_token}|" .env
    else
        sed -i.bak 's/GITOPS_ENABLED=.*/GITOPS_ENABLED=false/' .env
    fi

    # Set production values
    sed -i.bak 's/APP_ENV=.*/APP_ENV=production/' .env
    sed -i.bak 's/APP_DEBUG=.*/APP_DEBUG=false/' .env
    sed -i.bak "s|APP_URL=.*|APP_URL=http://${app_url}|" .env
    sed -i.bak "s/DB_PASSWORD=.*/DB_PASSWORD=${db_password}/" .env
    sed -i.bak "s/DB_ROOT_PASSWORD=.*/DB_ROOT_PASSWORD=${db_root_password}/" .env

    rm -f .env.bak

    print_success "Configured for production"
fi

# Step 5: Build Docker images
echo ""
print_info "Building Docker images (this may take 3-5 minutes)..."
docker-compose build --no-cache

print_success "Docker images built successfully"

# Step 6: Start services
echo ""
print_info "Starting Docker services..."
docker-compose up -d

print_success "Docker services started"

# Step 7: Wait for initialization
echo ""
print_info "Waiting for application initialization (this may take 3-5 minutes)..."
print_info "This includes:"
print_info "  - Installing Composer dependencies (~200 packages)"
print_info "  - Installing NPM dependencies"
print_info "  - Running database migrations"
print_info "  - Seeding test data"
print_info "  - Building frontend assets"
echo ""

# Wait for health check
max_attempts=60
attempt=0
while [ $attempt -lt $max_attempts ]; do
    if docker exec laravel-fsm-app curl -f http://localhost/health >/dev/null 2>&1; then
        print_success "Application is ready!"
        break
    fi

    attempt=$((attempt + 1))
    echo -ne "Waiting... (${attempt}/${max_attempts})\r"
    sleep 5
done

if [ $attempt -eq $max_attempts ]; then
    print_error "Application failed to start after $((max_attempts * 5)) seconds"
    print_info "Check logs with: docker-compose logs -f app"
    exit 1
fi

# Step 8: Display information
echo ""
echo -e "${GREEN}"
echo "╔════════════════════════════════════════════════════════════╗"
echo "║                    🎉 DEPLOYMENT SUCCESSFUL! 🎉              ║"
echo "╚════════════════════════════════════════════════════════════╝"
echo -e "${NC}"

echo ""
print_info "Access your application at:"
echo ""

if [ "$deployment_mode" == "1" ]; then
    echo "  🌐 Application: http://localhost:8080"
    echo "  🔧 Admin Panel: http://localhost:8080/admin"
    echo "  💚 Health Check: http://localhost:8080/health"
else
    echo "  🌐 Application: http://${app_url}:8080"
    echo "  🔧 Admin Panel: http://${app_url}:8080/admin"
    echo "  💚 Health Check: http://${app_url}:8080/health"
fi

echo ""
print_info "Test Login Credentials:"
echo "  📧 Email: admin@test.com"
echo "  🔑 Password: password"

echo ""
print_info "Other test users:"
echo "  Staff: staff@test.com (password: password)"
echo "  Customer: customer@test.com (password: password)"

echo ""
print_info "Useful Commands:"
echo "  View logs:        docker-compose logs -f app"
echo "  Stop services:    docker-compose stop"
echo "  Start services:   docker-compose start"
echo "  Restart services: docker-compose restart"
echo "  Remove all:       docker-compose down -v"
echo ""
echo "  Access container: docker exec -it laravel-fsm-app bash"
echo "  Run artisan:      docker exec laravel-fsm-app php artisan [command]"
echo "  View migrations:  docker exec laravel-fsm-app php artisan migrate:status"

if [ "$deployment_mode" == "2" ] && [[ $enable_gitops =~ ^[Yy]$ ]]; then
    echo ""
    print_info "GitOps is enabled:"
    echo "  • Updates check every 5 minutes"
    echo "  • Push to ${git_branch} branch to auto-deploy"
    echo "  • View update logs: docker exec laravel-fsm-app tail -f /var/log/gitops/updates.log"
fi

echo ""
print_info "Next Steps:"
echo "  1. Login to admin panel and explore features"
echo "  2. Test multi-tenancy (create teams and users)"
echo "  3. Install modules: docker exec laravel-fsm-app php artisan module:list"
echo "  4. Read DOCKER_DEPLOY.md for advanced configuration"
echo "  5. For production: Set up SSL, backups, and monitoring"

echo ""
print_success "Happy coding! 🚀"
echo ""
