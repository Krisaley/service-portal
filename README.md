# Laravel FSM Phase 1
## Multi-Tenant Field Service Management System

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-3.x-4E56A6?style=for-the-badge&logo=livewire&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=for-the-badge&logo=docker&logoColor=white)

**A complete, production-ready Laravel 11 application with multi-tenancy, role-based access control, and modular architecture.**

[Features](#features) • [Quick Start](#quick-start) • [Documentation](#documentation) • [Demo](#demo-credentials)

</div>

---

## 🎯 Overview

Laravel FSM Phase 1 is a **fully functional web application** designed for field service management with:

- ✅ **Multi-Tenancy** using Laravel Jetstream Teams
- ✅ **Role-Based Access Control** (Super Admin, Admin, Staff, Customer)
- ✅ **Modular Architecture** with install/uninstall capabilities
- ✅ **Admin Panel** powered by Filament 3
- ✅ **RESTful API** with Laravel Sanctum
- ✅ **Docker/GitOps Deployment** with automatic updates
- ✅ **Activity Logging** for complete audit trails
- ✅ **Media Management** for file uploads
- ✅ **PDF Generation** for documents

---

## ✨ Features

### Core Functionality

| Feature | Description | Status |
|---------|-------------|--------|
| **Authentication** | Login, registration, password reset, email verification | ✅ Ready |
| **Multi-Tenancy** | Team-based isolation with automatic scoping | ✅ Ready |
| **Authorization** | 4 roles, 20+ permissions, policy-based access | ✅ Ready |
| **User Management** | Create, edit, delete users with role assignment | ✅ Ready |
| **Team Management** | Create teams, invite members, manage settings | ✅ Ready |
| **Module System** | Install/uninstall modules per team | ✅ Ready |
| **Activity Logging** | Track all user actions with full audit trail | ✅ Ready |
| **File Management** | Upload, organize, and manage media files | ✅ Ready |
| **API** | RESTful API with token authentication | ✅ Ready |
| **Search** | Full-text search with Meilisearch | ✅ Ready |
| **Queues** | Background job processing with Redis | ✅ Ready |
| **Scheduler** | Laravel task scheduling (cron) | ✅ Ready |

### Admin Panel (Filament)

- 📊 **Dashboard** - Overview stats and recent activity
- 👥 **User Management** - Full CRUD with role assignment
- 🔧 **Module Management** - Install/uninstall modules
- 📝 **Activity Logs** - View all system activity
- ⚙️ **Settings** - Configure application settings
- 🔍 **Global Search** - Search across all resources

### User Roles & Permissions

| Role | Access Level | Capabilities |
|------|--------------|--------------|
| **Super Admin** | Full system access | Manage all teams, users, modules, settings |
| **Admin** | Team-wide access | Manage team users, modules, settings |
| **Staff** | Limited access | View and manage assigned tasks/jobs |
| **Customer** | Read-only | View own data, submit requests |

---

## 🚀 Quick Start

### Prerequisites

- **Docker** 20.10+ ([Install Docker](https://docs.docker.com/get-docker/))
- **Docker Compose** 2.0+
- **Git** (optional, for GitOps)

### Option 1: Automated Quick Start (Recommended)

```bash
# Clone or navigate to repository
cd laravel-fsm-phase1

# Run quick start script
bash quick-start.sh

# Follow the prompts to configure deployment
# Script handles everything: .env setup, building, starting services
```

**That's it!** The script will:
1. Check prerequisites
2. Configure environment
3. Build Docker images
4. Start all services
5. Wait for initialization
6. Display access URLs and credentials

### Option 2: Manual Setup

```bash
# 1. Configure environment
cp .env.example .env
nano .env  # Edit configuration

# 2. Build and start
docker-compose build
docker-compose up -d

# 3. Wait for initialization (3-5 minutes)
docker-compose logs -f app

# 4. Access application
open http://localhost:8080
```

---

## 🔐 Demo Credentials

Once deployed, login with these test accounts:

### Super Admin
```
Email: admin@test.com
Password: password
```

### Staff Member
```
Email: staff@test.com
Password: password
```

### Customer
```
Email: customer@test.com
Password: password
```

---

## 📋 Access Points

After deployment, access the application at:

| Service | URL | Description |
|---------|-----|-------------|
| **Frontend** | http://localhost:8080 | Main application |
| **Admin Panel** | http://localhost:8080/admin | Filament admin dashboard |
| **API** | http://localhost:8080/api | RESTful API endpoints |
| **Health Check** | http://localhost:8080/health | Application health status |
| **Detailed Health** | http://localhost:8080/health/detailed | Full system health check |

**Additional Services:**
- MySQL: `localhost:3306`
- Redis: `localhost:6379`
- Meilisearch: `localhost:7700`

---

## 📚 Documentation

### Deployment Guides

- **[DOCKER_DEPLOY.md](DOCKER_DEPLOY.md)** - Complete Docker deployment guide
  - Local development setup
  - Portainer deployment
  - Production deployment
  - GitOps configuration
  - Troubleshooting

- **[PROJECT_SUMMARY.md](PROJECT_SUMMARY.md)** - Project overview
  - Features breakdown
  - Architecture patterns
  - Database schema
  - Tech stack details

- **[INSTALLATION_CHECKLIST.md](INSTALLATION_CHECKLIST.md)** - Step-by-step verification
  - Pre-deployment checks
  - Post-deployment validation
  - Testing procedures

### Quick Reference

- **[quick-start.sh](quick-start.sh)** - Automated deployment script
- **[.env.example](.env.example)** - Environment configuration template
- **[docker-compose.yml](docker-compose.yml)** - Docker stack definition

---

## 🏗️ Architecture

### Tech Stack

**Backend:**
- Laravel 11.x (PHP 8.2+)
- MySQL 8.0
- Redis 7
- Meilisearch

**Frontend:**
- Livewire 3
- Tailwind CSS 3
- Alpine.js 3
- Blade Templates

**Admin Panel:**
- Filament 3

**Key Packages:**
- Laravel Jetstream (Teams)
- Laravel Sanctum (API)
- Spatie Permission
- Spatie Activity Log
- Spatie Media Library
- Spatie Laravel-PDF

### Multi-Tenancy Architecture

```
┌─────────────────────────────────────────┐
│           Application Layer             │
├─────────────────────────────────────────┤
│  ┌──────────────┐  ┌──────────────┐    │
│  │   Team A     │  │   Team B     │    │
│  │              │  │              │    │
│  │  Users       │  │  Users       │    │
│  │  Modules     │  │  Modules     │    │
│  │  Data        │  │  Data        │    │
│  └──────────────┘  └──────────────┘    │
├─────────────────────────────────────────┤
│     Team Isolation Layer (Scopes)      │
├─────────────────────────────────────────┤
│        Shared Infrastructure            │
│  • Authentication                       │
│  • Authorization (Roles/Permissions)    │
│  • Activity Logging                     │
│  • Media Management                     │
│  • Module System                        │
└─────────────────────────────────────────┘
```

### Module System

The application supports **installable/uninstallable modules**:

```bash
# List available modules
docker exec laravel-fsm-app php artisan module:list

# Install a module
docker exec laravel-fsm-app php artisan module:install crm

# Uninstall a module
docker exec laravel-fsm-app php artisan module:uninstall crm
```

Modules available in Phase 1:
- Core (always active)
- Activity Log
- Media Library
- API

*Additional modules (CRM, Ticketing, etc.) coming in Phase 2+*

---

## 🐳 Docker Services

The stack includes 6 services:

| Service | Description | Container Name |
|---------|-------------|----------------|
| **app** | Laravel application (Nginx + PHP-FPM) | laravel-fsm-app |
| **mysql** | MySQL 8.0 database | laravel-fsm-mysql |
| **redis** | Redis cache & queue | laravel-fsm-redis |
| **meilisearch** | Full-text search engine | laravel-fsm-meilisearch |
| **queue** | Queue worker | laravel-fsm-queue |
| **scheduler** | Laravel scheduler (cron) | laravel-fsm-scheduler |

---

## ⚙️ Configuration

### Environment Variables

Key variables to configure in `.env`:

```env
# Application
APP_NAME="Laravel FSM Phase 1"
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-domain.com

# Database
DB_PASSWORD=your_secure_password
DB_ROOT_PASSWORD=your_root_password

# GitOps (optional)
GITOPS_ENABLED=true
GIT_REPO=https://github.com/your-username/laravel-fsm.git
GIT_BRANCH=main
GIT_TOKEN=your_access_token
```

See [.env.example](.env.example) for full configuration options.

---

## 🔄 GitOps Deployment

Enable **automatic updates** from your Git repository:

1. **Enable GitOps in `.env`:**
   ```env
   GITOPS_ENABLED=true
   GIT_REPO=https://github.com/your-username/laravel-fsm.git
   GIT_BRANCH=main
   GIT_TOKEN=ghp_your_token_here
   ```

2. **Deploy with GitOps:**
   ```bash
   docker-compose up -d --build
   ```

3. **Updates happen automatically every 5 minutes:**
   - Pulls latest code from Git
   - Installs new dependencies
   - Runs new migrations
   - Clears caches
   - Reloads PHP-FPM (zero downtime)

**View GitOps activity:**
```bash
docker exec laravel-fsm-app tail -f /var/log/gitops/updates.log
```

---

## 🧪 Testing

### Manual Testing

```bash
# Access the application
docker exec -it laravel-fsm-app bash

# Run migrations
php artisan migrate:status

# List routes
php artisan route:list

# Test module system
php artisan module:list

# Run tinker
php artisan tinker
>>> App\Models\User::count()
>>> App\Models\Team::count()
```

### Health Checks

```bash
# Basic health
curl http://localhost:8080/health

# Detailed health (database, redis, storage)
curl http://localhost:8080/health/detailed
```

---

## 🔧 Development

### Running Artisan Commands

```bash
# General format
docker exec laravel-fsm-app php artisan [command]

# Examples:
docker exec laravel-fsm-app php artisan migrate
docker exec laravel-fsm-app php artisan db:seed
docker exec laravel-fsm-app php artisan cache:clear
docker exec laravel-fsm-app php artisan queue:work
```

### Accessing Container Shell

```bash
docker exec -it laravel-fsm-app bash
```

### Viewing Logs

```bash
# Application container logs
docker-compose logs -f app

# Laravel application logs
docker exec laravel-fsm-app tail -f storage/logs/laravel.log

# All services
docker-compose logs -f
```

### Database Access

```bash
# MySQL CLI
docker exec -it laravel-fsm-mysql mysql -usail -p

# Run SQL file
docker exec -i laravel-fsm-mysql mysql -usail -ppassword laravel_fsm < backup.sql
```

---

## 🛠️ Useful Commands

### Service Management

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose stop

# Restart services
docker-compose restart

# View status
docker-compose ps

# Remove everything (including volumes)
docker-compose down -v
```

### Module Management

```bash
# List available modules
docker exec laravel-fsm-app php artisan module:list

# Install module
docker exec laravel-fsm-app php artisan module:install [module-slug]

# Uninstall module
docker exec laravel-fsm-app php artisan module:uninstall [module-slug]
```

### Cache Management

```bash
# Clear all caches
docker exec laravel-fsm-app php artisan optimize:clear

# Clear specific caches
docker exec laravel-fsm-app php artisan cache:clear
docker exec laravel-fsm-app php artisan config:clear
docker exec laravel-fsm-app php artisan route:clear
docker exec laravel-fsm-app php artisan view:clear
```

---

## 📦 What's Included

### Database Tables (16 tables)

- **Authentication:** users, password_reset_tokens, sessions
- **Multi-Tenancy:** teams, team_user, team_invitations
- **Authorization:** roles, permissions, model_has_roles, model_has_permissions, role_has_permissions
- **Modules:** modules, module_dependencies, module_settings, installed_modules
- **Activity:** activity_log
- **Media:** media
- **API:** personal_access_tokens
- **System:** cache, jobs, job_batches, failed_jobs

### Test Data

- **3 Test Users** (Super Admin, Staff, Customer)
- **1 Default Team** ("Test Organization")
- **4 Roles** with full permissions
- **20 Available Modules** (ready to install)

### Admin Resources

- User Management
- Module Management
- Activity Log Viewer
- Global Search

---

## 🚦 Production Checklist

Before deploying to production:

- [ ] Change all default passwords in `.env`
- [ ] Set `APP_ENV=production`
- [ ] Set `APP_DEBUG=false`
- [ ] Configure proper `APP_URL`
- [ ] Set up SSL certificate (HTTPS)
- [ ] Configure firewall rules
- [ ] Set up automated backups
- [ ] Configure monitoring
- [ ] Review security settings
- [ ] Set up log rotation
- [ ] Configure mail server
- [ ] Test backup restoration

See [DOCKER_DEPLOY.md](DOCKER_DEPLOY.md) for detailed production deployment guide.

---

## 📖 API Documentation

The API is available at `/api` with token-based authentication.

### Generate API Token

```bash
# Via tinker
docker exec laravel-fsm-app php artisan tinker
>>> $user = User::find(1)
>>> $token = $user->createToken('api-token')->plainTextToken
>>> echo $token
```

### Example API Request

```bash
curl -H "Authorization: Bearer YOUR_TOKEN_HERE" \
     http://localhost:8080/api/user
```

---

## 🤝 Support

### Documentation
- [Docker Deployment Guide](DOCKER_DEPLOY.md)
- [Project Summary](PROJECT_SUMMARY.md)
- [Installation Checklist](INSTALLATION_CHECKLIST.md)

### Resources
- [Laravel Documentation](https://laravel.com/docs/11.x)
- [Filament Documentation](https://filamentphp.com/docs)
- [Docker Documentation](https://docs.docker.com/)

---

## 📄 License

This project is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 🎉 Next Steps

1. **Deploy the application** using `quick-start.sh`
2. **Login to admin panel** at `/admin`
3. **Create your first team** and invite users
4. **Install modules** as needed
5. **Customize** views and business logic
6. **Build Phase 2 features** (CRM, Ticketing, etc.)

---

<div align="center">

**Built with ❤️ using Laravel 11 and modern PHP**

[Report Bug](https://github.com/your-repo/issues) • [Request Feature](https://github.com/your-repo/issues)

</div>
