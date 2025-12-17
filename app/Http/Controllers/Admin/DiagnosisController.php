<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppointmentProvider;
use App\Models\PatientDiagnosis;
use App\Models\Medication;
use App\Models\MedicationSchedule;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DiagnosisController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:diagnosis-table', ['only' => ['index', 'show']]);
        $this->middleware('permission:diagnosis-add', ['only' => ['create', 'store']]);
        $this->middleware('permission:diagnosis-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:diagnosis-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of appointments needing diagnosis
     */
    public function index(Request $request)
    {
        $query = AppointmentProvider::with(['provider', 'user', 'diagnosis'])
            ->whereIn('status', [2, 3, 4]); // Accepted, OnTheWay, Delivered

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name_of_patient', 'like', "%{$search}%")
                  ->orWhere('phone_of_patient', 'like', "%{$search}%")
                  ->orWhereHas('provider', function($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Filter by diagnosis status
        if ($request->has('has_diagnosis')) {
            if ($request->has_diagnosis == '1') {
                $query->has('diagnosis');
            } else {
                $query->doesntHave('diagnosis');
            }
        }

        $appointments = $query->orderBy('date_of_appointment', 'desc')
                             ->orderBy('time_of_appointment', 'desc')
                             ->paginate(15);

        return view('admin.diagnosis.index', compact('appointments'));
    }

    /**
     * Show the form for creating a new diagnosis
     */
    public function create($appointmentId)
    {
        $appointment = AppointmentProvider::with(['provider', 'user'])->findOrFail($appointmentId);
        
        // Check if diagnosis already exists
        if ($appointment->diagnosis) {
            return redirect()->route('diagnosis.edit', $appointment->diagnosis->id)
                ->with('info', __('messages.diagnosis_already_exists'));
        }

        $rooms = Room::orderBy('title')->get();
        
        return view('admin.diagnosis.create', compact('appointment', 'rooms'));
    }

    /**
     * Store a newly created diagnosis
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointment_provider_id' => 'required|exists:appointment_providers,id',
            'patient_id' => 'required|exists:users,id',
            'room_id' => 'nullable|exists:rooms,id',
            'diagnosis' => 'required|string',
            'symptoms' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'notes' => 'nullable|string',
            'medications' => 'nullable|array',
            'medications.*.name' => 'required|string|max:255',
            'medications.*.dosage' => 'nullable|string|max:100',
            'medications.*.quantity' => 'nullable|integer|min:1',
            'medications.*.notes' => 'nullable|string|max:1000',
            'medications.*.schedules' => 'required|array|min:1',
            'medications.*.schedules.*.time' => 'required|date_format:H:i',
            'medications.*.schedules.*.frequency' => 'required|in:daily,weekly,monthly',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Create diagnosis
            $diagnosis = PatientDiagnosis::create([
                'appointment_provider_id' => $request->appointment_provider_id,
                'patient_id' => $request->patient_id,
                'diagnosed_by' => auth()->id(),
                'room_id' => $request->room_id,
                'diagnosis' => $request->diagnosis,
                'symptoms' => $request->symptoms,
                'treatment_plan' => $request->treatment_plan,
                'notes' => $request->notes,
            ]);

            // Create medications if provided
            if ($request->has('medications') && !empty($request->medications)) {
                foreach ($request->medications as $medicationData) {
                    if (!empty($medicationData['name'])) {
                        $medication = Medication::create([
                            'patient_id' => $request->patient_id,
                            'room_id' => $request->room_id,
                            'diagnosis_id' => $diagnosis->id,
                            'name' => $medicationData['name'],
                            'dosage' => $medicationData['dosage'] ?? null,
                            'quantity' => $medicationData['quantity'] ?? null,
                            'notes' => $medicationData['notes'] ?? null,
                        ]);

                        // Create medication schedules
                        if (isset($medicationData['schedules'])) {
                            foreach ($medicationData['schedules'] as $schedule) {
                                MedicationSchedule::create([
                                    'medication_id' => $medication->id,
                                    'time' => $schedule['time'],
                                    'frequency' => $schedule['frequency'],
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('diagnosis.index')
                ->with('success', __('messages.diagnosis_created_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', __('messages.error_creating_diagnosis') . ': ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified diagnosis
     */
    public function show(PatientDiagnosis $diagnosis)
    {
        $diagnosis->load([
            'appointment.provider',
            'patient',
            'diagnosedBy',
            'room',
            'medications.schedules'
        ]);

        return view('admin.diagnosis.show', compact('diagnosis'));
    }

    /**
     * Show the form for editing the specified diagnosis
     */
    public function edit(PatientDiagnosis $diagnosis)
    {
        $diagnosis->load(['appointment', 'medications.schedules']);
        $rooms = Room::orderBy('title')->get();
        
        return view('admin.diagnosis.edit', compact('diagnosis', 'rooms'));
    }

    /**
     * Update the specified diagnosis
     */
    public function update(Request $request, PatientDiagnosis $diagnosis)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'nullable|exists:rooms,id',
            'diagnosis' => 'required|string',
            'symptoms' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'notes' => 'nullable|string',
            'medications' => 'nullable|array',
            'medications.*.name' => 'required|string|max:255',
            'medications.*.dosage' => 'nullable|string|max:100',
            'medications.*.quantity' => 'nullable|integer|min:1',
            'medications.*.notes' => 'nullable|string|max:1000',
            'medications.*.schedules' => 'required|array|min:1',
            'medications.*.schedules.*.time' => 'required|date_format:H:i',
            'medications.*.schedules.*.frequency' => 'required|in:daily,weekly,monthly',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            // Update diagnosis
            $diagnosis->update([
                'room_id' => $request->room_id,
                'diagnosis' => $request->diagnosis,
                'symptoms' => $request->symptoms,
                'treatment_plan' => $request->treatment_plan,
                'notes' => $request->notes,
            ]);

            // Delete old medications
            Medication::where('diagnosis_id', $diagnosis->id)->delete();

            // Create new medications
            if ($request->has('medications') && !empty($request->medications)) {
                foreach ($request->medications as $medicationData) {
                    if (!empty($medicationData['name'])) {
                        $medication = Medication::create([
                            'patient_id' => $diagnosis->patient_id,
                            'room_id' => $request->room_id,
                            'diagnosis_id' => $diagnosis->id,
                            'name' => $medicationData['name'],
                            'dosage' => $medicationData['dosage'] ?? null,
                            'quantity' => $medicationData['quantity'] ?? null,
                            'notes' => $medicationData['notes'] ?? null,
                        ]);

                        // Create medication schedules
                        if (isset($medicationData['schedules'])) {
                            foreach ($medicationData['schedules'] as $schedule) {
                                MedicationSchedule::create([
                                    'medication_id' => $medication->id,
                                    'time' => $schedule['time'],
                                    'frequency' => $schedule['frequency'],
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();
            return redirect()->route('diagnosis.index')
                ->with('success', __('messages.diagnosis_updated_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', __('messages.error_updating_diagnosis'))
                ->withInput();
        }
    }

    /**
     * Remove the specified diagnosis
     */
    public function destroy(PatientDiagnosis $diagnosis)
    {
        try {
            $diagnosis->delete();
            
            return redirect()->route('diagnosis.index')
                ->with('success', __('messages.diagnosis_deleted_successfully'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', __('messages.error_deleting_diagnosis'));
        }
    }
}