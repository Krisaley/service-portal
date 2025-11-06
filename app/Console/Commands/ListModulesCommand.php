<?php

namespace App\Console\Commands;

use App\Models\Module;
use App\Models\Team;
use Illuminate\Console\Command;

class ListModulesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:list {--team= : Filter by team ID} {--available : Show only available (not installed) modules}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all modules';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $teamId = $this->option('team');
        $availableOnly = $this->option('available');

        $modules = Module::with('dependencies')->get();

        if ($modules->isEmpty()) {
            $this->warn('No modules found in the database.');
            return Command::SUCCESS;
        }

        $headers = ['Name', 'Slug', 'Version', 'Category', 'Enabled', 'Dependencies'];
        $rows = [];

        foreach ($modules as $module) {
            $dependencies = $module->dependencies->pluck('name')->implode(', ') ?: 'None';

            if ($teamId) {
                $team = Team::find($teamId);
                if (!$team) {
                    $this->error("Team with ID '{$teamId}' not found.");
                    return Command::FAILURE;
                }

                $installed = $team->installedModules()->where('module_id', $module->id)->exists();
                $status = $installed ? '✓ Installed' : '✗ Not Installed';

                if ($availableOnly && $installed) {
                    continue;
                }

                $rows[] = [
                    $module->name,
                    $module->slug,
                    $module->version,
                    $module->category ?? 'N/A',
                    $module->enabled ? 'Yes' : 'No',
                    $dependencies,
                    $status,
                ];
            } else {
                $rows[] = [
                    $module->name,
                    $module->slug,
                    $module->version,
                    $module->category ?? 'N/A',
                    $module->enabled ? 'Yes' : 'No',
                    $dependencies,
                ];
            }
        }

        if ($teamId) {
            $headers[] = 'Status';
            $this->info("Modules for Team ID: {$teamId}");
        } else {
            $this->info('All Available Modules:');
        }

        $this->table($headers, $rows);

        return Command::SUCCESS;
    }
}
