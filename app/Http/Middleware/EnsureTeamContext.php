<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureTeamContext Middleware
 *
 * Ensures the authenticated user has a team context set.
 */
class EnsureTeamContext
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return $next($request);
        }

        // If user doesn't have a current team, set it to their first team
        if (!$request->user()->currentTeam && $request->user()->allTeams()->count() > 0) {
            $request->user()->switchTeam($request->user()->allTeams()->first());
        }

        // If user still doesn't have a team, redirect to team creation
        if (!$request->user()->currentTeam) {
            return redirect()->route('teams.create')
                ->with('error', 'You must create or join a team to continue.');
        }

        return $next($request);
    }
}
