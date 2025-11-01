<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $team = $user->currentTeam;
        
        // Get dashboard data based on user role
        $dashboardData = [
            'user' => $user,
            'team' => $team,
            'stats' => $this->getDashboardStats($team),
            'recentActivity' => $this->getRecentActivity($team),
            'installedModules' => app(\App\Services\ModuleService::class)->getInstalledModules(),
        ];
        
        return view('dashboard', $dashboardData);
    }
    
    /**
     * Get dashboard statistics
     */
    private function getDashboardStats($team)
    {
        return [
            'total_users' => $team->users()->count(),
            'active_modules' => app(\App\Services\ModuleService::class)->getInstalledModules()->count(),
            // More stats will be added as modules are installed
        ];
    }
    
    /**
     * Get recent activity for the team
     */
    private function getRecentActivity($team)
    {
        // This will use Spatie Activity Log once fully set up
        return collect([
            [
                'type' => 'system',
                'message' => 'Phase 1 Core Framework initialized',
                'timestamp' => now()->subHours(1),
                'user' => 'System'
            ],
            [
                'type' => 'user',
                'message' => 'User registered and team created',
                'timestamp' => now()->subMinutes(30),
                'user' => $team->owner->name ?? 'Unknown'
            ]
        ]);
    }
}