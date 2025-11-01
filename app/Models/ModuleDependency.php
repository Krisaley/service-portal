<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ModuleDependency Model
 *
 * Represents a dependency relationship between modules.
 *
 * @property int $id
 * @property int $module_id
 * @property string $required_module_slug
 * @property string|null $minimum_version
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ModuleDependency extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'module_id',
        'required_module_slug',
        'minimum_version',
    ];

    /**
     * Get the module that owns this dependency.
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Get the required module.
     */
    public function requiredModule()
    {
        return Module::where('slug', $this->required_module_slug)->first();
    }
}
