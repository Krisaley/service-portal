<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    \App\Http\Middleware\EnsureTeamContext::class,
])->group(function () {
    Route::get('/dashboard', function () {
        $team = auth()->user()->currentTeam;
        $teamUsersCount = $team->users()->count();
        $installedModules = \App\Models\InstalledModule::where('team_id', $team->id)->count();
        $recentActivities = \Spatie\Activitylog\Models\Activity::latest()->take(5)->get();

        return view('dashboard', compact('team', 'teamUsersCount', 'installedModules', 'recentActivities'));
    })->name('dashboard');

    // Module Management Routes
    Route::prefix('modules')->name('modules.')->group(function () {
        Route::get('/', function () {
            $team = auth()->user()->currentTeam;
            $moduleService = app(\App\Services\ModuleService::class);

            $installed = $moduleService->getInstalledModules($team);
            $available = $moduleService->getAvailableModules($team);

            return view('modules.index', compact('installed', 'available'));
        })->name('index');

        Route::post('/{module}/install', function (\App\Models\Module $module) {
            $team = auth()->user()->currentTeam;
            $moduleService = app(\App\Services\ModuleService::class);

            $result = $moduleService->install($module, $team, auth()->id());

            if ($result['success']) {
                return redirect()->route('modules.index')->with('success', $result['message']);
            }

            return redirect()->route('modules.index')->with('error', $result['message']);
        })->name('install');

        Route::delete('/{module}/uninstall', function (\App\Models\Module $module) {
            $team = auth()->user()->currentTeam;
            $moduleService = app(\App\Services\ModuleService::class);

            $result = $moduleService->uninstall($module, $team);

            if ($result['success']) {
                return redirect()->route('modules.index')->with('success', $result['message']);
            }

            return redirect()->route('modules.index')->with('error', $result['message']);
        })->name('uninstall');
    });
});
