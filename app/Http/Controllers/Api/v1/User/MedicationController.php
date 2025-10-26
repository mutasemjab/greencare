<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;

use App\Models\Medication;
use App\Models\MedicationSchedule;
use Illuminate\Http\Request;
use App\Traits\Responses;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MedicationController extends Controller
{
    use Responses;

    /**
     * Get all medications for the authenticated patient
     */
    public function index()
    {
        $patient = Auth::user();

        $medications = Medication::with(['schedules', 'logs'])
            ->where('patient_id', $patient->id)
            ->get();

        return $this->success_response('Medications fetched successfully', $medications);
    }

    /**
     * Store new medication for authenticated patient
     */
    public function store(Request $request)
    {
        $patient = Auth::user();

        $validated = $request->validate([
            'room_id' => 'nullable|exists:rooms,id',
            'name' => 'required|string',
            'dosage' => 'nullable|string',
            'quantity' => 'nullable|integer',
            'notes' => 'nullable|string',
            'schedules' => 'nullable|array',
            'schedules.*.time' => 'required_with:schedules|date_format:H:i',
            'schedules.*.frequency' => 'in:daily,weekly,monthly'
        ]);

        DB::beginTransaction();

        try {
            $medication = Medication::create([
                'patient_id' => $patient->id,
                'room_id' => $validated['room_id'] ?? null,
                'name' => $validated['name'],
                'dosage' => $validated['dosage'] ?? null,
                'quantity' => $validated['quantity'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            if (!empty($validated['schedules'])) {
                foreach ($validated['schedules'] as $schedule) {
                    MedicationSchedule::create([
                        'medication_id' => $medication->id,
                        'time' => $schedule['time'],
                        'frequency' => $schedule['frequency'] ?? 'daily',
                    ]);
                }
            }

            DB::commit();

            return $this->success_response('Medication added successfully', $medication->load('schedules'));
        } catch (\Throwable $e) {
            DB::rollBack();
            return $this->error_response('Failed to add medication', $e->getMessage());
        }
    }

    /**
     * Update existing medication and its schedules
     */
    public function update(Request $request, $id)
    {
        $patient = Auth::user();

        $validated = $request->validate([
            'room_id' => 'nullable|exists:rooms,id',
            'name' => 'required|string',
            'dosage' => 'nullable|string',
            'quantity' => 'nullable|integer',
            'notes' => 'nullable|string',
            'schedules' => 'nullable|array',
            'schedules.*.time' => 'required_with:schedules|date_format:H:i',
            'schedules.*.frequency' => 'in:daily,weekly,monthly'
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
                'room_id' => $validated['room_id'] ?? null,
                'name' => $validated['name'],
                'dosage' => $validated['dosage'] ?? null,
                'quantity' => $validated['quantity'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Handle schedules update
            if (isset($validated['schedules'])) {
                // Delete old schedules and replace with new ones
                $medication->schedules()->delete();

                foreach ($validated['schedules'] as $schedule) {
                    MedicationSchedule::create([
                        'medication_id' => $medication->id,
                        'time' => $schedule['time'],
                        'frequency' => $schedule['frequency'] ?? 'daily',
                    ]);
                }
            }

            DB::commit();

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

}
