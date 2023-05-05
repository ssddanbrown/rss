<?php

namespace App\Console;

use App\Console\Commands\PrunePostsCommand;
use App\Console\Commands\UpdateFeedsCommand;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(UpdateFeedsCommand::class)->everyFiveMinutes();
        $schedule->command(PrunePostsCommand::class, ['-n'])->daily();
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
