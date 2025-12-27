<?php

namespace App\Console\Commands;

use App\Models\MedicationLog;
use App\Http\Controllers\Admin\FCMController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendMedicationReminders extends Command
{
    protected $signature = 'medications:send-reminders';
    protected $description = 'Send medication reminder notifications';

    public function handle()
    {
        $this->info('Sending medication reminders...');

        // جلب السجلات المجدولة خلال الـ 15 دقيقة القادمة والتي لم تُؤخذ
        $upcomingLogs = MedicationLog::with(['medication.patient'])
            ->where('taken', false)
            ->whereBetween('scheduled_time', [
                now(),
                now()->addMinutes(15)
            ])
            ->whereNull('notification_sent_at') 
            ->get();

        $sentCount = 0;

        foreach ($upcomingLogs as $log) {
            $patient = $log->medication->patient;
            
            if (!$patient || !$patient->fcm_token) {
                continue;
            }

            $medication = $log->medication;
            
            $title = '⏰ تذكير بموعد الدواء';
            $body = sprintf(
                'حان موعد تناول دواء %s - الجرعة: %s',
                $medication->name,
                $medication->dosage ?? 'غير محدد'
            );

            $result = FCMController::sendToUser(
                $patient->id,
                $title,
                $body,
                'medication_reminder'
            );

            if ($result) {
                // تسجيل أن الإشعار تم إرساله
                $log->update(['notification_sent_at' => now()]);
                $sentCount++;
                $this->info("Sent reminder to {$patient->name} for {$medication->name}");
            }
        }

        $this->info("Successfully sent {$sentCount} medication reminders!");
        return 0;
    }
}