<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeamContext
{
    /**
     * Handle an incoming request.
     *
     * Ensures all database queries are scoped to the current user's team
     * for multi-tenant data isolation.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Ensure user has a current team
            if (!$user->currentTeam) {
                // If user has teams, set the first one as current
                if ($user->teams->count() > 0) {
                    $user->current_team_id = $user->teams->first()->id;
                    $user->save();
                } else {
                    // User has no teams - this shouldn't happen in normal flow
                    // but we'll handle it gracefully
                    abort(403, 'No team access available');
                }
            }

            // Share the current team ID globally for query scoping
            app()->instance('current_team_id', $user->currentTeam->id);
            
            // Set team context in session for frontend use
            session(['current_team_id' => $user->currentTeam->id]);
        }

        return $next($request);
    }
}