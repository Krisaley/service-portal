<?php

namespace App\Console\Commands;

use App\Models\Module;
use App\Services\ModuleService;
use Illuminate\Console\Command;

class UninstallModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:uninstall {slug : The module slug} {--team= : The team ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Uninstall a module from a team';

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

        if (!$this->confirm("Are you sure you want to uninstall module '{$module->name}' from team '{$team->name}'?")) {
            $this->info('Uninstallation cancelled.');
            return Command::SUCCESS;
        }

        $this->info("Uninstalling module '{$module->name}' from team '{$team->name}'...");

        $result = $moduleService->uninstall($module, $team);

        if ($result['success']) {
            $this->info($result['message']);
            return Command::SUCCESS;
        } else {
            $this->error($result['message']);
            if (isset($result['dependents'])) {
                $this->error('Dependent modules:');
                foreach ($result['dependents'] as $dep) {
                    $this->error("  - {$dep}");
                }
            }
            return Command::FAILURE;
        }
    }
}
