<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            // Core Modules (Always Active)
            [
                'name' => 'Authentication',
                'slug' => 'auth',
                'description' => 'User authentication and authorization system',
                'version' => '1.0.0',
                'is_core' => true,
                'enabled' => true,
                'installed_at' => now(),
            ],
            [
                'name' => 'Dashboard',
                'slug' => 'dashboard',
                'description' => 'Main dashboard with statistics and quick links',
                'version' => '1.0.0',
                'is_core' => true,
                'enabled' => true,
                'installed_at' => now(),
            ],
            [
                'name' => 'Teams',
                'slug' => 'teams',
                'description' => 'Multi-tenant team management system',
                'version' => '1.0.0',
                'is_core' => true,
                'enabled' => true,
                'installed_at' => now(),
            ],
            [
                'name' => 'Settings',
                'slug' => 'settings',
                'description' => 'System and team settings management',
                'version' => '1.0.0',
                'is_core' => true,
                'enabled' => true,
                'installed_at' => now(),
            ],
            [
                'name' => 'Activity Log',
                'slug' => 'activity-log',
                'description' => 'Comprehensive audit trail and activity logging',
                'version' => '1.0.0',
                'is_core' => true,
                'enabled' => true,
                'installed_at' => now(),
            ],

            // Optional Modules
            [
                'name' => 'CRM',
                'slug' => 'crm',
                'description' => 'Customer relationship management with contacts and communication',
                'version' => '1.0.0',
                'is_core' => false,
                'enabled' => true,
                'installed_at' => null,
            ],
            [
                'name' => 'Products',
                'slug' => 'products',
                'description' => 'Product catalog with pricing and categories',
                'version' => '1.0.0',
                'is_core' => false,
                'enabled' => true,
                'installed_at' => null,
            ],
            [
                'name' => 'Quotes',
                'slug' => 'quotes',
                'description' => 'Quote generation and management system',
                'version' => '1.0.0',
                'is_core' => false,
                'enabled' => true,
                'installed_at' => null,
            ],
            [
                'name' => 'Assets',
                'slug' => 'assets',
                'description' => 'Asset registration and maintenance tracking',
                'version' => '1.0.0',
                'is_core' => false,
                'enabled' => true,
                'installed_at' => null,
            ],
            [
                'name' => 'Tickets',
                'slug' => 'tickets',
                'description' => 'Support ticket management with parent/sub-status system',
                'version' => '1.0.0',
                'is_core' => false,
                'enabled' => true,
                'installed_at' => null,
            ],
            [
                'name' => 'Jobs',
                'slug' => 'jobs',
                'description' => 'Field service job scheduling and dispatch',
                'version' => '1.0.0',
                'is_core' => false,
                'enabled' => true,
                'installed_at' => null,
            ],
            [
                'name' => 'Projects',
                'slug' => 'projects',
                'description' => 'Multi-job project coordination and management',
                'version' => '1.0.0',
                'is_core' => false,
                'enabled' => true,
                'installed_at' => null,
            ],
            [
                'name' => 'Timesheets',
                'slug' => 'timesheets',
                'description' => 'Time tracking and invoicing system',
                'version' => '1.0.0',
                'is_core' => false,
                'enabled' => true,
                'installed_at' => null,
            ],
            [
                'name' => 'Email Management',
                'slug' => 'email-management',
                'description' => 'Email-to-ticket automation with IMAP monitoring',
                'version' => '1.0.0',
                'is_core' => false,
                'enabled' => true,
                'installed_at' => null,
            ],
            [
                'name' => 'Warranty Management',
                'slug' => 'warranty',
                'description' => 'Warranty claim tracking and management',
                'version' => '1.0.0',
                'is_core' => false,
                'enabled' => true,
                'installed_at' => null,
            ],
            [
                'name' => 'Parts Tracking',
                'slug' => 'parts-tracking',
                'description' => 'Parts inventory with intelligence and trend analysis',
                'version' => '1.0.0',
                'is_core' => false,
                'enabled' => true,
                'installed_at' => null,
            ],
            [
                'name' => 'Purchase Orders',
                'slug' => 'purchase-orders',
                'description' => 'Purchase order creation and approval workflow',
                'version' => '1.0.0',
                'is_core' => false,
                'enabled' => true,
                'installed_at' => null,
            ],
            [
                'name' => 'Sub-contractors',
                'slug' => 'sub-contractors',
                'description' => 'Sub-contractor management and job assignment',
                'version' => '1.0.0',
                'is_core' => false,
                'enabled' => true,
                'installed_at' => null,
            ],
            [
                'name' => 'Workflow Builder',
                'slug' => 'workflow-builder',
                'description' => 'Custom workflow and status configuration system',
                'version' => '1.0.0',
                'is_core' => false,
                'enabled' => true,
                'installed_at' => null,
            ],
            [
                'name' => 'API',
                'slug' => 'api',
                'description' => 'RESTful API with webhooks and integrations',
                'version' => '1.0.0',
                'is_core' => false,
                'enabled' => true,
                'installed_at' => null,
            ],
        ];

        foreach ($modules as $moduleData) {
            Module::firstOrCreate(
                ['slug' => $moduleData['slug']],
                $moduleData
            );
        }

        // Create module dependencies
        $this->createDependencies();

        $this->command->info('Modules seeded successfully!');
    }

    /**
     * Create module dependencies.
     */
    protected function createDependencies(): void
    {
        // CRM module requires Teams (all modules require core modules implicitly)
        $crm = Module::where('slug', 'crm')->first();
        if ($crm) {
            $crm->dependencies()->firstOrCreate([
                'required_module_slug' => 'teams',
                'minimum_version' => '1.0.0',
            ]);
        }

        // Tickets module requires CRM
        $tickets = Module::where('slug', 'tickets')->first();
        if ($tickets) {
            $tickets->dependencies()->firstOrCreate([
                'required_module_slug' => 'crm',
                'minimum_version' => '1.0.0',
            ]);
        }

        // Jobs module requires Tickets
        $jobs = Module::where('slug', 'jobs')->first();
        if ($jobs) {
            $jobs->dependencies()->firstOrCreate([
                'required_module_slug' => 'tickets',
                'minimum_version' => '1.0.0',
            ]);
        }

        // Projects module requires Jobs
        $projects = Module::where('slug', 'projects')->first();
        if ($projects) {
            $projects->dependencies()->firstOrCreate([
                'required_module_slug' => 'jobs',
                'minimum_version' => '1.0.0',
            ]);
        }

        // Email Management requires Tickets
        $email = Module::where('slug', 'email-management')->first();
        if ($email) {
            $email->dependencies()->firstOrCreate([
                'required_module_slug' => 'tickets',
                'minimum_version' => '1.0.0',
            ]);
        }

        // Warranty requires Tickets
        $warranty = Module::where('slug', 'warranty')->first();
        if ($warranty) {
            $warranty->dependencies()->firstOrCreate([
                'required_module_slug' => 'tickets',
                'minimum_version' => '1.0.0',
            ]);
        }

        // Parts Tracking requires Products
        $parts = Module::where('slug', 'parts-tracking')->first();
        if ($parts) {
            $parts->dependencies()->firstOrCreate([
                'required_module_slug' => 'products',
                'minimum_version' => '1.0.0',
            ]);
        }

        // Purchase Orders requires Parts Tracking
        $po = Module::where('slug', 'purchase-orders')->first();
        if ($po) {
            $po->dependencies()->firstOrCreate([
                'required_module_slug' => 'parts-tracking',
                'minimum_version' => '1.0.0',
            ]);
        }
    }
}
