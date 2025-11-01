<?php

namespace App\Services;

use App\Models\InstalledModule;
use App\Models\Module;
use App\Models\Team;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ModuleService
 *
 * Handles module installation, uninstallation, and management.
 */
class ModuleService
{
    /**
     * Install a module for a team.
     *
     * @param Module $module
     * @param Team $team
     * @param int|null $userId
     * @return array
     */
    public function install(Module $module, Team $team, ?int $userId = null): array
    {
        DB::beginTransaction();

        try {
            // Check if module is already installed
            if ($this->isInstalled($module, $team)) {
                return [
                    'success' => false,
                    'message' => 'Module is already installed for this team.',
                ];
            }

            // Check if module is enabled globally
            if (!$module->enabled) {
                return [
                    'success' => false,
                    'message' => 'This module is not enabled. Please enable it globally first.',
                ];
            }

            // Check dependencies
            if ($module->hasUnmetDependencies()) {
                $unmet = $module->getUnmetDependencies();
                return [
                    'success' => false,
                    'message' => 'This module has unmet dependencies.',
                    'dependencies' => $unmet,
                ];
            }

            // Create installation record
            InstalledModule::create([
                'team_id' => $team->id,
                'module_id' => $module->id,
                'installed_at' => now(),
                'installed_by' => $userId ?? auth()->id(),
            ]);

            // Run module-specific installation logic
            $this->runModuleInstallation($module);

            // Log activity
            activity()
                ->performedOn($module)
                ->causedBy($userId ?? auth()->id())
                ->withProperties(['team_id' => $team->id])
                ->log('Module installed');

            DB::commit();

            return [
                'success' => true,
                'message' => "Module '{$module->name}' installed successfully.",
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Module installation failed', [
                'module' => $module->slug,
                'team_id' => $team->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Installation failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Uninstall a module for a team.
     *
     * @param Module $module
     * @param Team $team
     * @return array
     */
    public function uninstall(Module $module, Team $team): array
    {
        DB::beginTransaction();

        try {
            // Check if module is installed
            if (!$this->isInstalled($module, $team)) {
                return [
                    'success' => false,
                    'message' => 'Module is not installed for this team.',
                ];
            }

            // Check if module is core (cannot be uninstalled)
            if ($module->is_core) {
                return [
                    'success' => false,
                    'message' => 'Core modules cannot be uninstalled.',
                ];
            }

            // Check if other installed modules depend on this one
            $dependents = $this->getDependentModules($module, $team);
            if (!empty($dependents)) {
                return [
                    'success' => false,
                    'message' => 'Cannot uninstall. Other modules depend on this module.',
                    'dependents' => $dependents,
                ];
            }

            // Remove installation record
            InstalledModule::where('team_id', $team->id)
                ->where('module_id', $module->id)
                ->delete();

            // Run module-specific uninstallation logic
            $this->runModuleUninstallation($module);

            // Log activity
            activity()
                ->performedOn($module)
                ->causedBy(auth()->id())
                ->withProperties(['team_id' => $team->id])
                ->log('Module uninstalled');

            DB::commit();

            return [
                'success' => true,
                'message' => "Module '{$module->name}' uninstalled successfully.",
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Module uninstallation failed', [
                'module' => $module->slug,
                'team_id' => $team->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Uninstallation failed: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Check if a module is installed for a team.
     *
     * @param Module $module
     * @param Team $team
     * @return bool
     */
    public function isInstalled(Module $module, Team $team): bool
    {
        return InstalledModule::where('team_id', $team->id)
            ->where('module_id', $module->id)
            ->exists();
    }

    /**
     * Get all installed modules for a team.
     *
     * @param Team $team
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getInstalledModules(Team $team)
    {
        return Module::whereHas('installedTeams', function ($query) use ($team) {
            $query->where('team_id', $team->id);
        })->get();
    }

    /**
     * Get all available modules for a team (not installed).
     *
     * @param Team $team
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableModules(Team $team)
    {
        return Module::enabled()
            ->whereDoesntHave('installedTeams', function ($query) use ($team) {
                $query->where('team_id', $team->id);
            })
            ->get();
    }

    /**
     * Get modules that depend on the given module for a team.
     *
     * @param Module $module
     * @param Team $team
     * @return array
     */
    protected function getDependentModules(Module $module, Team $team): array
    {
        $installedModules = $this->getInstalledModules($team);
        $dependents = [];

        foreach ($installedModules as $installed) {
            foreach ($installed->dependencies as $dependency) {
                if ($dependency->required_module_slug === $module->slug) {
                    $dependents[] = $installed->name;
                    break;
                }
            }
        }

        return $dependents;
    }

    /**
     * Run module-specific installation logic.
     *
     * @param Module $module
     * @return void
     */
    protected function runModuleInstallation(Module $module): void
    {
        // Check if module has specific installation command
        $commandClass = "App\\Console\\Commands\\Modules\\Install" . str_replace(' ', '', $module->name) . "Command";

        if (class_exists($commandClass)) {
            Artisan::call($commandClass);
        }

        // Run module migrations if they exist
        $migrationPath = base_path("database/migrations/modules/{$module->slug}");
        if (file_exists($migrationPath)) {
            Artisan::call('migrate', ['--path' => $migrationPath]);
        }

        // Publish module assets if they exist
        $tag = "module-{$module->slug}";
        Artisan::call('vendor:publish', ['--tag' => $tag, '--force' => true]);
    }

    /**
     * Run module-specific uninstallation logic.
     *
     * @param Module $module
     * @return void
     */
    protected function runModuleUninstallation(Module $module): void
    {
        // Check if module has specific uninstallation command
        $commandClass = "App\\Console\\Commands\\Modules\\Uninstall" . str_replace(' ', '', $module->name) . "Command";

        if (class_exists($commandClass)) {
            Artisan::call($commandClass);
        }

        // Note: We don't automatically rollback migrations to preserve data integrity
        // Admins should manually handle data migration/cleanup if needed
    }

    /**
     * Enable a module globally.
     *
     * @param Module $module
     * @return array
     */
    public function enable(Module $module): array
    {
        try {
            if ($module->enabled) {
                return [
                    'success' => false,
                    'message' => 'Module is already enabled.',
                ];
            }

            $module->update(['enabled' => true]);

            activity()
                ->performedOn($module)
                ->causedBy(auth()->id())
                ->log('Module enabled globally');

            return [
                'success' => true,
                'message' => "Module '{$module->name}' enabled successfully.",
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to enable module: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Disable a module globally.
     *
     * @param Module $module
     * @return array
     */
    public function disable(Module $module): array
    {
        try {
            if ($module->is_core) {
                return [
                    'success' => false,
                    'message' => 'Core modules cannot be disabled.',
                ];
            }

            if (!$module->enabled) {
                return [
                    'success' => false,
                    'message' => 'Module is already disabled.',
                ];
            }

            $module->update(['enabled' => false]);

            activity()
                ->performedOn($module)
                ->causedBy(auth()->id())
                ->log('Module disabled globally');

            return [
                'success' => true,
                'message' => "Module '{$module->name}' disabled successfully.",
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to disable module: ' . $e->getMessage(),
            ];
        }
    }
}
