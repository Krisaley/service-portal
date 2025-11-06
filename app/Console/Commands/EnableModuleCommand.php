<?php

namespace App\Console\Commands;

use App\Models\Module;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EnableModuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:enable {slug : The module slug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable a module globally';

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

        if ($module->enabled) {
            $this->warn("Module '{$module->name}' is already enabled.");
            return Command::SUCCESS;
        }

        DB::beginTransaction();

        try {
            $module->update(['enabled' => true]);

            activity()
                ->performedOn($module)
                ->causedBy(auth()->id() ?? 1)
                ->log('Module enabled globally');

            DB::commit();

            $this->info("Module '{$module->name}' has been enabled successfully.");
            $this->comment('Teams can now install this module.');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to enable module: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
