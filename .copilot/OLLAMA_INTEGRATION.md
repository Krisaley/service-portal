# Ollama Integration Guide
## Local AI Code Analysis for Laravel FSM Phase 1

This guide explains how to use your local Ollama instances for code review, analysis, and generation.

---

## 🖥️ Available Ollama Instances

You have **two Ollama instances** available:

| Instance | GPU | VRAM | URL | Best For |
|----------|-----|------|-----|----------|
| **rtx3060** | RTX3060 | 12GB | http://192.168.0.104:11434 | Larger contexts, complex analysis |
| **vega64** | AMD VEGA64 | 8GB | http://192.168.0.5:11434 | Quick reviews, smaller tasks |

Both instances run **deepseek-coder:6.7b** - a specialized code model.

---

## 🚀 Quick Start

### Prerequisites

**Python Option:**
```bash
cd laravel-fsm-phase1/scripts
pip install -r requirements.txt
```

**Node.js Option:**
```bash
cd laravel-fsm-phase1/scripts
npm install
```

### Check Instance Health

**Python:**
```bash
python ollama-client.py --health
```

**Node.js:**
```bash
node ollama-client.js --health
```

**Expected Output:**
```
Checking Ollama instances...

✅ ONLINE - RTX3060: http://192.168.0.104:11434 (RTX3060, 12GB)
  Models: deepseek-coder:6.7b, ...
✅ ONLINE - VEGA64: http://192.168.0.5:11434 (AMD VEGA64, 8GB)
  Models: deepseek-coder:6.7b, ...
```

---

## 📋 Usage Examples

### 1. Code Review

Review a PHP file for issues, best practices, and security:

```bash
# Python
python ollama-client.py --review app/Models/User.php

# Node.js
node ollama-client.js --review app/Models/User.php

# Use specific instance
python ollama-client.py --instance vega64 --review app/Services/ModuleService.php
```

**What it checks:**
- ✅ Security vulnerabilities (SQL injection, XSS, etc.)
- ✅ Performance issues
- ✅ Code quality and best practices
- ✅ Potential bugs
- ✅ Suggestions for improvement

### 2. Explain Code

Get a clear explanation of what code does:

```bash
python ollama-client.py --explain app/Traits/HasTeamScope.php
```

### 3. Security Audit

Focused security analysis:

```bash
python ollama-client.py --security app/Http/Controllers/ApiController.php
```

**Checks for:**
- SQL injection vulnerabilities
- XSS (Cross-Site Scripting)
- CSRF (Cross-Site Request Forgery)
- Authentication/Authorization issues
- Data exposure risks
- Input validation problems

### 4. Generate Tests

Generate Pest test cases:

```bash
python ollama-client.py --generate-tests app/Services/ModuleService.php
```

### 5. Suggest Improvements

Get refactoring suggestions:

```bash
python ollama-client.py --improve app/Models/Module.php
```

### 6. Custom Prompts

Ask anything about Laravel, code, or architecture:

```bash
python ollama-client.py --prompt "Explain Laravel Jetstream multi-tenancy"

python ollama-client.py --prompt "How do I implement CQRS in Laravel?"

python ollama-client.py --prompt "Best practices for API rate limiting"
```

---

## 🔧 Advanced Usage

### Batch Review All PHP Files

Review entire directories:

```bash
# Make script executable
chmod +x scripts/ollama-batch-review.sh

# Run batch review (uses RTX3060 by default)
./scripts/ollama-batch-review.sh

# Use VEGA64 for batch review
./scripts/ollama-batch-review.sh vega64
```

**What it does:**
1. Finds all PHP files in:
   - `app/Models`
   - `app/Services`
   - `app/Http/Controllers`
   - `app/Filament/Resources`
   - `app/Console/Commands`
   - `database/seeders`

2. Reviews each file with Ollama
3. Saves reviews to `ollama-reviews/TIMESTAMP/`
4. Generates summary report

**Output structure:**
```
ollama-reviews/
└── 20250201_143022/
    ├── SUMMARY.md
    ├── app/
    │   ├── Models/
    │   │   ├── User.php.review.md
    │   │   ├── Team.php.review.md
    │   │   └── Module.php.review.md
    │   └── Services/
    │       └── ModuleService.php.review.md
    └── database/
        └── seeders/
            └── RolePermissionSeeder.php.review.md
```

### Review Specific Languages

```bash
# JavaScript
python ollama-client.py --review resources/js/app.js --language javascript

# TypeScript
python ollama-client.py --review resources/ts/components.ts --language typescript

# Blade templates
python ollama-client.py --review resources/views/dashboard.blade.php --language blade
```

---

## 🎯 Best Practices

### When to Use Each Instance

**Use RTX3060 (12GB VRAM) for:**
- Large files (500+ lines)
- Complex analysis requiring more context
- Generating comprehensive tests
- Architecture reviews
- Long discussions/explanations

**Use VEGA64 (8GB VRAM) for:**
- Quick code reviews (< 300 lines)
- Simple explanations
- Security scans
- Small improvements
- Batch processing individual files

### Optimizing Performance

1. **Parallel Reviews:**
   ```bash
   # Review multiple files in parallel using both instances
   python ollama-client.py --instance rtx3060 --review file1.php &
   python ollama-client.py --instance vega64 --review file2.php &
   wait
   ```

2. **Temperature Settings:**
   - Security audit: Low temp (0.2) for consistent results
   - Code review: Medium temp (0.3-0.5) for balanced analysis
   - Creative suggestions: Higher temp (0.6-0.8) for diverse ideas

---

## 🔌 Integration with Development Workflow

### Pre-Commit Hook

Add automatic code review before commits:

```bash
# .git/hooks/pre-commit
#!/bin/bash

# Get staged PHP files
STAGED_FILES=$(git diff --cached --name-only --diff-filter=ACM | grep ".php$")

if [ -n "$STAGED_FILES" ]; then
    echo "Running Ollama security audit..."

    for FILE in $STAGED_FILES; do
        python scripts/ollama-client.py --security "$FILE" > /dev/null 2>&1

        # Check exit code or parse output for critical issues
        # Fail commit if critical security issues found
    done
fi
```

### VS Code Integration

Add to `.vscode/tasks.json`:

```json
{
    "version": "2.0.0",
    "tasks": [
        {
            "label": "Ollama: Review Current File",
            "type": "shell",
            "command": "python",
            "args": [
                "${workspaceFolder}/scripts/ollama-client.py",
                "--review",
                "${file}"
            ],
            "group": "test",
            "presentation": {
                "reveal": "always",
                "panel": "new"
            }
        },
        {
            "label": "Ollama: Security Audit",
            "type": "shell",
            "command": "python",
            "args": [
                "${workspaceFolder}/scripts/ollama-client.py",
                "--security",
                "${file}"
            ],
            "group": "test"
        }
    ]
}
```

### CI/CD Pipeline

Add to `.github/workflows/ollama-review.yml`:

```yaml
name: Ollama Code Review

on: [pull_request]

jobs:
  review:
    runs-on: self-hosted  # Must have access to Ollama instances

    steps:
      - uses: actions/checkout@v3

      - name: Setup Python
        uses: actions/setup-python@v4
        with:
          python-version: '3.10'

      - name: Install dependencies
        run: pip install -r scripts/requirements.txt

      - name: Review changed files
        run: |
          git diff origin/main...HEAD --name-only | grep ".php$" | while read file; do
            python scripts/ollama-client.py --review "$file"
          done
```

---

## 📊 Example Workflows

### Workflow 1: New Feature Development

```bash
# 1. Plan the feature
python ollama-client.py --prompt "How should I implement a warranty tracking module in Laravel?"

# 2. Generate initial code
python ollama-client.py --prompt "Generate a Warranty model with tracking fields"

# 3. Review generated code
python ollama-client.py --review app/Models/Warranty.php

# 4. Security audit
python ollama-client.py --security app/Models/Warranty.php

# 5. Generate tests
python ollama-client.py --generate-tests app/Models/Warranty.php
```

### Workflow 2: Security Hardening

```bash
# Audit all controllers
find app/Http/Controllers -name "*.php" | while read file; do
    python ollama-client.py --security "$file" > "security-audit/$(basename $file).md"
done

# Review audit results
cat security-audit/*.md | grep -i "vulnerability\|risk\|danger"
```

### Workflow 3: Code Quality Improvement

```bash
# Get improvement suggestions for all models
find app/Models -name "*.php" | while read file; do
    echo "=== $file ===" >> improvements.md
    python ollama-client.py --improve "$file" >> improvements.md
    echo "" >> improvements.md
done

# Review suggestions
cat improvements.md
```

---

## 🛠️ Troubleshooting

### Ollama Instance Offline

```bash
# Check if Ollama is running
curl http://192.168.0.104:11434/api/tags
curl http://192.168.0.5:11434/api/tags

# SSH to instance and start Ollama if needed
ssh user@192.168.0.104
systemctl start ollama  # or: ollama serve
```

### Slow Responses

- Use VEGA64 for smaller files
- Reduce context size by reviewing smaller code sections
- Check GPU utilization on instance

### Model Not Found

```bash
# List available models
python ollama-client.py --list-models

# Pull deepseek-coder if missing
ssh user@192.168.0.104
ollama pull deepseek-coder:6.7b
```

### Network Timeouts

Increase timeout in script (default: 300 seconds):
```python
# Edit ollama-client.py
timeout = 600  # 10 minutes for large files
```

---

## 📈 Performance Metrics

### Typical Response Times

| Task | Small File (<200 lines) | Medium File (200-500) | Large File (500+) |
|------|-------------------------|----------------------|-------------------|
| **Code Review** | 15-30s | 30-60s | 60-120s |
| **Security Audit** | 10-20s | 20-45s | 45-90s |
| **Explain Code** | 10-15s | 15-30s | 30-60s |
| **Generate Tests** | 20-40s | 40-80s | 80-150s |

*Times vary based on GPU load and model cache*

### Memory Usage

| Instance | Idle | Processing | Peak |
|----------|------|------------|------|
| **RTX3060 (12GB)** | ~2GB | ~5-7GB | ~9GB |
| **VEGA64 (8GB)** | ~1.5GB | ~4-5GB | ~6.5GB |

---

## 🎓 Tips & Tricks

1. **Chain Multiple Analyses:**
   ```bash
   python ollama-client.py --review file.php > review.md
   python ollama-client.py --security file.php >> review.md
   python ollama-client.py --improve file.php >> review.md
   ```

2. **Compare Instance Outputs:**
   ```bash
   python ollama-client.py --instance rtx3060 --review file.php > rtx-review.md
   python ollama-client.py --instance vega64 --review file.php > vega-review.md
   diff rtx-review.md vega-review.md
   ```

3. **Use for Documentation:**
   ```bash
   python ollama-client.py --explain app/Services/ModuleService.php > docs/ModuleService.md
   ```

4. **Interactive Mode:**
   ```bash
   # Start conversation
   python ollama-client.py --prompt "I need help with Laravel multi-tenancy"

   # Continue conversation
   python ollama-client.py --prompt "How do I scope queries by team?"
   ```

---

## 📚 Additional Resources

- **Ollama Documentation:** https://ollama.ai/docs
- **DeepSeek Coder Model:** https://huggingface.co/deepseek-ai/deepseek-coder-6.7b
- **Laravel Best Practices:** https://github.com/alexeymezenin/laravel-best-practices

---

## 🤝 Support

If you encounter issues:

1. Check instance health: `python ollama-client.py --health`
2. Verify model availability: `python ollama-client.py --list-models`
3. Check network connectivity to instances
4. Review Ollama logs on the instance

---

**Ready to leverage local AI for code analysis!** 🚀

Try your first review:
```bash
python scripts/ollama-client.py --review app/Models/User.php
```
