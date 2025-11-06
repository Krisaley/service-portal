<?php

namespace App\Console\Commands;

use App\Models\Module;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DisableModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:disable {slug : The module slug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable a module globally';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $slug = $this->argument('slug');

        $module = Module::where('slug', $slug)->first();

        if (!$module) {
            $this->error("Module '{$slug}' not found.");
            return Command::FAILURE;
        }

        if (!$module->enabled) {
            $this->warn("Module '{$module->name}' is already disabled.");
            return Command::SUCCESS;
        }

        DB::beginTransaction();

        try {
            $module->update(['enabled' => false]);

            activity()
                ->performedOn($module)
                ->causedBy(auth()->id() ?? 1)
                ->log('Module disabled globally');

            DB::commit();

            $this->info("Module '{$module->name}' has been disabled successfully.");
            $this->comment('Teams can no longer install this module until it is re-enabled.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to disable module: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
