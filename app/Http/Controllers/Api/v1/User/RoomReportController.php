<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\ReportTemplate;
use App\Models\Report;
use App\Models\ReportAnswer;
use App\Models\Medication;
use App\Models\MedicationSchedule;
use App\Models\User;
use App\Services\FirestoreRoomService;
use App\Traits\Responses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class RoomReportController extends Controller
{
    use Responses;

    public function getPatient()
    {
        $data= User::where('user_type','patient')->get();
        return $this->success_response('Patients retrieved successfully',$data);
    }
    
    /**
     * Create a new room
     */
    public function createRoom(Request $request, FirestoreRoomService $firestoreService)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount' => 'required',
            'family_id' => 'nullable|exists:families,id',
            'patient_id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return $this->error_response('Validation failed', $validator->errors());
        }

        DB::beginTransaction();
        try {
            // Create the room
            $room = Room::create([
                'title' => $request->title,
                'description' => $request->description,
                'discount' => $request->discount,
                'family_id' => $request->family_id,
            ]);

            // Add patient to room
            $room->users()->attach($request->patient_id, ['role' => 'patient']);
            
            // Add current user (creator) to room
            $room->users()->attach(Auth::id(), ['role' => Auth::user()->user_type]);

            // Sync to Firestore for chat functionality
            $firestoreService->syncRoom($room);

            DB::commit();

            return $this->success_response('Room created successfully', [
                'room' => $room->load('users')
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return $this->error_response('Failed to create room', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get initial setup templates for a room
     */
    public function getInitialTemplates(Request $request,$room_id)
    {

        // Verify user has access to the room
        $room = Room::find($room_id);
        if (!$room->users()->where('user_id', Auth::id())->exists()) {
            return $this->error_response('Access denied', 'You do not have access to this room');
        }

        // Get initial setup templates
        $initialTemplates = ReportTemplate::where('report_type', 'initial_setup')
            ->with(['sections.fields.options'])
            ->get();

        return $this->success_response('Initial templates retrieved successfully', [
            'templates' => $initialTemplates
        ]);
    }

    /**
     * Submit initial report with patient data and medications
     */
    public function submitInitialReport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'template_id' => 'required|exists:report_templates,id',
            'answers' => 'required|array',
            'answers.*.field_id' => 'required|exists:report_fields,id',
            'answers.*.value' => 'required',
            'medications' => 'nullable|array',
            'medications.*.name' => 'required|string',
            'medications.*.dosage' => 'nullable|string',
            'medications.*.quantity' => 'nullable|integer',
            'medications.*.notes' => 'nullable|string',
            'medications.*.schedules' => 'required|array|min:1',
            'medications.*.schedules.*.time' => 'required|date_format:H:i',
            'medications.*.schedules.*.frequency' => 'required|in:daily,weekly,monthly',
        ]);

        if ($validator->fails()) {
            return $this->error_response('Validation failed', $validator->errors());
        }

        // Verify user has access to the room
        $room = Room::find($request->room_id);
        if (!$room->users()->where('user_id', Auth::id())->exists()) {
            return $this->error_response('Access denied', 'You do not have access to this room');
        }

        // Verify template is initial setup type
        $template = ReportTemplate::find($request->template_id);
        if ($template->report_type !== 'initial_setup') {
            return $this->error_response('Invalid template', 'Only initial setup templates are allowed');
        }

        DB::beginTransaction();
        try {
            // Create the report
            $report = Report::create([
                'room_id' => $request->room_id,
                'report_template_id' => $request->template_id,
                'created_by' => Auth::id(),
            ]);

            // Save report answers
            foreach ($request->answers as $answer) {
                ReportAnswer::create([
                    'report_id' => $report->id,
                    'report_field_id' => $answer['field_id'],
                    'value' => json_encode($answer['value']),
                ]);
            }

            // Get patient from room
            $patient = $room->users()->where('role', 'patient')->first();

            // Save medications if provided
            if ($request->has('medications') && !empty($request->medications)) {
                foreach ($request->medications as $medicationData) {
                    $medication = Medication::create([
                        'patient_id' => $patient->id,
                        'room_id' => $room->id,
                        'name' => $medicationData['name'],
                        'dosage' => $medicationData['dosage'] ?? null,
                        'quantity' => $medicationData['quantity'] ?? null,
                        'notes' => $medicationData['notes'] ?? null,
                    ]);

                    // Create medication schedules
                    foreach ($medicationData['schedules'] as $schedule) {
                        MedicationSchedule::create([
                            'medication_id' => $medication->id,
                            'time' => $schedule['time'],
                            'frequency' => $schedule['frequency'],
                        ]);
                    }
                }
            }

            DB::commit();

            // Load the complete report with relationships
            $report->load(['template.sections.fields.options', 'answers']);

            return $this->success_response('Initial report submitted successfully', [
                'report' => $report,
                'medications_count' => $request->has('medications') ? count($request->medications) : 0
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return $this->error_response('Failed to submit initial report', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get available report templates for current user type
     */
    public function getAvailableTemplates(Request $request,$room_id)
    {

        // Verify user has access to the room
        $room = Room::find($room_id);
        $userInRoom = $room->users()->where('user_id', Auth::id())->first();
        
        if (!$userInRoom) {
            return $this->error_response('Access denied', 'You do not have access to this room');
        }

        // Get user type from users table directly
        $currentUser = Auth::user();
        $userType = $currentUser->user_type;

        // Map user_type to created_for field in templates
        $createdFor = ($userType === 'doctor') ? 'doctor' : 'nurse';

        // Get recurring templates for this user type
        $templates = ReportTemplate::where('report_type', 'recurring')
            ->where('created_for', $createdFor)
            ->with(['sections.fields.options'])
            ->get();

        return $this->success_response('Templates retrieved successfully', [
            'user_type' => $userType,
            'templates' => $templates
        ]);
    }

   

    /**
     * Submit a recurring report
     */
    
    public function submitRecurringReport(Request $request)
    {
        $currentUser = Auth::user();
        $userType = $currentUser->user_type;

        // Dynamic validation based on user type
        $rules = [
            'room_id' => 'required|exists:rooms,id',
            'template_id' => 'required|exists:report_templates,id',
            'answers' => 'required|array',
            'answers.*.field_id' => 'required|exists:report_fields,id',
            'answers.*.value' => 'required',
        ];

        // Add date/hour validation based on user type
        if ($userType === 'doctor') {
            $rules['date'] = 'required|date_format:Y-m-d'; // e.g., 2025-10-07
        } else if ($userType === 'nurse') {
            $rules['hour'] = 'required|date_format:H:i'; // e.g., 04:00
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return $this->error_response('Validation failed', $validator->errors());
        }

        // Verify user has access to the room
        $room = Room::find($request->room_id);
        $userInRoom = $room->users()->where('user_id', Auth::id())->first();
        
        if (!$userInRoom) {
            return $this->error_response('Access denied', 'You do not have access to this room');
        }

        // Verify template is recurring type and matches user type
        $template = ReportTemplate::find($request->template_id);
        if ($template->report_type !== 'recurring') {
            return $this->error_response('Invalid template', 'Only recurring templates are allowed');
        }

        // Map user_type to created_for field in templates
        $createdFor = ($userType === 'doctor') ? 'doctor' : 'nurse';
        
        if ($template->created_for !== $createdFor) {
            return $this->error_response('Access denied', 'This template is not for your user type');
        }

        DB::beginTransaction();
        try {
            // Prepare report datetime based on user type
            $reportDatetime = null;
            
            if ($userType === 'doctor') {
                // For doctor: use the provided date with current time
                $reportDatetime = $request->date . ' ' . now()->format('H:i:s');
            } else if ($userType === 'nurse') {
                // For nurse: use today's date with the provided hour
                $reportDatetime = now()->format('Y-m-d') . ' ' . $request->hour . ':00';
            }

            // Create the report
            $report = Report::create([
                'room_id' => $request->room_id,
                'report_template_id' => $request->template_id,
                'created_by' => Auth::id(),
                'report_datetime' => $reportDatetime,
            ]);

            // Save report answers
            foreach ($request->answers as $answer) {
                ReportAnswer::create([
                    'report_id' => $report->id,
                    'report_field_id' => $answer['field_id'],
                    'value' => json_encode($answer['value']),
                ]);
            }

            DB::commit();

            // Load the complete report with relationships
            $report->load(['template.sections.fields.options', 'answers']);

            return $this->success_response('Report submitted successfully', [
                'report' => $report
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return $this->error_response('Failed to submit report', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Get reports by date (for doctors) or date+hour (for nurses)
     */
    public function getReportsByTime(Request $request)
    {
        $currentUser = Auth::user();
        $userType = $currentUser->user_type;
        
        // Validate based on user type
        if ($userType === 'doctor') {
            $validator = Validator::make($request->all(), [
                'room_id' => 'required|exists:rooms,id',
                'date' => 'required|date_format:Y-m-d', // e.g., 2025-10-07
            ]);
        } else if ($userType === 'nurse') {
            $validator = Validator::make($request->all(), [
                'room_id' => 'required|exists:rooms,id',
                'date' => 'required|date_format:Y-m-d', // e.g., 2025-10-07
                'hour' => 'required|date_format:H:i', // e.g., 04:00 or 16:00
            ]);
        } else {
            return $this->error_response('Access denied', 'Invalid user type');
        }
        
        if ($validator->fails()) {
            return $this->error_response('Validation failed', $validator->errors());
        }
        
        // Verify user has access to the room
        $room = Room::find($request->room_id);
        $userInRoom = $room->users()->where('user_id', Auth::id())->first();
        
        if (!$userInRoom) {
            return $this->error_response('Access denied', 'You do not have access to this room');
        }
        
        // Build query based on user type
        $query = Report::with(['template.sections.fields.options', 'answers', 'creator'])
            ->where('room_id', $request->room_id)
            ->whereNotNull('report_datetime');
        
        if ($userType === 'doctor') {
            // Filter by DATE only (all reports on 2025-10-07)
            $query->whereDate('report_datetime', $request->date);
        } else if ($userType === 'nurse') {
            // Filter by DATE AND HOUR (specific report at 2025-10-07 04:00)
            $query->whereDate('report_datetime', $request->date)
                ->whereRaw('TIME_FORMAT(report_datetime, "%H:%i") = ?', [$request->hour]);
        }
        
        $reports = $query->orderBy('report_datetime', 'desc')->get();
        
        return $this->success_response('Reports retrieved successfully', [
            'reports' => $reports,
            'count' => $reports->count()
        ]);
    }

    /**
     * Get room reports history
     */
    public function getRoomReports(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_id' => 'required|exists:rooms,id',
            'template_type' => 'nullable|in:initial_setup,recurring',
            'per_page' => 'nullable|integer|min:1|max:100'
        ]);

        if ($validator->fails()) {
            return $this->error_response('Validation failed', $validator->errors());
        }

        // Verify user has access to the room
        $room = Room::find($request->room_id);
        if (!$room->users()->where('user_id', Auth::id())->exists()) {
            return $this->error_response('Access denied', 'You do not have access to this room');
        }

        $query = Report::where('room_id', $request->room_id)
            ->with(['template', 'creator:id,name', 'answers.field']);

        // Filter by template type if specified
        if ($request->has('template_type')) {
            $query->whereHas('template', function($q) use ($request) {
                $q->where('report_type', $request->template_type);
            });
        }

        $perPage = $request->get('per_page', 15);
        $reports = $query->latest()->paginate($perPage);

        return $this->success_response('Reports retrieved successfully', [
            'reports' => $reports
        ]);
    }

    /**
     * Get specific report details
     */
    public function getReport($reportId)
    {
        $report = Report::with([
            'template.sections.fields.options',
            'answers.field',
            'creator:id,name',
            'room:id,title,description'
        ])->find($reportId);

        if (!$report) {
            return $this->error_response('Report not found', null);
        }

        // Verify user has access to the room
        if (!$report->room->users()->where('user_id', Auth::id())->exists()) {
            return $this->error_response('Access denied', 'You do not have access to this report');
        }

        return $this->success_response('Report retrieved successfully', [
            'report' => $report
        ]);
    }

    /**
     * Get room medications
     */
    public function getRoomMedications(Request $request,$room_id)
    {

        // Verify user has access to the room
        $room = Room::find($room_id);
        if (!$room->users()->where('user_id', Auth::id())->exists()) {
            return $this->error_response('Access denied', 'You do not have access to this room');
        }

        $medications = Medication::where('room_id', $request->room_id)
            ->with(['schedules', 'patient:id,name'])
            ->get();

        return $this->success_response('Medications retrieved successfully', [
            'medications' => $medications
        ]);
    }


     /**
     * Get all rooms for the authenticated nurse with completion status
     */
    public function getNurseRooms(Request $request)
    {
        $currentUser = Auth::user();
        $userType = $currentUser->user_type;
        
        // Check if user is a nurse
        if ($userType !== 'nurse') {
            return $this->error_response('Access denied', 'This endpoint is only for nurses');
        }
        
        // Get all rooms where the nurse is a member
        $rooms = Room::whereHas('users', function($query) use ($currentUser) {
            $query->where('user_id', $currentUser->id)
                ->where('role', 'nurse');
        })
        ->with(['users']) // Load room users if needed
        ->get();
        
        // Add is_complete flag to each room
        $roomsWithStatus = $rooms->map(function($room) use ($currentUser) {
            // Check if there are any reports created by this nurse for this room
            $report = Report::where('room_id', $room->id)
                        ->where('created_by', $currentUser->id)
                        ->first();
            
            $isComplete = false;
            
            if ($report) {
                // Check if the report has any answers
                $hasAnswers = ReportAnswer::where('report_id', $report->id)->exists();
                $isComplete = $hasAnswers;
            }
            
            return [
                'id' => $room->id,
                'title' => $room->title,
                'description' => $room->description,
                'discount' => $room->discount,
                'family_id' => $room->family_id,
                'created_at' => $room->created_at,
                'updated_at' => $room->updated_at,
                'is_complete' => $isComplete,
            ];
        });
        
        return $this->success_response('Rooms retrieved successfully', [
            'rooms' => $roomsWithStatus,
            'count' => $roomsWithStatus->count()
        ]);
    }
}