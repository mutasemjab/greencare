<?php

namespace App\Console\Commands;

use App\Models\ReportSchedule;
use App\Models\Notification;
use App\Http\Controllers\Admin\FCMController;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendReportReminders extends Command
{
    protected $signature = 'reports:send-reminders';
    protected $description = 'Send report reminder notifications to nurses and doctors';

    public function handle()
    {
        $this->info('Sending report reminders...');

        // Get schedules due in next 15 minutes that haven't been sent
        $upcomingSchedules = ReportSchedule::with(['room', 'template', 'user'])
            ->where('notification_sent', false)
            ->where('completed', false)
            ->whereBetween('scheduled_for', [
                now(),
                now()->addMinutes(15)
            ])
            ->get();

        $sentCount = 0;

        foreach ($upcomingSchedules as $schedule) {
            $user = $schedule->user;
            $template = $schedule->template;
            $room = $schedule->room;

            if (!$user || !$template || !$room) {
                continue;
            }

            $userTypeArabic = $user->user_type === 'doctor' ? 'طبيب' : 'ممرض/ممرضة';
            
            $title = '📋 تذكير بتعبئة التقرير';
            $body = sprintf(
                'حان موعد تعبئة تقرير "%s" للغرفة: %s',
                $template->title_ar ?? $template->title_en,
                $room->title
            );

            // Save notification in database
            Notification::create([
                'user_id' => $user->id,
                'title' => $title,
                'body' => $body,
            ]);

            // Send FCM notification if user has token
            if ($user->fcm_token) {
                $result = FCMController::sendMessage(
                    $title,
                    $body,
                    $user->fcm_token,
                    $user->id,
                    'report_reminder'
                );

                if ($result) {
                    $schedule->update([
                        'notification_sent' => true,
                        'notification_sent_at' => now()
                    ]);
                    $sentCount++;
                    $this->info("Sent reminder to {$user->name} ({$userTypeArabic}) for room: {$room->title}");
                }
            } else {
                // Mark as sent even without FCM token
                $schedule->update([
                    'notification_sent' => true,
                    'notification_sent_at' => now()
                ]);
                $sentCount++;
            }
        }

        $this->info("Successfully sent {$sentCount} report reminders!");
        return 0;
    }
}