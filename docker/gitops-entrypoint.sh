#!/bin/bash

################################################################################
# GitOps Entrypoint Script
#
# This script:
# 1. Performs initial git clone/pull
# 2. Checks and installs dependencies
# 3. Runs initial setup
# 4. Starts the update loop (every 5 minutes)
# 5. Starts PHP-FPM and Nginx via Supervisor
################################################################################

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
PROJECT_DIR="/var/www/html"
PHASE1_DIR="$PROJECT_DIR/phase_1"
LOG_DIR="/var/log/gitops"
GITOPS_LOG="$LOG_DIR/gitops.log"

# Ensure log directory exists
mkdir -p "$LOG_DIR"

################################################################################
# Logging Functions
################################################################################

log() {
    local level=$1
    shift
    local message="$@"
    local timestamp=$(date +"%Y-%m-%d %H:%M:%S")

    echo "[$timestamp] [$level] $message" | tee -a "$GITOPS_LOG"

    case $level in
        INFO)
            echo -e "${BLUE}[INFO]${NC} $message"
            ;;
        SUCCESS)
            echo -e "${GREEN}[SUCCESS]${NC} $message"
            ;;
        WARNING)
            echo -e "${YELLOW}[WARNING]${NC} $message"
            ;;
        ERROR)
            echo -e "${RED}[ERROR]${NC} $message"
            ;;
    esac
}

################################################################################
# Git Operations
################################################################################

setup_git_credentials() {
    if [ -n "$GIT_TOKEN" ]; then
        log INFO "Configuring git credentials with token"

        # Configure git credential helper
        git config --global credential.helper store

        # Store credentials
        echo "https://${GIT_TOKEN}@github.com" > ~/.git-credentials

        log SUCCESS "Git credentials configured"
    else
        log WARNING "No GIT_TOKEN provided, using public repository access"
    fi
}

initial_clone() {
    log INFO "========================================="
    log INFO "Starting Initial Repository Setup"
    log INFO "========================================="

    if [ -z "$GIT_REPO" ]; then
        log ERROR "GIT_REPO environment variable not set"
        exit 1
    fi

    log INFO "Repository: $GIT_REPO"
    log INFO "Branch: $GITOPS_BRANCH"

    # Setup git credentials if token provided
    setup_git_credentials

    # Add safe directory to prevent git ownership errors
    git config --global --add safe.directory /var/www/html

    # Remove existing directory if empty or invalid
    if [ -d "$PROJECT_DIR/.git" ]; then
        log INFO "Git repository already exists, pulling latest changes..."
        cd "$PROJECT_DIR"
        git fetch origin
        git checkout "$GITOPS_BRANCH" || git checkout -b "$GITOPS_BRANCH" origin/"$GITOPS_BRANCH"
        git pull origin "$GITOPS_BRANCH"
    else
        log INFO "Cloning repository..."

        # Backup any existing files
        if [ "$(ls -A $PROJECT_DIR 2>/dev/null)" ]; then
            log WARNING "Project directory not empty, creating backup..."
            mkdir -p /tmp/project-backup
            mv "$PROJECT_DIR"/* /tmp/project-backup/ 2>/dev/null || true
            mv "$PROJECT_DIR"/.[!.]* /tmp/project-backup/ 2>/dev/null || true
        fi

        # Clone to temp directory then move contents
        log INFO "Cloning to temporary directory..."
        TEMP_CLONE="/tmp/git-clone-$$"
        rm -rf "$TEMP_CLONE"

        git clone --branch "$GITOPS_BRANCH" "$GIT_REPO" "$TEMP_CLONE" || {
            log ERROR "Failed to clone repository"
            exit 1
        }

        # Move cloned files to project directory
        log INFO "Moving repository to project directory..."
        mkdir -p "$PROJECT_DIR"

        # Use cp -a to preserve all attributes, then remove temp
        cp -a "$TEMP_CLONE"/. "$PROJECT_DIR/"
        rm -rf "$TEMP_CLONE"

        # Fix ownership to www-data
        chown -R www-data:www-data "$PROJECT_DIR"

        log SUCCESS "Repository cloned successfully"
    fi

    # Navigate to phase_1 if it exists
    if [ -d "$PHASE1_DIR" ]; then
        cd "$PHASE1_DIR"
        log SUCCESS "Switched to phase_1 directory"
    else
        cd "$PROJECT_DIR"
        log WARNING "phase_1 directory not found, using project root"
    fi

    log SUCCESS "Repository setup complete"
}

################################################################################
# Dependency Management
################################################################################

check_and_install_dependencies() {
    log INFO "Checking dependencies..."

    local working_dir="$PWD"

    # Check if composer.json exists
    if [ ! -f "composer.json" ]; then
        log WARNING "No composer.json found in $working_dir"
        return 0
    fi

    # Check if vendor directory exists
    if [ ! -d "vendor" ]; then
        log INFO "Vendor directory missing, installing composer dependencies..."
        install_composer_dependencies
    else
        # Check if composer.lock has changed
        local lock_hash=$(md5sum composer.lock 2>/dev/null | awk '{print $1}')
        local stored_hash=$(cat .composer.lock.hash 2>/dev/null || echo "")

        if [ "$lock_hash" != "$stored_hash" ]; then
            log INFO "composer.lock has changed, updating dependencies..."
            install_composer_dependencies
            echo "$lock_hash" > .composer.lock.hash
        else
            log INFO "Composer dependencies up to date"
        fi
    fi

    # Check NPM dependencies
    if [ -f "package.json" ]; then
        if [ ! -d "node_modules" ]; then
            log INFO "Node modules missing, installing..."
            install_npm_dependencies
        else
            # Check if package-lock.json has changed
            local pkg_hash=$(md5sum package-lock.json 2>/dev/null | awk '{print $1}')
            local stored_pkg_hash=$(cat .package.lock.hash 2>/dev/null || echo "")

            if [ "$pkg_hash" != "$stored_pkg_hash" ]; then
                log INFO "package-lock.json has changed, updating npm dependencies..."
                install_npm_dependencies
                echo "$pkg_hash" > .package.lock.hash
            else
                log INFO "NPM dependencies up to date"
            fi
        fi
    fi
}

install_composer_dependencies() {
    log INFO "Installing Composer dependencies..."

    # Install with no interaction and optimized autoloader
    if composer install --no-interaction --no-dev --optimize-autoloader 2>&1 | tee -a "$GITOPS_LOG"; then
        log SUCCESS "Composer dependencies installed successfully"
    else
        log ERROR "Failed to install composer dependencies"
        return 1
    fi
}

install_npm_dependencies() {
    log INFO "Installing NPM dependencies..."

    if npm ci --production 2>&1 | tee -a "$GITOPS_LOG"; then
        log SUCCESS "NPM dependencies installed successfully"

        # Build assets if vite.config.js exists
        if [ -f "vite.config.js" ]; then
            log INFO "Building frontend assets..."
            npm run build 2>&1 | tee -a "$GITOPS_LOG" || log WARNING "Asset build failed"
        fi
    else
        log ERROR "Failed to install npm dependencies"
        return 1
    fi
}

################################################################################
# Laravel Bootstrap
################################################################################

bootstrap_laravel_if_needed() {
    log INFO "Checking Laravel installation completeness..."

    local working_dir="$PWD"
    local missing_structure=false

    # Check for critical Laravel directories
    local required_dirs=(
        "app"
        "bootstrap"
        "config"
        "database"
        "public"
        "resources"
        "routes"
        "storage"
    )

    local required_files=(
        "artisan"
        "composer.json"
    )

    # Check directories
    for dir in "${required_dirs[@]}"; do
        if [ ! -d "$dir" ]; then
            log WARNING "Missing directory: $dir"
            missing_structure=true
        fi
    done

    # Check files
    for file in "${required_files[@]}"; do
        if [ ! -f "$file" ]; then
            log WARNING "Missing file: $file"
            missing_structure=true
        fi
    done

    if [ "$missing_structure" = true ]; then
        log INFO "========================================="
        log INFO "Incomplete Laravel structure detected"
        log INFO "Bootstrapping fresh Laravel 11 installation..."
        log INFO "========================================="

        # Backup existing files
        local backup_dir="/tmp/phase1-backup-$(date +%s)"
        log INFO "Backing up existing files to $backup_dir"
        mkdir -p "$backup_dir"

        # Copy all existing files to backup (preserve git repo)
        if [ -d ".git" ]; then
            rsync -a --exclude='.git' ./ "$backup_dir/" 2>/dev/null || true
        else
            cp -r ./* "$backup_dir/" 2>/dev/null || true
        fi

        # Create fresh Laravel installation
        log INFO "Creating fresh Laravel 11 project..."
        cd /tmp
        composer create-project --prefer-dist laravel/laravel laravel-fresh "^11.0" --no-interaction --quiet 2>&1 | tee -a "$GITOPS_LOG"

        if [ ! -d "/tmp/laravel-fresh" ]; then
            log ERROR "Failed to create Laravel project"
            return 1
        fi

        # Copy fresh Laravel structure to working directory
        log INFO "Installing Laravel structure..."
        cd "$working_dir"

        # Copy all fresh Laravel files
        cp -r /tmp/laravel-fresh/* "$working_dir/" 2>&1 | tee -a "$GITOPS_LOG"
        cp -r /tmp/laravel-fresh/.* "$working_dir/" 2>/dev/null || true

        # Restore custom files from backup (overlay)
        log INFO "Restoring custom files from phase_1..."

        # Restore app directory (models, traits, services, etc.)
        if [ -d "$backup_dir/app" ]; then
            log INFO "Restoring app/ directory..."
            cp -r "$backup_dir/app"/* "$working_dir/app/" 2>/dev/null || true
        fi

        # Restore database directory (migrations, seeders)
        if [ -d "$backup_dir/database" ]; then
            log INFO "Restoring database/ directory..."
            cp -r "$backup_dir/database"/* "$working_dir/database/" 2>/dev/null || true
        fi

        # Restore config files (custom configs)
        if [ -d "$backup_dir/config" ]; then
            log INFO "Restoring custom config files..."
            # Only copy non-standard config files to preserve Laravel defaults
            find "$backup_dir/config" -type f -name "*.php" | while read config_file; do
                filename=$(basename "$config_file")
                # Skip standard Laravel configs, keep custom ones
                if [[ ! "$filename" =~ ^(app|auth|broadcasting|cache|cors|database|filesystems|hashing|logging|mail|queue|sanctum|services|session|view)\.php$ ]]; then
                    cp "$config_file" "$working_dir/config/" 2>/dev/null || true
                fi
            done
        fi

        # Restore routes (if they exist)
        if [ -d "$backup_dir/routes" ]; then
            log INFO "Restoring routes..."
            cp -r "$backup_dir/routes"/* "$working_dir/routes/" 2>/dev/null || true
        fi

        # Restore modules directory
        if [ -d "$backup_dir/modules" ]; then
            log INFO "Restoring modules/ directory..."
            cp -r "$backup_dir/modules" "$working_dir/" 2>/dev/null || true
        fi

        # Restore public assets (if any custom ones exist)
        if [ -d "$backup_dir/public" ]; then
            log INFO "Restoring custom public assets..."
            # Preserve Laravel's default index.php, only restore custom files
            find "$backup_dir/public" -type f ! -name "index.php" -exec cp {} "$working_dir/public/" \; 2>/dev/null || true
        fi

        # Restore resources (views, js, css)
        if [ -d "$backup_dir/resources" ]; then
            log INFO "Restoring resources/ directory..."
            cp -r "$backup_dir/resources"/* "$working_dir/resources/" 2>/dev/null || true
        fi

        # Restore composer.json if it had custom packages
        if [ -f "$backup_dir/composer.json" ]; then
            log INFO "Checking for custom Composer packages..."
            # We'll keep the fresh composer.json but note custom packages
            log WARNING "Review composer.json for any custom packages from phase_1"
        fi

        # Clean up
        rm -rf /tmp/laravel-fresh
        log SUCCESS "Laravel bootstrap complete"
        log INFO "Backup preserved at: $backup_dir"

    else
        log SUCCESS "Complete Laravel structure found"
    fi
}

################################################################################
# Laravel Setup
################################################################################

setup_laravel() {
    log INFO "Setting up Laravel application..."

    # Generate app key if not exists
    if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
        if [ -f "artisan" ]; then
            log INFO "Generating application key..."
            php artisan key:generate --force
        fi
    fi

    # Create .env if doesn't exist
    if [ ! -f ".env" ]; then
        if [ -f ".env.example" ]; then
            log INFO "Creating .env from .env.example..."
            cp .env.example .env
            php artisan key:generate --force
        else
            log WARNING "No .env.example found"
        fi
    fi

    # Fix permissions
    log INFO "Setting permissions..."
    chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
    chmod -R 775 storage bootstrap/cache 2>/dev/null || true

    # Create module directories
    mkdir -p modules database/modules public/modules

    log SUCCESS "Laravel setup complete"
}

run_migrations() {
    if [ "$GITOPS_AUTO_MIGRATE" = "true" ]; then
        log INFO "Running database migrations..."

        # Wait for database to be ready
        log INFO "Waiting for database connection..."
        local max_attempts=30
        local attempt=1

        while [ $attempt -le $max_attempts ]; do
            if php artisan db:show 2>/dev/null; then
                log SUCCESS "Database connection established"
                break
            fi

            log INFO "Attempt $attempt/$max_attempts - Database not ready, waiting..."
            sleep 2
            ((attempt++))
        done

        if [ $attempt -gt $max_attempts ]; then
            log ERROR "Database connection timeout"
            return 1
        fi

        # Run migrations
        if php artisan migrate --force 2>&1 | tee -a "$GITOPS_LOG"; then
            log SUCCESS "Migrations completed successfully"
        else
            log ERROR "Migrations failed"
            return 1
        fi
    else
        log INFO "Auto-migration disabled, skipping..."
    fi
}

clear_caches() {
    log INFO "Clearing Laravel caches..."

    php artisan config:clear 2>/dev/null || true
    php artisan cache:clear 2>/dev/null || true
    php artisan route:clear 2>/dev/null || true
    php artisan view:clear 2>/dev/null || true

    # Optimize for production
    php artisan config:cache 2>/dev/null || true
    php artisan route:cache 2>/dev/null || true
    php artisan view:cache 2>/dev/null || true

    log SUCCESS "Caches cleared and optimized"
}

################################################################################
# Health Check
################################################################################

create_health_endpoint() {
    # Create a simple health check file if routes not available
    local health_file="$PWD/public/health.php"

    if [ ! -f "$health_file" ]; then
        log INFO "Creating health check endpoint..."
        cat > "$health_file" <<'EOF'
<?php
header('Content-Type: application/json');
echo json_encode([
    'status' => 'ok',
    'timestamp' => date('c'),
    'gitops' => 'enabled'
]);
EOF
        log SUCCESS "Health check endpoint created"
    fi
}

################################################################################
# GitOps Update Loop
################################################################################

start_gitops_loop() {
    if [ "$GITOPS_ENABLED" = "true" ]; then
        log INFO "Starting GitOps update loop (interval: ${GITOPS_INTERVAL}s)"

        # Start update script in background
        /usr/local/bin/gitops-update.sh &

        log SUCCESS "GitOps loop started"
    else
        log INFO "GitOps disabled, skipping update loop"
    fi
}

################################################################################
# Main Execution
################################################################################

main() {
    log INFO "========================================="
    log INFO "GitOps Container Starting"
    log INFO "========================================="

    # Initial repository setup
    initial_clone

    # Bootstrap Laravel if structure is incomplete
    bootstrap_laravel_if_needed

    # Check and install dependencies
    check_and_install_dependencies

    # Setup Laravel
    setup_laravel

    # Create health endpoint
    create_health_endpoint

    # Run migrations
    run_migrations || log WARNING "Continuing despite migration issues..."

    # Clear caches
    clear_caches

    # Start GitOps update loop
    start_gitops_loop

    log SUCCESS "========================================="
    log SUCCESS "GitOps Container Initialization Complete"
    log SUCCESS "========================================="
    log INFO "Starting services via Supervisor..."

    # Start supervisor (PHP-FPM + Nginx)
    exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
}

# Run main function
main "$@"
