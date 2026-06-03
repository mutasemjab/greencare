<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Medication;
use App\Models\MedicationSchedule;
use App\Models\MedicationLog;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class MedicationController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:medication-table', ['only' => ['index', 'show']]);
        $this->middleware('permission:medication-add', ['only' => ['create', 'store']]);
        $this->middleware('permission:medication-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:medication-delete', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of medications
     */
    public function index(Request $request)
    {
        $query = Medication::with(['patient', 'room', 'schedules']);

        // Filter by room
        if ($request->has('room_id') && !empty($request->room_id)) {
            $query->where('room_id', $request->room_id);
        }

        // Filter by patient
        if ($request->has('patient_id') && !empty($request->patient_id)) {
            $query->where('patient_id', $request->patient_id);
        }

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('dosage', 'like', "%{$search}%")
                  ->orWhereHas('patient', function($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $medications = $query->orderBy('created_at', 'desc')->paginate(15);
        $rooms = Room::orderBy('title')->get();
        $patients = User::where('user_type', 'patient')->orderBy('name')->get();

        return view('admin.medications.index', compact('medications', 'rooms', 'patients'));
    }

    /**
     * Show the form for creating a new medication
     */
    public function create(Request $request)
    {
        $rooms = Room::with('patients')->orderBy('title')->get();
        $patients = User::where('user_type', 'patient')->orderBy('name')->get();
        
        $selectedRoom = null;
        $selectedPatient = null;
        
        if ($request->has('room_id')) {
            $selectedRoom = Room::find($request->room_id);
        }
        
        if ($request->has('patient_id')) {
            $selectedPatient = User::find($request->patient_id);
        }

        return view('admin.medications.create', compact('rooms', 'patients', 'selectedRoom', 'selectedPatient'));
    }

    /**
     * Store a newly created medication
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'patient_id'                    => 'required|exists:users,id',
            'room_id'                       => 'nullable|exists:rooms,id',
            'name'                          => 'required|string|max:255',
            'dosage'                        => 'nullable|string|max:100',
            'routes'                        => 'nullable|string|max:100',
            'quantity'                      => 'nullable|integer|min:1',
            'notes'                         => 'nullable|string',
            'schedules'                     => 'required|array|min:1',
            'schedules.*.time'              => 'required|date_format:H:i',
            'schedules.*.frequency'         => 'required|in:daily,weekly,monthly',
            'schedules.*.day_of_week'       => 'nullable|integer|between:0,6',
            'schedules.*.day_of_month'      => 'nullable|integer|between:1,31',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $medication = Medication::create([
                'patient_id' => $request->patient_id,
                'room_id'    => $request->room_id,
                'name'       => $request->name,
                'dosage'     => $request->dosage,
                'routes'     => $request->routes,
                'quantity'   => $request->quantity,
                'notes'      => $request->notes,
            ]);

            foreach ($request->schedules as $scheduleData) {
                MedicationSchedule::create([
                    'medication_id' => $medication->id,
                    'time'          => $scheduleData['time'],
                    'frequency'     => $scheduleData['frequency'],
                    'day_of_week'   => $scheduleData['day_of_week'] ?? null,
                    'day_of_month'  => $scheduleData['day_of_month'] ?? null,
                ]);
            }

            // Generate initial medication logs
            $this->generateMedicationLogs($medication);

            DB::commit();
            return redirect()->route('medications.index')
                ->with('success', __('messages.medication_created_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating medication: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', __('messages.error_creating_medication') . ' - ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified medication
     */
    public function show(Medication $medication)
    {
        $medication->load(['patient', 'room', 'schedules', 'logs' => function($query) {
            $query->orderBy('scheduled_time', 'desc')->limit(20);
        }]);

        $upcomingLogs = $medication->logs()
            ->where('scheduled_time', '>', now())
            ->where('taken', false)
            ->orderBy('scheduled_time')
            ->limit(5)
            ->get();

        $overdueLogs = $medication->logs()
            ->where('scheduled_time', '<', now())
            ->where('taken', false)
            ->orderBy('scheduled_time', 'desc')
            ->limit(5)
            ->get();

        return view('admin.medications.show', compact('medication', 'upcomingLogs', 'overdueLogs'));
    }

    /**
     * Show the form for editing the specified medication
     */
    public function edit(Medication $medication)
    {
        $medication->load('schedules');
        $rooms = Room::with('patients')->orderBy('title')->get();
        $patients = User::where('user_type', 'patient')->orderBy('name')->get();

        return view('admin.medications.edit', compact('medication', 'rooms', 'patients'));
    }

    /**
     * Update the specified medication
     */
    public function update(Request $request, Medication $medication)
    {
        $validator = Validator::make($request->all(), [
            'patient_id'                    => 'required|exists:users,id',
            'room_id'                       => 'nullable|exists:rooms,id',
            'name'                          => 'required|string|max:255',
            'dosage'                        => 'nullable|string|max:100',
            'routes'                        => 'nullable|string|max:100',
            'quantity'                      => 'nullable|integer|min:1',
            'notes'                         => 'nullable|string',
            'schedules'                     => 'required|array|min:1',
            'schedules.*.time'              => 'required|date_format:H:i',
            'schedules.*.frequency'         => 'required|in:daily,weekly,monthly',
            'schedules.*.day_of_week'       => 'nullable|integer|between:0,6',
            'schedules.*.day_of_month'      => 'nullable|integer|between:1,31',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $medication->update([
                'patient_id' => $request->patient_id,
                'room_id'    => $request->room_id,
                'name'       => $request->name,
                'dosage'     => $request->dosage,
                'routes'     => $request->routes,
                'quantity'   => $request->quantity,
                'notes'      => $request->notes,
            ]);

            $medication->schedules()->delete();

            foreach ($request->schedules as $scheduleData) {
                MedicationSchedule::create([
                    'medication_id' => $medication->id,
                    'time'          => $scheduleData['time'],
                    'frequency'     => $scheduleData['frequency'],
                    'day_of_week'   => $scheduleData['day_of_week'] ?? null,
                    'day_of_month'  => $scheduleData['day_of_month'] ?? null,
                ]);
            }

            // Regenerate future medication logs
            $this->regenerateFutureLogs($medication);

            DB::commit();
            return redirect()->route('medications.index')
                ->with('success', __('messages.medication_updated_successfully'));

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error updating medication: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', __('messages.error_updating_medication'))
                ->withInput();
        }
    }

    /**
     * Remove the specified medication
     */
    public function destroy(Medication $medication)
    {
        try {
            $medication->delete();
            return redirect()->route('medications.index')
                ->with('success', __('messages.medication_deleted_successfully'));
        } catch (\Exception $e) {
            Log::error('Error deleting medication: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', __('messages.error_deleting_medication'));
        }
    }

    /**
     * Mark medication as taken
     */
    public function markTaken(Request $request, MedicationLog $log)
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            $log->update([
                'taken' => true,
                'taken_at' => now(),
                'notes' => $request->notes
            ]);

            return redirect()->back()
                ->with('success', __('messages.medication_marked_taken'));
        } catch (\Exception $e) {
            Log::error('Error marking medication taken: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', __('messages.error_marking_medication'));
        }
    }

    /**
     * Mark medication as missed
     */
    public function markMissed(Request $request, MedicationLog $log)
    {
        $validator = Validator::make($request->all(), [
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            $log->update([
                'taken' => false,
                'notes' => $request->notes
            ]);

            return redirect()->back()
                ->with('success', __('messages.medication_marked_missed'));
        } catch (\Exception $e) {
            Log::error('Error marking medication missed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', __('messages.error_marking_medication'));
        }
    }

    /**
     * Get medication calendar data
     */
    public function getCalendarData(Request $request, Medication $medication)
    {
        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);

        $logs = $medication->logs()
            ->whereBetween('scheduled_time', [$start, $end])
            ->get();

        $events = $logs->map(function ($log) {
            return [
                'id' => $log->id,
                'title' => $log->medication->name,
                'start' => $log->scheduled_time->toISOString(),
                'backgroundColor' => $log->taken ? '#28a745' : ($log->is_overdue ? '#dc3545' : '#ffc107'),
                'borderColor' => $log->taken ? '#28a745' : ($log->is_overdue ? '#dc3545' : '#ffc107'),
                'textColor' => '#fff',
                'extendedProps' => [
                    'taken' => $log->taken,
                    'overdue' => $log->is_overdue,
                    'notes' => $log->notes,
                    'dosage' => $log->medication->dosage,
                    'quantity' => $log->medication->quantity
                ]
            ];
        });

        return response()->json($events);
    }

    /**
     * Get upcoming medications for dashboard
     */
    public function getUpcoming(Request $request)
    {
        $hours = $request->get('hours', 24); // Default 24 hours
        
        $query = MedicationLog::with(['medication.patient'])
            ->where('taken', false)
            ->where('scheduled_time', '>=', now())
            ->where('scheduled_time', '<=', now()->addHours($hours));

        if ($request->has('room_id')) {
            $query->whereHas('medication', function($q) use ($request) {
                $q->where('room_id', $request->room_id);
            });
        }

        $upcomingMedications = $query->orderBy('scheduled_time')->get();

        return response()->json($upcomingMedications);
    }

    /**
     * Get overdue medications
     */
    public function getOverdue(Request $request)
    {
        $query = MedicationLog::with(['medication.patient'])
            ->where('taken', false)
            ->where('scheduled_time', '<', now());

        if ($request->has('room_id')) {
            $query->whereHas('medication', function($q) use ($request) {
                $q->where('room_id', $request->room_id);
            });
        }

        $overdueMedications = $query->orderBy('scheduled_time', 'desc')->get();

        return response()->json($overdueMedications);
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