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
            'room_id'                       => 'nullable|exists:rooms,id',
            'name'                          => 'required|string',
            'dosage'                        => 'nullable|string',
            'routes'                        => 'nullable|string',
            'quantity'                      => 'nullable|integer|min:1',
            'notes'                         => 'nullable|string',
            'schedules'                     => 'nullable|array',
            'schedules.*.time'              => 'required_with:schedules|date_format:H:i',
            'schedules.*.frequency'         => 'required_with:schedules|in:daily,weekly,monthly',
            'schedules.*.day_of_week'       => 'nullable|integer|between:0,6',
            'schedules.*.day_of_month'      => 'nullable|integer|between:1,31',
        ]);

        DB::beginTransaction();

        try {
            $medication = Medication::create([
                'patient_id' => $patient->id,
                'room_id'    => $validated['room_id'] ?? null,
                'name'       => $validated['name'],
                'dosage'     => $validated['dosage'] ?? null,
                'routes'     => $validated['routes'] ?? null,
                'quantity'   => $validated['quantity'] ?? null,
                'notes'      => $validated['notes'] ?? null,
            ]);

            if (!empty($validated['schedules'])) {
                foreach ($validated['schedules'] as $schedule) {
                    MedicationSchedule::create([
                        'medication_id' => $medication->id,
                        'time'          => $schedule['time'],
                        'frequency'     => $schedule['frequency'] ?? 'daily',
                        'day_of_week'   => $schedule['day_of_week'] ?? null,
                        'day_of_month'  => $schedule['day_of_month'] ?? null,
                    ]);
                }
            }

            DB::commit();

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
            'room_id'                       => 'nullable|exists:rooms,id',
            'name'                          => 'required|string',
            'dosage'                        => 'nullable|string',
            'routes'                        => 'nullable|string',
            'quantity'                      => 'nullable|integer|min:1',
            'notes'                         => 'nullable|string',
            'schedules'                     => 'nullable|array',
            'schedules.*.time'              => 'required_with:schedules|date_format:H:i',
            'schedules.*.frequency'         => 'required_with:schedules|in:daily,weekly,monthly',
            'schedules.*.day_of_week'       => 'nullable|integer|between:0,6',
            'schedules.*.day_of_month'      => 'nullable|integer|between:1,31',
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
                'routes'   => $validated['routes'] ?? null,
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
                        'day_of_week'   => $schedule['day_of_week'] ?? null,
                        'day_of_month'  => $schedule['day_of_month'] ?? null,
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

    private function generateMedicationLogs(Medication $medication): void
    {
        $endDate = now()->addDays(30);

        foreach ($medication->schedules as $schedule) {
            [$hour, $minute] = array_map('intval', explode(':', $schedule->time_for_input));

            switch ($schedule->frequency) {
                case 'weekly':
                    if ($schedule->day_of_week === null) break;
                    $date = now()->copy()->startOfDay();
                    if ($date->dayOfWeek !== $schedule->day_of_week) {
                        $date = $date->next($schedule->day_of_week);
                    }
                    while ($date <= $endDate) {
                        $st = $date->copy()->setTime($hour, $minute);
                        if ($st > now()) $this->createLogIfNotExists($medication->id, $st);
                        $date->addWeek();
                    }
                    break;

                case 'monthly':
                    if ($schedule->day_of_month === null) break;
                    $month = now()->copy()->startOfMonth();
                    while ($month <= $endDate) {
                        $actualDay = min($schedule->day_of_month, $month->daysInMonth);
                        $st = $month->copy()->day($actualDay)->setTime($hour, $minute);
                        if ($st > now()) $this->createLogIfNotExists($medication->id, $st);
                        $month->addMonth();
                    }
                    break;

                default: // daily
                    $date = now()->copy()->startOfDay();
                    while ($date <= $endDate) {
                        $st = $date->copy()->setTime($hour, $minute);
                        if ($st > now()) $this->createLogIfNotExists($medication->id, $st);
                        $date->addDay();
                    }
                    break;
            }
        }
    }

    private function regenerateFutureLogs(Medication $medication): void
    {
        $medication->logs()
            ->where('scheduled_time', '>', now())
            ->where('taken', false)
            ->whereNull('notification_sent_at')
            ->delete();

        $this->generateMedicationLogs($medication);
    }

    private function createLogIfNotExists(int $medicationId, Carbon $scheduledTime): void
    {
        if (!MedicationLog::where('medication_id', $medicationId)->where('scheduled_time', $scheduledTime)->exists()) {
            MedicationLog::create([
                'medication_id'  => $medicationId,
                'scheduled_time' => $scheduledTime,
                'taken'          => false,
            ]);
        }
    }
}
