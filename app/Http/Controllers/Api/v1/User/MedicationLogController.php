<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\MedicationLog;
use Illuminate\Http\Request;

class MedicationLogController extends Controller
{
    public function markAsTaken(Request $request, $logId)
    {
        $log = MedicationLog::findOrFail($logId);
        
        // تحقق من أن المستخدم هو صاحب الدواء
        if ($log->medication->patient_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $log->update([
            'taken' => true,
            'taken_at' => now()
        ]);

        return response()->json([
            'message' => 'تم تسجيل أخذ الدواء بنجاح',
            'log' => $log
        ]);
    }

    public function getTodaySchedule()
    {
        $userId = auth()->id();
        
        $logs = MedicationLog::whereHas('medication', function($query) use ($userId) {
                $query->where('patient_id', $userId);
            })
            ->whereDate('scheduled_time', today())
            ->orderBy('scheduled_time')
            ->with('medication')
            ->get();

        return response()->json([
            'logs' => $logs
        ]);
    }

    public function getUpcomingReminders()
    {
        $userId = auth()->id();
        
        $logs = MedicationLog::whereHas('medication', function($query) use ($userId) {
                $query->where('patient_id', $userId);
            })
            ->where('taken', false)
            ->where('scheduled_time', '>', now())
            ->orderBy('scheduled_time')
            ->limit(10)
            ->with('medication')
            ->get();

        return response()->json([
            'upcoming' => $logs
        ]);
    }
}