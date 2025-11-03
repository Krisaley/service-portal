#!/bin/bash

################################################################################
# Setup Validation Script
#
# Checks if all required files exist and configuration is valid
################################################################################

set -e

# Colors
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo "========================================="
echo "Laravel FSM Phase 1 - Setup Validation"
echo "========================================="
echo ""

# Track errors
ERRORS=0
WARNINGS=0

# Check required files
echo "Checking required files..."

required_files=(
    "docker-compose.yml"
    "Dockerfile"
    ".env.docker.example"
    "gitops-entrypoint.sh"
    "gitops-update.sh"
    "scheduler-entrypoint.sh"
    "nginx.conf"
    "site.conf"
    "php.ini"
    "supervisord.conf"
    "PORTAINER_DEPLOY.md"
    "README.md"
)

for file in "${required_files[@]}"; do
    if [ -f "$file" ]; then
        echo -e "${GREEN}✓${NC} $file"
    else
        echo -e "${RED}✗${NC} $file (MISSING)"
        ((ERRORS++))
    fi
done

echo ""

# Check if scripts are executable
echo "Checking script permissions..."

scripts=(
    "gitops-entrypoint.sh"
    "gitops-update.sh"
    "scheduler-entrypoint.sh"
)

for script in "${scripts[@]}"; do
    if [ -f "$script" ]; then
        if [ -x "$script" ]; then
            echo -e "${GREEN}✓${NC} $script is executable"
        else
            echo -e "${YELLOW}⚠${NC} $script is not executable (will be fixed in Docker build)"
            ((WARNINGS++))
        fi
    fi
done

echo ""

# Check if .env exists
echo "Checking environment configuration..."

if [ -f ".env" ]; then
    echo -e "${GREEN}✓${NC} .env file exists"

    # Check critical variables
    if grep -q "^GIT_REPO=" .env; then
        GIT_REPO=$(grep "^GIT_REPO=" .env | cut -d '=' -f2)
        if [ -n "$GIT_REPO" ] && [ "$GIT_REPO" != "https://github.com/your-username/laravel-fsm.git" ]; then
            echo -e "${GREEN}✓${NC} GIT_REPO is configured"
        else
            echo -e "${YELLOW}⚠${NC} GIT_REPO not configured or using default"
            ((WARNINGS++))
        fi
    else
        echo -e "${RED}✗${NC} GIT_REPO not found in .env"
        ((ERRORS++))
    fi

    if grep -q "^DB_PASSWORD=" .env; then
        DB_PASSWORD=$(grep "^DB_PASSWORD=" .env | cut -d '=' -f2)
        if [ "$DB_PASSWORD" = "password" ]; then
            echo -e "${YELLOW}⚠${NC} DB_PASSWORD is using default value (change recommended)"
            ((WARNINGS++))
        else
            echo -e "${GREEN}✓${NC} DB_PASSWORD is configured"
        fi
    fi

else
    echo -e "${YELLOW}⚠${NC} .env file not found (copy .env.docker.example to .env)"
    echo "  Run: cp .env.docker.example .env"
    ((WARNINGS++))
fi

echo ""

# Check Docker
echo "Checking Docker environment..."

if command -v docker &> /dev/null; then
    echo -e "${GREEN}✓${NC} Docker is installed"

    if docker ps &> /dev/null; then
        echo -e "${GREEN}✓${NC} Docker daemon is running"
    else
        echo -e "${RED}✗${NC} Docker daemon is not running"
        ((ERRORS++))
    fi
else
    echo -e "${RED}✗${NC} Docker is not installed"
    ((ERRORS++))
fi

if command -v docker-compose &> /dev/null || docker compose version &> /dev/null; then
    echo -e "${GREEN}✓${NC} Docker Compose is available"
else
    echo -e "${RED}✗${NC} Docker Compose is not available"
    ((ERRORS++))
fi

echo ""

# Summary
echo "========================================="
echo "Validation Summary"
echo "========================================="

if [ $ERRORS -eq 0 ] && [ $WARNINGS -eq 0 ]; then
    echo -e "${GREEN}✓ All checks passed!${NC}"
    echo ""
    echo "You're ready to deploy:"
    echo "1. Copy .env.docker.example to .env (if not done)"
    echo "2. Edit .env with your settings"
    echo "3. Run: docker-compose up -d --build"
    echo ""
    echo "Or deploy via Portainer (see PORTAINER_DEPLOY.md)"
    exit 0
elif [ $ERRORS -eq 0 ]; then
    echo -e "${YELLOW}⚠ ${WARNINGS} warning(s) found${NC}"
    echo ""
    echo "You can proceed, but review warnings above."
    exit 0
else
    echo -e "${RED}✗ ${ERRORS} error(s) and ${WARNINGS} warning(s) found${NC}"
    echo ""
    echo "Please fix errors before deploying."
    exit 1
fi
