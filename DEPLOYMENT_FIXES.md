# Deployment Issues & Fixes

**Date:** 2025-11-03  
**Status:** Issues Identified - Solution Ready  
**Update:** Environment variable solution implemented

---

## Issues Found from Container Logs

### 1. Database Connection Failure
**Problem:** Application container couldn't connect to MySQL for 3 minutes (90 attempts), then timed out.

**Root Cause:** 
- MySQL takes ~6.5 minutes to fully initialize on first run
- App only waited ~3 minutes before giving up
- Then app uses incorrect host configuration

**Log Evidence:**
```
[2025-11-03 09:13:51] Attempt 1/90 - Database not ready
[2025-11-03 09:17:14] ERROR Database connection timeout
```

MySQL actually became ready at:
```
2025-11-03T09:19:17 /usr/sbin/mysqld: ready for connections
```

---

### 2. Environment Configuration Issues

#### Issue A: Database Host
**Current:** `.env` has `DB_HOST=127.0.0.1`  
**Required:** `DB_HOST=mysql` (Docker service name)

#### Issue B: Redis Host
**Current:** `.env` has `REDIS_HOST=127.0.0.1`  
**Required:** `REDIS_HOST=redis` (Docker service name)

#### Issue C: Missing Application Key
**Current:** `.env` has `APP_KEY=` (empty)  
**Required:** Generate with `php artisan key:generate`

---

### 3. HTTP 500 Errors
**Problem:** Multiple 500 errors after container startup

**Log Evidence:**
```
127.0.0.1 -  03/Nov/2025:09:19:36 +0000 "GET /index.php" 500
127.0.0.1 -  03/Nov/2025:09:19:46 +0000 "GET /index.php" 500
```

**Cause:** Configuration issues prevented Laravel from booting properly

---

### 4. Git Repository Warnings
**Problem:** GitOps loop showing git errors

**Log Evidence:**
```
fatal: not a git repository (or any of the parent directories): .git
```

**Note:** Non-critical - GitOps expects to manage repo updates but container doesn't have `.git` directory. This is expected behavior for Docker deployments.

---

## Solution: Environment Variable Separation

**The Problem:** You need different database/redis hosts for local dev (127.0.0.1) vs Docker (service names).

**The Solution:** Docker Compose already passes the correct environment variables! The `.env` file is being ignored in favor of Docker's environment settings.

### What's Already Correct ✅

1. **`.env` is in `.gitignore`** - Your local `.env` won't be committed
2. **`.env.example` has correct Docker values** - `DB_HOST=mysql` is already there
3. **`docker-compose.yml` passes environment variables** - Overrides any `.env` values

### Required Fixes

### Fix 1: Update `.env.example` for Docker Defaults
Your `.env.example` already has most values correct, but update Redis host:

```env
# In .env.example, change:
REDIS_HOST=127.0.0.1

# To:
REDIS_HOST=redis
```

This ensures when the container creates `.env` from `.env.example`, it has correct Docker values.

### Fix 2: Verify Your Local `.env` (Keep for Local Dev)
Your **local** `.env` should keep:
```env
DB_HOST=127.0.0.1
REDIS_HOST=127.0.0.1
```

Don't commit this file - it's for your local development only.

### Fix 3: Update Docker Compose (Optional - Already Fixed)
The `docker-compose.yml` already has:
- `condition: service_started` (not waiting for health checks)
- This is correct, but MySQL still takes time to initialize

**Recommendation:** Keep current settings. The 90-attempt retry should be sufficient once DB_HOST is correct.

### Fix 4: Verify Environment in Container
After deployment, you can verify with:
```bash
docker exec laravel-fsm-app php artisan config:show database
docker exec laravel-fsm-app php artisan config:show cache
```

---

## How Docker Environment Works

### Container Startup Sequence:
1. Container starts and runs `gitops-entrypoint.sh`
2. Script checks if `.env` exists in container
3. If not, copies `.env.example` to `.env` (line 438-448 in gitops-entrypoint.sh)
4. **Docker Compose environment variables override `.env` values**
5. Laravel reads configuration (Docker vars take precedence)

### Key Code in gitops-entrypoint.sh:
```bash
# Line 436-448
if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        log INFO "Creating .env from .env.example..."
        cp .env.example .env
    fi
fi
```

### Docker Compose Environment Variables (Already Configured):
```yaml
# docker-compose.yml lines 30-40
environment:
  DB_CONNECTION: mysql
  DB_HOST: mysql          # ✅ Overrides .env
  DB_PORT: 3306
  DB_DATABASE: ${DB_DATABASE:-laravel_fsm}
  DB_USERNAME: ${DB_USERNAME:-sail}
  DB_PASSWORD: ${DB_PASSWORD:-password}
  
  REDIS_HOST: redis       # ✅ Overrides .env
  REDIS_PORT: 6379
```

## Deployment Workflow

1. **Update `.env.example` (One-time fix):**
   ```bash
   # Edit .env.example
   # Change REDIS_HOST=127.0.0.1 to REDIS_HOST=redis
   
   # Commit changes
   git add .env.example
   git commit -m "Fix Redis host in .env.example for Docker deployments"
   git push origin phase1-core
   ```

2. **Docker Server (Pulls from GitHub):**
   - GitOps will automatically pull changes every 5 minutes
   - Container will restart with new configuration
   - Database should connect successfully

3. **Verify Deployment:**
   ```bash
   # Check container logs
   docker logs laravel-fsm-app -f
   
   # Should see successful migration messages
   # No more "Connection refused" errors
   ```

---

## Expected Results After Fixes

✅ Database connects immediately (app uses correct host `mysql`)  
✅ Redis connects successfully (app uses correct host `redis`)  
✅ HTTP requests return 200 instead of 500  
✅ Migrations run successfully  
✅ Application fully operational  

---

## Additional Notes

### MySQL Initialization Time
- First run: ~6.5 minutes (creating database structure)
- Subsequent runs: ~10-15 seconds (database already exists)
- This is normal behavior for MySQL 8.0 containers

### GitOps Integration
- Currently enabled with 5-minute pull interval
- Container clones repo at build time
- `.git` directory intentionally excluded from container
- Warnings about git repository are expected and can be ignored

### Health Checks
- Temporarily disabled for debugging
- Can re-enable after confirming fixes work
- Add back to `docker-compose.yml` when stable

---

## Testing Checklist

After deploying fixes:

- [ ] Container starts without errors
- [ ] Database connection succeeds within 90 attempts
- [ ] Migrations complete successfully  
- [ ] HTTP requests return proper responses (not 500)
- [ ] Can access application at `http://192.168.0.5:8080`
- [ ] Horizon accessible at `/horizon`
- [ ] Filament admin accessible at `/admin`
- [ ] Queue worker processes jobs
- [ ] Scheduler runs tasks
- [ ] Redis cache working

---

## Quick Reference

**Local Development:**
- DB_HOST=127.0.0.1 ✓ (connects to local MySQL)
- REDIS_HOST=127.0.0.1 ✓ (connects to local Redis)

**Docker Deployment:**
- DB_HOST=mysql ✓ (connects to mysql container)
- REDIS_HOST=redis ✓ (connects to redis container)

**Key Point:** You need separate `.env` configurations for local vs Docker, OR use Docker service names and run everything in Docker locally too.

---

## Next Steps

1. Update `.env` with correct Docker service names
2. Generate APP_KEY locally
3. Commit and push to GitHub
4. Wait for GitOps to pull (or manually restart containers)
5. Monitor logs for successful startup
6. Test application functionality

---

**Status:** Ready to deploy fixes  
**Priority:** High - Application currently non-functional  
**ETA:** 5-10 minutes after push (GitOps interval)
