<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Collection;

class ModuleService
{
    protected string $modulesPath;
    protected bool $cacheEnabled;

    public function __construct()
    {
        $this->modulesPath = base_path(config('modules.path', 'modules'));
        $this->cacheEnabled = config('modules.cache_enabled', true);
    }

    /**
     * Get all available modules
     */
    public function getAllModules(): Collection
    {
        $cacheKey = 'modules.all';
        
        if ($this->cacheEnabled && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $modules = collect();
        
        if (!File::exists($this->modulesPath)) {
            return $modules;
        }

        $directories = File::directories($this->modulesPath);
        
        foreach ($directories as $directory) {
            $manifest = $this->loadModuleManifest($directory);
            if ($manifest) {
                $modules->push($manifest);
            }
        }

        if ($this->cacheEnabled) {
            Cache::put($cacheKey, $modules, now()->addHours(24));
        }

        return $modules;
    }

    /**
     * Get installed (active) modules
     */
    public function getInstalledModules(): Collection
    {
        return $this->getAllModules()->where('installed', true);
    }

    /**
     * Load module manifest from directory
     */
    protected function loadModuleManifest(string $directory): ?array
    {
        $manifestPath = $directory . '/module.json';
        
        if (!File::exists($manifestPath)) {
            return null;
        }

        $manifest = json_decode(File::get($manifestPath), true);
        
        if (!$manifest || !$this->validateManifest($manifest)) {
            return null;
        }

        $manifest['path'] = $directory;
        $manifest['slug'] = basename($directory);
        $manifest['installed'] = $this->isModuleInstalled($manifest['slug']);
        
        return $manifest;
    }

    /**
     * Validate module manifest structure
     */
    protected function validateManifest(array $manifest): bool
    {
        $required = ['name', 'version', 'description', 'author'];
        
        foreach ($required as $field) {
            if (!isset($manifest[$field])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if module is installed
     */
    public function isModuleInstalled(string $slug): bool
    {
        return File::exists(database_path("modules/{$slug}_installed.flag"));
    }

    /**
     * Install a module
     */
    public function installModule(string $slug): bool
    {
        $module = $this->getAllModules()->firstWhere('slug', $slug);
        
        if (!$module) {
            throw new \Exception("Module {$slug} not found");
        }

        if ($this->isModuleInstalled($slug)) {
            throw new \Exception("Module {$slug} is already installed");
        }

        // Check dependencies
        if (!$this->checkDependencies($module)) {
            throw new \Exception("Module {$slug} has unmet dependencies");
        }

        try {
            // Run migrations
            $this->runModuleMigrations($module);
            
            // Register permissions
            $this->registerModulePermissions($module);
            
            // Copy assets
            $this->copyModuleAssets($module);
            
            // Mark as installed
            $this->markModuleInstalled($slug);
            
            // Clear cache
            $this->clearModuleCache();
            
            return true;
            
        } catch (\Exception $e) {
            // Rollback on failure
            $this->rollbackModuleInstallation($slug);
            throw $e;
        }
    }

    /**
     * Uninstall a module
     */
    public function uninstallModule(string $slug): bool
    {
        if (!$this->isModuleInstalled($slug)) {
            throw new \Exception("Module {$slug} is not installed");
        }

        $module = $this->getAllModules()->firstWhere('slug', $slug);
        
        if (!$module) {
            throw new \Exception("Module {$slug} not found");
        }

        // Check if other modules depend on this one
        if ($this->hasDependentModules($slug)) {
            throw new \Exception("Cannot uninstall {$slug}: other modules depend on it");
        }

        try {
            // Rollback migrations
            $this->rollbackModuleMigrations($module);
            
            // Remove permissions
            $this->removeModulePermissions($module);
            
            // Remove assets
            $this->removeModuleAssets($module);
            
            // Mark as uninstalled
            $this->markModuleUninstalled($slug);
            
            // Clear cache
            $this->clearModuleCache();
            
            return true;
            
        } catch (\Exception $e) {
            throw new \Exception("Failed to uninstall module {$slug}: " . $e->getMessage());
        }
    }

    /**
     * Check module dependencies
     */
    protected function checkDependencies(array $module): bool
    {
        if (!isset($module['dependencies']) || empty($module['dependencies'])) {
            return true;
        }

        foreach ($module['dependencies'] as $dependency) {
            if (!$this->isModuleInstalled($dependency)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if other modules depend on this one
     */
    protected function hasDependentModules(string $slug): bool
    {
        $installedModules = $this->getInstalledModules();
        
        foreach ($installedModules as $module) {
            $dependencies = $module['dependencies'] ?? [];
            if (in_array($slug, $dependencies)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Run module migrations
     */
    protected function runModuleMigrations(array $module): void
    {
        $migrationsPath = $module['path'] . '/database/migrations';
        
        if (File::exists($migrationsPath)) {
            Artisan::call('migrate', [
                '--path' => "modules/{$module['slug']}/database/migrations",
                '--force' => true
            ]);
        }
    }

    /**
     * Rollback module migrations
     */
    protected function rollbackModuleMigrations(array $module): void
    {
        $migrationsPath = $module['path'] . '/database/migrations';
        
        if (File::exists($migrationsPath)) {
            // This would need more sophisticated migration tracking
            // For now, we'll implement a basic rollback
            Artisan::call('migrate:rollback', [
                '--path' => "modules/{$module['slug']}/database/migrations",
                '--force' => true
            ]);
        }
    }

    /**
     * Register module permissions
     */
    protected function registerModulePermissions(array $module): void
    {
        if (!isset($module['permissions']) || empty($module['permissions'])) {
            return;
        }

        foreach ($module['permissions'] as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }
    }

    /**
     * Remove module permissions
     */
    protected function removeModulePermissions(array $module): void
    {
        if (!isset($module['permissions']) || empty($module['permissions'])) {
            return;
        }

        foreach ($module['permissions'] as $permission) {
            \Spatie\Permission\Models\Permission::where('name', $permission)->delete();
        }
    }

    /**
     * Copy module assets
     */
    protected function copyModuleAssets(array $module): void
    {
        $assetsPath = $module['path'] . '/resources/assets';
        $publicPath = public_path("modules/{$module['slug']}");
        
        if (File::exists($assetsPath)) {
            File::copyDirectory($assetsPath, $publicPath);
        }
    }

    /**
     * Remove module assets
     */
    protected function removeModuleAssets(array $module): void
    {
        $publicPath = public_path("modules/{$module['slug']}");
        
        if (File::exists($publicPath)) {
            File::deleteDirectory($publicPath);
        }
    }

    /**
     * Mark module as installed
     */
    protected function markModuleInstalled(string $slug): void
    {
        $flagPath = database_path("modules/{$slug}_installed.flag");
        File::ensureDirectoryExists(dirname($flagPath));
        File::put($flagPath, now()->toISOString());
    }

    /**
     * Mark module as uninstalled
     */
    protected function markModuleUninstalled(string $slug): void
    {
        $flagPath = database_path("modules/{$slug}_installed.flag");
        if (File::exists($flagPath)) {
            File::delete($flagPath);
        }
    }

    /**
     * Rollback module installation
     */
    protected function rollbackModuleInstallation(string $slug): void
    {
        try {
            $this->markModuleUninstalled($slug);
            $this->clearModuleCache();
        } catch (\Exception $e) {
            // Log error but don't throw to avoid masking original error
            \Log::error("Failed to rollback module installation for {$slug}: " . $e->getMessage());
        }
    }

    /**
     * Clear module cache
     */
    public function clearModuleCache(): void
    {
        if ($this->cacheEnabled) {
            Cache::forget('modules.all');
            Cache::forget('modules.installed');
        }
    }
}