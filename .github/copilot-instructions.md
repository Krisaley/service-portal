# AI Agent Instructions for Laravel Field Service Management System

## Architecture Overview
This is a modern multi-tenant Laravel SaaS application for field service management combining CRM, project management, compliance tracking, e-commerce, and customer portal functionality. The system serves both internal staff and external customers with role-based access.

### Core Tech Stack
- **Backend**: Laravel (latest) with PHP 8.2+, MySQL, Redis
- **Frontend**: Livewire Volt (single-file components), Tailwind CSS, Alpine.js
- **UI Components**: WireUI (Livewire components), Filament (admin panel)
- **Auth**: Laravel Jetstream with Teams feature (multi-tenancy)
- **Real-time**: Laravel Echo + Pusher, Redis + Horizon for queues

## Key Architectural Patterns

### Multi-Tenancy Model
Uses **Laravel Jetstream Teams** for Phase 1 (confirmed approach):
- Staff users belong to company teams with admin/manager/engineer roles
- Customers have their own teams with limited portal access
- All queries must be scoped by `team_id` for data isolation
- Shared resources (products, blog) accessible across tenants
- **Migration path**: Documented upgrade to Spatie Multi-tenancy/DB-per-tenant for Phase 2

### Component Architecture
- **Livewire Volt**: Single-file components in `resources/views/livewire/`
- **Blade Components**: Reusable UI elements in `resources/views/components/`
- **Filament**: Admin CRUD operations and data tables
- **WireUI**: Form inputs, modals, notifications
- **Modular System**: First-class modular installation for optional features

### Data Flow Patterns
1. **Quote → Job Conversion**: Quote approval → customer e-signature → auto-create Job/Project
2. **Email-to-Ticket**: IMAP monitoring → email parsing → auto-create ticket with asset linking
3. **Asset Expiry Management**: Daily scheduled jobs → notifications → auto-escalation
4. **Warranty Workflow**: Complex parent/sub-status system with internal ticket creation

## Critical Developer Knowledge

### Database Schema Essentials
- **Core entities**: `users`, `teams`, `customers`, `assets`, `tickets`, `jobs`, `quotes`
- **Status tracking**: Parent statuses (customer-visible) + sub-statuses (internal)
- **Parts management**: `parts`, `inventory`, `purchase_orders`, `inventory_movements`
- **Communication**: `emails`, `email_threads` for complete audit trails
- **Custom fields**: JSON columns + polymorphic relationships for flexibility

### Custom Workflow System
The application includes a sophisticated workflow builder:
- **Phase 1** (current): Configurable status transitions, auto-assignment rules, SLA timers
- **Phase 2** (planned): Visual drag-drop workflow designer - interface spec in repo
- Status changes trigger automated actions (notifications, assignments, ticket creation)
- Focus on shipping Phase 1 functionality now

### File Handling
Uses **Spatie Media Library** for all file management:
- Asset documents, email attachments, user photos, team logos
- Image conversions and responsive images
- Document versioning for compliance

### Real-time Features
- WebSocket updates for ticket status changes
- Live dashboard metrics updates
- Push notifications for mobile PWA

## Development Workflows

### Testing Strategy
Use **Pest** for testing with focus on critical business workflows:
```php
// Example critical test paths
test('quote approval workflow triggers job creation')
test('email-to-ticket parsing links correct assets')
test('warranty claim workflow completes end-to-end')
test('asset expiry notifications sent correctly')
```

### Queue Management
Background processing for:
- Email parsing and ticket creation
- Asset expiry notifications (daily scheduled job)
- PDF generation (invoices, quotes, reports)
- File processing and image optimization
- Webhook delivery to external systems

### API Design
RESTful API with Laravel Sanctum:
- All endpoints scoped by team (tenant isolation)
- Rate limiting per team/user
- Webhook system for external integrations
- Two-way sync capabilities

## Key Business Logic

### Asset Management
Assets have service schedules with automated notifications:
- Configurable notification thresholds (30/14/7 days)
- Auto-creation of jobs when service due
- Compliance tracking with expiry alerts
- Maintenance history with parts cost tracking

### Parts & Inventory
Sophisticated inventory management:
- Multi-location stock (warehouse, van stock per engineer)
- Stock reservations for specific jobs
- Automated reorder points and purchase orders
- Parts usage tracking linked to jobs for trend analysis

### Email Integration
Complete email-to-ticket automation:
- IMAP monitoring with `webklex/laravel-imap` (priority integration)
- Intelligent parsing (asset detection, error codes, priority keywords)
- Thread preservation and response tracking
- Auto-assignment based on content rules

### Warranty Management
Complex warranty claim workflow:
- Asset warranty status checking
- Manufacturer claim submission with documentation (priority integration)
- Status tracking from submission to payment
- Integration with job completion and parts usage

## UI/UX Patterns

### Dashboard Customization
- Drag-drop widget customization per user
- Role-based default layouts (admin, manager, engineer, customer)
- Persistent view preferences (table/card/kanban per page)

### White-Label Theming
Teams can customize:
- Logo upload and brand colors
- Email template styling
- Customer portal appearance
- Invoice/quote PDF templates

### Mobile Experience
Desktop-first with PWA mobile app:
- Bottom tab navigation on mobile
- Offline capability for field engineers
- Camera integration for job photos
- GPS verification for job clock-in/out

## Common Pitfalls & Solutions

### Tenant Data Isolation
Always scope queries by team:
```php
// Correct
$tickets = Ticket::where('team_id', auth()->user()->currentTeam->id)->get();

// Dangerous - exposes all tenant data
$tickets = Ticket::all();
```

### Status Management
Use the parent/sub-status system correctly:
- Parent statuses are customer-visible workflow stages
- Sub-statuses provide granular internal tracking
- Status changes should trigger workflow rules

### File Security
Ensure proper file access control:
- Files are scoped to teams via Media Library
- Use signed URLs for temporary access
- Validate file types and sizes on upload

### Performance Considerations
- Eager load relationships to prevent N+1 queries
- Use database indexes on frequently filtered columns (`team_id`, `status`, `priority`)
- Cache expensive queries (dashboard metrics, reports)
- Queue heavy operations (PDF generation, email sending)

## Integration Points

### External APIs
Priority integrations with documentation and stubs:
- **Accounting software** (QuickBooks, Xero, Sage) for invoice sync
- **IMAP email ingest** for email-to-ticket automation
- **Manufacturer warranty portals** for claim submission

Secondary integrations:
- Google Services (Calendar, Maps, Drive) for scheduling
- SMS providers for notifications

### Webhook System
Outbound webhooks for:
- Ticket status changes
- Job completions
- Invoice generation
- Asset expiry alerts

## Development Commands

### Key Artisan Commands
```bash
# Queue processing
php artisan horizon:start

# Asset expiry notifications (daily)
php artisan schedule:run

# Email processing
php artisan email:process

# Clear application cache
php artisan optimize:clear
```

### Database Management
```bash
# Fresh migration with seeders
php artisan migrate:fresh --seed

# Generate test data
php artisan db:seed --class=DemoDataSeeder
```

This system emphasizes **complex business workflows**, **multi-tenant data isolation**, and **comprehensive audit trails**. When implementing features, always consider the tenant context, workflow automation opportunities, and integration with the broader ecosystem of assets, tickets, and jobs.