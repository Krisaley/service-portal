# Laravel FSM - Unified Task Management

**Lead:** Claude (Coordinator)  
**Updated:** 2025-11-02 09:00 UTC  
**Status:** Active Deployment Phase

---

## Quick Command Reference

```bash
# Gemini Commands
gemini -p "task description"           # Execute with confirmation
gemini -yolo -p "task description"     # Auto-accept mode

# Copilot Commands  
copilot -p "task description"          # Execute with confirmation
copilot -yolo -p "task description"    # Auto-accept mode

# Examples
gemini -yolo -p "add log rotation to docker-compose.yml"
copilot -p "review NavigationMenu.php for security issues"
```

---

## Current Sprint: Production Deployment

**Goal:** Deploy working UI to http://192.168.0.5:8080

### 🔴 Critical Tasks (Do Now)

- [ ] **DEPLOY-001** Git commit Jetstream UI (27 files)
  - **Owner:** Claude (Lead)
  - **Status:** Ready to execute
  - **Files:** app/Providers/*, app/Actions/*, resources/views/components/*
  - **Command:** `git add . && git commit && git push origin phase1-core`
  
- [ ] **DEPLOY-002** Redeploy Portainer stack
  - **Owner:** Claude (Lead) 
  - **Status:** Blocked by DEPLOY-001
  - **Steps:** Delete image → Redeploy → Monitor logs

- [ ] **FIX-001** Database connection timeout
  - **Owner:** Unassigned
  - **File:** docker/gitops-entrypoint.sh
  - **Change:** MAX_ATTEMPTS=30 → MAX_ATTEMPTS=90
  - **Time:** 15 min
  - **Delegate:** `gemini -yolo -p "fix database timeout in gitops-entrypoint.sh"`

### 🟠 High Priority (Do Soon)

- [x] **FIX-002** Meilisearch log explosion
  - **Owner:** Gemini ✅ COMPLETED
  - **File:** docker-compose.yml
  - **Change:** Added MEILI_LOG_LEVEL: 'WARN'
  - **Status:** Done, awaiting git commit

- [ ] **FIX-003** Docker log rotation
  - **Owner:** Unassigned
  - **File:** docker-compose.yml  
  - **Add:** Logging config to all services
  - **Time:** 10 min
  - **Delegate:** `gemini -yolo -p "add log rotation config to all docker services"`

- [ ] **REVIEW-001** Code review Jetstream components
  - **Owner:** Copilot (In Progress)
  - **Files:** 27 files created by laravel-architect
  - **Focus:** Security, best practices, type hints
  - **Time:** 20 min
  - **Status:** Agent running

### 🟢 Normal Priority (Do Later)

- [ ] **OPT-001** Reduce GitOps interval
  - **Change:** GITOPS_INTERVAL=300 → GITOPS_INTERVAL=1800
  - **Delegate:** `gemini -p "reduce gitops interval to 30 minutes"`

- [ ] **OPT-002** Redis memory overcommit (host-level)
  - **Requires:** SSH to 192.168.0.5
  - **Command:** `sudo sysctl -w vm.overcommit_memory=1`

- [ ] **OPT-003** MySQL auth plugin update
  - **Change:** mysql_native_password → caching_sha2_password
  - **Priority:** Low (works fine now)

---

## Completed Tasks

- [x] **SETUP-001** Jetstream UI framework (Claude - laravel-architect agent)
  - Created 27 files: Providers, Actions, Components, Views
  - Fixes HTTP 500 error
  - Completed: 2025-11-02 08:00

- [x] **SETUP-002** Log analysis (Claude - general-purpose agent)  
  - Analyzed 6 service logs
  - Identified 2 critical issues
  - Completed: 2025-11-02 07:50

- [x] **SETUP-003** Multi-agent coordination
  - Created task management system
  - Completed: 2025-11-02 08:45

- [x] **FIX-002** Meilisearch logging (Gemini)
  - Added MEILI_LOG_LEVEL: 'WARN' to docker-compose.yml
  - Completed: 2025-11-02 08:55

---

## Agent Command System

### How It Works

1. **Claude (Lead)** manages this file and coordinates all work
2. **User delegates** to Gemini/Copilot via command-style prompts
3. **Agents execute** tasks and report back
4. **Claude updates** this file with results

### Command Syntax

**Gemini:**
```bash
# With confirmation
gemini -p "description of task"

# Auto-accept mode (use for trusted operations)
gemini -yolo -p "description of task"
```

**Copilot:**
```bash
# With confirmation  
copilot -p "description of task"

# Auto-accept mode
copilot -yolo -p "description of task"
```

### When to Use Each Agent

**Gemini (Configuration & Docs):**
- YAML/config file edits
- Docker compose changes
- Documentation updates
- Testing scripts
- Simple, well-defined tasks

**Copilot (Code Quality):**
- Code reviews
- Refactoring suggestions
- Type hint improvements
- Security audits
- Performance optimization

**Claude (Lead - Architecture & Coordination):**
- Complex Laravel features
- Multi-step deployments
- Git operations
- Agent coordination
- Architecture decisions

---

## Task Queue

### Ready to Delegate

```bash
# Database timeout fix (15 min)
gemini -yolo -p "In docker/gitops-entrypoint.sh, find the wait_for_database function and change MAX_ATTEMPTS from 30 to 90. This increases database connection timeout from 60s to 180s."

# Docker log rotation (10 min)
gemini -yolo -p "Add log rotation to all services in docker-compose.yml. Use json-file driver with max-size 10m and max-file 3."

# Health check improvements (20 min)
gemini -p "Update docker-compose.yml to change all depends_on from service_started to service_healthy. Ensure MySQL, Redis, and Meilisearch have proper healthcheck configurations."
```

---

## Project Context

**Repository:** https://github.com/Krisaley/service-portal.git  
**Branch:** phase1-core  
**Server:** 192.168.0.5  
**Portainer:** http://192.168.0.5:9000  
**App:** http://192.168.0.5:8080 (currently HTTP 500)

**Stack Services:**
- MySQL ✅ Healthy
- Redis ✅ Healthy  
- Meilisearch ✅ Healthy
- App ❌ HTTP 500 (missing UI - fixed, awaiting deploy)
- Queue ⏳ Not tested
- Scheduler ⏳ Not tested

**Recent Work:**
- Created 27 Jetstream files (auth UI, teams, navigation)
- Fixed Meilisearch logging (WARN level)
- Updated gitops script (auto-publish vendor resources)

---

## Status Log

### 2025-11-02 09:00 UTC - Claude
- Cleaned up coordination files (consolidated to TASKS.md)
- Created command-style delegation system
- Ready to commit Jetstream UI + Meilisearch fix

### 2025-11-02 08:55 UTC - Gemini
- ✅ Completed FIX-002 (Meilisearch logging)
- Added MEILI_LOG_LEVEL: 'WARN' to docker-compose.yml

### 2025-11-02 08:00 UTC - Claude  
- ✅ Completed SETUP-001 (Jetstream UI framework)
- Used laravel-architect agent to create 27 files

---

## Next Actions

1. **Claude:** Commit and push (Jetstream UI + Meilisearch fix)
2. **Claude:** Guide Portainer redeploy
3. **User:** Delegate database timeout fix to Gemini
4. **User:** Verify UI loads after deployment

---

**Managed by:** Claude Code (Lead Agent)  
**For:** Multi-agent coordination (Claude + Gemini + Copilot)
