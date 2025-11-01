<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    // Module API endpoints
    Route::prefix('modules')->group(function () {
        Route::get('/', function (Request $request) {
            $team = $request->user()->currentTeam;
            $moduleService = app(\App\Services\ModuleService::class);

            return response()->json([
                'installed' => $moduleService->getInstalledModules($team),
                'available' => $moduleService->getAvailableModules($team),
            ]);
        });

        Route::post('/{module}/install', function (Request $request, \App\Models\Module $module) {
            $team = $request->user()->currentTeam;
            $moduleService = app(\App\Services\ModuleService::class);

            $result = $moduleService->install($module, $team, $request->user()->id);

            return response()->json($result, $result['success'] ? 200 : 400);
        });

        Route::delete('/{module}/uninstall', function (Request $request, \App\Models\Module $module) {
            $team = $request->user()->currentTeam;
            $moduleService = app(\App\Services\ModuleService::class);

            $result = $moduleService->uninstall($module, $team);

            return response()->json($result, $result['success'] ? 200 : 400);
        });
    });
});
