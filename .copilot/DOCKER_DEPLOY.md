# Docker Deployment Guide
## Laravel FSM Phase 1 - Complete Deployment Instructions

This guide covers deploying the Laravel FSM Phase 1 application using Docker with GitOps automation.

---

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Quick Start (Local Development)](#quick-start-local-development)
3. [Portainer Deployment](#portainer-deployment)
4. [Manual Docker Compose Deployment](#manual-docker-compose-deployment)
5. [Environment Configuration](#environment-configuration)
6. [GitOps Configuration](#gitops-configuration)
7. [Testing & Verification](#testing--verification)
8. [Troubleshooting](#troubleshooting)
9. [Production Deployment](#production-deployment)

---

## Prerequisites

### Required Software
- **Docker** 20.10+ ([Install Docker](https://docs.docker.com/get-docker/))
- **Docker Compose** 2.0+ (included with Docker Desktop)
- **Git** 2.30+ ([Install Git](https://git-scm.com/downloads))

### Optional (for production)
- **Portainer** 2.18+ ([Install Portainer](https://docs.portainer.io/start/install))
- **Domain name** with DNS configured
- **SSL certificate** (Let's Encrypt recommended)

### System Requirements
- **RAM:** 2GB minimum, 4GB recommended
- **Disk:** 10GB available space
- **CPU:** 2 cores minimum
- **OS:** Linux, macOS, or Windows with WSL2

---

## Quick Start (Local Development)

### Step 1: Clone or Initialize Repository

If you're starting from this codebase (not from a Git repository):

```bash
cd laravel-fsm-phase1
```

If you want to create a Git repository:

```bash
cd laravel-fsm-phase1
git init
git add .
git commit -m "Initial commit: Laravel FSM Phase 1"
```

### Step 2: Configure Environment

```bash
# Copy environment template
cp .env.example .env

# Edit environment variables
nano .env  # or use your preferred editor
```

**Minimum required changes in `.env`:**

```env
# For local development (no GitOps)
GITOPS_ENABLED=false

# Database
DB_PASSWORD=your_secure_password

# If using GitOps, configure these:
GIT_REPO=https://github.com/Krisaley/service-portal.git
GIT_BRANCH=main
GIT_TOKEN=your_github_token  # For private repos
```

### Step 3: Build and Start

```bash
# Build the Docker images
docker-compose build

# Start all services
docker-compose up -d

# Watch the logs
docker-compose logs -f app
```

### Step 4: Wait for Initialization

The first start takes **3-5 minutes** as it:
1. Clones/copies the application code
2. Installs Composer dependencies (~200 packages)
3. Installs NPM dependencies
4. Runs database migrations
5. Seeds test data
6. Builds frontend assets

**Watch for this message in logs:**
```
[INFO] Application ready! Access at http://localhost:8080
```

### Step 5: Access Application

Open your browser to:
- **Frontend:** http://localhost:8080
- **Admin Panel:** http://localhost:8080/admin
- **Health Check:** http://localhost:8080/health

**Login with test credentials:**
```
Email: admin@test.com
Password: password
```

---

## Portainer Deployment

### Scenario 1: Portainer on Local Machine

1. **Start Portainer** (if not already running):
   ```bash
   docker volume create portainer_data
   docker run -d -p 9000:9000 --name=portainer --restart=always \
     -v /var/run/docker.sock:/var/run/docker.sock \
     -v portainer_data:/data portainer/portainer-ce:latest
   ```

2. **Access Portainer:** http://localhost:9000

3. **Create admin account** (first time only)

4. **Add Stack:**
   - Go to **Stacks** → **Add Stack**
   - Name: `laravel-fsm-phase1`
   - Build method: **Upload**
   - Upload `docker-compose.yml`

5. **Configure Environment Variables:**
   ```
   APP_NAME=Laravel FSM
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=http://localhost:8080

   DB_PASSWORD=your_secure_password
   DB_ROOT_PASSWORD=your_root_password

   GITOPS_ENABLED=true
   GIT_REPO=https://github.com/your-username/laravel-fsm.git
   GIT_BRANCH=main
   GIT_TOKEN=your_github_token

   GITOPS_INTERVAL=300
   ```

6. **Deploy Stack:** Click "Deploy the stack"

7. **Monitor:** Go to **Containers** to watch initialization

### Scenario 2: Portainer on Remote Server

1. **Copy files to server:**
   ```bash
   # From your local machine
   scp -r laravel-fsm-phase1 user@server:/opt/laravel-fsm
   ```

2. **SSH to server:**
   ```bash
   ssh user@server
   cd /opt/laravel-fsm/laravel-fsm-phase1
   ```

3. **Follow Scenario 1 steps** but access Portainer at:
   ```
   http://your-server-ip:9000
   ```

4. **Update APP_URL:**
   ```
   APP_URL=http://your-server-ip:8080
   ```

---

## Manual Docker Compose Deployment

### On Remote Server

1. **Install Docker on server:**
   ```bash
   # Ubuntu/Debian
   curl -fsSL https://get.docker.com | sh
   sudo usermod -aG docker $USER

   # Log out and back in for group changes
   ```

2. **Copy repository to server:**
   ```bash
   # Option A: Via Git
   git clone https://github.com/your-username/laravel-fsm.git
   cd laravel-fsm

   # Option B: Via SCP
   scp -r laravel-fsm-phase1 user@server:/opt/laravel-fsm
   ```

3. **Configure environment:**
   ```bash
   cp .env.example .env
   nano .env

   # Set production values:
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=http://your-domain.com
   DB_PASSWORD=strong_password
   GITOPS_ENABLED=true
   ```

4. **Deploy:**
   ```bash
   docker-compose up -d --build
   ```

5. **Verify:**
   ```bash
   docker-compose ps
   docker-compose logs -f app
   ```

---

## Environment Configuration

### Complete `.env` Template

```env
# ====================
# Application
# ====================
APP_NAME="Laravel FSM Phase 1"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.com
APP_PORT=8080
VITE_PORT=5173

# ====================
# Database
# ====================
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_fsm
DB_USERNAME=sail
DB_PASSWORD=secure_password_here
DB_ROOT_PASSWORD=root_password_here

# ====================
# Redis
# ====================
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_PASSWORD=null

# ====================
# GitOps (Automatic Updates)
# ====================
GITOPS_ENABLED=true
GITOPS_INTERVAL=300
GITOPS_AUTO_MIGRATE=true
GITOPS_AUTO_INSTALL=true

# Git Repository
GIT_REPO=https://github.com/your-username/laravel-fsm.git
GIT_BRANCH=main
GIT_TOKEN=ghp_your_personal_access_token

# ====================
# Meilisearch
# ====================
MEILISEARCH_HOST=http://meilisearch:7700
MEILISEARCH_PORT=7700
MEILI_MASTER_KEY=masterKey
MEILI_ENV=production
MEILI_NO_ANALYTICS=true

# ====================
# Mail (Optional)
# ====================
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@laravel-fsm.local
MAIL_FROM_NAME="${APP_NAME}"

# ====================
# Pusher (Optional - for real-time)
# ====================
BROADCAST_DRIVER=pusher
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

# ====================
# Filament
# ====================
FILAMENT_ADMIN_PATH=admin

# ====================
# Multi-Tenancy
# ====================
TENANT_ISOLATION_STRICT=true
```

### Environment Variables Explained

| Variable | Description | Default | Required |
|----------|-------------|---------|----------|
| `APP_ENV` | Environment (local/production) | production | ✅ |
| `APP_DEBUG` | Enable debug mode | false | ✅ |
| `APP_URL` | Your application URL | http://localhost:8080 | ✅ |
| `DB_PASSWORD` | MySQL password | password | ✅ CHANGE! |
| `GITOPS_ENABLED` | Enable GitOps auto-updates | true | ❌ |
| `GIT_REPO` | Your Git repository URL | - | ⚠️ If GitOps enabled |
| `GIT_BRANCH` | Branch to track | main | ⚠️ If GitOps enabled |
| `GIT_TOKEN` | GitHub/GitLab token | - | ⚠️ For private repos |
| `GITOPS_INTERVAL` | Update check interval (seconds) | 300 | ❌ |

---

## GitOps Configuration

GitOps enables **automatic updates** from your Git repository every 5 minutes (or your custom interval).

### When to Enable GitOps

✅ **Enable GitOps when:**
- You want automatic deployments from Git
- You're deploying to a server (not local dev)
- You have a Git repository set up
- You want zero-downtime updates

❌ **Disable GitOps when:**
- Local development on your machine
- Testing changes before committing
- You want manual control over updates

### Setting Up GitOps

#### Step 1: Create Git Repository

```bash
# Initialize repository
cd laravel-fsm-phase1
git init
git add .
git commit -m "Initial commit"

# Create repository on GitHub/GitLab
# Then push:
git remote add origin https://github.com/your-username/laravel-fsm.git
git push -u origin main
```

#### Step 2: Generate Access Token

**For GitHub:**
1. Go to: https://github.com/settings/tokens
2. Click **Generate new token** → **Generate new token (classic)**
3. Select scopes: `repo` (full control)
4. Generate and copy token

**For GitLab:**
1. Go to: https://gitlab.com/-/profile/personal_access_tokens
2. Create token with `read_repository` scope

#### Step 3: Configure Environment

```env
GITOPS_ENABLED=true
GIT_REPO=https://github.com/your-username/laravel-fsm.git
GIT_BRANCH=main
GIT_TOKEN=ghp_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

#### Step 4: Deploy and Test

```bash
# Deploy with GitOps
docker-compose up -d --build

# Watch GitOps logs
docker logs -f laravel-fsm-app | grep -i gitops

# Test automatic update:
# 1. Make a change to your code
# 2. Commit and push to Git
# 3. Wait 5 minutes (or your GITOPS_INTERVAL)
# 4. Check logs for update activity
```

### GitOps Update Cycle

```
Every 5 minutes:
├─ Check remote repository for changes
├─ If new commits found:
│  ├─ Pull latest code
│  ├─ Check for composer.json changes
│  │  └─ Run: composer install (if changed)
│  ├─ Check for package.json changes
│  │  └─ Run: npm install && npm run build (if changed)
│  ├─ Check for new migrations
│  │  └─ Run: php artisan migrate --force
│  ├─ Clear Laravel caches
│  ├─ Reload PHP-FPM (zero downtime)
│  └─ Log update completion
└─ If no changes: Sleep until next check
```

### Adjusting Update Frequency

```env
# Every 3 minutes
GITOPS_INTERVAL=180

# Every 5 minutes (default)
GITOPS_INTERVAL=300

# Every 10 minutes
GITOPS_INTERVAL=600

# Every 30 minutes
GITOPS_INTERVAL=1800
```

---

## Testing & Verification

### 1. Check Container Health

```bash
# View all containers
docker-compose ps

# Should show:
# laravel-fsm-app       healthy
# laravel-fsm-mysql     healthy
# laravel-fsm-redis     healthy
# laravel-fsm-queue     running
# laravel-fsm-scheduler running
```

### 2. Check Application Health

```bash
# Basic health check
curl http://localhost:8080/health

# Expected response:
# {"status":"healthy"}

# Detailed health check
curl http://localhost:8080/health/detailed

# Shows: database, redis, cache, storage status
```

### 3. Test Login

```bash
# Access admin panel
open http://localhost:8080/admin

# Login with:
# Email: admin@test.com
# Password: password
```

### 4. Test Multi-Tenancy

```bash
# Login as admin
# Navigate to: Users
# Create new user
# Assign to a team
# Logout and login as new user
# Verify team isolation works
```

### 5. Test Module System

```bash
# Access container
docker exec -it laravel-fsm-app bash

# List available modules
php artisan module:list

# Install a module
php artisan module:install crm

# Verify installation
php artisan module:list
```

### 6. Check Logs

```bash
# Application logs
docker logs -f laravel-fsm-app

# Database logs
docker logs -f laravel-fsm-mysql

# Queue worker logs
docker logs -f laravel-fsm-queue

# Laravel application logs
docker exec laravel-fsm-app tail -f storage/logs/laravel.log
```

### 7. Test GitOps (if enabled)

```bash
# Make a simple change
echo "// Test GitOps" >> routes/web.php

# Commit and push
git add routes/web.php
git commit -m "Test GitOps"
git push

# Watch for update (wait 5 minutes)
docker logs -f laravel-fsm-app | grep "GitOps"

# You should see:
# [INFO] GitOps: Changes detected
# [INFO] GitOps: Pulling latest code
# [INFO] GitOps: Update complete
```

---

## Troubleshooting

### Container Won't Start

**Check logs:**
```bash
docker logs laravel-fsm-app
```

**Common issues:**

1. **Port already in use:**
   ```bash
   # Change port in .env
   APP_PORT=8081

   # Or find and kill process
   lsof -i :8080
   ```

2. **Git authentication failed:**
   ```
   # Verify token is correct
   # Check repository is accessible
   # For public repos, remove GIT_TOKEN
   ```

3. **Database connection failed:**
   ```bash
   # Wait for MySQL to be healthy
   docker-compose ps

   # Check credentials match
   # Verify DB_HOST=mysql (not localhost)
   ```

### GitOps Not Updating

```bash
# Check if GitOps is enabled
docker exec laravel-fsm-app env | grep GITOPS

# View GitOps logs
docker exec laravel-fsm-app cat /var/log/gitops/updates.log

# Manually trigger update
docker exec laravel-fsm-app /usr/local/bin/gitops-update.sh

# Check Git credentials
docker exec -it laravel-fsm-app bash
cd /var/www/html
git pull  # Should work without errors
```

### Permissions Errors

```bash
# Fix storage permissions
docker exec laravel-fsm-app chown -R www-data:www-data /var/www/html/storage
docker exec laravel-fsm-app chmod -R 775 /var/www/html/storage
```

### Database Issues

```bash
# Access MySQL
docker exec -it laravel-fsm-mysql mysql -usail -p

# Run migrations manually
docker exec laravel-fsm-app php artisan migrate --force

# Reset database (CAUTION: deletes all data)
docker exec laravel-fsm-app php artisan migrate:fresh --seed
```

### Composer/NPM Errors

```bash
# Clear and reinstall
docker exec -it laravel-fsm-app bash
composer install --no-interaction --optimize-autoloader
npm install
npm run build
```

### View All Logs

```bash
# All services
docker-compose logs --tail=100 -f

# Specific service
docker-compose logs --tail=100 -f app

# GitOps logs
docker exec laravel-fsm-app tail -f /var/log/gitops/gitops.log
docker exec laravel-fsm-app tail -f /var/log/gitops/updates.log
```

---

## Production Deployment

### Security Checklist

Before deploying to production:

- [ ] Change `DB_PASSWORD` to strong password
- [ ] Change `DB_ROOT_PASSWORD` to strong password
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Generate new `APP_KEY` (done automatically)
- [ ] Configure firewall (allow only 80, 443, 22)
- [ ] Set up SSL certificate (Let's Encrypt)
- [ ] Configure backups (database + volumes)
- [ ] Set up monitoring (Uptime Robot, etc.)
- [ ] Review `.env` for sensitive data
- [ ] Enable HTTPS redirect
- [ ] Configure CORS if using API
- [ ] Set rate limiting
- [ ] Enable log rotation

### SSL/HTTPS Setup

**Using Traefik (recommended):**

1. Add Traefik labels to `docker-compose.yml`:
   ```yaml
   labels:
     - "traefik.enable=true"
     - "traefik.http.routers.laravel-fsm.rule=Host(`your-domain.com`)"
     - "traefik.http.routers.laravel-fsm.entrypoints=websecure"
     - "traefik.http.routers.laravel-fsm.tls.certresolver=letsencrypt"
   ```

2. Deploy with Traefik network

**Using Nginx Reverse Proxy:**

See full guide: [Laravel Deployment with Nginx](https://laravel.com/docs/deployment#nginx)

### Backup Strategy

```bash
# Backup database
docker exec laravel-fsm-mysql mysqldump -usail -ppassword laravel_fsm > backup.sql

# Backup volumes
docker run --rm -v laravel-fsm-phase1_mysql-data:/data -v $(pwd):/backup alpine tar czf /backup/mysql-data.tar.gz /data

# Automated daily backups
crontab -e
0 2 * * * /path/to/backup-script.sh
```

### Monitoring

```bash
# Container health
docker-compose ps

# Resource usage
docker stats

# Disk usage
docker system df
```

---

## Next Steps

✅ **Application is deployed!**

**For development:**
1. Start building features (see `README.md`)
2. Create modules (see Module System documentation)
3. Customize views and layouts
4. Add business logic

**For production:**
1. Set up domain and SSL
2. Configure backups
3. Set up monitoring
4. Review security checklist
5. Load production data

---

## Support Resources

- **Laravel Documentation:** https://laravel.com/docs/11.x
- **Filament Documentation:** https://filamentphp.com/docs
- **Docker Documentation:** https://docs.docker.com/
- **Portainer Documentation:** https://docs.portainer.io/

---

**Deployment complete!** Your Laravel FSM Phase 1 application is ready for testing. 🚀
