<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * HasTeamScope Trait
 *
 * Automatically scopes queries to the current user's team.
 * Apply this trait to any model that should be team-scoped.
 */
trait HasTeamScope
{
    /**
     * Boot the HasTeamScope trait for a model.
     */
    protected static function bootHasTeamScope(): void
    {
        // Automatically scope queries to current team
        static::addGlobalScope('team', function (Builder $builder) {
            if (auth()->check() && auth()->user()->currentTeam) {
                $builder->where(
                    $builder->getModel()->getTable() . '.team_id',
                    auth()->user()->currentTeam->id
                );
            }
        });

        // Automatically set team_id when creating
        static::creating(function (Model $model) {
            if (auth()->check() && auth()->user()->currentTeam && !$model->team_id) {
                $model->team_id = auth()->user()->currentTeam->id;
            }
        });
    }

    /**
     * Get the team that owns this record.
     */
    public function team()
    {
        return $this->belongsTo(\App\Models\Team::class);
    }

    /**
     * Scope a query to include all teams (removes team scope).
     */
    public function scopeWithAllTeams(Builder $query): Builder
    {
        return $query->withoutGlobalScope('team');
    }

    /**
     * Scope a query to a specific team.
     */
    public function scopeForTeam(Builder $query, int $teamId): Builder
    {
        return $query->withoutGlobalScope('team')->where('team_id', $teamId);
    }
}
