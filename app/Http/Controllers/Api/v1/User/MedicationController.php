<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\Medication;
use App\Models\MedicationLog;
use App\Models\MedicationSchedule;
use App\Traits\Responses;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MedicationController extends Controller
{
    use Responses;

    public function index()
    {
        $patient = Auth::user();

        $medications = Medication::with(['schedules', 'logs'])
            ->where('patient_id', $patient->id)
            ->get();

        return $this->success_response('Medications fetched successfully', $medications);
    }

    public function store(Request $request)
    {
        $patient = Auth::user();

        $validated = $request->validate([
            'room_id'              => 'nullable|exists:rooms,id',
            'name'                 => 'required|string',
            'dosage'               => 'nullable|string',
            'quantity'             => 'nullable',
            'notes'                => 'nullable|string',
            'schedules'            => 'nullable|array',
            'schedules.*.time'     => 'required_with:schedules|date_format:H:i',
            'schedules.*.frequency' => 'in:daily,weekly,monthly',
        ]);

        DB::beginTransaction();

        try {
            $medication = Medication::create([
                'patient_id' => $patient->id,
                'room_id'    => $validated['room_id'] ?? null,
                'name'       => $validated['name'],
                'dosage'     => $validated['dosage'] ?? null,
                'quantity'   => $validated['quantity'] ?? null,
                'notes'      => $validated['notes'] ?? null,
            ]);

            if (!empty($validated['schedules'])) {
                foreach ($validated['schedules'] as $schedule) {
                    MedicationSchedule::create([
                        'medication_id' => $medication->id,
                        'time'          => $schedule['time'],
                        'frequency'     => $schedule['frequency'] ?? 'daily',
                    ]);
                }
            }

            DB::commit();

            // Generate logs after commit so IDs are stable
            $medication->load('schedules');
            $this->generateMedicationLogs($medication);

            return $this->success_response('Medication added successfully', $medication->load('schedules'));
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error_response('Failed to add medication', $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $patient = Auth::user();

        $validated = $request->validate([
            'room_id'              => 'nullable|exists:rooms,id',
            'name'                 => 'required|string',
            'dosage'               => 'nullable|string',
            'quantity'             => 'nullable',
            'notes'                => 'nullable|string',
            'schedules'            => 'nullable|array',
            'schedules.*.time'     => 'required_with:schedules|date_format:H:i',
            'schedules.*.frequency' => 'in:daily,weekly,monthly',
        ]);

        $medication = Medication::where('id', $id)
            ->where('patient_id', $patient->id)
            ->first();

        if (!$medication) {
            return $this->error_response('Medication not found', null);
        }

        DB::beginTransaction();

        try {
            $medication->update([
                'room_id'  => $validated['room_id'] ?? null,
                'name'     => $validated['name'],
                'dosage'   => $validated['dosage'] ?? null,
                'quantity' => $validated['quantity'] ?? null,
                'notes'    => $validated['notes'] ?? null,
            ]);

            if (isset($validated['schedules'])) {
                $medication->schedules()->delete();

                foreach ($validated['schedules'] as $schedule) {
                    MedicationSchedule::create([
                        'medication_id' => $medication->id,
                        'time'          => $schedule['time'],
                        'frequency'     => $schedule['frequency'] ?? 'daily',
                    ]);
                }
            }

            DB::commit();

            $medication->load('schedules');
            $this->regenerateFutureLogs($medication);

            return $this->success_response('Medication updated successfully', $medication->load('schedules'));
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error_response('Failed to update medication', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        $patient = Auth::user();

        $medication = Medication::where('id', $id)
            ->where('patient_id', $patient->id)
            ->first();

        if (!$medication) {
            return $this->error_response('Medication not found', null);
        }

        try {
            $medication->delete();

            return $this->success_response('Medication deleted successfully', null);
        } catch (\Throwable $e) {
            return $this->error_response('Failed to delete medication', $e->getMessage());
        }
    }

    /**
     * Generate medication logs for the next 30 days.
     * Skips slots that already exist to avoid duplicates.
     */
    private function generateMedicationLogs(Medication $medication)
    {
        $endDate     = now()->addDays(30);
        $currentDate = now()->startOfDay();

        foreach ($medication->schedules as $schedule) {
            $date = $currentDate->copy();

            while ($date <= $endDate) {
                $scheduledTime = $date->copy()->setTimeFromTimeString($schedule->time_for_input);

                if ($scheduledTime > now()) {
                    $exists = MedicationLog::where('medication_id', $medication->id)
                        ->where('scheduled_time', $scheduledTime)
                        ->exists();

                    if (!$exists) {
                        MedicationLog::create([
                            'medication_id'  => $medication->id,
                            'scheduled_time' => $scheduledTime,
                            'taken'          => false,
                        ]);
                    }
                }

                switch ($schedule->frequency) {
                    case 'weekly':  $date->addWeek();  break;
                    case 'monthly': $date->addMonth(); break;
                    default:        $date->addDay();   break;
                }
            }
        }
    }

    /**
     * Delete future untaken logs and regenerate from the updated schedules.
     */
    private function regenerateFutureLogs(Medication $medication)
    {
        $medication->logs()
            ->where('scheduled_time', '>', now())
            ->where('taken', false)
            ->whereNull('notification_sent_at')
            ->delete();

        $this->generateMedicationLogs($medication);
    }
}
