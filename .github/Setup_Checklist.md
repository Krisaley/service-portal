# KrisAley SmartWorx — Phase 1 Developer Checklist

## Core Setup
- [ ] Install Laravel with Jetstream (teams enabled)
- [ ] Configure Spatie Multitenancy (PathTenantFinder)
- [ ] Set up Volt UI shell (sidebar, topbar, content slots)
- [ ] Create `.env.tenant` pattern or config override per tenant

## Module System
- [ ] Create `/modules` folder with autoloading logic
- [ ] Build `module:install`, `module:list`, `module:disable` Artisan commands
- [ ] Add module registry to config or DB
- [ ] Parse `module.json` files and register routes, migrations, seeders

## Phase 1 Modules
- [ ] Scaffold `customers` module (Volt views, routes, Pest tests)
- [ ] Scaffold `products` module
- [ ] Scaffold `assets` module
- [ ] Scaffold `dashboard` module

## Testing & CI
- [ ] Install Pest and write core tests
- [ ] Write module install/uninstall tests
- [ ] Add health check endpoint (`/healthz`)
- [ ] Set up GitHub Actions or local CI runner

## Documentation
- [ ] Add `copilot-instructions.md` with phase references
- [ ] Add `DESIGN_BRIEF.md` with goals and personas
- [ ] Create README with install guide and module dev instructions