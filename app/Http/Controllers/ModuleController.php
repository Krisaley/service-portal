<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ModuleService;
use Illuminate\Http\JsonResponse;

class ModuleController extends Controller
{
    protected ModuleService $moduleService;
    
    public function __construct(ModuleService $moduleService)
    {
        $this->moduleService = $moduleService;
    }
    
    /**
     * Display module management interface
     */
    public function index()
    {
        $modules = $this->moduleService->getAllModules();
        $installed = $this->moduleService->getInstalledModules();
        
        return view('admin.modules.index', [
            'modules' => $modules,
            'installedModules' => $installed,
            'categories' => config('modules.categories', [])
        ]);
    }
    
    /**
     * Install a module
     */
    public function install(Request $request, string $slug)
    {
        try {
            $this->moduleService->installModule($slug);
            
            return response()->json([
                'success' => true,
                'message' => "Module '{$slug}' installed successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
    
    /**
     * Uninstall a module
     */
    public function uninstall(Request $request, string $slug)
    {
        try {
            $this->moduleService->uninstallModule($slug);
            
            return response()->json([
                'success' => true,
                'message' => "Module '{$slug}' uninstalled successfully"
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
    
    /**
     * Toggle module status (enable/disable)
     */
    public function toggle(Request $request, string $slug)
    {
        try {
            $isInstalled = $this->moduleService->isModuleInstalled($slug);
            
            if ($isInstalled) {
                $this->moduleService->uninstallModule($slug);
                $message = "Module '{$slug}' disabled";
            } else {
                $this->moduleService->installModule($slug);
                $message = "Module '{$slug}' enabled";
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'installed' => !$isInstalled
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
    
    /**
     * Get installed modules (API endpoint)
     */
    public function getInstalled(): JsonResponse
    {
        return response()->json([
            'modules' => $this->moduleService->getInstalledModules()
        ]);
    }
}