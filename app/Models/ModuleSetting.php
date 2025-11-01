<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ModuleSetting Model
 *
 * Represents settings for a module, optionally scoped to a team.
 *
 * @property int $id
 * @property int $module_id
 * @property int|null $team_id
 * @property string $key
 * @property string|null $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class ModuleSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'module_id',
        'team_id',
        'key',
        'value',
    ];

    /**
     * Get the module that owns this setting.
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Get the team that owns this setting.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the decoded value.
     */
    public function getDecodedValue()
    {
        return json_decode($this->value, true) ?? $this->value;
    }

    /**
     * Set the value as encoded JSON.
     */
    public function setEncodedValue($value): void
    {
        $this->value = is_array($value) || is_object($value)
            ? json_encode($value)
            : $value;
    }
}
