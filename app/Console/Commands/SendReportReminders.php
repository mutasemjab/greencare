<?php

namespace App\Console\Commands;

use App\Models\ReportSchedule;
use App\Models\Notification;
use App\Models\RoomReportTemplateHistory;
use App\Http\Controllers\Admin\FCMController;
use Illuminate\Console\Command;

class SendReportReminders extends Command
{
    protected $signature = 'reports:send-reminders';
    protected $description = 'Send report reminder notifications to nurses and doctors';

    public function handle()
    {
        $this->info('Sending report reminders...');

        // Get the currently active recurring template per room
        $activeTemplatesByRoom = RoomReportTemplateHistory::where('is_active', true)
            ->whereHas('template', fn($q) => $q->where('report_type', 'recurring'))
            ->pluck('report_template_id', 'room_id'); // [room_id => template_id]

        if ($activeTemplatesByRoom->isEmpty()) {
            $this->info('No active recurring templates found in any room.');
            return 0;
        }

        // Get schedules due in the next 15 minutes
        $upcomingSchedules = ReportSchedule::with(['room', 'template', 'user'])
            ->where('notification_sent', false)
            ->where('completed', false)
            ->whereBetween('scheduled_for', [now(), now()->addMinutes(15)])
            // Only recurring templates
            ->whereHas('template', fn($q) => $q->where('report_type', 'recurring'))
            // Only schedules whose template is the CURRENT active one for their room
            ->where(function ($query) use ($activeTemplatesByRoom) {
                foreach ($activeTemplatesByRoom as $roomId => $templateId) {
                    $query->orWhere(function ($q) use ($roomId, $templateId) {
                        $q->where('room_id', $roomId)
                          ->where('report_template_id', $templateId);
                    });
                }
            })
            ->get();

        $sentCount = 0;

        foreach ($upcomingSchedules as $schedule) {
            $user     = $schedule->user;
            $template = $schedule->template;
            $room     = $schedule->room;

            if (!$user || !$template || !$room) {
                $this->warn("Schedule #{$schedule->id} missing user/template/room — skipped.");
                continue;
            }

            $userTypeArabic = $user->user_type === 'doctor' ? 'طبيب' : 'ممرض/ممرضة';

            $title = '📋 تذكير بتعبئة التقرير';
            $body  = sprintf(
                'حان موعد تعبئة تقرير "%s" للغرفة: %s',
                $template->title_ar ?? $template->title_en,
                $room->title
            );

            // Save in-app notification
            Notification::create([
                'user_id' => $user->id,
                'title'   => $title,
                'body'    => $body,
            ]);

            if ($user->fcm_token) {
                $result = FCMController::sendMessage(
                    $title,
                    $body,
                    $user->fcm_token,
                    $user->id,
                    'report_reminder'
                );

                if ($result) {
                    $this->info("Sent to {$user->name} ({$userTypeArabic}) — room: {$room->title} — template: {$template->title_ar}");
                } else {
                    $this->warn("FCM failed for {$user->name} (user #{$user->id})");
                }
            } else {
                $this->warn("No FCM token for {$user->name} (user #{$user->id}) — in-app notification saved only.");
            }

            // Mark as sent regardless (in-app notification was saved)
            $schedule->update([
                'notification_sent'    => true,
                'notification_sent_at' => now(),
            ]);
            $sentCount++;
        }

        $this->info("Done. Processed {$sentCount} reminder(s).");
        return 0;
    }
}
