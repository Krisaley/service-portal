# Laravel FSM Platform - Phase 1 Deployment Guide

## Complete File List

### Generated Files (50+ files created)

```
laravel-fsm-phase1/
├── .env.example
├── .gitignore
├── artisan
├── composer.json
├── package.json
├── postcss.config.js
├── README.md
├── tailwind.config.js
├── vite.config.js
├── DEPLOYMENT.md (this file)
│
├── app/
│   ├── Console/Commands/
│   │   ├── InstallModuleCommand.php
│   │   └── UninstallModuleCommand.php
│   ├── Filament/Resources/
│   │   ├── UserResource.php
│   │   ├── UserResource/Pages/
│   │   │   ├── ListUsers.php
│   │   │   ├── CreateUser.php
│   │   │   └── EditUser.php
│   │   ├── ModuleResource.php
│   │   └── ModuleResource/Pages/
│   │       ├── ListModules.php
│   │       ├── CreateModule.php
│   │       └── EditModule.php
│   ├── Http/Middleware/
│   │   └── EnsureTeamContext.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Team.php
│   │   ├── Module.php
│   │   ├── ModuleDependency.php
│   │   ├── ModuleSetting.php
│   │   └── InstalledModule.php
│   ├── Services/
│   │   └── ModuleService.php
│   └── Traits/
│       └── HasTeamScope.php
│
├── bootstrap/
│   └── app.php
│
├── config/
│   ├── modules.php
│   └── permission.php
│
├── database/
│   ├── factories/
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   ├── 0001_01_01_000001_create_cache_table.php
│   │   ├── 0001_01_01_000002_create_jobs_table.php
│   │   ├── 2024_01_01_000003_create_teams_table.php
│   │   ├── 2024_01_01_000004_create_personal_access_tokens_table.php
│   │   ├── 2024_01_01_000005_create_permission_tables.php
│   │   ├── 2024_01_01_000006_create_activity_log_table.php
│   │   ├── 2024_01_01_000007_create_media_table.php
│   │   └── 2024_01_01_000008_create_modules_table.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── RolePermissionSeeder.php
│       ├── TestUserSeeder.php
│       └── ModuleSeeder.php
│
├── public/
│   ├── .htaccess
│   └── index.php
│
├── resources/views/
│   ├── dashboard.blade.php
│   └── modules/
│       └── index.blade.php
│
└── routes/
    ├── web.php
    ├── api.php
    └── console.php
```

## Deployment Steps

### 1. Initial Setup

```bash
cd C:\claude_ai\laravel-fsm-phase1

# Install Composer dependencies
composer install --no-dev --optimize-autoloader

# Install NPM dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 2. Configure Environment

Edit `.env` file with your settings:

```env
APP_NAME="FSM Platform"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_fsm
DB_USERNAME=your_username
DB_PASSWORD=your_secure_password

# Cache & Queue
CACHE_STORE=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1

# Meilisearch (optional)
MEILISEARCH_HOST=http://127.0.0.1:7700
MEILISEARCH_KEY=your_key

# Mail
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_FROM_ADDRESS=noreply@your-domain.com
```

### 3. Database Setup

```bash
# Run migrations
php artisan migrate --force

# Seed database
php artisan db:seed --force
```

### 4. Build Frontend Assets

```bash
# Production build
npm run build
```

### 5. Storage & Permissions

```bash
# Create storage link
php artisan storage:link

# Set permissions (Linux/Mac)
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 6. Cache Optimization

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache
```

### 7. Queue Worker (Production)

```bash
# Run queue worker as supervisor process
php artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
```

**Supervisor Configuration** (`/etc/supervisor/conf.d/laravel-worker.conf`):

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/laravel-fsm-phase1/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=4
redirect_stderr=true
stdout_logfile=/path/to/laravel-fsm-phase1/storage/logs/worker.log
stopwaitsecs=3600
```

## Web Server Configuration

### Nginx Configuration

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com;
    root /path/to/laravel-fsm-phase1/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Apache Configuration

The included `.htaccess` file in `/public` should handle everything. Ensure `mod_rewrite` is enabled:

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

## Post-Deployment

### 1. Test Application

```bash
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Test queue
php artisan queue:work --once
```

### 2. Create First Admin User

Login with test credentials:
- Email: `admin@test.com`
- Password: `password`

**IMPORTANT**: Change the password immediately after first login!

### 3. Module Installation

Install optional modules via:
- Web UI: `/modules`
- Admin Panel: `/admin/modules`
- CLI: `php artisan module:install crm --team=1`

### 4. Security Checklist

- [ ] Change all test user passwords
- [ ] Configure HTTPS (SSL certificate)
- [ ] Enable firewall (UFW/iptables)
- [ ] Configure fail2ban
- [ ] Set up automated backups
- [ ] Configure monitoring (logs, uptime)
- [ ] Review and update `.env` security settings
- [ ] Enable rate limiting on API routes
- [ ] Configure CORS if needed

### 5. Performance Optimization

```bash
# Enable OPcache (php.ini)
opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000

# Redis optimization
# Edit /etc/redis/redis.conf
maxmemory 256mb
maxmemory-policy allkeys-lru
```

## Backup Strategy

### Database Backup (Daily)

```bash
# Add to crontab
0 2 * * * cd /path/to/laravel-fsm-phase1 && php artisan backup:run --only-db
```

### Full Backup (Weekly)

```bash
# Add to crontab
0 3 * * 0 cd /path/to/laravel-fsm-phase1 && php artisan backup:run
```

## Monitoring

### Logs Location

```
storage/logs/laravel.log          # Application logs
storage/logs/worker.log           # Queue worker logs
/var/log/nginx/error.log          # Nginx errors
/var/log/php8.2-fpm.log          # PHP-FPM logs
```

### Health Check

```bash
# Application health
curl https://your-domain.com/up

# API health
curl https://your-domain.com/api/user -H "Authorization: Bearer TOKEN"
```

## Troubleshooting

### Common Issues

**500 Internal Server Error:**
- Check storage permissions
- Clear cache: `php artisan cache:clear`
- Check logs: `tail -f storage/logs/laravel.log`

**Database Connection Failed:**
- Verify `.env` database credentials
- Test: `php artisan tinker` → `DB::connection()->getPdo();`

**Assets Not Loading:**
- Run `npm run build`
- Check `public/build` directory exists
- Verify web server static file serving

**Queue Jobs Not Processing:**
- Restart queue worker
- Check Redis connection
- Review `storage/logs/worker.log`

## Maintenance Mode

```bash
# Enable maintenance mode
php artisan down --secret="your-secret-token"

# Access during maintenance
https://your-domain.com/your-secret-token

# Disable maintenance mode
php artisan up
```

## Updating the Application

```bash
# Backup first!
php artisan backup:run

# Pull latest code (if using Git)
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader
npm install && npm run build

# Run migrations
php artisan migrate --force

# Clear and rebuild cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Restart queue workers
php artisan queue:restart
```

## Support Contacts

- **Technical Issues**: Check logs and documentation
- **Security Concerns**: Review Laravel security best practices
- **Performance Issues**: Enable query logging and profiling

## Next Steps

After successful deployment:
1. Configure backups and monitoring
2. Set up SSL certificate (Let's Encrypt)
3. Configure email settings for notifications
4. Install and test required modules
5. Train team members on system usage
6. Begin Phase 2 development (CRM, Products, etc.)
