<?php

namespace App\Console\Commands;

use App\Models\Module;
use App\Services\ModuleService;
use Illuminate\Console\Command;

class InstallModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:install {slug : The module slug} {--team= : The team ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install a module for a team';

    /**
     * Execute the console command.
     */
    public function handle(ModuleService $moduleService): int
    {
        $slug = $this->argument('slug');
        $teamId = $this->option('team');

        if (!$teamId) {
            $this->error('Team ID is required. Use --team=ID');
            return Command::FAILURE;
        }

        $module = Module::where('slug', $slug)->first();

        if (!$module) {
            $this->error("Module '{$slug}' not found.");
            return Command::FAILURE;
        }

        $team = \App\Models\Team::find($teamId);

        if (!$team) {
            $this->error("Team with ID '{$teamId}' not found.");
            return Command::FAILURE;
        }

        $this->info("Installing module '{$module->name}' for team '{$team->name}'...");

        $result = $moduleService->install($module, $team);

        if ($result['success']) {
            $this->info($result['message']);
            return Command::SUCCESS;
        } else {
            $this->error($result['message']);
            if (isset($result['dependencies'])) {
                $this->error('Unmet dependencies:');
                foreach ($result['dependencies'] as $dep) {
                    $this->error("  - {$dep['slug']}: {$dep['reason']}");
                }
            }
            return Command::FAILURE;
        }
    }
}
