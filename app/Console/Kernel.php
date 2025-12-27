<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // هذا سيعمل مرة واحدة يومياً في منتصف الليل
        $schedule->command('medications:generate-logs 7')
            ->daily()
            ->at('00:00')
            ->appendOutputTo(storage_path('logs/schedule.log'));

        // هذا سيعمل كل 5 دقائق
        $schedule->command('medications:send-reminders')
            ->everyFiveMinutes()
            ->appendOutputTo(storage_path('logs/schedule.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
