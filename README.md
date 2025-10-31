# Laravel Field Service Management System

A modern, multi-tenant Laravel SaaS application for field service management combining CRM, project management, compliance tracking, e-commerce, and customer portal functionality.

## Phase 1: Core Framework & Modular Foundation

This is **Phase 1** of development, focusing on establishing a solid foundation with a modular installation system before adding business features.

### 🎯 Phase 1 Objectives

- ✅ Laravel foundation with Jetstream Teams authentication
- ✅ Robust module installation and management system  
- ✅ Core UI shell and navigation framework
- ✅ Multi-tenant data isolation patterns
- ✅ Testing framework for validating modules

## 🚀 Quick Start

### Prerequisites

- PHP 8.2+
- Composer
- Docker & Docker Compose
- Node.js & NPM

### Installation

1. **Clone and setup Laravel**:
```bash
# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Start Docker environment
./vendor/bin/sail up -d

# Run migrations
./vendor/bin/sail artisan migrate

# Install Jetstream with Teams
./vendor/bin/sail artisan jetstream:install livewire --teams

# Build frontend assets
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

2. **Initialize the module system**:
```bash
# Create modules directory structure
./vendor/bin/sail artisan module:init

# Install core permissions
./vendor/bin/sail artisan db:seed --class=CorePermissionsSeeder
```

3. **Access the application**:
- Main app: http://localhost
- Admin panel: http://localhost/admin

## 🏗️ Architecture Overview

### Multi-Tenancy
- **Laravel Jetstream Teams** for Phase 1 tenant isolation
- All queries automatically scoped by `team_id`
- Planned migration path to Spatie Multi-tenancy for Phase 2

### Module System
- **Upload & Install**: Drop modules into `/modules` folder and run installer
- **JSON Manifests**: Each module has a `module.json` with metadata and dependencies
- **Automatic Setup**: Migrations, permissions, assets, and UI integration handled automatically
- **Dependency Resolution**: Modules can depend on other modules

### Core Components
- **Livewire Volt**: Single-file components for rapid development
- **WireUI**: Modern UI components for consistent design
- **Filament**: Admin panel for data management
- **Spatie Packages**: Media Library, Permissions, Activity Log

## 📁 Project Structure

```
/
├── app/
│   ├── Services/ModuleService.php    # Core module management
│   ├── Traits/HasTeamScope.php       # Multi-tenant data scoping
│   └── Http/Middleware/EnsureTeamContext.php
├── modules/                          # Installable modules directory
│   ├── CustomerManagement/          # Example module
│   │   ├── module.json              # Module manifest
│   │   ├── src/                     # Module source code
│   │   ├── database/migrations/     # Module migrations
│   │   └── resources/views/         # Module views
├── config/modules.php               # Module system configuration
└── .github/
    ├── copilot-instructions.md      # AI agent guidance
    └── build_phases/phase1.json     # Current phase plan
```

## 🔧 Module Development

### Creating a Module

1. **Create module directory**:
```bash
mkdir modules/YourModule
cd modules/YourModule
```

2. **Create module manifest** (`module.json`):
```json
{
  "name": "Your Module",
  "slug": "your-module", 
  "version": "1.0.0",
  "description": "Description of your module",
  "author": "Your Name",
  "category": "core",
  "dependencies": [],
  "permissions": [
    "view_your_module",
    "create_your_module", 
    "edit_your_module",
    "delete_your_module"
  ],
  "navigation": [
    {
      "label": "Your Module",
      "route": "your-module.index",
      "icon": "heroicon-o-star",
      "permission": "view_your_module"
    }
  ]
}
```

3. **Install the module**:
```bash
./vendor/bin/sail artisan module:install your-module
```

### Module Structure

```
modules/YourModule/
├── module.json              # Required: Module manifest
├── src/                     # Required: Source code
│   ├── Controllers/
│   ├── Models/
│   └── Livewire/
├── database/               # Optional: Database files
│   ├── migrations/
│   └── seeders/
├── resources/              # Optional: Views and assets
│   ├── views/
│   └── assets/
├── routes/                 # Optional: Route files
│   ├── web.php
│   └── api.php
└── tests/                  # Optional: Module tests
```

## 🧪 Testing

```bash
# Run all tests
./vendor/bin/sail test

# Run specific test suite
./vendor/bin/sail test --testsuite=Feature

# Test module installation
./vendor/bin/sail artisan module:test-install
```

## 📋 Phase 1 Milestones

- [x] **M1: Laravel Foundation** - Basic Jetstream setup
- [ ] **M2: Module System** - Core module installation framework  
- [ ] **M3: UI Shell** - Dynamic navigation & dashboard
- [ ] **M4: Security & Settings** - Multi-tenant isolation
- [ ] **M5: Testing & Validation** - Test modules to prove system

**Total Duration**: 7 weeks  
**Current Status**: M1 Complete, M2 In Progress

## 🔒 Security

### Multi-Tenant Isolation
- All models use `HasTeamScope` trait for automatic team scoping
- Middleware ensures team context is always set
- Global scopes prevent cross-tenant data access

### Module Security
- Module manifests validated before installation
- File type restrictions on module uploads
- Permission system integrated with module installation

## 📚 Documentation

- [Module Development Guide](docs/modules.md) *(coming soon)*
- [Multi-Tenancy Patterns](docs/multi-tenancy.md) *(coming soon)*
- [API Documentation](docs/api.md) *(coming soon)*

## 🤝 Contributing

This is Phase 1 - focus on core framework stability. Business features will be added as modules in later phases.

1. Follow the existing code patterns
2. Ensure all changes maintain multi-tenant isolation
3. Add tests for new functionality
4. Update this README for significant changes

## 📄 License

MIT License - see [LICENSE](LICENSE) file for details.

---

**Phase 1 Goal**: Solid, testable foundation with module system before any business features.  
**Next Phase**: Business modules (CRM, Ticketing, Assets, etc.) as installable packages.