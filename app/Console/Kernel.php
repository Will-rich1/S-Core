<?php

namespace App\Console;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Auto-reset Telescope logs when entries exceed threshold.
        $schedule->call(function () {
            if (!Schema::hasTable('telescope_entries')) {
                return;
            }

            $entriesCount = DB::table('telescope_entries')->count();

            if ($entriesCount > 500) {
                Artisan::call('telescope:clear');
            }
        })->everyFiveMinutes();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
