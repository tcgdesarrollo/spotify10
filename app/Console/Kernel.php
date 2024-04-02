<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
         $schedule->command('app:read-chart')->cron('0 6-14 * * *');
         $schedule->command('send:telegram')->cron('* * * * *');
         $schedule->command('app:read-history')->cron('0 17 * * 6-7')->withoutOverlapping(120);
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
