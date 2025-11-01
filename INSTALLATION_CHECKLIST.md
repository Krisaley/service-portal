# Installation Checklist - Laravel FSM Platform Phase 1

Use this checklist to ensure a successful installation and deployment.

## Pre-Installation Requirements

### System Requirements
- [ ] PHP 8.2 or higher installed
- [ ] Composer installed
- [ ] Node.js 18+ and NPM installed
- [ ] MySQL 8.0+ installed and running
- [ ] Git installed (optional)

### Optional Requirements
- [ ] Redis server (for caching and queues)
- [ ] Meilisearch (for search functionality)
- [ ] Supervisor (for queue workers in production)

## Installation Steps

### 1. Download and Setup
```bash
- [ ] Navigate to project directory: cd C:\claude_ai\laravel-fsm-phase1
- [ ] Install PHP dependencies: composer install
- [ ] Install JavaScript dependencies: npm install
```

### 2. Environment Configuration
```bash
- [ ] Copy environment file: cp .env.example .env
- [ ] Generate application key: php artisan key:generate
- [ ] Edit .env with your settings (see below)
```

#### Required .env Settings
```env
- [ ] APP_NAME="Your Company Name"
- [ ] APP_URL=http://localhost (or your domain)
- [ ] DB_DATABASE=laravel_fsm
- [ ] DB_USERNAME=your_db_user
- [ ] DB_PASSWORD=your_db_password
```

#### Optional .env Settings
```env
- [ ] REDIS_HOST=127.0.0.1 (if using Redis)
- [ ] MAIL_MAILER=smtp (configure SMTP settings)
- [ ] MEILISEARCH_HOST=http://127.0.0.1:7700 (if using search)
```

### 3. Database Setup
```bash
- [ ] Create database: CREATE DATABASE laravel_fsm;
- [ ] Run migrations: php artisan migrate
- [ ] Seed database: php artisan db:seed
- [ ] Verify users created: php artisan tinker -> User::count();
```

### 4. Build Frontend Assets
```bash
- [ ] Development build: npm run dev
- [ ] OR Production build: npm run build
```

### 5. Storage and Permissions
```bash
- [ ] Create storage link: php artisan storage:link
```

**Windows:**
```bash
- [ ] No additional permissions needed
```

**Linux/Mac:**
```bash
- [ ] Set permissions: chmod -R 775 storage bootstrap/cache
- [ ] Set ownership: chown -R www-data:www-data storage bootstrap/cache
```

### 6. Test Installation
```bash
- [ ] Start dev server: php artisan serve
- [ ] Open browser: http://localhost:8000
- [ ] Verify welcome page loads
```

### 7. Test Authentication
```bash
- [ ] Click "Log In"
- [ ] Use: admin@test.com / password
- [ ] Verify dashboard loads
- [ ] Check stats cards display correctly
```

### 8. Test Admin Panel
```bash
- [ ] Navigate to: http://localhost:8000/admin
- [ ] Verify Filament admin loads
- [ ] Check Users menu item
- [ ] Check Modules menu item
- [ ] Test creating a new user
```

### 9. Test Module System
```bash
- [ ] Navigate to: Dashboard -> Manage Modules
- [ ] Verify "Available Modules" section shows modules
- [ ] Test installing a module (e.g., CRM)
- [ ] Verify it moves to "Installed Modules"
- [ ] Test uninstalling (if not core module)
```

### 10. Test API
```bash
- [ ] Generate API token in dashboard
- [ ] Test API: curl http://localhost:8000/api/user -H "Authorization: Bearer YOUR_TOKEN"
- [ ] Verify JSON response with user data
```

## Post-Installation Configuration

### Production Only

#### Cache Optimization
```bash
- [ ] php artisan config:cache
- [ ] php artisan route:cache
- [ ] php artisan view:cache
```

#### Queue Workers (if using queues)
```bash
- [ ] Set QUEUE_CONNECTION=redis in .env
- [ ] Install Supervisor
- [ ] Configure worker (see DEPLOYMENT.md)
- [ ] Start workers: supervisorctl start laravel-worker:*
```

#### Security
```bash
- [ ] Change all test user passwords
- [ ] Set APP_DEBUG=false in .env
- [ ] Configure SSL certificate
- [ ] Set up firewall rules
- [ ] Configure fail2ban
- [ ] Enable rate limiting
```

#### Backups
```bash
- [ ] Install backup package: composer require spatie/laravel-backup
- [ ] Configure backup destinations in config/backup.php
- [ ] Test backup: php artisan backup:run
- [ ] Schedule backups in crontab
```

## Verification Checklist

### Core Functionality
- [ ] Welcome page loads without errors
- [ ] User registration works
- [ ] User login works
- [ ] Dashboard displays stats correctly
- [ ] Team switching works
- [ ] Profile update works
- [ ] Logout works

### Admin Panel
- [ ] Filament admin is accessible at /admin
- [ ] User management works (create, edit, delete)
- [ ] Role assignment works
- [ ] Module management works
- [ ] Activity log is viewable

### Module System
- [ ] Modules page loads at /modules
- [ ] Can view installed modules
- [ ] Can view available modules
- [ ] Can install a module
- [ ] Dependency checking works
- [ ] Can uninstall a module
- [ ] Core modules cannot be uninstalled

### API
- [ ] API token generation works
- [ ] GET /api/user returns user data
- [ ] GET /api/modules returns module list
- [ ] POST /api/modules/{id}/install works
- [ ] DELETE /api/modules/{id}/uninstall works
- [ ] Unauthorized requests return 401

### Database
- [ ] All 9 migrations ran successfully
- [ ] All tables created (check with: SHOW TABLES;)
- [ ] 4 roles exist (Super Admin, Admin, Staff, Customer)
- [ ] 3 test users exist
- [ ] 20 modules seeded
- [ ] Team records exist for each user

### Performance
- [ ] Page load time < 2 seconds
- [ ] No N+1 query issues (check debug bar)
- [ ] Assets load correctly
- [ ] No console errors in browser

### Security
- [ ] CSRF protection working
- [ ] XSS protection working
- [ ] SQL injection protected
- [ ] Passwords are hashed
- [ ] Middleware protecting routes
- [ ] Unauthorized access denied

## Common Issues and Solutions

### Issue: 500 Internal Server Error
**Solutions:**
- [ ] Check storage permissions
- [ ] Run: php artisan cache:clear
- [ ] Check: tail -f storage/logs/laravel.log
- [ ] Verify .env database credentials

### Issue: Database Connection Failed
**Solutions:**
- [ ] Verify MySQL is running
- [ ] Check .env DB credentials
- [ ] Test connection: php artisan tinker -> DB::connection()->getPdo();
- [ ] Check MySQL port (usually 3306)

### Issue: npm run dev/build fails
**Solutions:**
- [ ] Delete node_modules folder
- [ ] Run: npm install
- [ ] Clear NPM cache: npm cache clean --force
- [ ] Try: npm run build

### Issue: Filament assets not loading
**Solutions:**
- [ ] Run: php artisan filament:assets
- [ ] Run: npm run build
- [ ] Clear cache: php artisan cache:clear
- [ ] Check public/build directory exists

### Issue: Queue jobs not processing
**Solutions:**
- [ ] Verify Redis is running
- [ ] Check QUEUE_CONNECTION in .env
- [ ] Run manually: php artisan queue:work --once
- [ ] Check queue table for failed jobs

## Support Resources

### Documentation
- [ ] README.md - Project overview
- [ ] DEPLOYMENT.md - Production deployment guide
- [ ] PROJECT_SUMMARY.md - Complete feature list
- [ ] DIRECTORY_TREE.txt - File structure

### External Documentation
- [ ] Laravel 11: https://laravel.com/docs/11.x
- [ ] Filament 3: https://filamentphp.com/docs
- [ ] Jetstream: https://jetstream.laravel.com
- [ ] Tailwind CSS: https://tailwindcss.com/docs

### Community
- [ ] Laravel Discord: https://discord.gg/laravel
- [ ] Filament Discord: https://filamentphp.com/discord
- [ ] Stack Overflow: tag [laravel]

## Next Steps After Installation

### Immediate (Today)
- [ ] Change test user passwords
- [ ] Configure email settings
- [ ] Test all core features
- [ ] Review documentation

### This Week
- [ ] Configure Redis for caching
- [ ] Set up queue workers
- [ ] Configure automated backups
- [ ] Install SSL certificate (production)

### Next Week
- [ ] Begin Phase 2 development (CRM module)
- [ ] Set up monitoring and logging
- [ ] Configure error tracking
- [ ] Load test the application

### This Month
- [ ] Implement additional modules
- [ ] Train team members
- [ ] Set up staging environment
- [ ] Plan production deployment

## Installation Complete! 🎉

When all checkboxes above are complete, your Laravel FSM Platform Phase 1 is fully installed and ready for use.

**Default Login Credentials:**
- Super Admin: admin@test.com / password
- Staff User: staff@test.com / password
- Customer: customer@test.com / password

**IMPORTANT:** Change all passwords before going to production!

---

**Need Help?**
- Review DEPLOYMENT.md for detailed instructions
- Check PROJECT_SUMMARY.md for feature overview
- Consult Laravel documentation for framework issues
- Review logs in storage/logs/laravel.log
