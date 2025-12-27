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
        // ============================================
        // Medication Schedules
        // ============================================
        
        // Generate medication logs daily at midnight
        $schedule->command('medications:generate-logs 7')
            ->daily()
            ->at('00:00')
            ->appendOutputTo(storage_path('logs/medications-generate.log'));

        // Send medication reminders every 5 minutes
        $schedule->command('medications:send-reminders')
            ->everyFiveMinutes()
            ->appendOutputTo(storage_path('logs/medications-reminders.log'));

        // ============================================
        // Report Schedules
        // ============================================
        
        // Generate report schedules daily at midnight
        $schedule->command('reports:generate-schedules 7')
            ->daily()
            ->at('00:00')
            ->appendOutputTo(storage_path('logs/reports-generate.log'));

        // Send report reminders every 5 minutes
        $schedule->command('reports:send-reminders')
            ->everyFiveMinutes()
            ->appendOutputTo(storage_path('logs/reports-reminders.log'));

        // ============================================
        // Optional: Cleanup old logs (recommended)
        // ============================================
        
        // Clean up old completed schedules (older than 30 days)
        $schedule->call(function () {
            \App\Models\ReportSchedule::where('completed', true)
                ->where('updated_at', '<', now()->subDays(30))
                ->delete();
                
            \App\Models\MedicationLog::where('taken', true)
                ->where('updated_at', '<', now()->subDays(30))
                ->delete();
        })
        ->daily()
        ->at('02:00')
        ->appendOutputTo(storage_path('logs/cleanup.log'));

        // ============================================
        // Optional: Summary log daily
        // ============================================
        
        $schedule->call(function () {
            $summary = [
                'date' => now()->format('Y-m-d H:i:s'),
                'medications' => [
                    'total_logs' => \App\Models\MedicationLog::count(),
                    'pending' => \App\Models\MedicationLog::where('taken', false)->count(),
                    'taken' => \App\Models\MedicationLog::where('taken', true)->count(),
                ],
                'reports' => [
                    'total_schedules' => \App\Models\ReportSchedule::count(),
                    'pending' => \App\Models\ReportSchedule::where('completed', false)->count(),
                    'completed' => \App\Models\ReportSchedule::where('completed', true)->count(),
                ],
            ];
            
            \Log::channel('daily')->info('Daily Summary', $summary);
        })
        ->daily()
        ->at('23:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Get the timezone that should be used by default for scheduled events.
     */
    protected function scheduleTimezone(): string
    {
        return 'Asia/Amman'; // أو حسب timezone بلدك
    }
}