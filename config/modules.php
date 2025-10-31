<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Modules Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for the modular system that allows dynamic installation
    | and management of features as separate modules.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Modules Path
    |--------------------------------------------------------------------------
    |
    | This is the path where modules will be stored relative to the base path.
    | Modules should follow the standard structure with manifest files.
    |
    */
    'path' => env('MODULES_PATH', 'modules'),

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Enable caching of module discovery and metadata for better performance.
    | Disable during development for immediate changes.
    |
    */
    'cache_enabled' => env('MODULES_CACHE_ENABLED', true),
    'cache_key' => 'modules',
    'cache_lifetime' => 24 * 60, // 24 hours in minutes

    /*
    |--------------------------------------------------------------------------
    | Auto Discovery
    |--------------------------------------------------------------------------
    |
    | Automatically discover and register modules on application boot.
    | Disable this in production for better performance.
    |
    */
    'auto_discovery' => env('MODULES_AUTO_DISCOVERY', true),

    /*
    |--------------------------------------------------------------------------
    | Module Structure
    |--------------------------------------------------------------------------
    |
    | Define the expected structure for modules including required
    | and optional directories and files.
    |
    */
    'structure' => [
        'manifest_file' => 'module.json',
        'required_directories' => [
            'src',
        ],
        'optional_directories' => [
            'database',
            'database/migrations',
            'database/seeders',
            'resources',
            'resources/views',
            'resources/assets',
            'routes',
            'tests',
            'config',
        ],
        'autoload_paths' => [
            'src',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Installation Settings
    |--------------------------------------------------------------------------
    |
    | Settings related to module installation and management.
    |
    */
    'installation' => [
        'flags_directory' => 'database/modules',
        'backup_on_install' => true,
        'rollback_on_failure' => true,
        'validate_dependencies' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Core Modules
    |--------------------------------------------------------------------------
    |
    | Modules that are considered core and cannot be uninstalled.
    | These are always available and loaded.
    |
    */
    'core_modules' => [
        // Core modules will be defined here
    ],

    /*
    |--------------------------------------------------------------------------
    | Module Categories
    |--------------------------------------------------------------------------
    |
    | Define categories for organizing modules in the admin interface.
    |
    */
    'categories' => [
        'core' => 'Core System',
        'crm' => 'Customer Management',
        'service' => 'Service Management', 
        'commerce' => 'E-commerce',
        'communication' => 'Communication',
        'reporting' => 'Reports & Analytics',
        'integration' => 'Integrations',
        'workflow' => 'Workflow & Automation',
        'compliance' => 'Compliance & Security',
        'other' => 'Other',
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Security-related configuration for module installation and management.
    |
    */
    'security' => [
        'allowed_file_types' => ['php', 'json', 'blade.php', 'css', 'js', 'vue'],
        'forbidden_functions' => ['exec', 'shell_exec', 'system', 'passthru'],
        'validate_signatures' => false, // Enable in production
        'sandbox_installation' => false, // Enable in production
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Integration
    |--------------------------------------------------------------------------
    |
    | Settings for how modules integrate with the UI.
    |
    */
    'ui' => [
        'menu_integration' => true,
        'dashboard_widgets' => true,
        'custom_routes' => true,
        'livewire_components' => true,
    ],
];