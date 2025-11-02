# AI Helper Scripts

This folder contains scripts and tools for integrating with various AI assistants to accelerate development.

**⚠️ This folder is excluded from git** (via `.gitignore`)

---

## Available Scripts

### Ollama Integration

#### 1. `ollama-client.py`
**Purpose:** Python client for interacting with Ollama API
**Server:** http://192.168.0.104:11434
**Usage:**
```bash
# Install dependencies
pip install -r requirements.txt

# Run client
python ollama-client.py
```

**Features:**
- Code review automation
- Documentation generation
- Test case creation
- Refactoring suggestions

---

#### 2. `ollama-client.js`
**Purpose:** Node.js client for Ollama API
**Usage:**
```bash
# Install dependencies
npm install

# Run client
node ollama-client.js
```

**Features:**
- Same as Python client
- Better integration with npm-based projects
- Async/await patterns

---

#### 3. `ollama-batch-review.sh`
**Purpose:** Batch code review script using Ollama
**Usage:**
```bash
# Review all PHP files in app/
./ollama-batch-review.sh app/

# Review specific file
./ollama-batch-review.sh app/Models/Customer.php
```

**Output:** Creates review reports in logs/

---

## Multi-Agent Collaboration

### Available AI Agents

This project supports coordinated work across multiple AI assistants:

#### 1. **Claude Code** (Lead Agent)
- **Role:** Architect, coordinator, complex implementations
- **Best For:**
  - Multi-step features requiring planning
  - Laravel-specific architecture decisions
  - Git operations and deployments
  - Agent coordination
  - Complex debugging

#### 2. **GitHub Copilot**
- **Access:** Via GitHub API
- **Best For:**
  - Code reviews and security audits
  - Pull request management
  - GitHub Actions/CI/CD
  - Repository operations
  - Best practices enforcement

**How to delegate to Copilot:**
```bash
# From user to Claude:
"copilot -p 'review the Customer model for security issues'"
"copilot -yolo -p 'create PR for phase1-core branch'"
```

#### 3. **Google Gemini**
- **Best For:**
  - Configuration file editing (YAML, ENV)
  - Documentation updates
  - Simple, well-defined tasks
  - Testing scripts
  - Quick fixes

**How to delegate to Gemini:**
```bash
# From user to Claude:
"gemini -p 'add log rotation to docker-compose.yml'"
"gemini -yolo -p 'update .env.example with new variables'"
```

#### 4. **Ollama** (Local AI Server)
- **Server:** http://192.168.0.104:11434
- **Best For:**
  - Code reviews (offline)
  - Documentation generation
  - Test case suggestions
  - Batch processing
  - Privacy-sensitive operations

**How to use Ollama:**
```bash
# Direct script usage:
./ollama-batch-review.sh app/Models/

# Via Python client:
python ollama-client.py --review app/Models/Customer.php

# Via Node client:
node ollama-client.js review app/Models/Customer.php
```

---

## Task Delegation Strategy

### When to Use Each Agent

| Task Type | Recommended Agent | Why |
|-----------|------------------|-----|
| Architecture decisions | Claude Code | Context-aware, complex reasoning |
| Code reviews | Copilot or Ollama | Security focus, best practices |
| Config file edits | Gemini | Fast, accurate for YAML/JSON |
| Git operations | Claude Code or Copilot | Git expertise, PR management |
| Documentation | Gemini or Ollama | Writing quality, consistency |
| Database migrations | Claude Code | Laravel-specific patterns |
| Testing | Ollama | Batch generation, offline |
| Refactoring | Claude Code or Copilot | Context preservation |

### Delegation Patterns

#### Pattern 1: Parallel Work
```bash
# User delegates multiple tasks simultaneously
gemini -yolo -p "add health checks to docker-compose"
copilot -yolo -p "review security in Customer model"
# Claude continues with main feature

Result: 3 agents working concurrently
```

#### Pattern 2: Pipeline Work
```bash
# Sequential delegation with dependencies
1. Claude creates feature
2. "copilot -p 'review the new feature code'"
3. Claude applies review feedback
4. "gemini -p 'update documentation for new feature'"

Result: Quality pipeline with reviews
```

#### Pattern 3: Batch Processing
```bash
# Use Ollama for bulk operations
./ollama-batch-review.sh app/Models/
# Reviews all models in one pass

Result: Comprehensive analysis without API limits
```

---

## Command Reference

### Gemini Commands
```bash
# Standard (with confirmation)
gemini -p "task description"

# Auto-accept (for trusted operations)
gemini -yolo -p "task description"

# Examples
gemini -p "add NODE_ENV to .env.example"
gemini -yolo -p "fix typos in README.md"
```

### Copilot Commands
```bash
# Standard (with confirmation)
copilot -p "task description"

# Auto-accept
copilot -yolo -p "task description"

# Examples
copilot -p "review NavigationMenu.php for XSS vulnerabilities"
copilot -yolo -p "create PR from phase1-core to main"
```

### Ollama Commands
```bash
# Batch review
./ollama-batch-review.sh <path>

# Python client
python ollama-client.py --review <file>
python ollama-client.py --document <file>
python ollama-client.py --test <file>

# Node.js client
node ollama-client.js review <file>
node ollama-client.js document <file>
node ollama-client.js test <file>
```

---

## Integration Examples

### Example 1: Feature Development Pipeline
```bash
User: "Add customer search feature with tests and docs"

Claude: "I'll coordinate this across agents:
1. I'll create the search feature
2. I'll delegate tests to Ollama
3. I'll delegate docs to Gemini
4. I'll delegate security review to Copilot"

# Claude creates SearchController.php
# Then delegates in parallel:
"ollama: generate tests for SearchController"
"gemini: document the search API in README"
"copilot: review SearchController for SQL injection"

Result: Complete feature with tests, docs, and security review
```

### Example 2: Deployment Preparation
```bash
User: "Prepare for production deployment"

Claude coordinates:
1. copilot -p "review all code for security issues"
2. gemini -p "update production .env.example"
3. Claude runs migrations and tests
4. copilot -p "create release PR with changelog"

Result: Production-ready deployment with checks
```

### Example 3: Refactoring Sprint
```bash
User: "Refactor app/Models/ for better performance"

Claude coordinates:
1. ./ollama-batch-review.sh app/Models/ (identify issues)
2. Claude refactors based on review
3. copilot -p "verify refactoring follows Laravel best practices"
4. gemini -p "update model documentation"

Result: Improved code with verification and docs
```

---

## Configuration

### Ollama Server
**Host:** 192.168.0.104
**Port:** 11434
**Status Check:**
```bash
curl http://192.168.0.104:11434/api/tags
```

### GitHub Copilot
**Access:** Via GitHub API (configured in Claude Code)
**Permissions:** Requires GitHub token with repo access

### Google Gemini
**Access:** Via Claude Code coordination
**Best For:** Non-code text operations

---

## Best Practices

### DO ✅
- Delegate simple, well-defined tasks to Gemini
- Use Copilot for GitHub/PR operations
- Use Ollama for batch processing and privacy-sensitive reviews
- Let Claude coordinate complex, multi-step features
- Run parallel tasks when possible

### DON'T ❌
- Don't delegate complex architecture to Gemini
- Don't use Ollama for tasks requiring external API access
- Don't delegate git operations to Gemini
- Don't over-coordinate simple tasks (just do them)

---

## Troubleshooting

### Ollama Connection Issues
```bash
# Test connectivity
curl http://192.168.0.104:11434/api/tags

# Check if server is running
ping 192.168.0.104

# Restart Ollama container
docker restart ollama
```

### Script Permissions
```bash
# Make scripts executable
chmod +x .ai_helper_scripts/*.sh
```

### Python Dependencies
```bash
# Install requirements
pip install -r requirements.txt

# Or use venv
python -m venv venv
source venv/bin/activate
pip install -r requirements.txt
```

### Node Dependencies
```bash
# Install packages
cd .ai_helper_scripts
npm install
```

---

## Output Locations

### Ollama Reviews
```
logs/ollama-reviews/
├── 2025-11-02-Customer-review.md
├── 2025-11-02-User-review.md
└── batch-summary.md
```

### Copilot Reports
```
.ai-docs/
├── copilot-security-audit-2025-11-02.md
└── copilot-pr-review-123.md
```

### Gemini Outputs
Typically inline edits to files, logged in:
```
.ai-docs/gemini-tasks.log
```

---

## Maintenance

### Regular Tasks
- Clean up old review files in logs/
- Update Ollama models: `docker exec ollama ollama pull llama2`
- Archive completed task logs in .ai-docs/

### Updates
- Keep Python dependencies updated: `pip install -U -r requirements.txt`
- Update Node dependencies: `npm update`
- Check Ollama server version: `docker exec ollama ollama --version`

---

**Created:** 2025-11-02
**Purpose:** Multi-agent AI collaboration for rapid development
**Maintained By:** Development team + Claude Code
