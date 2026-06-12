<?php

namespace App\Console\Commands;

use App\Models\MedicationLog;
use App\Models\Notification;
use App\Http\Controllers\Admin\FCMController;
use Illuminate\Console\Command;

class SendMedicationReminders extends Command
{
    protected $signature = 'medications:send-reminders';
    protected $description = 'Send medication reminder notifications to room nurses';

    public function handle()
    {
        $this->info('Sending medication reminders...');

        $upcomingLogs = MedicationLog::with(['medication.room.users'])
            ->where('taken', false)
            ->whereBetween('scheduled_time', [
                now(),
                now()->addMinutes(15),
            ])
            ->whereNull('notification_sent_at')
            ->get();

        if ($upcomingLogs->isEmpty()) {
            $this->info('No upcoming medication logs found in the next 15 minutes.');
            return 0;
        }

        $sentCount = 0;

        foreach ($upcomingLogs as $log) {
            $medication = $log->medication;

            if (!$medication) {
                $this->warn("MedicationLog #{$log->id} has no medication — skipped.");
                continue;
            }

            $room = $medication->room;

            if (!$room) {
                $this->warn("Medication #{$medication->id} ({$medication->name}) has no room — skipped.");
                continue;
            }

            $title = '💊 تذكير بموعد الدواء';
            $body  = sprintf(
                'حان موعد إعطاء دواء "%s"%s — الغرفة: %s',
                $medication->name,
                $medication->dosage ? ' — الجرعة: ' . $medication->dosage : '',
                $room->title
            );

            $roomUsers = $room->users;

            if ($roomUsers->isEmpty()) {
                $this->warn("Room #{$room->id} ({$room->title}) has no users — skipped medication #{$medication->id}.");
                continue;
            }

            $notified = false;

            foreach ($roomUsers as $roomUser) {
                // Save in-app notification regardless of FCM
                Notification::create([
                    'user_id' => $roomUser->id,
                    'title'   => $title,
                    'body'    => $body,
                ]);

                if ($roomUser->fcm_token) {
                    $result = FCMController::sendMessage(
                        $title,
                        $body,
                        $roomUser->fcm_token,
                        $roomUser->id,
                        'medication_reminder'
                    );

                    if ($result) {
                        $this->info("Sent to {$roomUser->name} ({$roomUser->pivot->role}) — {$medication->name} — room: {$room->title}");
                    } else {
                        $this->warn("FCM failed for {$roomUser->name} (#{$roomUser->id}) — in-app notification saved.");
                    }
                } else {
                    $this->warn("{$roomUser->name} (#{$roomUser->id}) has no FCM token — in-app notification saved only.");
                }

                $notified = true;
            }

            if ($notified) {
                $log->update(['notification_sent_at' => now()]);
                $sentCount++;
            }
        }

        $this->info("Done. Processed {$sentCount} medication reminder(s).");
        return 0;
    }
}
