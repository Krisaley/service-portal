<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Module System Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the modular system.
    |
    */

    /**
     * Core modules that cannot be disabled or uninstalled.
     */
    'core_modules' => [
        'auth',
        'dashboard',
        'teams',
        'settings',
        'activity-log',
    ],

    /**
     * Module paths and directories.
     */
    'paths' => [
        'migrations' => database_path('migrations/modules'),
        'seeders' => database_path('seeders/modules'),
        'assets' => public_path('modules'),
    ],

    /**
     * Module installation settings.
     */
    'installation' => [
        // Run migrations automatically when installing a module
        'auto_migrate' => true,

        // Run seeders automatically when installing a module
        'auto_seed' => false,

        // Publish assets automatically when installing a module
        'auto_publish_assets' => true,

        // Check dependencies before installation
        'check_dependencies' => true,
    ],

    /**
     * Module caching settings.
     */
    'cache' => [
        // Enable module caching
        'enabled' => env('MODULE_CACHE_ENABLED', true),

        // Cache key prefix
        'prefix' => 'modules',

        // Cache TTL in seconds
        'ttl' => 3600,
    ],

    /**
     * Available modules with their metadata.
     */
    'available' => [
        'crm' => [
            'name' => 'CRM',
            'description' => 'Customer Relationship Management',
            'icon' => 'heroicon-o-users',
        ],
        'products' => [
            'name' => 'Products',
            'description' => 'Product Catalog Management',
            'icon' => 'heroicon-o-cube',
        ],
        'quotes' => [
            'name' => 'Quotes',
            'description' => 'Quote Generation System',
            'icon' => 'heroicon-o-document-text',
        ],
        'assets' => [
            'name' => 'Assets',
            'description' => 'Asset Tracking & Maintenance',
            'icon' => 'heroicon-o-server',
        ],
        'tickets' => [
            'name' => 'Tickets',
            'description' => 'Support Ticket Management',
            'icon' => 'heroicon-o-ticket',
        ],
        'jobs' => [
            'name' => 'Jobs',
            'description' => 'Field Service Job Scheduling',
            'icon' => 'heroicon-o-briefcase',
        ],
        'projects' => [
            'name' => 'Projects',
            'description' => 'Multi-Job Project Management',
            'icon' => 'heroicon-o-folder',
        ],
        'timesheets' => [
            'name' => 'Timesheets',
            'description' => 'Time Tracking & Invoicing',
            'icon' => 'heroicon-o-clock',
        ],
        'email-management' => [
            'name' => 'Email Management',
            'description' => 'Email-to-Ticket Automation',
            'icon' => 'heroicon-o-envelope',
        ],
        'warranty' => [
            'name' => 'Warranty Management',
            'description' => 'Warranty Claim Tracking',
            'icon' => 'heroicon-o-shield-check',
        ],
        'parts-tracking' => [
            'name' => 'Parts Tracking',
            'description' => 'Parts Inventory & Intelligence',
            'icon' => 'heroicon-o-wrench',
        ],
        'purchase-orders' => [
            'name' => 'Purchase Orders',
            'description' => 'PO Creation & Approval',
            'icon' => 'heroicon-o-shopping-cart',
        ],
        'sub-contractors' => [
            'name' => 'Sub-contractors',
            'description' => 'Sub-contractor Management',
            'icon' => 'heroicon-o-user-group',
        ],
        'workflow-builder' => [
            'name' => 'Workflow Builder',
            'description' => 'Custom Workflow Configuration',
            'icon' => 'heroicon-o-cog',
        ],
        'api' => [
            'name' => 'API',
            'description' => 'RESTful API & Webhooks',
            'icon' => 'heroicon-o-code-bracket',
        ],
    ],
];
