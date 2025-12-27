<?php

namespace App\Console\Commands;

use App\Models\Medication;
use App\Models\MedicationLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateMedicationLogs extends Command
{
    protected $signature = 'medications:generate-logs {days=7}';
    protected $description = 'Generate medication logs for upcoming days';

    public function handle()
    {
        $days = $this->argument('days');
        $this->info("Generating medication logs for next {$days} days...");

        $medications = Medication::with('schedules')->get();
        $logsCreated = 0;

        foreach ($medications as $medication) {
            foreach ($medication->schedules as $schedule) {
                for ($day = 0; $day < $days; $day++) {
                    $scheduledTime = $this->calculateScheduledTime($schedule, $day);
                    
                    // تحقق من عدم وجود سجل مسبق
                    $exists = MedicationLog::where('medication_id', $medication->id)
                        ->where('scheduled_time', $scheduledTime)
                        ->exists();

                    if (!$exists) {
                        MedicationLog::create([
                            'medication_id' => $medication->id,
                            'scheduled_time' => $scheduledTime,
                            'taken' => false,
                        ]);
                        $logsCreated++;
                    }
                }
            }
        }

        $this->info("Created {$logsCreated} medication logs successfully!");
        return 0;
    }

    private function calculateScheduledTime($schedule, $dayOffset)
    {
        $baseDate = Carbon::today()->addDays($dayOffset);
        $time = Carbon::parse($schedule->time);

        $scheduledDateTime = $baseDate->setTime($time->hour, $time->minute);

        // حساب التكرار
        switch ($schedule->frequency) {
            case 'weekly':
                // إذا كان أسبوعي، تحقق من اليوم المناسب
                $dayOfWeek = $time->dayOfWeek;
                $scheduledDateTime = $baseDate->next($dayOfWeek)->setTime($time->hour, $time->minute);
                break;
            
            case 'monthly':
                // إذا كان شهري، نفس اليوم من كل شهر
                $dayOfMonth = $time->day;
                $scheduledDateTime = $baseDate->day($dayOfMonth)->setTime($time->hour, $time->minute);
                break;
            
            case 'daily':
            default:
                // يومي - الإعداد الافتراضي
                break;
        }

        return $scheduledDateTime;
    }
}