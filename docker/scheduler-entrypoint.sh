#!/bin/bash

################################################################################
# Laravel Scheduler Entrypoint
#
# Runs Laravel's schedule:run every minute (Laravel's cron equivalent)
################################################################################

set -e

# Configuration
PROJECT_DIR="/var/www/html"
PHASE1_DIR="$PROJECT_DIR/phase_1"

# Determine working directory
if [ -d "$PHASE1_DIR" ]; then
    cd "$PHASE1_DIR"
    echo "Working directory: $PHASE1_DIR"
else
    cd "$PROJECT_DIR"
    echo "Working directory: $PROJECT_DIR"
fi

echo "Starting Laravel Scheduler..."
echo "Running schedule:run every minute"

# Wait for application to be ready
sleep 30

# Run scheduler every minute
while true; do
    php artisan schedule:run --verbose --no-interaction >> /var/log/scheduler.log 2>&1
    sleep 60
done
