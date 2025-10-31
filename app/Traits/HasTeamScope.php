<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Trait HasTeamScope
 * 
 * Automatically scopes all queries to the current user's team for multi-tenant isolation.
 * Add this trait to any model that should be team-scoped.
 */
trait HasTeamScope
{
    /**
     * Boot the trait
     */
    protected static function bootHasTeamScope(): void
    {
        // Automatically set team_id when creating
        static::creating(function (Model $model) {
            if (!isset($model->team_id) && Auth::check()) {
                $model->team_id = Auth::user()->currentTeam?->id;
            }
        });

        // Automatically scope all queries to current team
        static::addGlobalScope('team', function (Builder $builder) {
            $teamId = app('current_team_id', Auth::user()?->currentTeam?->id);
            
            if ($teamId) {
                $builder->where('team_id', $teamId);
            }
        });
    }

    /**
     * Scope query to specific team
     */
    public function scopeForTeam(Builder $query, int $teamId): Builder
    {
        return $query->where('team_id', $teamId);
    }

    /**
     * Scope query without team restrictions (admin use)
     */
    public function scopeWithoutTeamScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope('team');
    }

    /**
     * Get the team that owns this record
     */
    public function team()
    {
        return $this->belongsTo(\Laravel\Jetstream\Team::class);
    }

    /**
     * Check if current user can access this record based on team
     */
    public function isAccessibleByCurrentUser(): bool
    {
        if (!Auth::check()) {
            return false;
        }

        return $this->team_id === Auth::user()->currentTeam?->id;
    }

    /**
     * Get team_id attribute with fallback
     */
    public function getTeamIdAttribute($value)
    {
        return $value ?? Auth::user()?->currentTeam?->id;
    }
}