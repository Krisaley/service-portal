<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Module Model
 *
 * Represents a system module that can be installed and enabled.
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string $version
 * @property bool $is_core
 * @property bool $enabled
 * @property \Illuminate\Support\Carbon|null $installed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Module extends Model
{
    use HasFactory;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'version',
        'is_core',
        'enabled',
        'installed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_core' => 'boolean',
            'enabled' => 'boolean',
            'installed_at' => 'datetime',
        ];
    }

    /**
     * Get the activity log options for this model.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'slug', 'enabled'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Module has been {$eventName}");
    }

    /**
     * Get the dependencies for this module.
     */
    public function dependencies(): HasMany
    {
        return $this->hasMany(ModuleDependency::class);
    }

    /**
     * Get the settings for this module.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(ModuleSetting::class);
    }

    /**
     * Get the teams that have installed this module.
     */
    public function installedTeams(): HasMany
    {
        return $this->hasMany(InstalledModule::class);
    }

    /**
     * Scope a query to only include enabled modules.
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }

    /**
     * Scope a query to only include core modules.
     */
    public function scopeCore($query)
    {
        return $query->where('is_core', true);
    }

    /**
     * Scope a query to only include optional modules.
     */
    public function scopeOptional($query)
    {
        return $query->where('is_core', false);
    }

    /**
     * Check if this module has unmet dependencies.
     */
    public function hasUnmetDependencies(): bool
    {
        foreach ($this->dependencies as $dependency) {
            $requiredModule = static::where('slug', $dependency->required_module_slug)
                ->where('enabled', true)
                ->first();

            if (!$requiredModule) {
                return true;
            }

            if ($dependency->minimum_version && version_compare($requiredModule->version, $dependency->minimum_version, '<')) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the list of unmet dependencies.
     */
    public function getUnmetDependencies(): array
    {
        $unmet = [];

        foreach ($this->dependencies as $dependency) {
            $requiredModule = static::where('slug', $dependency->required_module_slug)
                ->where('enabled', true)
                ->first();

            if (!$requiredModule) {
                $unmet[] = [
                    'slug' => $dependency->required_module_slug,
                    'reason' => 'Module not installed or not enabled',
                ];
            } elseif ($dependency->minimum_version && version_compare($requiredModule->version, $dependency->minimum_version, '<')) {
                $unmet[] = [
                    'slug' => $dependency->required_module_slug,
                    'reason' => "Requires version {$dependency->minimum_version} or higher, found {$requiredModule->version}",
                ];
            }
        }

        return $unmet;
    }
}
