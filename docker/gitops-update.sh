#!/bin/bash

################################################################################
# GitOps Update Loop
#
# This script runs continuously in the background, checking for git updates
# every GITOPS_INTERVAL seconds (default: 300 = 5 minutes)
################################################################################

set -e

# Configuration
PROJECT_DIR="/var/www/html"
PHASE1_DIR="$PROJECT_DIR/phase_1"
LOG_DIR="/var/log/gitops"
UPDATE_LOG="$LOG_DIR/updates.log"
GITOPS_INTERVAL="${GITOPS_INTERVAL:-300}"

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

################################################################################
# Functions
################################################################################

log() {
    local level=$1
    shift
    local message="$@"
    local timestamp=$(date +"%Y-%m-%d %H:%M:%S")

    echo "[$timestamp] [$level] $message" >> "$UPDATE_LOG"
}

check_for_updates() {
    # Determine working directory
    if [ -d "$PHASE1_DIR" ]; then
        cd "$PHASE1_DIR"
    else
        cd "$PROJECT_DIR"
    fi

    log INFO "Checking for updates..."

    # Fetch latest changes
    git fetch origin >/dev/null 2>&1

    # Get current and remote commit hashes
    local LOCAL=$(git rev-parse HEAD)
    local REMOTE=$(git rev-parse origin/"$GITOPS_BRANCH")

    if [ "$LOCAL" = "$REMOTE" ]; then
        log INFO "No updates available"
        return 1
    fi

    log INFO "Updates found: $LOCAL -> $REMOTE"
    return 0
}

apply_updates() {
    log INFO "Applying updates..."

    # Determine working directory
    if [ -d "$PHASE1_DIR" ]; then
        cd "$PHASE1_DIR"
    else
        cd "$PROJECT_DIR"
    fi

    local BEFORE_COMMIT=$(git rev-parse HEAD)

    # Pull latest changes
    if git pull origin "$GITOPS_BRANCH" 2>&1 | tee -a "$UPDATE_LOG"; then
        local AFTER_COMMIT=$(git rev-parse HEAD)
        log INFO "Updated from $BEFORE_COMMIT to $AFTER_COMMIT"
    else
        log ERROR "Git pull failed"
        return 1
    fi

    # Check if composer.json or composer.lock changed
    if git diff --name-only "$BEFORE_COMMIT" "$AFTER_COMMIT" | grep -q "composer\.\(json\|lock\)"; then
        log INFO "Composer dependencies changed, updating..."
        composer install --no-interaction --no-dev --optimize-autoloader 2>&1 | tee -a "$UPDATE_LOG"
    fi

    # Check if package.json or package-lock.json changed
    if git diff --name-only "$BEFORE_COMMIT" "$AFTER_COMMIT" | grep -q "package\.\(json\|lock\.json\)"; then
        log INFO "NPM dependencies changed, updating..."
        npm ci --production 2>&1 | tee -a "$UPDATE_LOG"

        if [ -f "vite.config.js" ]; then
            log INFO "Building assets..."
            npm run build 2>&1 | tee -a "$UPDATE_LOG"
        fi
    fi

    # Check for new migrations
    if [ "$GITOPS_AUTO_MIGRATE" = "true" ]; then
        if git diff --name-only "$BEFORE_COMMIT" "$AFTER_COMMIT" | grep -q "database/migrations/"; then
            log INFO "New migrations detected, running..."
            php artisan migrate --force 2>&1 | tee -a "$UPDATE_LOG"
        fi
    fi

    # Clear caches
    log INFO "Clearing caches..."
    php artisan config:clear >/dev/null 2>&1 || true
    php artisan cache:clear >/dev/null 2>&1 || true
    php artisan route:clear >/dev/null 2>&1 || true
    php artisan view:clear >/dev/null 2>&1 || true

    # Re-cache
    php artisan config:cache >/dev/null 2>&1 || true
    php artisan route:cache >/dev/null 2>&1 || true
    php artisan view:cache >/dev/null 2>&1 || true

    # Reload PHP-FPM gracefully
    log INFO "Reloading PHP-FPM..."
    kill -USR2 $(pgrep -o php-fpm) 2>/dev/null || true

    log INFO "Update completed successfully"
}

health_check() {
    # Simple health check
    if curl -sf http://localhost/health >/dev/null 2>&1; then
        return 0
    else
        log ERROR "Health check failed after update"
        return 1
    fi
}

################################################################################
# Main Loop
################################################################################

main() {
    log INFO "========================================="
    log INFO "GitOps Update Loop Starting"
    log INFO "Branch: $GITOPS_BRANCH"
    log INFO "Interval: ${GITOPS_INTERVAL}s ($(($GITOPS_INTERVAL / 60)) minutes)"
    log INFO "========================================="

    # Wait for initial startup to complete
    sleep 30

    while true; do
        log INFO "--- Update Check Cycle ---"

        if check_for_updates; then
            log INFO "Updates available, applying..."

            if apply_updates; then
                log INFO "Update successful"

                # Verify health after update
                sleep 5
                if health_check; then
                    log INFO "Health check passed"
                else
                    log ERROR "Health check failed, but continuing..."
                fi
            else
                log ERROR "Update failed"
            fi
        fi

        log INFO "Next check in ${GITOPS_INTERVAL}s..."
        sleep "$GITOPS_INTERVAL"
    done
}

# Start the loop
main "$@"
