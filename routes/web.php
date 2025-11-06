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

    // Customers Module Routes
    Route::middleware(['can:view_customers'])->prefix('customers')->name('customers.')->group(function () {
        Route::get('/', \App\Livewire\Customers\Index::class)->name('index');
        Route::get('/create', \App\Livewire\Customers\Create::class)->name('create');
        Route::get('/{customer}', \App\Livewire\Customers\Show::class)->name('show');
        Route::get('/{customer}/edit', \App\Livewire\Customers\Edit::class)->name('edit');
    });

    // Products Module Routes
    Route::middleware(['can:view_products'])->prefix('products')->name('products.')->group(function () {
        Route::get('/', \App\Livewire\Products\Index::class)->name('index');
        Route::get('/create', \App\Livewire\Products\Create::class)->name('create');
        Route::get('/{product}', \App\Livewire\Products\Show::class)->name('show');
        Route::get('/{product}/edit', \App\Livewire\Products\Edit::class)->name('edit');
    });

    // Assets Module Routes
    Route::middleware(['can:view_assets'])->prefix('assets')->name('assets.')->group(function () {
        Route::get('/', \App\Livewire\Assets\Index::class)->name('index');
        Route::get('/create', \App\Livewire\Assets\Create::class)->name('create');
        Route::get('/{asset}', \App\Livewire\Assets\Show::class)->name('show');
        Route::get('/{asset}/edit', \App\Livewire\Assets\Edit::class)->name('edit');
    });

    // Tickets Module Routes
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', \App\Livewire\Tickets\Index::class)->name('index');
        Route::get('/create', \App\Livewire\Tickets\Create::class)->name('create');
        Route::get('/{ticket}', \App\Livewire\Tickets\Show::class)->name('show');
        Route::get('/{ticket}/edit', \App\Livewire\Tickets\Edit::class)->name('edit');
    });
});
