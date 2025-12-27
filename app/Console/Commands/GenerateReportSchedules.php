<?php

namespace App\Console\Commands;

use App\Models\Room;
use App\Models\RoomUser;
use App\Models\ReportTemplate;
use App\Models\ReportSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;

class GenerateReportSchedules extends Command
{
    protected $signature = 'reports:generate-schedules {days=7}';
    protected $description = 'Generate report schedules for nurses and doctors';

    public function handle()
    {
        $days = $this->argument('days');
        $this->info("Generating report schedules for next {$days} days...");

        $schedulesCreated = 0;

        // Get all active rooms
        $rooms = Room::with(['users' => function($query) {
            $query->whereIn('role', ['doctor', 'nurse']);
        }])->get();

        foreach ($rooms as $room) {
            // Get recurring templates
            $templates = ReportTemplate::where('report_type', 'recurring')->get();

            foreach ($templates as $template) {
                // Get users for this template type
                $users = $room->users()
                    ->where('user_type', $template->created_for)
                    ->get();

                foreach ($users as $user) {
                    $schedulesCreated += $this->generateSchedulesForUser(
                        $room,
                        $template,
                        $user,
                        $days
                    );
                }
            }
        }

        $this->info("Created {$schedulesCreated} report schedules successfully!");
        return 0;
    }

    private function generateSchedulesForUser($room, $template, $user, $days)
    {
        $created = 0;

        switch ($template->frequency) {
            case 'daily':
                // For nurses - every hour
                if ($template->created_for === 'nurse') {
                    for ($day = 0; $day < $days; $day++) {
                        for ($hour = 0; $hour < 24; $hour++) {
                            $scheduledTime = Carbon::today()
                                ->addDays($day)
                                ->setHour($hour)
                                ->setMinute(0);

                            if ($this->createScheduleIfNotExists($room, $template, $user, $scheduledTime)) {
                                $created++;
                            }
                        }
                    }
                } 
                // For doctors - once per day
                else {
                    for ($day = 0; $day < $days; $day++) {
                        $scheduledTime = Carbon::today()
                            ->addDays($day)
                            ->setHour(9) // 9 AM
                            ->setMinute(0);

                        if ($this->createScheduleIfNotExists($room, $template, $user, $scheduledTime)) {
                            $created++;
                        }
                    }
                }
                break;

            case 'weekly':
                $weeksToGenerate = ceil($days / 7);
                for ($week = 0; $week < $weeksToGenerate; $week++) {
                    $scheduledTime = Carbon::today()
                        ->addWeeks($week)
                        ->startOfWeek()
                        ->setHour(9)
                        ->setMinute(0);

                    if ($this->createScheduleIfNotExists($room, $template, $user, $scheduledTime)) {
                        $created++;
                    }
                }
                break;

            case 'monthly':
                $monthsToGenerate = ceil($days / 30);
                for ($month = 0; $month < $monthsToGenerate; $month++) {
                    $scheduledTime = Carbon::today()
                        ->addMonths($month)
                        ->startOfMonth()
                        ->setHour(9)
                        ->setMinute(0);

                    if ($this->createScheduleIfNotExists($room, $template, $user, $scheduledTime)) {
                        $created++;
                    }
                }
                break;
        }

        return $created;
    }

    private function createScheduleIfNotExists($room, $template, $user, $scheduledTime)
    {
        // Check if schedule already exists
        $exists = ReportSchedule::where('room_id', $room->id)
            ->where('report_template_id', $template->id)
            ->where('user_id', $user->id)
            ->where('scheduled_for', $scheduledTime)
            ->exists();

        if (!$exists && $scheduledTime->isFuture()) {
            ReportSchedule::create([
                'room_id' => $room->id,
                'report_template_id' => $template->id,
                'user_id' => $user->id,
                'scheduled_for' => $scheduledTime,
                'notification_sent' => false,
                'completed' => false,
            ]);
            return true;
        }

        return false;
    }
}